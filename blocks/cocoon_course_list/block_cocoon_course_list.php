<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

include_once($CFG->dirroot . '/course/lib.php');

class block_cocoon_course_list extends block_list {
    function init() {
        $this->title = get_string('pluginname', 'block_cocoon_course_list');
    }

    function has_config() {
        return true;
    }

    function applicable_formats() {
        return array(
          'all' => true,
          'my' => false,
        );
    }


    function get_content() {
        global $CFG, $USER, $DB, $OUTPUT;

        if($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';
        if(!empty($this->config->title)){$this->content->title = $this->config->title;}

        $icon = $OUTPUT->pix_icon('i/course', get_string('course'));

        $adminseesall = true;
        if (isset($CFG->block_cocoon_course_list_adminview)) {
           if ( $CFG->block_cocoon_course_list_adminview == 'own'){
               $adminseesall = false;
           }
        }

        $allcourselink =
            (has_capability('moodle/course:update', context_system::instance())
            || empty($CFG->block_cocoon_course_list_hideallcourseslink)) &&
            core_course_category::user_top();

        // if (empty($CFG->disablemycourses) and isloggedin() and !isguestuser() and
        //   !(has_capability('moodle/course:update', context_system::instance()) and $adminseesall)) {    // Just print My Courses
        //     if ($courses = enrol_get_my_courses()) {
        //         foreach ($courses as $course) {
        //             $coursecontext = context_course::instance($course->id);
        //             $linkcss = $course->visible ? "" : " class=\"dimmed\" ";
        //             $this->content->items[]="<a $linkcss title=\"" . format_string($course->shortname, true, array('context' => $coursecontext)) . "\" ".
        //                        "href=\"$CFG->wwwroot/course/view.php?id=$course->id\">".$icon.format_string(get_course_display_name_for_list($course)). "</a>";
        //         }
        //         $this->title = get_string('mycourses');
        //     /// If we can update any course of the view all isn't hidden, show the view all courses link
        //         if ($allcourselink) {
        //             $this->content->footer = "<a href=\"$CFG->wwwroot/course/index.php\">".get_string("fulllistofcourses")."</a> ...";
        //         }
        //     }
        //     $this->get_remote_courses();
        //     if ($this->content->items) { // make sure we don't return an empty list
        //         return $this->content;
        //     }
        // }
        $this->content->footer = '
        <div class="selected_filter_widget style2 mb30">
          <div id="accordion" class="panel-group">
            <div class="panel">
              <div class="panel-heading">
                <h4 class="panel-title">
                  <a href="#panelBodySoftware" class="accordion-toggle link fz20 mb15" data-toggle="collapse" data-parent="#accordion">'. format_text($this->content->title, FORMAT_HTML, array('filter' => true)) .'</a>
                </h4>
              </div>
              <div id="panelBodySoftware" class="panel-collapse collapse show">
                <div class="panel-body">
                  <div class="category_sidebar_widget">
                    <ul class="category_list">';
        $topcategory = core_course_category::top();
        if ($topcategory->is_uservisible() && ($categories = $topcategory->get_children())) { // Check we have categories.
          if (count($categories) > 1 || (count($categories) == 1 && $DB->count_records('course') > 200)) {     // Just print top level category links
            foreach ($categories as $category) {
              $categoryname = $category->get_formatted_name();
              $linkcss = $category->visible ? "" : " class=\"dimmed\" ";
              $this->content->footer .= '
              <li><a href="'.$CFG->wwwroot .'/course/index.php?categoryid='.$category->id.'">'. $categoryname .' <span class="float-right">('. $category->coursecount .')</span></a></li>';
            }
          }
        } else {                          // Just print course names of single category
          $category = array_shift($categories);
          $courses = $category->get_courses();
          if ($courses) {
            foreach ($courses as $course) {
              $coursecontext = context_course::instance($course->id);
              $linkcss = $course->visible ? "" : " class=\"dimmed\" ";
              $this->content->footer .= '
              <li><a href="'.$CFG->wwwroot .'/course/view.php?id='.$course->id.'">'. $course->get_formatted_name() .'</a></li>';
            }
          }
        }
        $this->content->footer .='
                    </ul>
                    <a class="color-orose" href=" '.$CFG->wwwroot.'/course/index.php"><span class="fa fa-plus pr10"></span> '.get_string('see_more', 'theme_edumy').'</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>';
        return $this->content;
    }

    function get_remote_courses() {
        global $CFG, $USER, $OUTPUT;

        if (!is_enabled_auth('mnet')) {
            // no need to query anything remote related
            return;
        }

        $icon = $OUTPUT->pix_icon('i/mnethost', get_string('host', 'mnet'));

        // shortcut - the rest is only for logged in users!
        if (!isloggedin() || isguestuser()) {
            return false;
        }

        if ($courses = get_my_remotecourses()) {
            $this->content->items[] = get_string('remotecourses','mnet');
            $this->content->icons[] = '';
            foreach ($courses as $course) {
                $this->content->items[]="<a title=\"" . format_string($course->shortname, true) . "\" ".
                    "href=\"{$CFG->wwwroot}/auth/mnet/jump.php?hostid={$course->hostid}&amp;wantsurl=/course/view.php?id={$course->remoteid}\">"
                    .$icon. format_string(get_course_display_name_for_list($course)) . "</a>";
            }
            // if we listed courses, we are done
            return true;
        }

        if ($hosts = get_my_remotehosts()) {
            $this->content->items[] = get_string('remotehosts', 'mnet');
            $this->content->icons[] = '';
            foreach($USER->mnet_foreign_host_array as $somehost) {
                $this->content->items[] = $somehost['count'].get_string('courseson','mnet').'<a title="'.$somehost['name'].'" href="'.$somehost['url'].'">'.$icon.$somehost['name'].'</a>';
            }
            // if we listed hosts, done
            return true;
        }

        return false;
    }

    /**
     * Returns the role that best describes the course list block.
     *
     * @return string
     */
    public function get_aria_role() {
        return 'navigation';
    }
    public function html_attributes() {
      global $CFG;
      $attributes = parent::html_attributes();
      include($CFG->dirroot . '/theme/edumy/ccn/block_handler/attributes.php');
      return $attributes;
    }
}
