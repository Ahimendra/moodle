<?php
/*
@ccnRef: @ USER HANDLER
*/

defined('MOODLE_INTERNAL') || die();

use \core_user\output\myprofile\category;
use core_user\output\myprofile\tree;
use core_user\output\myprofile\node;
use core_user\output\myprofile;
use context_course;
use core_course_list_element;
use DateTime;
use core_date;

class ccnUserHandler {
  public function ccnGetUserDetails($userId) {
    global $CFG, $USER, $DB, $SESSION, $SITE, $PAGE, $OUTPUT;

    $ccnUser = new \stdClass();

    $userData = get_complete_user_data('id', $userId);
    $moreUserData = $DB->get_record('user', array('id' => $userId), '*', MUST_EXIST);

    $userDescription = file_rewrite_pluginfile_urls($moreUserData->description, 'pluginfile.php', $userId, 'user', 'profile', null);
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

    // @ccnNote: Step 1: get user enrolments
    $userEnroledCourses = enrol_get_users_courses($userId);

    // @ccnNote: Step 2: get contextIds of user enrolments
    $userEnrolContexts = array();
    foreach($userEnroledCourses as $key => $enrolment) {
      $userEnrolContexts[] = $enrolment->ctxid;
    }
    // @ccnNote: Step 3: check whether user is a teacher anywhere in Moodle; get records of assignments with contextIds
    $teacherRole = $DB->get_field('role', 'id', array('shortname' => 'editingteacher'));
    $isTeacher = $DB->record_exists('role_assignments', ['userid' => $userId, 'roleid' => $teacherRole]);
    $userRoleAssignmentsAsTeacher = $DB->get_records('role_assignments', ['userid' => $userId, 'roleid' => $teacherRole]);

    // @ccnNote: Step 4: check for contextIds where user is a teacher
    $userTeachingContexts = new \stdClass();
    foreach($userEnrolContexts as $key => $context) {
      if($DB->record_exists('role_assignments', ['userid' => $userId, 'roleid' => $teacherRole, 'contextid' => $context])){
        $userTeachingContexts->$context = $context;
      }
    }

    // @ccnNote: Step 5: hashmap so we have course details of only the courses the user teaches
    $teachingCourses = array();
    foreach ($userEnroledCourses as $key => $enrolment){
      $ccnCtx = $enrolment->ctxid;
      if($enrolment->ctxid == $userTeachingContexts->$ccnCtx){
        $teachingCourses[$enrolment->id] = $enrolment;
      }
    }

    $enrolmentCount = count($userEnroledCourses);
    $teachingCoursesCount = count($userRoleAssignmentsAsTeacher);

    $teachingStudentCount = 0;
    $teacherCourseRatings = array();
    foreach($teachingCourses as $key => $course) {
      $courseID = $course->id;
      if ($DB->record_exists('course', array('id' => $courseID))) {
        $context = context_course::instance($courseID);
        $numberOfUsers = count_enrolled_users($context);
        $teachingStudentCount+= $numberOfUsers;
        $ccnRating = null;
        if($PAGE->theme->settings->course_ratings == 2){
          $ratingBlock = block_instance('cocoon_course_rating');
          $ccnRating = $ratingBlock->overall_rating($courseID);
          $teacherCourseRatings[] = $ccnRating;
        }
      }
    }

    if($teacherCourseRatings){
      $teacherRating = array_sum($teacherCourseRatings) / count($teacherCourseRatings);
      $teacherRating = number_format($teacherRating, 1);
    }

    $userLastCourses = $userData->lastcourseaccess;

    $ccnProfileCountTable = 'theme_edumy_counter';
    $ccnProfileCountConditions = array('course'=>$ccn_page->id);
    $ccnProfileViews = $DB->get_records($ccnProfileCountTable,array('course'=>$ccn_page->id));
    $ccnProfileCount = count($ccnProfileViews);

    $printUserAvatar = $OUTPUT->user_picture($userData, array('size' => 150, 'class' => 'img-fluid'));
    $rawAvatar = new \user_picture($userData);
    $rawAvatar->size = 500; // Size f2.
    $ccnRawAvatar = $rawAvatar->get_url($PAGE)->out(false);
    $profileUrl = $CFG->wwwroot . '/user/profile.php?id='. $userId;

    /* Map data */
    $ccnUser->userId = $userId;
    $ccnUser->fullname = $userFirst . ' ' . $userLast;
    $ccnUser->firstname = $userFirst;
    $ccnUser->lastname = $userLast;
    $ccnUser->description = $userDescription;
    $ccnUser->socialIcq = $userIcq;
    $ccnUser->socialSkype = $userSkype;
    $ccnUser->socialYahoo = $userYahoo;
    $ccnUser->socialAim = $userAim;
    $ccnUser->socialMsn = $userMsn;
    $ccnUser->phone1 = $userPhone1;
    $ccnUser->phone2 = $userPhone2;
    $ccnUser->since = $userSince;
    $ccnUser->lastLogin = $userLastLogin;
    $ccnUser->status = $userStatus;
    $ccnUser->email = $userEmail;
    $ccnUser->lang = $userLanguage;
    $ccnUser->enrolmentCount = $enrolmentCount;
    $ccnUser->isTeacher = $isTeacher;
    $ccnUser->teachingCoursesCount = $teachingCoursesCount;
    $ccnUser->teachingStudentCount = $teachingStudentCount;
    $ccnUser->teacherRating = $teacherRating;
    $ccnUser->profileCount = $ccnProfileCount;
    $ccnUser->printAvatar = $printUserAvatar;
    $ccnUser->rawAvatar = $ccnRawAvatar;
    $ccnUser->profileUrl = $profileUrl;

    return $ccnUser;
  }

