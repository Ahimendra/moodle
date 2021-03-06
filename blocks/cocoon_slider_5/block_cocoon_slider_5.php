<?php
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot. '/course/renderer.php');

class block_cocoon_slider_5 extends block_base {

    /**
     * Start block instance.
     */
    function init() {
        $this->title = get_string('pluginname', 'block_cocoon_slider_5');
    }

    /**
     * The block is usable in all pages
     */
    function applicable_formats() {
        return array(
          'all' => true,
          'my' => false,
        );
    }

    /**
     * Customize the block title dynamically.
     */
    function specialization() {
        global $CFG;
        include($CFG->dirroot . '/theme/edumy/ccn/block_handler/specialization.php');
    }

    /**
     * The block can be used repeatedly in a page.
     */
    function instance_allow_multiple() {
        return true;
    }

    /**
     * Build the block content.
     */
    function get_content() {
        global $CFG, $PAGE, $COURSE, $DB;

        require_once($CFG->libdir . '/filelib.php');


        if ($this->content !== NULL) {
            return $this->content;
        }

        if (!empty($this->config) && is_object($this->config)) {
            $data = $this->config;
            $data->slidesnumber = is_numeric($data->slidesnumber) ? (int)$data->slidesnumber : 0;
        } else {
            $data = new stdClass();
            $data->slidesnumber = 0;
        }
        $this->content = new stdClass();
        if(!empty($this->config->prev_1)){$this->content->prev_1 = $this->config->prev_1;}
        if(!empty($this->config->prev_2)){$this->content->prev_2 = $this->config->prev_2;}
        if(!empty($this->config->next_1)){$this->content->next_1 = $this->config->next_1;}
        if(!empty($this->config->next_2)){$this->content->next_2 = $this->config->next_2;}
        if(!empty($this->config->prev)){$this->content->prev = $this->config->prev;}
        if(!empty($this->config->next)){$this->content->next = $this->config->next;}
        if(!empty($this->config->arrow_style)){$this->content->arrow_style = $this->config->arrow_style;}

        $text = '';
        $bannerstyle = '';
        if ($data->slidesnumber > 1) {
          $bannerstyle .= 'banner-style-one--multiple';
        } else {
          $bannerstyle .= 'banner-style-one--single';
        }

        if ($data->slidesnumber > 0) {
            $text = '






            <section class="p0">
  <div class="container-fluid p0">
          <div class="home8-slider vh-85">
              <div id="bs_carousel" class="carousel slide bs_carousel" data-ride="carousel" data-pause="false" data-interval="7000">
                  <div class="carousel-inner">';

                  $fs = get_file_storage();
                  for ($i = 1; $i <= $data->slidesnumber; $i++) {
                      $sliderimage = 'file_slide' . $i;
                      $slide_title = 'slide_title' . $i;
                      $slide_title_2 = 'slide_title_2' . $i;
                      $slide_subtitle = 'slide_subtitle' . $i;
                      $slide_btn_url = 'slide_btn_url' . $i;
                      $slide_btn_text = 'slide_btn_text' . $i;
                      $courseid = 'course' . $i;
                      $course = $DB->get_record('course',array('id' => $data->$courseid));
                      $courseid = $course->id;
                      $chelper = new coursecat_helper();

                      if ($DB->record_exists('course', array('id' => $courseid))) {
                        $course = new core_course_list_element($course);
                        $context = context_course::instance($courseid);
                        $numberofusers = count_enrolled_users($context);
                        $coursename = $chelper->get_course_formatted_name($course);
                        $coursenamelink = new moodle_url('/course/view.php', array('id' => $courseid));
                        $courseCategory = $DB->get_record('course_categories',array('id' => $course->category));
                        $courseCategory = core_course_category::get($courseCategory->id);
                        $courseCategory = $courseCategory->get_formatted_name();


                      // print_object($course);
                      if($i == 1){
                        $class = 'active';
                      } else {
                        $class = '';
                      }

                      if (!empty($data->$sliderimage)) {
                          $files = $fs->get_area_files($this->context->id, 'block_cocoon_slider_5', 'slides', $i, 'sortorder DESC, id ASC', false, 0, 0, 1);
                          if (count($files) >= 1) {
                              $mainfile = reset($files);
                              $mainfile = $mainfile->get_filename();
                          } else {
                              continue;
                          }

                          $text .= '
                          <div class="carousel-item '.$class.'" data-slide="'.$i.'" data-interval="false">
                              <div class="bs_carousel_bg" style="background-image: url(' . moodle_url::make_file_url("$CFG->wwwroot/pluginfile.php", "/{$this->context->id}/block_cocoon_slider_5/slides/" . $i . '/' . $mainfile) . ');"></div>
                              <div class="bs-caption">
                                  <div class="container">
                                      <div class="row">
                                          <div class="col-md-7 col-lg-8">
                                              <div class="main_title">'.format_text($data->$slide_title, FORMAT_HTML, array('filter' => true)).' <span>'.format_text($data->$slide_title_2, FORMAT_HTML, array('filter' => true)).'</span></div>
                                              <p class="parag">'.format_text($data->$slide_subtitle, FORMAT_HTML, array('filter' => true)).'</p>
                                          </div>
                                          <div class="col-md-5 col-lg-4">
                        <div class="feat_property home8">
                        <a href="'.$coursenamelink.'">
                          <div class="details">
                            <div class="tc_content">
                              <div class="tag">
                                Top Seller
                              </div>';
                              if($PAGE->theme->settings->coursecat_modified != 1){
                                $text.='<p>'.get_string('updated', 'theme_edumy').' '.userdate($course->timemodified, get_string('strftimedatefullshort', 'langconfig')).'</p>';
                              }
                              $text.='
                              <h4>'.$coursename.'</h4>';
                              if($PAGE->theme->settings->course_ratings == 1){
                                $text .='<ul class="tc_review">
                                  <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                  <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                  <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                  <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                  <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                </ul>';
                              } elseif($PAGE->theme->settings->course_ratings == 2){
                                $block = block_instance('cocoon_course_rating');
                                $ccnRating = $block->external_star_rating($courseid);
                                $text .= $ccnRating;
                              }
$text .='


                            </div>
                            <div class="fp_footer">
                             <ul class="fp_meta float-left mb0">';
                            if($PAGE->theme->settings->coursecat_enrolments != 1){
                              $text .='
                              <li class="list-inline-item"><i class="flaticon-profile"></i></li>
                              <li class="list-inline-item">'. $numberofusers .'</li>';
                            }

                            if($PAGE->theme->settings->coursecat_announcements != 1){
                              $text .='	<li class="list-inline-item"><i class="flaticon-comment"></i></li>
                                <li class="list-inline-item">'.$course->newsitems.'</li>';
                            }
$text .='</ul>

                              <div class="fp_pdate float-right">'.$courseCategory.'</div>
                            </div>
                          </div>
                          </a>
                        </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>




                        ';
                      }
}
                  }

$text .= '




                  </div>
                  <div class="property-carousel-controls">
                      <a class="property-carousel-control-prev" role="button" data-slide="prev">
                          <span class="flaticon-left-arrow"></span>
                      </a>
                      <a class="property-carousel-control-next" role="button" data-slide="next">
                          <span class="flaticon-right-arrow-1"></span>
                      </a>
                  </div>
              </div>
              <div class="carousel slide bs_carousel_prices" data-ride="carousel" data-pause="false" data-interval="false">
                  <div class="carousel-inner"></div>
                  <div class="property-carousel-ticker">
                      <div class="property-carousel-ticker-counter"></div>
                      <div class="property-carousel-ticker-divider">&nbsp;&nbsp;/&nbsp;&nbsp;</div>
                      <div class="property-carousel-ticker-total"></div>
                  </div>
              </div>
          </div>
  </div>
</section>
<div class="container">
  <div class="row">
    <div class="col-lg-12">
      <a href="#continue">
          <div class="discover_scroll home8">
              <div class="icons">
              <h4>'.get_string('scroll_down', 'theme_edumy').'</h4>
              <p>'.get_string('to_discover_more', 'theme_edumy').'</p>
              </div>
              <div class="thumb">
                <img src="'.$CFG->wwwroot.'/theme/edumy/images/resource/mouse.png" alt="mouse.png">
              </div>
          </div>
        </a>
    </div>
  </div>
</div>
<a id="continue" class="" style="visibility:hidden"></a>

<script type="text/javascript">

  $(window).on("load", function(){
var bsCarouselItems = 1;
if($(".bs_carousel .carousel-item").length){
  $(".bs_carousel .carousel-item").each(function(index, element) {
      if (index == 0) {
         $(".bs_carousel_prices").addClass("pprty-price-active pprty-first-time");
      }
      $(".bs_carousel_prices .property-carousel-ticker-counter").append("<span>0" + bsCarouselItems + "</span>");
      bsCarouselItems += 1;
  });
}

$(".bs_carousel_prices .property-carousel-ticker-total").append("<span>0" + $(".bs_carousel .carousel-item").length + "</span>");

if($(".bs_carousel").length){
  $(".bs_carousel").on("slide.bs.carousel", function(carousel) {
      $(".bs_carousel_prices").removeClass("pprty-first-time");
      $(".bs_carousel_prices").carousel(carousel.to);
  });
}

if($(".bs_carousel").length){
  $(".bs_carousel").on("slid.bs.carousel", function(carousel) {
      var tickerPos = (carousel.to) * 25;
      $(".bs_carousel_prices .property-carousel-ticker-counter > span").css("transform", "translateY(-" + tickerPos + "px)");
  });
}

if($(".bs_carousel .property-carousel-control-next").length){
  $(".bs_carousel .property-carousel-control-next").on("click",function(e) {
      $(".bs_carousel").carousel("next");
  });
}

if($(".bs_carousel .property-carousel-control-prev").length){
  $(".bs_carousel .property-carousel-control-prev").on("click",function(e) {
      $(".bs_carousel").carousel("prev");
  });
}
if($(".bs_carousel").length){
  $(".bs_carousel").carousel({
  interval: 6000,
  pause: "true"
});
}
});


</script>';
        }

        $this->content = new stdClass;
        $this->content->footer = '';
        $this->content->text = $text;

        return $this->content;

  }


