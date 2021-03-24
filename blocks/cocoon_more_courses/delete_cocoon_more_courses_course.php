<?php

require_once("../../config.php");
require_once($CFG->dirroot.'/blocks/cocoon_more_courses/delete_cocoon_more_courses_course_form.php');

$courseid = required_param('courseid', PARAM_INT);

$PAGE->set_url('/blocks/cocoon_more_courses/delete_cocoon_more_courses_course.php', array('courseid' => $courseid));
$context = context_system::instance();
$PAGE->set_context($context);

require_login();

require_capability('block/cocoon_more_courses:addinstance', $context);

$mform = new block_cocoon_more_courses_delete_featuredcourse_form();
$newformdata = array('courseid' => $courseid, 'confirmdelete' => '1');
$mform->set_data($newformdata);
$formdata = $mform->get_data();

if ($mform->is_cancelled()) {
    redirect($CFG->wwwroot.'/blocks/cocoon_more_courses/cocoon_more_courses.php');
}

if (isset($formdata->confirmdelete) AND $formdata->confirmdelete == 1) {
    require_once($CFG->dirroot.'/blocks/moodleblock.class.php');
    require_once($CFG->dirroot.'/blocks/cocoon_more_courses/block_cocoon_more_courses.php');
    block_cocoon_more_courses::delete_featuredcourse($formdata->courseid);
    redirect($CFG->wwwroot.'/blocks/cocoon_more_courses/cocoon_more_courses.php');
}

$title = get_string('delete_featuredcourse', 'block_cocoon_more_courses');

$PAGE->navbar->add($title);
$PAGE->set_heading($title);
$PAGE->set_title($title);

echo $OUTPUT->header(),

     $OUTPUT->box_start('generalbox errorboxcontent boxaligncenter boxwidthnormal'),
     html_writer::tag('p', get_string('confirmdelete', 'block_cocoon_more_courses'), array('class' => 'bold'));

$mform->display();

echo $OUTPUT->box_end(),
     $OUTPUT->footer();
