<?php
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot. '/course/renderer.php');

class block_cocoon_course_grid extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_cocoon_course_grid');
    }

    public function get_content() {
        global $CFG, $DB, $COURSE, $USER, $PAGE;

        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        if (isset($PAGE->theme->settings->course_enrolment_payment) && ($PAGE->theme->settings->course_enrolment_payment == 1)) {
          $paymentForced = false;
        } else {
          $paymentForced = true;
        }

        $this->content = new stdClass();
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';
        $this->content->text = '';
        if(!empty($this->config->title)){$this->content->title = $this->config->title;}
        if(!empty($this->config->subtitle)){$this->content->subtitle = $this->config->subtitle;}
        if(!empty($this->config->button_text)){$this->content->button_text = $this->config->button_text;}
        if(!empty($this->config->button_link)){$this->content->button_link = $this->config->button_link;}
        if(!empty($this->config->hover_text)){$this->content->hover_text = $this->config->hover_text;}
        if(!empty($this->config->hover_accent)){$this->content->hover_accent = $this->config->hover_accent;}
        // if(!empty($this->config->updated)){$this->content->updated = $this->config->updated;}
        // if(!empty($this->config->rating)){$this->content->rating = $this->config->rating;}
        if(!empty($this->config->description)){$this->content->description = $this->config->description;}
        // if(!empty($this->config->enrolments)){$this->content->enrolments = $this->config->enrolments;}
        // if(!empty($this->config->newsitems)){$this->content->newsitems = $this->config->newsitems;}
        if(!empty($this->config->course_image)){$this->content->course_image = $this->config->course_image;}
        if(!empty($this->config->price)){$this->content->price = $this->config->price;}
        if(!empty($this->config->enrol_btn)){$this->content->enrol_btn = $this->config->enrol_btn;}
        if(!empty($this->config->enrol_btn_text)){$this->content->enrol_btn_text = $this->config->enrol_btn_text;}
        if(!empty($this->config->group)){
          $filter = $this->config->group;
          $coursesContainerClass = 'masonry_abc';
        } else {
          $filter = null;
          $coursesContainerClass = '';
        }
        if(!empty($this->config->courses)){
          $coursesArr = $this->config->courses;
          $courses = new stdClass();
          foreach ($coursesArr as $key => $course) {
            $courseObj = new stdClass();
            $courseObj->id = $course;
            $courseRecord = $DB->get_record('course', array('id' => $courseObj->id), 'category');
            $courseCategory = $DB->get_record('course_categories',array('id' => $courseRecord->category));
            $courseCategory = core_course_category::get($courseCategory->id);
            $courseObj->category = $courseCategory->id;
            $courseObj->category_name = $courseCategory->get_formatted_name();
            // print_object($courseObj);
            $courses->$course = $courseObj;
          }
          $categories = array();
          foreach ($courses as $key => $course) {
            $categories[$course->category] = $course->category_name;
          }
          $categories = array_unique($categories);

        }



        $this->content->text .= '
        <section id="our-top-courses" class="our-courses ccn-courses-grid-block">
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
              <div class="col-xs-12">';
              if($filter == 1){
                $this->content->text .= '
                <div id="options" class="alpha-pag full">
                  <div class="option-isotop">
                    <ul id="filter" class="option-set" data-option-key="filter">
                      <li class="list-inline-item"><a href="#all" data-option-value="*" class="selected">'.get_string('all').'</a></li>';
                      foreach ($categories as $key => $category) {
                        $key = 'cat-'.$key;
                        $this->content->text .='<li class="list-inline-item"><a href="#'.$key.'" data-option-value=".'.$key.'">'.$category.'</a></li>';
                      }
                      $this->content->text .='
                    </ul>
                  </div>
                </div><!-- /#options -->
                <div class="emply-text-sec">';
              }
              $this->content->text .='
                  <div class="row" id="'.$coursesContainerClass.'">';

        // $courses = self::get_featured_courses();
        // print_object($courses);
        // $courses = null;
        $chelper = new coursecat_helper();
        $total_courses = count($coursesArr);

        if($total_courses < 2) {
          $col_class = 'col-md-12';
        } else if($total_courses == 2) {
          $col_class = 'col-md-6';
        } else if($total_courses == 3) {
          $col_class = 'col-md-4';
        } else  {
          $col_class = 'col-md-6 col-lg-4 col-xl-3';
        }

        foreach ($courses as $course) {
          if ($DB->record_exists('course', array('id' => $course->id))) {
            $course = new core_course_list_element($course);
            $courseid = $course->id;
            $context = context_course::instance($courseid);
            $numberofusers = count_enrolled_users($context);

            // @ccnComm: Start Payment
            $enrolinstances = enrol_get_instances($courseid, true);
            $ccnmethods = array_column($enrolinstances, null, "enrol");

            // Paypal
            if(array_key_exists('paypal', $ccnmethods)){
              $ccnpp = $ccnmethods['paypal'];
              if($ccnpp){
                $cost = $ccnpp->cost;
                $currency = $ccnpp->currency;
                $enrol = $ccnpp->enrol;
              }
            }
            // Stripe
            if(array_key_exists('stripepayment', $ccnmethods)){
              $ccnstripe = $ccnmethods['stripepayment'];
              if($ccnstripe){
                $cost_stripe = $ccnstripe->cost;
                $currency_stripe = $ccnstripe->currency;
                $enrol_stripe = $ccnstripe->enrol;
              }
            }

            $price_matches = false;
            if(array_key_exists('paypal', $ccnmethods) && array_key_exists('stripepayment', $ccnmethods)){
              if($cost == $cost_stripe) {
                $price_matches = true;
              } else {
                $price_matches = false;
              }
            }

            $enrolment_link = $CFG->wwwroot . '/enrol/index.php?id=' . $courseid;
            // @ccnComm: End Payment

            $coursename = $chelper->get_course_formatted_name($course);
            $coursenamelink = new moodle_url('/course/view.php', array('id' => $course->id));

            if ($course->has_summary()) {
              $ccn_course_summary = $chelper->get_course_formatted_summary($course, array('noclean' => true, 'para' => false));
              if(!empty($this->content->description) && $this->content->description == '7'){
                $maxlength = 500;
                if (strlen($ccn_course_summary) > $maxlength) {
                  $ccn_course_summary = wordwrap($ccn_course_summary, $maxlength);
                  $ccn_course_summary = substr($ccn_course_summary, 0, strpos($ccn_course_summary, "\n")) . '...';
                }
              } elseif(!empty($this->content->description) && $this->content->description == '6'){
                $maxlength = 350;
                if (strlen($ccn_course_summary) > $maxlength) {
                  $ccn_course_summary = wordwrap($ccn_course_summary, $maxlength);
                  $ccn_course_summary = substr($ccn_course_summary, 0, strpos($ccn_course_summary, "\n")) . '...';
                }
              } elseif(!empty($this->content->description) && $this->content->description == '5'){
                $maxlength = 200;
                if (strlen($ccn_course_summary) > $maxlength) {
                  $ccn_course_summary = wordwrap($ccn_course_summary, $maxlength);
                  $ccn_course_summary = substr($ccn_course_summary, 0, strpos($ccn_course_summary, "\n")) . '...';
                }
              } elseif(!empty($this->content->description) && $this->content->description == '4'){
                $maxlength = 150;
                if (strlen($ccn_course_summary) > $maxlength) {
                  $ccn_course_summary = wordwrap($ccn_course_summary, $maxlength);
                  $ccn_course_summary = substr($ccn_course_summary, 0, strpos($ccn_course_summary, "\n")) . '...';
                }
              } elseif(!empty($this->content->description) && $this->content->description == '3'){
                $maxlength = 100;
                if (strlen($ccn_course_summary) > $maxlength) {
                  $ccn_course_summary = wordwrap($ccn_course_summary, $maxlength);
                  $ccn_course_summary = substr($ccn_course_summary, 0, strpos($ccn_course_summary, "\n")) . '...';
                }
              } elseif(!empty($this->content->description) && $this->content->description == '2'){
                $maxlength = 50;
                if (strlen($ccn_course_summary) > $maxlength) {
                  $ccn_course_summary = wordwrap($ccn_course_summary, $maxlength);
                  $ccn_course_summary = substr($ccn_course_summary, 0, strpos($ccn_course_summary, "\n")) . '...';
                }
              }
            }

            // Display course overview files.
            $contentimages = $contentfiles = '';
            foreach ($course->get_course_overviewfiles() as $file) {
                $isimage = $file->is_valid_image();
                $url = file_encode_url("{$CFG->wwwroot}/pluginfile.php",
                        '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                        $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
                if ($isimage) {
                    $contentimages .= $url;
                } else {
                    $image = $this->output->pix_icon(file_file_icon($file, 24), $file->get_filename(), 'moodle');
                    $filename = html_writer::tag('span', $image, array('class' => 'fp-icon')).
                            html_writer::tag('span', $file->get_filename(), array('class' => 'fp-filename'));
                    $contentfiles .= html_writer::tag('span',
                            html_writer::link($url, $filename),
                            array('class' => 'coursefile fp-filename-icon'));
                }
            }



            if(!empty($this->config->hover_text)){$this->content->hover_text = $this->config->hover_text;}
            if(!empty($this->config->hover_accent)){$this->content->hover_accent = $this->config->hover_accent;}
            if(($this->content->enrolments == '1') || ($this->content->newsitems == '1') || ($this->content->price == '1') || ($this->content->enrol_btn == '1' && $this->content->enrol_btn_text)){
              $topCoursesClass = 'ccnWithFoot';
            } else {
              $topCoursesClass = '';
            }

            $this->content->text .='

            <div class="'.$col_class.' cat-'.$course->category.'">
							<div class="top_courses '.$topCoursesClass.'">';
								if($contentimages && $this->content->course_image == '1'){
                  $this->content->text .='
                  <a href="'. $coursenamelink .'"><div class="thumb">
									<img class="img-whp" src="'. $contentimages .'" alt="'.$coursename.'">
									<div class="overlay">';
										if($this->content->hover_accent){
                     $this->content->text .=' <div class="tag">'.format_text($this->content->hover_accent, FORMAT_HTML, array('filter' => true)).'</div>';
                    }
                    if($this->content->hover_text){
                     $this->content->text .='<span class="tc_preview_course">'.format_text($this->content->hover_text, FORMAT_HTML, array('filter' => true)).'</span>';
                    }
										$this->content->text .='
									</div>
								</div></a>';
              }
              $this->content->text .='
								<div class="details">
									<div class="tc_content">';
                  if($PAGE->theme->settings->coursecat_modified != 1){
                    $this->content->text .='<p>'.get_string('updated', 'theme_edumy').' '.userdate($course->timemodified, get_string('strftimedatefullshort', 'langconfig')).'</p>';
                  }
                  // if(!empty($this->content->updated) && $this->content->updated == '1'){
                  //   $this->content->text .='<p>'.get_string('updated', 'theme_edumy').' '.userdate($course->timemodified, get_string('strftimedatefullshort', 'langconfig')).'</p>';
                  // }
                  $this->content->text .='
										<a href="'. $coursenamelink .'"><h5>'. $coursename .'</h5></a>';
                    if($this->content->description != 0){
                    $this->content->text .='<p>'.$ccn_course_summary.'</p>';
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
                  $this->content->text .='
									</div>
                  </div>';
                  if(
                    (!empty($this->content->enrolments) && $this->content->enrolments == '1') ||
                    (!empty($this->content->newsitems) && $this->content->newsitems == '1') ||
                    (!empty($this->content->price) && $this->content->price == '1') ||
                    (!empty($this->content->enrol_btn) &&
                     !empty($this->content->enrol_btn_text) &&
                     $this->content->enrol_btn == '1' &&
                     $this->content->enrol_btn_text)
                   ){
                  $this->content->text .='
                  <div class="tc_footer">
									<ul class="tc_meta float-left">';

                  if($PAGE->theme->settings->coursecat_enrolments != 1){
                    $this->content->text .='
                    <li class="list-inline-item"><i class="flaticon-profile"></i></li>
                    <li class="list-inline-item">'. $numberofusers .'</li>';
                  }

                  // if(!empty($this->content->enrolments) && $this->content->enrolments == '1'){
									// $this->content->text .='
                  //   <li class="list-inline-item"><i class="flaticon-profile"></i></li>
									// 	<li class="list-inline-item">'. $numberofusers .'</li>';
                  //  }
                  if($PAGE->theme->settings->coursecat_announcements != 1){
                    $this->content->text .='	<li class="list-inline-item"><i class="flaticon-comment"></i></li>
                      <li class="list-inline-item">'.$course->newsitems.'</li>';
                  }

                   // if(!empty($this->content->newsitems) && $this->content->newsitems == '1'){
                   // $this->content->text .='	<li class="list-inline-item"><i class="flaticon-comment"></i></li>
                   //   <li class="list-inline-item">'.$course->newsitems.'</li>';
                   // }
                   $this->content->text .='</ul>';
                   if($this->content->enrol_btn == '1' && $this->content->enrol_btn_text){
                     $this->content->text .='<a href="'.$enrolment_link.'" class="tc_enrol_btn float-right">'.$this->content->enrol_btn_text.'</a>';
                   }
                    if(
                      $this->content->price == '1'
                      && $enrolinstances
                      && ((array_key_exists('paypal', $ccnmethods) && $ccnmethods['paypal']) || (array_key_exists('stripepayment', $ccnmethods) && $ccnmethods['stripepayment']) || $paymentForced == false)
                      ) {
                          if(array_key_exists('paypal', $ccnmethods) && $ccnmethods['paypal'] && array_key_exists('stripepayment', $ccnmethods) && $ccnmethods['stripepayment'] && $price_matches) {
                            $this->content->text .='<div class="tc_price float-right">'.get_string('course_currency', 'theme_edumy') . $cost_stripe .'</div>';
                          } else {
                            if(array_key_exists('paypal', $ccnmethods) && $ccnmethods['paypal']) {
                              $this->content->text .='<div class="tc_price float-right">'.get_string('course_currency', 'theme_edumy') . $cost .'</div>';
                            }
                            if(array_key_exists('stripepayment', $ccnmethods) && $ccnmethods['stripepayment']) {
                              $this->content->text .='<div class="tc_price float-right">'.get_string('course_currency', 'theme_edumy') . $cost_stripe .'</div>';
                            }
                            if(!$ccnmethods['paypal'] && !$ccnmethods['stripepayment'] && $paymentForced == false){
                              $this->content->text .='<div class="tc_price float-right">'. get_string('course_free', 'theme_edumy') .'</div>';
                            }
                          }
                        }
                    $this->content->text .='
									</div>';
                   }
                $this->content->text .='
							</div>
						</div>';
          }
        }

        $this->content->text .='
 			</div>';

      if($filter == 1){
        $this->content->text .= '
</div>';
}
$this->content->text .='
        </div></div>';

      if($this->content->button_text){
      $this->content->text .='
      <div class="row">
      <div class="col-lg-6 offset-lg-3">
        <div class="courses_all_btn text-center">
          <a class="btn btn-transparent" href="'.format_text($this->content->button_link, FORMAT_HTML, array('filter' => true)).'">'.format_text($this->content->button_text, FORMAT_HTML, array('filter' => true)).'</a>
        </div>
      </div></div>'  ;
      }
$this->content->text .='

		</div>
	</section>

';

        return $this->content;
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


    public function instance_allow_multiple() {
          return false;
    }

    public function has_config() {
        return false;
    }

    public function cron() {
        return true;
    }

    // public static function get_featured_courses() {
    //     global $DB;
    //
    //     $sql = 'SELECT c.id, c.shortname, c.fullname, fc.sortorder
    //               FROM {block_cocoon_course_grid} fc
    //               JOIN {course} c
    //                 ON (c.id = fc.courseid)
    //           ORDER BY sortorder';
    //     return $DB->get_records_sql($sql);
    // }

    // public static function delete_cocoon_featuredcourse($courseid) {
    //     global $DB;
    //     return $DB->delete_records('block_cocoon_course_grid', array('courseid' => $courseid));
    // }
}
