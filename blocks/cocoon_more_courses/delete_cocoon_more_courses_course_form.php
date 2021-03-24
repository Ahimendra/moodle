<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class block_cocoon_more_courses_delete_featuredcourse_form extends moodleform {

    public function definition() {
        $mform =& $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);
        $mform->addElement('hidden', 'confirmdelete');
        $mform->setType('confirmdelete', PARAM_INT);

        $this->add_action_buttons(true, get_string('yes'));

    }
}
