<?php
global $CFG;
class block_cocoon_course_intro extends block_base {
    public function init() { $this->title = get_string('cocoon_course_intro', 'block_cocoon_course_intro'); }
    public function specialization() {
      global $CFG;
      include($CFG->dirroot . '/theme/edumy/ccn/block_handler/specialization.php');
    }
    public function get_content() {
        global $CFG, $DB, $COURSE, $PAGE;
        // global $COURSE;
        $courseid = $COURSE->id;
        $context = context_course::instance($courseid);
        require_once($CFG->libdir . '/behat/lib.php');
        require_once($CFG->libdir . '/filelib.php');

        if ($this->content !== null) {
            return $this->content;
        }
        $this->content         =  new stdClass;
        if(!empty($this->config->title)){$this->content->title = $this->config->title;}
        if(!empty($this->config->teacher)){$this->content->teacher = $this->config->teacher;}
        if(!empty($this->config->accent)){$this->content->accent = $this->config->accent;}
        if(!empty($this->config->video)){$this->content->video = $this->config->video;}
        if(!empty($this->config->style)){$this->content->style = $this->config->style;}
        // if(!empty($this->config->rating)){$this->content->rating = $this->config->rating;}
        // if(!empty($this->config->updated)){$this->content->updated = $this->config->updated;}
        if(!empty($this->config->show_teacher)){$this->content->show_teacher = $this->config->show_teacher;}
        $cocoon_share_fb = 'https://www.facebook.com/sharer/sharer.php?u='. $this->page->url;
        // if(!empty($this->content->style) && $this->content->style == 1){
        //   $white = 'color-white';
        //   $breadcrumb = 'ccn-pullto-breadcrumb';
        // } elseif(!empty($this->content->style) && $this->content->style == 2){
        //   $white = 'color-white';
        //   $breadcrumb = 'ccn-pullto-breadcrumb-fullwidth';
        // } else {
        //   $white = '';
        //   $breadcrumb = '';
        // }
        $white = '';
        if($PAGE->theme->settings->course_single_style != 0){
          $white = 'color-white';
        }
        //Begin CCN Image Processing
        $fs = get_file_storage();
        $files = $fs->get_area_files($this->context->id, 'block_cocoon_course_intro', 'content');
        foreach ($files as $file) {
            $filename = $file->get_filename();
            if ($filename <> '.') {
                $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), null, $file->get_filepath(), $filename);
                $this->content->image = '<img class="thumb" src="' . $url . '" alt="' . $filename . '" />';
            }
        }
        // End CCN Image Processing

        $this->content->text = '
          <div class="cs_row_one">
            <div class="cs_ins_container">
              <div class="ccn-identify-course-intro">
                <div class="cs_instructor">
                  <ul class="cs_instrct_list float-left mb0">';
                  if(!empty($this->content->image)) {
                   $this->content->text .='
                    <li class="list-inline-item">'. $this->content->image .'</li>';
                  }
                  if($this->content->show_teacher == '1' && $this->content->teacher){
                    $this->content->text .='    <li class="list-inline-item"><a class="'.$white.'">'. format_text($this->content->teacher, FORMAT_HTML, array('filter' => true)) .'</a></li>';
                  }
                  if($PAGE->theme->settings->coursecat_modified != 1){
                    $this->content->text .='  <li class="list-inline-item"><a class="'.$white.'">'.get_string('last_updated', 'theme_edumy').' '. userdate($COURSE->timemodified, get_string('strftimedate', 'langconfig'), 0) .'</a></li>';
                  }
                  $this->content->text .='
                  </ul>
                  <ul class="cs_watch_list float-right mb0">
                    <li class="list-inline-item"><a class="'.$white.'" target="_blank" href="'.$cocoon_share_fb.'"><span class="flaticon-share"> '.get_string('share','theme_edumy').'</span></a></li>
                  </ul>
                </div>
                <h3 class="cs_title '.$white.'">'. format_text($COURSE->fullname, FORMAT_HTML, array('filter' => true)) .'</h3>
                <ul class="cs_review_seller">';
                  if(!empty($this->content->accent)) {
                    $this->content->text .='
                    <li class="list-inline-item"><a href="#"><span>'. format_text($this->content->accent, FORMAT_HTML, array('filter' => true)) .'</span></a></li>';
                  }

                  if($PAGE->theme->settings->course_ratings == 1){
                    $this->content->text .='<ul class="tc_review">
                      <li class="list-inline-item"><i class="fa fa-star"></i></li>
                      <li class="list-inline-item"><i class="fa fa-star"></i></li>
                      <li class="list-inline-item"><i class="fa fa-star"></i></li>
                      <li class="list-inline-item"><i class="fa fa-star"></i></li>
                      <li class="list-inline-item"><i class="fa fa-star"></i></li>
                    </ul>';
                  } elseif($PAGE->theme->settings->course_ratings == 2){
                    $block = block_instance('cocoon_course_rating');
                    $ccnRating = $block->external_star_rating($courseid);
                    $this->content->text .= $ccnRating;
                  }

                  // if(!empty($this->content->rating)) {
                  // $this->content->text .='
                  //   <li class="list-inline-item"><i class="fa fa-star"></i></li>
                  //   <li class="list-inline-item"><i class="fa fa-star"></i></li>
                  //   <li class="list-inline-item"><i class="fa fa-star"></i></li>
                  //   <li class="list-inline-item"><i class="fa fa-star"></i></li>
                  //   <li class="list-inline-item"><i class="fa fa-star"></i></li>';
                  // }
                  $this->content->text .='
                </ul>';
                if($PAGE->theme->settings->coursecat_enrolments != 1 || $PAGE->theme->settings->coursecat_announcements != 1){
                $this->content->text .='<ul class="cs_review_enroll">';
                if($PAGE->theme->settings->coursecat_enrolments != 1){
                  $this->content->text .='<li class="list-inline-item"><a class="'.$white.'" href="#"><span class="flaticon-profile"></span> '. count_enrolled_users($context) .' '.get_string('students_enrolled', 'theme_edumy').'</a></li>';
                }
                if($PAGE->theme->settings->coursecat_announcements != 1){
                  $this->content->text .='<li class="list-inline-item"><a class="'.$white.'" href="#"><span class="flaticon-comment"></span> '. $COURSE->newsitems .' '.get_string('topics', 'theme_edumy').'</a></li>';
                }
                $this->content->text .='</ul>';
              }
              $this->content->text .='</div>';
              if(!empty($this->content->video)) {
                $this->content->text .='
              <div class="courses_big_thumb">
                <div class="thumb">
                  <iframe class="iframe_video" src="'.format_text($this->content->video, FORMAT_HTML, array('filter' => true)).'" frameborder="0" allowfullscreen></iframe>
                </div>
              </div>';
              }
              $this->content->text .='
            </div>
          </div>';
        return $this->content;
    }

    public function instance_allow_multiple() {
          return true;
    }
    function applicable_formats() {
        return array(
          'all' => true,
          'my' => false,
        );
    }
    public function html_attributes() {
      global $CFG;
      $attributes = parent::html_attributes();
      include($CFG->dirroot . '/theme/edumy/ccn/block_handler/attributes.php');
      return $attributes;
    }


}
