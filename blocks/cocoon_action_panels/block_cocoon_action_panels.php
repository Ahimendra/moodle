<?php
global $CFG;

class block_cocoon_action_panels extends block_base
{
    // Declare first
    public function init()
    {
        $this->title = get_string('cocoon_action_panels', 'block_cocoon_action_panels');
    }

    // Declare second
    public function specialization()
    {
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


        if(!empty($this->config->panel_1_title)){$this->content->panel_1_title = $this->config->panel_1_title;}
        if(!empty($this->config->panel_1_text)){$this->content->panel_1_text = $this->config->panel_1_text;}
        if(!empty($this->config->panel_1_button_text)){$this->content->panel_1_button_text = $this->config->panel_1_button_text;}
        if(!empty($this->config->panel_1_button_url)){$this->content->panel_1_button_url = $this->config->panel_1_button_url;}
        if(!empty($this->config->panel_2_title)){$this->content->panel_2_title = $this->config->panel_2_title;}
        if(!empty($this->config->panel_2_text)){$this->content->panel_2_text = $this->config->panel_2_text;}
        if(!empty($this->config->panel_2_button_text)){$this->content->panel_2_button_text = $this->config->panel_2_button_text;}
        if(!empty($this->config->panel_2_button_url)){$this->content->panel_2_button_url = $this->config->panel_2_button_url;}



        $this->content->text = '
        <div class="container mb60">
        <div class="row mt60">
  <div class="col-xs-12 col-sm-13 col-md-6 col-lg-6 col-xl-6">
    <div class="becomea_instructor tac-xxsd">
      <div class="bi_grid">';
      if (!empty($this->content->panel_1_title)){
        $this->content->text .='<h3>'.format_text($this->content->panel_1_title, FORMAT_HTML, array('filter' => true)).'</h3>';
      }
      if (!empty($this->content->panel_1_text)){
        $this->content->text .='<p>'.format_text($this->content->panel_1_text, FORMAT_HTML, array('filter' => true)).'</p>';
      }
      if (!empty($this->content->panel_1_button_url) && !empty($this->content->panel_1_button_text)){
        $this->content->text .='<a class="btn btn-thm" href="'.format_text($this->content->panel_1_button_url, FORMAT_HTML, array('filter' => true)).'">'.format_text($this->content->panel_1_button_text, FORMAT_HTML, array('filter' => true)).' <span class="flaticon-right-arrow-1"></span></a>';
      }
      $this->content->text .='</div>
    </div>
  </div>
  <div class="col-xs-12 col-sm-13 col-md-6 col-lg-6 col-xl-6">
    <div class="becomea_instructor style2 text-right tac-xxsd">
      <div class="bi_grid">';
      if (!empty($this->content->panel_2_title)){
      $this->content->text .='  <h3>'.format_text($this->content->panel_2_title, FORMAT_HTML, array('filter' => true)).'</h3>';
      }
      if (!empty($this->content->panel_2_text)){
        $this->content->text .='<p>'.format_text($this->content->panel_2_text, FORMAT_HTML, array('filter' => true)).'</p>';
      }
      if (!empty($this->content->panel_2_button_url) && !empty($this->content->panel_2_button_text)){
        $this->content->text .='<a class="btn btn-dark" href="'.format_text($this->content->panel_2_button_url, FORMAT_HTML, array('filter' => true)).'">'.format_text($this->content->panel_2_button_text, FORMAT_HTML, array('filter' => true)).' <span class="flaticon-right-arrow-1"></span></a>';
      }
      $this->content->text .='</div>
    </div>
  </div>
</div>
</div>
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
