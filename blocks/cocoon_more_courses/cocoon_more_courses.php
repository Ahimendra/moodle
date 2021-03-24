<?php

require_once('../../config.php');
require_once($CFG->libdir.'/coursecatlib.php');
require_once($CFG->dirroot.'/blocks/moodleblock.class.php');
require_once($CFG->dirroot.'/blocks/cocoon_more_courses/block_cocoon_more_courses.php');
require_once($CFG->dirroot.'/blocks/cocoon_more_courses/cocoon_more_courses_form.php');

require_login();

$systemcontext = context_system::instance();

require_capability('block/cocoon_more_courses:addinstance', $systemcontext);

$PAGE->set_pagelayout('admin');
$PAGE->set_url('/blocks/cocoon_more_courses/cocoon_more_courses.php');
$PAGE->set_context($systemcontext);

$args = array(
    'availablecourses' => coursecat::get(0)->get_courses(array('recursive' => true)),
    'cocoon_more_courses' => block_cocoon_more_courses::get_featured_courses()
);

$editform = new cocoon_more_courses_form(null, $args);
if ($editform->is_cancelled()) {
    redirect($CFG->wwwroot.'/?redirect=0');
} else if ($data = $editform->get_data()) {

    if (isset($data->doadd) && $data->doadd == 1) {
        try {
            $DB->insert_record('block_cocoon_more_courses', $data->newfeatured);
        } catch (Exception $e) {
            throw $e;
        }
    }

    if (isset($data->featured) && !empty($data->featured)) {
        try {
            foreach ($data->featured as $f) {
                $DB->update_record('block_cocoon_more_courses', $f);
            }
        } catch (Exception $e) {
            throw new moodle_exception();
        }
    }
    redirect($CFG->wwwroot.'/blocks/cocoon_more_courses/cocoon_more_courses.php');
}

$site = get_site();

$PAGE->set_title(get_string('editpagetitle', 'block_cocoon_more_courses'));
$PAGE->set_heading($site->fullname . ' - ' .  get_string('pluginname', 'block_cocoon_more_courses'));

echo $OUTPUT->header(),

     $OUTPUT->heading(get_string('editpagedesc', 'block_cocoon_more_courses')),

     $editform->render(),

     $OUTPUT->footer();
