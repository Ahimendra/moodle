<?php
global $CFG;

class block_cocoon_about_2 extends block_base
{
    // Declare first
    public function init()
    {
        $this->title = get_string('cocoon_about_2', 'block_cocoon_about_2');
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
        if(!empty($this->config->col_1_title)){$this->content->col_1_title = $this->config->col_1_title;}
        if(!empty($this->config->col_1_body)){$this->content->col_1_body = $this->config->col_1_body['text'];}
        if(!empty($this->config->col_2_title)){$this->content->col_2_title = $this->config->col_2_title;}
        if(!empty($this->config->col_2_body)){$this->content->col_2_body = $this->config->col_2_body['text'];}

        $this->content->text = '
<div class="container mb70">
<div class="row">
  <div class="col-lg-6">
    <div class="about_whoweare">
      <h4>'.format_text($this->content->col_1_title, FORMAT_HTML, array('filter' => true)).'</h4>
      '.format_text($this->content->col_1_body, FORMAT_HTML, array('filter' => true)).'
    </div>
  </div>
  <div class="col-lg-6">
    <div class="about_whoweare">
      <h4>'.format_text($this->content->col_2_title, FORMAT_HTML, array('filter' => true)).'</h4>
      '.format_text($this->content->col_2_body, FORMAT_HTML, array('filter' => true)).'
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
