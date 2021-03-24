<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

require_once("../../config.php");
require_once($CFG->dirroot.'/blocks/cocoon_courses_slider/delete_cocoon_courses_slider_form.php');

$courseid = required_param('courseid', PARAM_INT);

$PAGE->set_url('/blocks/cocoon_courses_slider/delete_cocoon_courses_slider_course.php', array('courseid' => $courseid));
$context = context_system::instance();
$PAGE->set_context($context);

require_login();

require_capability('block/cocoon_courses_slider:addinstance', $context);

$mform = new block_cocoon_courses_slider_delete_featuredcourse_form();
$newformdata = array('courseid' => $courseid, 'confirmdelete' => '1');
$mform->set_data($newformdata);
$formdata = $mform->get_data();

if ($mform->is_cancelled()) {
    redirect($CFG->wwwroot.'/blocks/cocoon_courses_slider/cocoon_courses_slider.php');
}

if (isset($formdata->confirmdelete) AND $formdata->confirmdelete == 1) {
    require_once($CFG->dirroot.'/blocks/moodleblock.class.php');
    require_once($CFG->dirroot.'/blocks/cocoon_courses_slider/block_cocoon_courses_slider.php');
    block_cocoon_courses_slider::delete_featuredcourse($formdata->courseid);
    redirect($CFG->wwwroot.'/blocks/cocoon_courses_slider/cocoon_courses_slider.php');
}

$title = get_string('delete_featuredcourse', 'block_cocoon_courses_slider');

$PAGE->navbar->add($title);
$PAGE->set_heading($title);
$PAGE->set_title($title);

echo $OUTPUT->header(),

     $OUTPUT->box_start('generalbox errorboxcontent boxaligncenter boxwidthnormal'),
     html_writer::tag('p', get_string('confirmdelete', 'block_cocoon_courses_slider'), array('class' => 'bold'));

$mform->display();

echo $OUTPUT->box_end(),
     $OUTPUT->footer();