  public function ccnOutputUserSocials($userId, $htmlElement, $htmlElementClass) {
    global $CFG, $USER, $DB, $SESSION, $SITE, $PAGE, $OUTPUT;

    $render = '';

    $userData = get_complete_user_data('id', $userId);
    $userIcq = $userData->icq;
    $userSkype = $userData->skype;
    $userYahoo = $userData->yahoo;
    $userAim = $userData->aim;
    $userMsn = $userData->msn;

    if($userSkype){
      $render .= '<'.$htmlElement.' class="'.$htmlElementClass.'"><span data-toggle="tooltip" data-placement="top" data-original-title="'.get_string('skypeid').': '.$userSkype.'"><i class="fa fa-skype"></i></span></'.$htmlElement.'>';
    }
    if($userIcq){
      $render .= '<'.$htmlElement.' class="'.$htmlElementClass.'"><span data-toggle="tooltip" data-placement="top" data-original-title="'.get_string('icqnumber').': '.$userIcq.'"><i class="fa fa-icq"></i></span></'.$htmlElement.'>';
    }
    if($userYahoo){
      $render .= '<'.$htmlElement.' class="'.$htmlElementClass.'"><span data-toggle="tooltip" data-placement="top" data-original-title="'.get_string('yahooid').': '.$userYahoo.'"><i class="fa fa-yahoo"></i></span></'.$htmlElement.'>';
    }
    if($userAim){
      $render .= '<'.$htmlElement.' class="'.$htmlElementClass.'"><span data-toggle="tooltip" data-placement="top" data-original-title="'.get_string('aimid').': '.$userAim.'"><i class="fa fa-aim"></i></span></'.$htmlElement.'>';
    }
    if($userMsn){
      $render .= '<'.$htmlElement.' class="'.$htmlElementClass.'"><span data-toggle="tooltip" data-placement="top" data-original-title="'.get_string('msnid').': '.$userMsn.'"><i class="fa fa-windows"></i></span></'.$htmlElement.'>';
    }

    return $render;

  }
}
