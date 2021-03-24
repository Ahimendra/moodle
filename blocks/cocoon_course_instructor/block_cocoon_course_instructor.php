<?php
global $CFG;

class block_cocoon_course_instructor extends block_base
{
    // Declare first
    public function init()
    {
        $this->title = get_string('cocoon_course_instructor', 'block_cocoon_course_instructor');
    }

    // Declare second
    public function specialization()
    {
        // $this->title = isset($this->config->title) ? format_string($this->config->title) : '';
        $this->name = isset($this->config->name) ? format_string($this->config->name) : '';
        $this->position = isset($this->config->position) ? format_string($this->config->position) : '';
        $this->students = isset($this->config->students) ? format_string($this->config->students) : '';
        $this->reviews = isset($this->config->reviews) ? format_string($this->config->reviews) : '';
        $this->courses = isset($this->config->courses) ? format_string($this->config->courses) : '';
        $this->image = isset($this->config->image) ? format_string($this->config->image) : '';
        $this->bio = isset($this->config->bio) ? format_string($this->config->bio) : '';
        global $CFG;
        include($CFG->dirroot . '/theme/edumy/ccn/block_handler/specialization.php');
    }

    function applicable_formats() {
        return array(
          'all' => true,
          'my' => false,
        );
    }

    function instance_allow_multiple() {
        return true;
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
            							<div class="team_member style2 text-center">
            								<div class="instructor_col">
            									<div class="thumb">
            										<img class="img-fluid img-rounded-circle" src="'.$record->profileimageurlsmall.'" alt="">
            									</div>
            									<div class="details">
            										<h4>'. $record->fullname .'</h4>
            										<p>'. $record->department .'</p>
            										<ul>
                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                <li class="list-inline-item"><i class="fa fa-star"></i></li>
            										</ul>
            									</div>
            								</div>
            								<div class="tm_footer">
            									<ul>
            										<li class="list-inline-item">'.get_string('last_online', 'theme_edumy').' '.userdate($record->lastaccess, get_string('strftimedateshort', 'langconfig'), 0) .'</li>
            									</ul>
            								</div>
            							</div></a>
            						</div>

                    ';
        }
        $rs->close();

