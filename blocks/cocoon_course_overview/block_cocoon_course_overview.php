<?php
global $CFG;

class block_cocoon_course_overview extends block_base
{
    // Declare first
    public function init()
    {
        $this->title = get_string('cocoon_course_overview', 'block_cocoon_course_overview');

    }

    // Declare second
    public function specialization()
    {
        // $this->title = isset($this->config->title) ? format_string($this->config->title) : '';
        global $CFG;
        include($CFG->dirroot . '/theme/edumy/ccn/block_handler/specialization.php');

    }

    function applicable_formats() {
        return array(
          'all' => true,
          'my' => false,
        );
    }



    public function get_content()
    {
        global $CFG, $DB;

        if ($this->content !== null) {
            return $this->content;
        }

        // Declare third
        $this->content         =  new stdClass;

        if(!empty($this->config->title)){$this->content->title = $this->config->title;}
        if(!empty($this->config->description)){$this->content->description = $this->config->description['text'];}



        $this->content->text = '
        <div class="cs_row_two">
          <div class="cs_overview">
            <h4 class="title">'. format_text($this->content->title, FORMAT_HTML, array('filter' => true)) .'</h4>
            '. format_text($this->content->description, FORMAT_HTML, array('filter' => true)) .'
          </div>
        </div>';
        return $this->content;
    }
    public function html_attributes() {
      global $CFG;
      $attributes = parent::html_attributes();
      include($CFG->dirroot . '/theme/edumy/ccn/block_handler/attributes.php');
      return $attributes;
    }
}