    /**
     * Serialize and store config data
     */
    function instance_config_save($data, $nolongerused = false) {
        global $CFG;

        $filemanageroptions = array('maxbytes'      => $CFG->maxbytes,
                                    'subdirs'       => 0,
                                    'maxfiles'      => 1,
                                    'accepted_types' => array('.jpg', '.png', '.gif'));

        for($i = 1; $i <= $data->slidesnumber; $i++) {
            $field = 'file_slide' . $i;
            if (!isset($data->$field)) {
                continue;
            }

            file_save_draft_area_files($data->$field, $this->context->id, 'block_cocoon_slider_5', 'slides', $i, $filemanageroptions);
        }

        parent::instance_config_save($data, $nolongerused);
    }

    /**
     * When a block instance is deleted.
     */
    function instance_delete() {
        global $DB;
        $fs = get_file_storage();
        $fs->delete_area_files($this->context->id, 'block_cocoon_slider_5');
        return true;
    }

    /**
     * Copy any block-specific data when copying to a new block instance.
     * @param int $fromid the id number of the block instance to copy from
     * @return boolean
     */
    public function instance_copy($fromid) {
        global $CFG;

        $fromcontext = context_block::instance($fromid);
        $fs = get_file_storage();

        if (!empty($this->config) && is_object($this->config)) {
            $data = $this->config;
            $data->slidesnumber = is_numeric($data->slidesnumber) ? (int)$data->slidesnumber : 0;
        } else {
            $data = new stdClass();
            $data->slidesnumber = 0;
        }

        $filemanageroptions = array('maxbytes'      => $CFG->maxbytes,
                                    'subdirs'       => 0,
                                    'maxfiles'      => 1,
                                    'accepted_types' => array('.jpg', '.png', '.gif'));

        for($i = 1; $i <= $data->slidesnumber; $i++) {
            $field = 'file_slide' . $i;
            if (!isset($data->$field)) {
                continue;
            }

            // This extra check if file area is empty adds one query if it is not empty but saves several if it is.
            if (!$fs->is_area_empty($fromcontext->id, 'block_cocoon_slider_5', 'slides', $i, false)) {
                $draftitemid = 0;
                file_prepare_draft_area($draftitemid, $fromcontext->id, 'block_cocoon_slider_5', 'slides', $i, $filemanageroptions);
                file_save_draft_area_files($draftitemid, $this->context->id, 'block_cocoon_slider_5', 'slides', $i, $filemanageroptions);
            }
        }

        return true;
    }

    /**
     * The block should only be dockable when the title of the block is not empty
     * and when parent allows docking.
     *
     * @return bool
     */
    public function instance_can_be_docked() {
        return (!empty($this->config->title) && parent::instance_can_be_docked());
    }

    public function html_attributes() {
      global $CFG;
      $attributes = parent::html_attributes();
      include($CFG->dirroot . '/theme/edumy/ccn/block_handler/attributes.php');
      return $attributes;
    }

}