        return $usernames;
    }

    public function get_content()
    {
        global $CFG, $DB, $USER, $OUTPUT, $COURSE;
        require_once($CFG->libdir . '/filelib.php');


        $courseid = $COURSE->id;
        $context = context_course::instance($courseid);

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content         =  new stdClass;
        $this->content->text = '';

        $userID = $userDescription = $userURL = $userFirst = $userLast = $userLastLogin = $userAvatar = $teachingCoursesCount = $teachingStudentCount = null;



        if(!empty($this->config->user))
        {
            $userID = $this->config->user;

            $userData = get_complete_user_data('id', $userID);
            $moreUserData = $DB->get_record('user', array('id' => $userID), '*', MUST_EXIST);
            $userDescription = file_rewrite_pluginfile_urls($moreUserData->description, 'pluginfile.php', $userID, 'user', 'profile', null);
            $userURL = $CFG->wwwroot.'/user/profile.php?id='.$userID;
            $userFirst = $userData->firstname;
            $userLast = $userData->lastname;
            $userIcq = $userData->icq;
            $userSkype = $userData->skype;
            $userYahoo = $userData->yahoo;
            $userAim = $userData->aim;
            $userMsn = $userData->msn;
            $userPhone1 = $userData->phone1;
            $userPhone2 = $userData->phone2;
            $userSince = userdate($userData->timecreated);
            $userLastLogin = userdate($userData->lastaccess);
            $userStatus = $userData->currentlogin;
            $userEmail = $userData->email;
            $userLang = $userData->lang.'-Latn-IT-nedis';
            if (class_exists('Locale')) {
              $userLanguage = \Locale::getDisplayLanguage($userLang, $CFG->lang);
            }
            $userEnroledCourses = enrol_get_users_courses($userID);
            $enrolmentCount = count($userEnroledCourses);

            $userAvatar = $OUTPUT->user_picture($userData, array('size' => 150, 'class' => 'img-fluid'));

            //check if user is a teacher ANYWHERE in Moodle
            $teacherRole = $DB->get_field('role', 'id', array('shortname' => 'editingteacher'));
            $isTeacher = $DB->record_exists('role_assignments', ['userid' => $userID, 'roleid' => $teacherRole]);
            // if($isTeacher && $teacherRole){
              $teachingCourses = $DB->get_records('role_assignments', ['userid' => $userID, 'roleid' => $teacherRole]);
              $teachingCoursesCount = count($teachingCourses);

              $teachingStudentCount = 0;
              foreach($teachingCourses as $course) {
                $courseID = $course->id;
                if ($DB->record_exists('course', array('id' => $courseID))) {
                  $context = context_course::instance($courseID);
                  $numberOfUsers = count_enrolled_users($context);
                  $teachingStudentCount+= $numberOfUsers;
                }
              }
            // }

        }
        // $users = $this->get_users($userID);
        // if(empty($users))
        // {
        //     $this->content->text .= get_string('empty', 'block_cocoon_users_slider');
        // }
        // else
        // {
            // $list = [];
            // foreach ($users as $id => $username)
            // {
            //     $link = $username;
            //     $list[] = $link;
            // }






        if(!empty($this->config->title)){$this->content->title = $this->config->title;}
        if(!empty($this->config->name)){
          $renderName = $this->config->name;
        } elseif(!empty($this->config->user)){
          $renderName = '<a href="'.$userURL.'">'. $userFirst .' '. $userLast .'</a>';
        } else {
          $renderName = 'Name';
        }
        if(!empty($this->config->position)){
          $renderPosition = $this->config->position;
        } elseif(!empty($this->config->user)){
          $renderPosition = get_string('lastsiteaccess').': '.$userLastLogin;
        } else {
          $renderPosition = get_string('config_teacher', 'theme_edumy');
        }
        if(!empty($this->config->students)){
          $renderStudents = $this->config->students;
        } elseif(!empty($this->config->user)){
          if($teachingStudentCount != null) {
            $renderStudents = $teachingStudentCount . ' ' . get_string('students');
          } else {
            $renderStudents = 'No students yet';
          }
        } else {
          $renderStudents = count_enrolled_users($context);
        }
        if(!empty($this->config->reviews)){
          $this->content->reviews = $this->config->reviews;
        }
        if(!empty($this->config->courses)){
          $renderCourses = $this->config->courses;
        }elseif(!empty($this->config->user)){
          if($teachingCoursesCount != null) {
            $renderCourses = $teachingCoursesCount . ' ' . get_string('courses');
          } else {
            $renderCourses = 'No courses yet';
          }
        } else {
          $renderStudents = '';
        }
        if(!empty($this->config->bio) && strlen($this->config->bio) > 2){
          $renderBio = $this->config->bio['text'];
        } elseif(!empty($this->config->user)){
          $renderBio = $userDescription;
        } else {
          $renderBio = '';
        }
        if(!empty($this->config->rating)){$this->content->rating = $this->config->rating;}




        $fs = get_file_storage();
        $files = $fs->get_area_files($this->context->id, 'block_cocoon_course_instructor', 'content');

        if(!empty($this->config->image) && !empty($files)){
          //Begin CCN Image Processing
          foreach ($files as $file) {
              $filename = $file->get_filename();
              if ($filename <> '.') {
                  $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), null, $file->get_filepath(), $filename);
                  $renderImage = '<img src="' . $url . '" alt="' . $filename . '" />';
              }
          }
          // End CCN Image Processing
        } elseif(!empty($this->config->user)){
          $renderImage = $userAvatar;

        } else {
          $renderImage = '<img src="' . $CFG->wwwroot .'/theme/edumy/images/team/6.png" alt="" />';
        }











        $this->content->text .= '
        <div class="cs_row_four">
          <div class="about_ins_container">';
          if($this->content->title){
          $this->content->text .='
            <h4 class="aii_title">'. format_text($this->content->title, FORMAT_HTML, array('filter' => true)) .'</h4>';
          }
          if($renderImage){
          $this->content->text .='
            <div class="about_ins_info">
              <div class="thumb">'. $renderImage .'</div>
            </div>';
          }
          $this->content->text .='
            <div class="details">';
          if($this->content->rating){
          $this->content->text .='
              <ul class="review_list">
                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                <li class="list-inline-item"><i class="fa fa-star"></i></li>
                <li class="list-inline-item">'.$this->content->rating.'</li>
              </ul>';
            }
            $this->content->text .='
              <ul class="about_info_list">';
            if($this->content->reviews){
              $this->content->text .='
                <li class="list-inline-item"><span class="flaticon-rating"></span> '. format_text($this->content->reviews, FORMAT_HTML, array('filter' => true)) .' </li>';
            }
            if($renderStudents){
              $this->content->text .='
                <li class="list-inline-item"><span class="flaticon-profile"></span> '. format_text($renderStudents, FORMAT_HTML, array('filter' => true)) .' </li>';
            }
            if($renderCourses){
              $this->content->text .='
                <li class="list-inline-item"><span class="flaticon-play-button-1"></span> '. format_text($renderCourses, FORMAT_HTML, array('filter' => true)) .' </li>';
            }
            $this->content->text .='
              </ul>';
            if($renderName){
            $this->content->text .='
              <h4>'. format_text($renderName, FORMAT_HTML, array('filter' => true)) .'</h4>';
            }
            if($renderPosition){
            $this->content->text .='
              <p class="subtitle">'. format_text($renderPosition, FORMAT_HTML, array('filter' => true)) .'</p>';
            }
            if($renderBio){
              $this->content->text .= format_text($renderBio, FORMAT_HTML, array('filter' => true));
            }
            $this->content->text .='
            </div>
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
