<?php
require_once($CFG->dirroot. '/theme/edumy/ccn/user_handler/ccn_user_handler.php');
class block_cocoon_users_slider_2 extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_cocoon_users_slider_2');
    }

    private function get_users($ids)
    {
        $return = '';
        foreach($ids as $userId) {
          $ccnUserHandler = new ccnUserHandler();
          $ccnUser = $ccnUserHandler->ccnGetUserDetails($userId);
          // print_object($ccnUser);
          $teacherRating = '';
          if($ccnUser->teacherRating){
           $teacherRating = '<span class="float-right">'.$ccnUser->teacherRating.' <i class="fa fa-star color-golden"></i></span>';
          }
          $return .= '
            <div class="our_agent">
              <div class="thumb">
            <a href="'.$ccnUser->profileUrl.'">
              <img class="img-fluid w100" src="'.$ccnUser->rawAvatar.'" alt="">
              </a>
              <div class="overylay">
              <div class="ccn-control">
                <ul class="social_icon">
                  '.$ccnUserHandler->ccnOutputUserSocials($userId, 'li', 'list-inline-item').'
                </ul>
                <a href="'.$ccnUser->profileUrl.'">
                <div class="ccn-instructor-meta">
                <span class="float-left">'.$ccnUser->teachingCoursesCount.' '.get_string('courses').'</span>
                <span class="float-right">'.$ccnUser->teachingStudentCount.' '.get_string('students').'</span>
                </div>
                </a>
                </div>
              </div>
            </div>
            <a href="'.$ccnUser->profileUrl.'">
            <div class="details">
              <h4>'. $ccnUser->fullname .'</h4>
              <p>'.get_string('speaks', 'theme_edumy').' '.$ccnUser->lang . $teacherRating .'</p>
            </div>
            </a>
          </div>';
        }
        return $return;
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
            if(empty($users)) {
              $this->content->text = get_string('empty', 'block_cocoon_users_slider_2');
            } else {
              $this->content->text = '
                <section class="our-team instructor-page pb40">
                 <div class="container">
                  <div class="row">
                    <div class="col-lg-12">
                      <div class="main-title text-center">
                        <h3 class="mb0 mt0">'.$this->content->title.'</h3>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-12">
                      <div class="team_slider"> '. $this->get_users($userconfig) .' </div>
                      </div>
                    </div>
                  </div>
                 </section>';
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
