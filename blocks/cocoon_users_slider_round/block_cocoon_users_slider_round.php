<?php

class block_cocoon_users_slider_round extends block_base {

    /**
     * Initializes class member variables.
     */
    public function init() {
        // Needed by Moodle to differentiate between blocks.
        $this->title = get_string('pluginname', 'block_cocoon_users_slider_round');
    }

    private function get_users($ids)
    {
        global $DB, $OUTPUT, $PAGE;

        $usernames = [];
        if(empty($ids)) return [];

        list($uids, $params) = $DB->get_in_or_equal($ids);
        $rs = $DB->get_recordset_select('user', 'id ' . $uids, $params, '', 'id,firstname,lastname,email,picture,imagealt,lastnamephonetic,firstnamephonetic,middlename,alternatename,department,lastaccess');

        foreach ($rs as $record)
        {
            $record->fullname = fullname($record);
            $record->department = $record->department;
            $record->identity = $record->email;
            $record->hasidentity = true;

            $url = new moodle_url('/user/profile.php', array('id' => $record->id));

            // Get the user picture data - messaging has always shown these to the user.
            $userpicture = new \user_picture($record);

            $userpicture->size = 300; // Size f2.
            $record->profileimageurlsmall = $userpicture->get_url($PAGE)->out(false);

            $usernames[$record->id] = '
            <div class="item">
            <a href="'.$url.'">
  <div class="instructor_col">
    <div class="thumb">
      <img class="img-fluid img-rounded-circle" src="'.$record->profileimageurlsmall.'" alt="'. $record->fullname.'">
    </div>
    <div class="details">
      <ul>
      <li class="list-inline-item"><i class="fa fa-star"></i></li>
      <li class="list-inline-item"><i class="fa fa-star"></i></li>
      <li class="list-inline-item"><i class="fa fa-star"></i></li>
      <li class="list-inline-item"><i class="fa fa-star"></i></li>
      <li class="list-inline-item"><i class="fa fa-star"></i></li>
      </ul>
      <h4>'. $record->fullname.'</h4>
      <p>'. $record->department .'</p>
    </div>
  </div>
  </a>
</div>
                    ';
        }
        $rs->close();

        return $usernames;
    }


    /**
     * Returns the block contents.
     *
     * @return stdClass The block contents.
     */
    public function get_content() {


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

        if(!empty($this->config->title)){$this->content->title = $this->config->title;}
        if(!empty($this->config->subtitle)){$this->content->subtitle = $this->config->subtitle;}

        if (!empty($this->config->text)) {
            $this->content->text = $this->config->text;
        } else
        {
            $userconfig = null;
            if(!empty($this->config->users))
            {
                $userconfig = $this->config->users;
            }
            $users = $this->get_users($userconfig);
            if(empty($users))
            {
                $this->content->text = get_string('empty', 'block_cocoon_users_slider_round');
            }
            else
            {
                $list = [];
                foreach ($users as $id => $username)
                {
                    $link = $username;
                    $list[] = $link;
                }
                $this->content->text = '
                <section class="our-team home8 pb10 pt30">
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
      <div class="col-lg-12">
        <div class="instructor_slider_home3 home8">
 '. implode('', $list) .' </div>
				</div>
			</div>
		</div>
	</section>
                ';
            }
        }

        return $this->content;
    }

    /**
     * Defines configuration data.
     *
     * The function is called immediatly after init().
     */
    public function specialization() {

        // Load user defined title and make sure it's never empty.
        // if (empty($this->config->title)) {
        //     $this->title = get_string('pluginname', 'block_cocoon_users_slider_round');
        // } else {
        //     $this->title = $this->config->title;
        // }
        global $CFG;
        include($CFG->dirroot . '/theme/edumy/ccn/block_handler/specialization.php');
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
