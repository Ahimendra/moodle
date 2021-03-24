<?php

class block_cocoon_slider_3 extends block_base {

    /**
     * Start block instance.
     */
    function init() {
        $this->title = get_string('pluginname', 'block_cocoon_slider_3');
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
        // if (isset($this->config->title)) {
        //     $this->title = $this->title = format_string($this->config->title, true, ['context' => $this->context]);
        // } else {
        //     $this->title = get_string('newcustomsliderblock', 'block_cocoon_slider_3');
        // }
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
        global $CFG, $PAGE;

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
        if ($data->slidesnumber > 0) {
            $text = '		<div class="home-seven home2-slider p0">
		<div class="container-fluid p0">
			<div class="row">
				<div class="col-lg-12">
					<div class="main-banner-wrapper home7">
					    <div class="banner-style-two owl-theme owl-carousel">';
            $fs = get_file_storage();
            for ($i = 1; $i <= $data->slidesnumber; $i++) {
                $sliderimage = 'file_slide' . $i;
                $slide_title = 'slide_title' . $i;
                $slide_subtitle = 'slide_subtitle' . $i;

                if (!empty($data->$sliderimage)) {
                    $files = $fs->get_area_files($this->context->id, 'block_cocoon_slider_3', 'slides', $i, 'sortorder DESC, id ASC', false, 0, 0, 1);
                    if (count($files) >= 1) {
                        $mainfile = reset($files);
                        $mainfile = $mainfile->get_filename();
                    } else {
                        continue;
                    }

                    $text .= '
                    <div class="slide slide-one" style="background-image: url(' . moodle_url::make_file_url("$CFG->wwwroot/pluginfile.php", "/{$this->context->id}/block_cocoon_slider_3/slides/" . $i . '/' . $mainfile) . ');">
					            <div class="container">
					                <div class="row">
					                    <div class="col-lg-12 text-center">
					                        <h3 class="banner-title">'.format_text($data->$slide_title, FORMAT_HTML, array('filter' => true)).'</h3>
					                        <p>'.format_text($data->$slide_subtitle, FORMAT_HTML, array('filter' => true)).'</p>
					                    </div>
					                </div>
					            </div>
					        </div>';
                }

            }
            $text .= '
            </div>';
            if($this->content->arrow_style != '2' && $data->slidesnumber > 1) {
             $text .='
            <div class="carousel-btn-block banner-carousel-btn2">
              <span class="carousel-btn left-btn">
                <i class="flaticon-left-arrow left"></i> ';
                if($this->content->arrow_style != '1'){
                  $text .=' <span class="left">'.$this->content->prev_1.' <br> '.$this->content->prev_2.'</span>';
                } else {
                  $text .=' <span class="left">'.$this->content->prev.'</span>';
                }
                $text .='
              </span>
                  <span class="carousel-btn right-btn">';
                  if($this->content->arrow_style != '1'){
                    $text .='<span class="right">'.$this->content->next_1.' <br> '.$this->content->next_2.'</span> <i class="flaticon-right-arrow-1 right"></i>';
                  } else {
                    $text .='<span class="right">'.$this->content->next.'</span> <i class="flaticon-right-arrow-1 right"></i>';
                  }
                  $text .='
                  </span>
              </div><!-- /.carousel-btn-block banner-carousel-btn -->';
            }
            $text .='
					</div><!-- /.main-banner-wrapper -->
				</div>
			</div>
		</div>
	</div>
  <section class="home7_about pt0 pb0">
  		<div class="container">
  			<div class="row home7_row">
  				<div class="col-sm-6 col-lg-3">
  					<div class="img_hvr_box home7" style="background-image: url('.$CFG->wwwroot.'/theme/edumy/images/service/5.jpg);">
  						<div class="overlay">
  							<div class="details">
  								<h4>'.format_text($data->feature_1_title, FORMAT_HTML, array('filter' => true)).'</h4>
  							</div>
  						</div>
  					</div>
  				</div>
  				<div class="col-sm-6 col-lg-3">
  					<div class="img_hvr_box home7 two" style="background-image: url('.$CFG->wwwroot.'/theme/edumy/images/service/6.jpg);">
  						<div class="overlay">
  							<div class="details">
  								<h4>'.format_text($data->feature_2_title, FORMAT_HTML, array('filter' => true)).'</h4>
  							</div>
  						</div>
  					</div>
  				</div>
  				<div class="col-sm-6 col-lg-3">
  					<div class="img_hvr_box home7 three" style="background-image: url('.$CFG->wwwroot.'/theme/edumy/images/service/7.jpg);">
  						<div class="overlay">
  							<div class="details">
  								<h4>'.format_text($data->feature_3_title, FORMAT_HTML, array('filter' => true)).'</h4>
  							</div>
  						</div>
  					</div>
  				</div>
  				<div class="col-sm-6 col-lg-3">
  					<div class="img_hvr_box home7 four" style="background-image: url('.$CFG->wwwroot.'/theme/edumy/images/service/8.jpg);">
  						<div class="overlay">
  							<div class="details">
  								<h4>'.format_text($data->feature_4_title, FORMAT_HTML, array('filter' => true)).'</h4>
  							</div>
  						</div>
  					</div>
  				</div>
  			</div>
        </div>
        </section>';
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

            file_save_draft_area_files($data->$field, $this->context->id, 'block_cocoon_slider_3', 'slides', $i, $filemanageroptions);
        }

        parent::instance_config_save($data, $nolongerused);
    }

    /**
     * When a block instance is deleted.
     */
    function instance_delete() {
        global $DB;
        $fs = get_file_storage();
        $fs->delete_area_files($this->context->id, 'block_cocoon_slider_3');
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
            if (!$fs->is_area_empty($fromcontext->id, 'block_cocoon_slider_3', 'slides', $i, false)) {
                $draftitemid = 0;
                file_prepare_draft_area($draftitemid, $fromcontext->id, 'block_cocoon_slider_3', 'slides', $i, $filemanageroptions);
                file_save_draft_area_files($draftitemid, $this->context->id, 'block_cocoon_slider_3', 'slides', $i, $filemanageroptions);
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
