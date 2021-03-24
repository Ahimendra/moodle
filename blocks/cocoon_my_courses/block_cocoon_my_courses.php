<?php

defined('MOODLE_INTERNAL') || die();

//require_once($CFG->libdir. '/coursecatlib.php');
require_once($CFG->dirroot. '/course/renderer.php');

class block_cocoon_my_courses extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_cocoon_my_courses');
    }

    public function get_content() {
        global $CFG, $PAGE, $USER, $DB;

        if (isset($PAGE->theme->settings->course_enrolment_payment) && ($PAGE->theme->settings->course_enrolment_payment == 1)) {
          $paymentForced = false;
        } else {
          $paymentForced = true;
        }

        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';
        $this->content->text = '';

        if (isloggedin() && !isguestuser()) {


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
        if(($this->content->enrolments == '1') || ($this->content->newsitems == '1') || ($this->content->price == '1') || ($this->content->enrol_btn == '1' && $this->content->enrol_btn_text)){
          $topCoursesClass = 'ccnWithFoot';
        } else {
          $topCoursesClass = '';
        }
        $courses = enrol_get_all_users_courses($USER->id);
        $total_courses = count($courses);
        if($total_courses < 2) {
          $topColumnClass = 'col-md-12';
          $col_class = 'col-md-6 offset-md-3 col-xl-4 offset-xl-4';
        } else if($total_courses == 2) {
          $topColumnClass = 'col-sm-8 offset-sm-2 col-md-12 offset-md-0 col-lg-10 offset-lg-1 col-xl-8 offset-xl-2';
          $col_class = 'col-md-6';
        } else if($total_courses == 3) {
          $topColumnClass = 'col-sm-8 offset-sm-2 col-md-12 offset-md-0 col-xl-10 offset-xl-1';
          $col_class = 'col-md-6 col-lg-4';
        } else  {
          $topColumnClass = 'col-xs-12';
          $col_class = 'col-md-6 col-lg-4 col-xl-3';
        }

        $this->content->text .= '


<section class="our-courses">
<div class="container">
        <div class="row">
    <div class="col-lg-6 offset-lg-3">
    <div class="main-title text-center">
      <h3 class="mt0">'.format_text($this->content->title, FORMAT_HTML, array('filter' => true)).'</h3>
      <p>'.format_text($this->content->subtitle, FORMAT_HTML, array('filter' => true)).'</p>
    </div></div>
</div>
<div class="row">
<div class="'.$topColumnClass.'">
<div class="row">
';




        $chelper = new coursecat_helper();
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
            $ccnpp = $ccnmethods['paypal'];
            $cost = $ccnpp->cost;
            $currency = $ccnpp->currency;
            $enrol = $ccnpp->enrol;
            // Stripe
            $ccnstripe = $ccnmethods['stripepayment'];
            $cost_stripe = $ccnstripe->cost;
            $currency_stripe = $ccnstripe->currency;
            $enrol_stripe = $ccnstripe->enrol;

            $price_matches = false;
            if($cost == $cost_stripe) {
              $price_matches = true;
            } else {
              $price_matches = false;
            }

            $enrolment_link = $CFG->wwwroot . '/enrol/index.php?id=' . $courseid;
            // @ccnComm: End Payment


            // $content = '';
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





            $this->content->text .='

            <div class="'.$col_class.'">
							<div class="top_courses '.$topCoursesClass.'">
              <a href="'. $coursenamelink .'">';
              if($contentimages && $this->content->course_image == '1'){
								$this->content->text .='
                <div class="thumb">
									<img class="img-whp" src="'. $contentimages .'" alt="'.$coursename.'">
									<div class="overlay">';
                  if($this->content->hover_accent){
										$this->content->text .='<div class="tag">'.format_text($this->content->hover_accent, FORMAT_HTML, array('filter' => true)).'</div>';
                  }
                  if($this->content->hover_text){
										$this->content->text .='<span class="tc_preview_course">'.format_text($this->content->hover_text, FORMAT_HTML, array('filter' => true)).'</span>';
                  }
                  $this->content->text .='
									</div>
								</div>';
              }
              $this->content->text .='
								<div class="details">
									<div class="tc_content">';
                  if($PAGE->theme->settings->coursecat_modified != 1){
                    $this->content->text .='<p>'.get_string('updated', 'theme_edumy').' '.userdate($course->timemodified, get_string('strftimedatefullshort', 'langconfig')).'</p>';
                  }
                //   if($this->content->updated == '1'){
                //   $this->content->text .='<p>'.get_string('updated', 'theme_edumy').' '.userdate($course->timemodified, get_string('strftimedatefullshort', 'langconfig'), 0).'</p>';
                // }
                $this->content->text .='
										<h5>'. $coursename .'</h5>';
                    if($this->content->description == '1'){
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
                  //   if($this->content->rating == '1'){
									// 	$this->content->text .='
									// 	<ul class="tc_review">
									// 		<li class="list-inline-item"><i class="fa fa-star"></i></li>
									// 		<li class="list-inline-item"><i class="fa fa-star"></i></li>
									// 		<li class="list-inline-item"><i class="fa fa-star"></i></li>
									// 		<li class="list-inline-item"><i class="fa fa-star"></i></li>
									// 		<li class="list-inline-item"><i class="fa fa-star"></i></li>
									// 	</ul>';
                  // }
                  $this->content->text .='
									</div>
                  </div>';
                  if(($this->content->enrolments == '1') || ($this->content->newsitems == '1') || ($this->content->price == '1') || ($this->content->enrol_btn == '1' && $this->content->enrol_btn_text)){
                    $this->content->text .='
									<div class="tc_footer">
										<ul class="tc_meta float-left">';
                    if($PAGE->theme->settings->coursecat_enrolments != 1){
                      $this->content->text .='
                      <li class="list-inline-item"><i class="flaticon-profile"></i></li>
                      <li class="list-inline-item">'. $numberofusers .'</li>';
                    }
                    if($PAGE->theme->settings->coursecat_announcements != 1){
                      $this->content->text .='	<li class="list-inline-item"><i class="flaticon-comment"></i></li>
                        <li class="list-inline-item">'.$course->newsitems.'</li>';
                    }
                    // if($this->content->enrolments == '1'){
  									// $this->content->text .='
										// 	<li class="list-inline-item"><i class="flaticon-profile"></i></li>
										// 	<li class="list-inline-item">'. $numberofusers .'</li>';
                    // }
                    // if($this->content->newsitems == '1'){
                    // $this->content->text .='
										// 	<li class="list-inline-item"><i class="flaticon-comment"></i></li>
										// 	<li class="list-inline-item">'.$course->newsitems.'</li>';
                    // }
                    $this->content->text .='
										</ul>';
                    if($this->content->enrol_btn == '1' && $this->content->enrol_btn_text){
                      $this->content->text .='<a href="'.$enrolment_link.'" class="tc_enrol_btn float-right">'.$this->content->enrol_btn_text.'</a>';
                    }
                    if($this->content->price == '1' && $enrolinstances && ($ccnmethods['paypal'] || $ccnmethods['stripepayment'] || $paymentForced == false)) {
                      if($ccnmethods['paypal'] && $ccnmethods['stripepayment'] && $price_matches) {
                        $this->content->text .='<div class="tc_price float-right">'.get_string('course_currency', 'theme_edumy') . $cost_stripe .'</div>';
                      } else {
                        if($ccnmethods['paypal']) {
                          $this->content->text .='<div class="tc_price float-right">'.get_string('course_currency', 'theme_edumy') . $cost .'</div>';
                        }
                        if($ccnmethods['stripepayment']) {
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
								</a>
							</div>
						</div>


            ';
          }
        }


        $this->content->text .= '
        		</div></div></div></div></section>

        ';


      } else {
        $this->content->text = '';
      }




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
    //               FROM {block_cocoon_my_courses} fc
    //               JOIN {course} c
    //                 ON (c.id = fc.courseid)
    //           ORDER BY sortorder';
    //     return $DB->get_records_sql($sql);
    // }
    //
    // public static function delete_featuredcourse($courseid) {
    //     global $DB;
    //     return $DB->delete_records('block_cocoon_my_courses', array('courseid' => $courseid));
    // }
}
