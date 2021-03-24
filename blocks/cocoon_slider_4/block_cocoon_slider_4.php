<?php

class block_cocoon_slider_4 extends block_base {

    /**
     * Start block instance.
     */
    function init() {
        $this->title = get_string('pluginname', 'block_cocoon_slider_4');
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
        //     $this->title = get_string('newcustomsliderblock', 'block_cocoon_slider_4');
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
            if(!empty($this->config->style)){
              if ($data->style == 1) {
                $slidersize = 'slide slide-one home6';
              } else {
                $slidersize = 'slide slide-one sh2';
              }
            } else {
              $slidersize = 'slide slide-one sh2';
            }
            $fs = get_file_storage();
            $files = $fs->get_area_files($this->context->id, 'block_cocoon_slider_4', 'content');
            $ccn_image = '';
            foreach ($files as $file) {
                $filename = $file->get_filename();
                if ($filename <> '.') {
                    $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), null, $file->get_filepath(), $filename);
                    $ccn_image .=  $url;
                }
            }
        } else {
            $data = new stdClass();
            $data->slidesnumber = 0;
        }

        $text = '';
        $bannerstyle = '';
        if ($data->slidesnumber > 1) {
          $bannerstyle .= 'home5_slider--multiple';
        } else {
          $bannerstyle .= 'home5_slider--single';
        }
        if ($data->slidesnumber > 0) {
            $text = '
            <section class="home-five bg-img5" style="background-image:url('.$ccn_image.');background-size:cover;">
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<div class="home5_slider '.$bannerstyle.'">';
            $fs = get_file_storage();
            for ($i = 1; $i <= $data->slidesnumber; $i++) {
                $sliderimage = 'file_slide' . $i;
                $slide_title = 'slide_title' . $i;
                $slide_title_2 = 'slide_title_2' . $i;
                $slide_subtitle = 'slide_subtitle' . $i;
                $slide_btn_url = 'slide_btn_url' . $i;
                $slide_btn_text = 'slide_btn_text' . $i;
                $slide_line_break = 'slide_line_break' . $i;
                $slide_btn_target = 'slide_btn_target' . $i;

                $text .= '
                <div class="item">
                  <div class="home-text">';
                  if(!empty($data->$slide_title)){
                    $text .='<h2>'.format_text($data->$slide_title, FORMAT_HTML, array('filter' => true));
                    if(!empty($data->$slide_title_2)){
                      if(!empty($data->$slide_line_break) && $data->$slide_line_break == '1'){
                        $text .='<br>';
                      }
                      $text .='<span class="text-thm">'.format_text($data->$slide_title_2, FORMAT_HTML, array('filter' => true)).'</span>';
                    }
                    $text .='</h2>';
                  }
                  if(!empty($data->$slide_subtitle)){
                    $text .='<p>'.format_text($data->$slide_subtitle, FORMAT_HTML, array('filter' => true)).'</p>';
                  }
                  if(!empty($data->$slide_btn_text) && !empty($slide_btn_url)){
                    $text .='<a target="'.$data->$slide_btn_target.'" class="btn home_btn" href="'.format_text($data->$slide_btn_url, FORMAT_HTML, array('filter' => true)).'">'.format_text($data->$slide_btn_text, FORMAT_HTML, array('filter' => true)).'</a>';
                  }
                  $text .='
                  </div>
                </div>';
              }
              $text .= '
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

            file_save_draft_area_files($data->$field, $this->context->id, 'block_cocoon_slider_4', 'slides', $i, $filemanageroptions);
        }

        parent::instance_config_save($data, $nolongerused);
    }

    /**
     * When a block instance is deleted.
     */
    function instance_delete() {
        global $DB;
        $fs = get_file_storage();
        $fs->delete_area_files($this->context->id, 'block_cocoon_slider_4');
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
            if (!$fs->is_area_empty($fromcontext->id, 'block_cocoon_slider_4', 'slides', $i, false)) {
                $draftitemid = 0;
                file_prepare_draft_area($draftitemid, $fromcontext->id, 'block_cocoon_slider_4', 'slides', $i, $filemanageroptions);
                file_save_draft_area_files($draftitemid, $this->context->id, 'block_cocoon_slider_4', 'slides', $i, $filemanageroptions);
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
