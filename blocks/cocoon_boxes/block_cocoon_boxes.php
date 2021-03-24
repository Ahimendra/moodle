<?php

include_once($CFG->dirroot . '/course/lib.php');
// require_once($CFG->libdir . '/coursecatlib.php');
require_once($CFG->dirroot. '/course/renderer.php');

class block_cocoon_boxes extends block_list {
    function init() {
        $this->title = get_string('pluginname', 'block_cocoon_boxes');
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


    function instance_allow_config() {
        return true;
    }

    function get_content() {
        global $CFG, $USER, $DB, $OUTPUT;

        if($this->content !== NULL) {
            return $this->content;
        }

        if (!empty($this->config) && is_object($this->config)) {
            $data = $this->config;
            $data->items = is_numeric($data->items) ? (int)$data->items : 8;
        } else {
            $data = new stdClass();
            $data->items = 0;
        }

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';
        if(!empty($this->config->title)){$this->content->title = $this->config->title;}
        if(!empty($this->config->subtitle)){$this->content->subtitle = $this->config->subtitle;}
        if(!empty($this->config->body)){$this->content->body = $this->config->body;}
        if(!empty($this->config->button_link)){$this->content->button_link = $this->config->button_link;}else{$this->content->button_link = "#our-courses";}
        if(!empty($this->config->button_text)){$this->content->button_text = $this->config->button_text;}else{$this->content->button_text = "View All Courses";}
        if ($this->config->style == 1) {
          $this->content->style = '<a href="#our-courses">
				    	<div class="mouse_scroll">
			        		<div class="icon"><span class="flaticon-download-arrow"></span></div>
				    	</div>
				    </a>';
        } else {
          $this->content->style = '';
        }

        $adminseesall = true;
        if (isset($CFG->block_cocoon_boxes_adminview)) {
           if ( $CFG->block_cocoon_boxes_adminview == 'own'){
               $adminseesall = false;
           }
        }

        // if ($data->items > 0) {
        $this->content->footer = '

        <section id="our-courses" class="our-courses pt90 pt650-992">
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					'.$this->content->style.'
				</div>
			</div>
		</div>
		<div class="container">
			<div class="row">
      <div class="col-lg-6 offset-lg-3">
        <div class="main-title text-center">
          <h3 class="mt0">'. format_text($this->content->title, FORMAT_HTML, array('filter' => true)) .'</h3>
          <p>'.format_text($this->content->subtitle, FORMAT_HTML, array('filter' => true)).'</p>
        </div>
      </div>

			</div>
			<div class="row">
      <div class="col-xs-12">
      <div class="row">';

          $topcategory = core_course_category::top();
          $col_class = "";
          if ($data->items == 1) {
            $col_class = "col-sm-12 col-lg-12";
          } else if ($data->items == 2) {
            $col_class = "col-sm-6 col-lg-6";
          } else if ($data->items == 3) {
            $col_class = "col-sm-6 col-lg-4";
          } else {
            $col_class = "col-sm-6 col-lg-3";
          }
          if ($data->items > 0) {
            for ($i = 1; $i <= $data->items; $i++) {
              // if(++$i > $data->items) break;
              $icon = 'icon' . $i;
              $color = 'color' . $i;
              $title = 'title' . $i;
              $body = 'body' . $i;

              $this->content->footer .='<div class="col-sm-6 col-lg-3">
					<div class="icon_hvr_img_box ccn-box sbbg1" style="background-color: '.$data->$color.';">
						<div class="overlay">
							<div class="ccn_icon_2 icon"><span class="'.$data->$icon.'"></span></div>
							<div class="details">
								<h5>'.$data->$title.'</h5>
								<p>'.$data->$body.'</p>
							</div>
						</div>
					</div>
				</div>';


              // $courseid = $course->id;
              // $chelper = new coursecat_helper();
              // print_object($category);

            }
          // if ($topcategory->is_uservisible() && ($categories = $topcategory->get_children())) { // Check we have categories.
          //     if (count($categories) > 1 || (count($categories) == 1 && $DB->count_records('course') > 200)) {     // Just print top level category links
          //         $i = 0;
          //         foreach ($categories as $category) {
          //           if(++$i > $data->items) break;
          //
          //           $outputimage = '';
          //           //ccnComm: Fetching the image manually added to the coursecat description via the Atto editor.
          //           $chelper = new coursecat_helper();
          //           $description = $chelper->get_category_formatted_description($category);
          //           if ($description) {
          //             $dom = new DOMDocument();
          //             $dom->loadHTML($description);
          //             $xpath = new DOMXPath($dom);
          //             $src = $xpath->evaluate("string(//img/@src)");
          //           }
          //           if ($src && $description){
          //             $outputimage = $src;
          //           } else {
          //             $children_courses = $category->get_courses();
          //             if($children_courses >= 1){
          //               $countNoOfCourses = '<p>'.get_string('number_of_courses', 'theme_edumy', count($children_courses)).'</p>';
          //             } else {
          //               $countNoOfCourses = '';
          //             }
          //             foreach($children_courses as $child_course) {
          //               if ($child_course === reset($children_courses)) {
          //                 foreach ($child_course->get_course_overviewfiles() as $file) {
          //                   if ($file->is_valid_image()) {
          //                     $imagepath = '/' . $file->get_contextid() . '/' . $file->get_component() . '/' . $file->get_filearea() . $file->get_filepath() . $file->get_filename();
          //                     $imageurl = file_encode_url($CFG->wwwroot . '/pluginfile.php', $imagepath, false);
          //                     $outputimage  =  $imageurl;
          //                     // Use the first image found.
          //                     break;
          //                   }
          //                 }
          //               }
          //             }
          //           }
          //             $categoryname = $category->get_formatted_name();
          //             $linkcss = $category->visible ? "" : " class=\"dimmed\" ";
          //             $this->content->footer .= '
          //             <div class="'.$col_class.'">
          //               <a href="'.$CFG->wwwroot .'/course/index.php?categoryid='.$category->id.'" class="img_hvr_box" style="background-image: url('. $outputimage .');">
          //                 <div class="overlay">
          //                   <div class="details">
          //                     <h5>'. $categoryname .'</h5>';
          //                     if($this->content->body != '1'){
          //                       $this->content->footer .='<p>'. format_string($category->description, $striplinks = true,$options = null).'</p>';
          //                     } else {
          //                       $this->content->footer .= $countNoOfCourses;
          //                     }
          //                     $this->content->footer .='
          //                   </div>
          //                 </div>
          //               </a>
          //             </div>';
          //
          //         }
          //       }
          //     }
              // else {                          // Just print course names of single category
              //   $category = array_shift($categories);
              //   $courses = $category->get_courses();
              //
              //   if ($courses) {
              //       foreach ($courses as $course) {
              //           $coursecontext = context_course::instance($course->id);
              //           $linkcss = $course->visible ? "" : " class=\"dimmed\" ";
              //
              //           $this->content->footer .= '
              //           <li><a href="'.$CFG->wwwroot .'/course/view.php?id='.$course->id.'">'. $course->get_formatted_name() .' <span class="float-right">()</span></a></li>
              //
              //           ';
              //       }
              //     }
              //   }
              }
              $this->content->footer .='
              </div>
            </div>
          </div>
        </div>
      </section>';
// }

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
