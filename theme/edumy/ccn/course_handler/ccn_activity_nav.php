<?php
/*
@ccnRef: @
*/

defined('MOODLE_INTERNAL') || die();
include_once($CFG->dirroot . '/course/lib.php');

$ccnFocusActivities = get_array_of_activities($COURSE->id);
$ccnCourseUrl = course_get_url($COURSE->id);
$ccnCourseSections = array();
$ccnUriForCourseFocus = $_SERVER['REQUEST_URI'];
$_ccnCourseSectionNav = '';

foreach($ccnFocusActivities as $section){
  if(empty($section->deletioninprogress)){
    if(!isset($ccnCourseSections[$section->section])){
     $ccnCourseSections[$section->section] = array();
    }
    $ccnCourseSections[$section->section][] = $section;
    if(course_format_uses_sections($COURSE->format)){
      $ccnCourseSections[$section->section]['name'] = get_section_name($COURSE->id, $section);
    } else {
      $ccnCourseSections[$section->section]['name'] = $COURSE->fullname;
    }
  }
}

foreach($ccnCourseSections as $key => $section){
  $_ccnCourseSectionNav .= '
  <div class="details">
    <div id="accordion-'.$key.'" class="panel-group cc_tab">
      <div class="panel">
        <div class="panel-heading">
          <h4 class="panel-title">
            <a href="#panelBodyCourseStart-'.$key.'" class="accordion-toggle link dropbtn" data-toggle="collapse" data-parent="#accordion-'.$key.'">'.$section['name'].'</a>
          </h4>
        </div>
        <div id="panelBodyCourseStart-'.$key.'" class="panel-collapse collapse show">
          <div class="panel-body">
            <ul class="cs_list mb0">';
              foreach($section as $key=>$activity){
                if($key !== 'name'){
                  $url = $CFG->wwwroot.'/mod/'.$activity->mod.'/view.php?id='.$activity->cm;
                  $uri = '/mod/'.$activity->mod.'/view.php?id='.$activity->cm;
                  if(strpos($ccnUriForCourseFocus, $uri)) {
                    $_ccnCourseSectionNavItemClass = 'active';
                  } else {
                    $_ccnCourseSectionNavItemClass = '';
                  }
                  $image = $OUTPUT->pix_url('icon', $activity->mod);
                  $_ccnCourseSectionNav .= '<li class="'.$_ccnCourseSectionNavItemClass.'"><a href="'.$url.'"><img src="'.$image.'"> '.$activity->name .'</a></li>';
                }
              }
              $_ccnCourseSectionNav .='
             </ul>
          </div>
        </div>
      </div>
    </div>
  </div>';
}
