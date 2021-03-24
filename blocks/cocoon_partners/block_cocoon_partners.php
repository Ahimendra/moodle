<?php
global $CFG;

class block_cocoon_partners extends block_base
{
    // Declare first
    public function init()
    {
        $this->title = get_string('cocoon_partners', 'block_cocoon_partners');
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
        $fs = get_file_storage();
        $files = $fs->get_area_files($this->context->id, 'block_cocoon_partners', 'content');
        $this->content->image = '';
        foreach ($files as $file) {
            $filename = $file->get_filename();
            if ($filename <> '.') {
                $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), null, $file->get_filepath(), $filename);
                $this->content->image .= '
                <div class="col-sm-6 col-md-4 col-lg">
							<div class="our_partner">
								<img class="img-fluid" src="'. $url.'" alt="'. $filename.'">
							</div>
						</div>


				';
            }
        }



        $this->content->text = '


        <section id="our-partners" class="our-partners">
		<div class="container">
			<div class="row">
				<div class="col-lg-6 offset-lg-3">
					<div class="main-title text-center">
						<h3 class="mt0">'.$this->content->title.'</h3>
						<p>'.$this->content->subtitle.'</p>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-8 offset-lg-2">
					<div class="row">

'. $this->content->image .'
</div>
</div>
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
