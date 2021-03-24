<?php
global $CFG;

class block_cocoon_features extends block_base
{
    // Declare first
    public function init()
    {
        $this->title = get_string('cocoon_features', 'block_cocoon_features');
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
        $col_class = "col-sm-6 col-lg-3";
        if(!empty($this->config->feature_1_title)){$this->content->feature_1_title = $this->config->feature_1_title;}
        if(!empty($this->config->feature_1_icon)){$this->content->feature_1_icon = $this->config->feature_1_icon;}
        if(!empty($this->config->feature_2_title)){   $this->content->feature_2_title = $this->config->feature_2_title;
                                                      $col_class = "col-sm-6 col-lg-6";
                                                  }
        if(!empty($this->config->feature_2_icon)){$this->content->feature_2_icon = $this->config->feature_2_icon;}
        if(!empty($this->config->feature_3_title)){   $this->content->feature_3_title = $this->config->feature_3_title;
                                                      $col_class = "col-sm-6 col-lg-4";
                                                  }
        if(!empty($this->config->feature_3_icon)){$this->content->feature_3_icon = $this->config->feature_3_icon;}
        if(!empty($this->config->feature_4_title)){   $this->content->feature_4_title = $this->config->feature_4_title;
                                                      $col_class = "col-sm-6 col-lg-3";
                                                  }
        if(!empty($this->config->feature_4_icon)){$this->content->feature_4_icon = $this->config->feature_4_icon;}
        if(!empty($this->config->title)){$this->content->title = $this->config->title;}
        if(!empty($this->config->subtitle)){$this->content->subtitle = $this->config->subtitle;}
        if(!empty($this->config->style)){$this->content->style = $this->config->style;}

        if($this->content->style == 1){
          $class = 'text-center';
        } else {
          $class = '';
        }

        $this->content->text = '';
        if(!empty($this->content->title)){
          $this->content->text .='
          <section class="home3_about2 pb10 pt30">';
        }
        $this->content->text .= '
        <div class="container">';
        if(!empty($this->content->title)){
          $this->content->text .='
            <div class="row">
              <div class="col-lg-6 offset-lg-3">
					       <div class="main-title text-center">
						        <h3 class="mt0">'.format_text($this->content->title, FORMAT_HTML, array('filter' => true)).'</h3>';
                    if(!empty($this->content->subtitle)){
						         $this->content->text .='<p>'.format_text($this->content->subtitle, FORMAT_HTML, array('filter' => true)).'</p>';
                   }
                   $this->content->text .='
					       </div>
				       </div>
			      </div>';
          }
      if(!empty($this->content->title)){
        $this->content->text .='<div class="row">';
      } else {
        $this->content->text .='<div class="row mt40">';
      }
        $this->content->text .='
        <div class="'.$col_class.' '.$class.'">
          <div class="home_icon_box home6">';
          if(!empty($this->content->feature_1_icon)){
            $this->content->text .='<div class="icon ccn-icon-reset"><span class="'.format_text($this->content->feature_1_icon, FORMAT_HTML, array('filter' => true)).'"></span></div>';
          }
          if(!empty($this->content->feature_1_title)){
            $this->content->text .='<p>'.format_text($this->content->feature_1_title, FORMAT_HTML, array('filter' => true)).'</p>';
          }
          $this->content->text .='
          </div>
        </div>';
        if(!empty($this->content->feature_3_title)){
       $this->content->text .='
        <div class="'.$col_class.' '.$class.'">
          <div class="home_icon_box home6">';
          if(!empty($this->content->feature_2_icon)){
            $this->content->text .='<div class="icon ccn-icon-reset"><span class="'.format_text($this->content->feature_2_icon, FORMAT_HTML, array('filter' => true)).'"></span></div>';
          }
          if(!empty($this->content->feature_2_title)){
            $this->content->text .='<p>'.format_text($this->content->feature_2_title, FORMAT_HTML, array('filter' => true)).'</p>';
          }
          $this->content->text .='
          </div>
        </div>';
      }
        if(!empty($this->content->feature_3_title)){
       $this->content->text .=' <div class="'.$col_class.' '.$class.'">
          <div class="home_icon_box home6">';
          if(!empty($this->content->feature_3_icon)){
            $this->content->text .='<div class="icon ccn-icon-reset"><span class="'.format_text($this->content->feature_3_icon, FORMAT_HTML, array('filter' => true)).'"></span></div>';
          }
          if(!empty($this->content->feature_3_title)){
            $this->content->text .='  <p>'.format_text($this->content->feature_3_title, FORMAT_HTML, array('filter' => true)).'</p>';
          }
          $this->content->text .='
          </div>
        </div>';
      }
        if(!empty($this->content->feature_4_title)){
        $this->content->text .='
        <div class="'.$col_class.' '.$class.'">
          <div class="home_icon_box home6">';
          if(!empty($this->content->feature_4_icon)){
            $this->content->text .='<div class="icon ccn-icon-reset"><span class="'.format_text($this->content->feature_4_icon, FORMAT_HTML, array('filter' => true)).'"></span></div>';
          }
          if(!empty($this->content->feature_4_title)){
            $this->content->text .='<p>'.format_text($this->content->feature_4_title, FORMAT_HTML, array('filter' => true)).'</p>';
          }
          $this->content->text .='
          </div>
        </div>';
      }
      $this->content->text .='
      </div>
    </div>';
if(!empty($this->content->title)){
  $this->content->text .='
  </section>';
}
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
