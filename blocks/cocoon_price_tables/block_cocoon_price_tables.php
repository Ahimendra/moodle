<?php
global $CFG;

class block_cocoon_price_tables extends block_base
{
    // Declare first
    public function init()
    {
        $this->title = get_string('pluginname', 'block_cocoon_price_tables');
    }

    // Declare second
    public function specialization()
    {
        // $this->title = isset($this->config->title) ? format_string($this->config->title) : '';
        global $CFG;
        include($CFG->dirroot . '/theme/edumy/ccn/block_handler/specialization.php');
    }
    public function get_content(){
        global $CFG, $DB;
        require_once($CFG->libdir . '/filelib.php');
        if ($this->content !== null) {
            return $this->content;
        }
        $this->content         =  new stdClass;
        if(!empty($this->config->title)){$this->content->title = $this->config->title;}
        if(!empty($this->config->subtitle)){$this->content->subtitle = $this->config->subtitle;}
        if(!empty($this->config->counter_1)){$this->content->counter_1 = $this->config->counter_1;}
        if(!empty($this->config->counter_1_text)){$this->content->counter_1_text = $this->config->counter_1_text;}
        if(!empty($this->config->counter_1_icon)){$this->content->counter_1_icon = $this->config->counter_1_icon;}
        if(!empty($this->config->counter_2)){$this->content->counter_2 = $this->config->counter_2;}
        if(!empty($this->config->counter_2_text)){$this->content->counter_2_text = $this->config->counter_2_text;}
        if(!empty($this->config->counter_2_icon)){$this->content->counter_2_icon = $this->config->counter_2_icon;}
        if(!empty($this->config->counter_3)){$this->content->counter_3 = $this->config->counter_3;}
        if(!empty($this->config->counter_3_text)){$this->content->counter_3_text = $this->config->counter_3_text;}
        if(!empty($this->config->counter_3_icon)){$this->content->counter_3_icon = $this->config->counter_3_icon;}
        if(!empty($this->config->counter_4)){$this->content->counter_4 = $this->config->counter_4;}
        if(!empty($this->config->counter_4_text)){$this->content->counter_4_text = $this->config->counter_4_text;}
        if(!empty($this->config->counter_4_icon)){$this->content->counter_4_icon = $this->config->counter_4_icon;}
        if ($this->config->style == 1) {
          $this->content->style = 'divider_home2';
        } else {
          $this->content->style = 'divider_home1';
        }


        if (!empty($this->config) && is_object($this->config)) {
            $data = $this->config;
            $data->slidesnumber = is_numeric($data->slidesnumber) ? (int)$data->slidesnumber : 0;
        } else {
            $data = new stdClass();
            $data->slidesnumber = 0;
        }

        $fs = get_file_storage();
        $files = $fs->get_area_files($this->context->id, 'block_cocoon_price_tables', 'content');
        $this->content->image = '';
        foreach ($files as $file) {
            $filename = $file->get_filename();
            if ($filename <> '.') {
                $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), null, $file->get_filepath(), $filename);
                $this->content->image .=  $url;
            }
        }



        $this->content->text = '
        <section class="pricing-section">
      		<div class="container">
      			<div class="row">
      				<div class="col-lg-6 offset-lg-3">
      					<div class="main-title text-center">
      						<h3 class="mt0">'.format_text($this->content->title, FORMAT_HTML, array('filter' => true)).'</h3>
      						<p class="">'.format_text($this->content->subtitle, FORMAT_HTML, array('filter' => true)).'</p>
      					</div>
      				</div>
      			</div>
      			<div class="row">
            <div class="col-xs-12 col-xl-10 offset-xl-1">
            <div class="row">';

            if ($data->slidesnumber > 0) {

              for ($i = 1; $i <= $data->slidesnumber; $i++) {
                  $title = 'title' . $i;
                  $subtitle = 'subtitle' . $i;
                  $featured_text = 'featured_text' . $i;
                  $price = 'price' . $i;
                  $body = 'body' . $i;
                  $button_text = 'button_text' . $i;
                  $button_link = 'button_link' . $i;
                  $features = 'features' . $i;
                  if(!empty($data->$featured_text)) {
                    $class = 'tagged';
                    $btn_class = 'btn-style-two';
                  } else {
                    $class = '';
                    $btn_class = 'btn-style-three';
                  }
$this->content->text .= '

<div class="pricing-table '.$class.' col-lg-4 col-md-12 col-sm-12">
                    <div class="inner-box">
                        <div class="title-box">';
                        if(!empty($data->$featured_text)) {
                          $this->content->text .=' <span class="status">'.format_text($data->$featured_text, FORMAT_HTML, array('filter' => true)).'</span>';
                        }
                      $this->content->text .='
                            <span class="title">'.format_text($data->$title, FORMAT_HTML, array('filter' => true)).'</span>
                            <h3 class="price">'.format_text($data->$price, FORMAT_HTML, array('filter' => true)).'</h3>
                            <div class="text">'.format_text($data->$subtitle, FORMAT_HTML, array('filter' => true)).'</div>
                        </div>
                        <div class="table-content">
                            <ul>
                                <li><span class="ccn-pre-line">'.format_text($data->$features, FORMAT_HTML, array('filter' => true)).'</span></li>
                            </ul>
                        </div>
                        <div class="table-footer">
                            <a href="'.format_text($data->$button_link, FORMAT_HTML, array('filter' => true)).'" class="theme-btn '.$btn_class.'">'.format_text($data->$button_text, FORMAT_HTML, array('filter' => true)).' <span class="flaticon-right-arrow-1"></span></a>
                        </div>
                    </div>
                </div>';

                }
}
$this->content->text .='
  </div></div>
      			</div>
      		</div>
      	</section>
';
        return $this->content;
    }

    /**
     * Allow multiple instances in a single course?
     *
     * @return bool True if multiple instances are allowed, false otherwise.
     */
    public function instance_allow_multiple() {
        return true;
    }

    /**
     * Enables global configuration of the block in settings.php.
     *
     * @return bool True if the global configuration is enabled.
     */
    function has_config() {
        return true;
    }

    /**
     * Sets the applicable formats for the block.
     *
     * @return string[] Array of pages and permissions.
     */
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
