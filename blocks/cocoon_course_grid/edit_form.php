<?php

defined('MOODLE_INTERNAL') || die();

class block_cocoon_course_grid_edit_form extends block_edit_form {

    protected function specific_definition($mform) {
        global $CFG;

        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        // Title
        $mform->addElement('text', 'config_title', get_string('config_title', 'theme_edumy'));
        $mform->setDefault('config_title', 'Browse Our Top Courses');
        $mform->setType('config_title', PARAM_RAW);

        // Subtitle
        $mform->addElement('text', 'config_subtitle', get_string('config_subtitle', 'theme_edumy'));
        $mform->setDefault('config_subtitle', 'Cum doctus civibus efficiantur in imperdiet deterruisCum doctus civibus efficiantur in imperdiet deterruisset.');
        $mform->setType('config_subtitle', PARAM_RAW);

        // Button Text
        $mform->addElement('text', 'config_button_text', get_string('config_button_text', 'theme_edumy'));
        $mform->setDefault('config_button_text', 'View all courses');
        $mform->setType('config_button_text', PARAM_RAW);

        // Button Link
        $mform->addElement('text', 'config_button_link', get_string('config_button_link', 'theme_edumy'));
        $mform->setDefault('config_button_link', '#');
        $mform->setType('config_button_link', PARAM_RAW);

        // Hover text
        $mform->addElement('text', 'config_hover_text', get_string('config_hover_text', 'block_cocoon_course_grid'));
        $mform->setDefault('config_hover_text', 'Preview Course');
        $mform->setType('config_hover_text', PARAM_RAW);

        // Hover accent
        $mform->addElement('text', 'config_hover_accent', get_string('config_hover_accent', 'block_cocoon_course_grid'));
        $mform->setDefault('config_hover_accent', 'Top Seller');
        $mform->setType('config_hover_accent', PARAM_RAW);

        $options = array(
            '0' => 'Hidden',
            '1' => 'Visible',
        );
        $select = $mform->addElement('select', 'config_course_image', get_string('config_image', 'theme_edumy'), $options);
        $select->setSelected('1');

        // $options = array(
        //     '0' => 'Hidden',
        //     '1' => 'Visible',
        // );
        // $select = $mform->addElement('select', 'config_updated', get_string('config_updated', 'theme_edumy'), $options);
        // $select->setSelected('1');

        // $options = array(
        //     '0' => 'Hidden',
        //     '1' => 'Visible',
        // );
        // $select = $mform->addElement('select', 'config_rating', get_string('config_rating', 'theme_edumy'), $options);
        // $select->setSelected('1');

        $options = array(
            '0' => 'Hidden',
            '1' => 'Visible',
            '2' => 'Visible (max 50 characters)',
            '3' => 'Visible (max 100 characters)',
            '4' => 'Visible (max 150 characters)',
            '5' => 'Visible (max 200 characters)',
            '6' => 'Visible (max 350 characters)',
            '7' => 'Visible (max 500 characters)',
        );
        $select = $mform->addElement('select', 'config_description', get_string('config_description', 'theme_edumy'), $options);
        $select->setSelected('0');

        // $options = array(
        //     '0' => 'Hidden',
        //     '1' => 'Visible',
        // );
        // $select = $mform->addElement('select', 'config_enrolments', get_string('config_enrolments', 'theme_edumy'), $options);
        // $select->setSelected('1');

        // $options = array(
        //     '0' => 'Hidden',
        //     '1' => 'Visible',
        // );
        // $select = $mform->addElement('select', 'config_newsitems', get_string('config_newsitems', 'theme_edumy'), $options);
        // $select->setSelected('1');

        $options = array(
            '0' => 'Hidden',
            '1' => 'Visible',
        );
        $select = $mform->addElement('select', 'config_price', get_string('config_price', 'theme_edumy'), $options);
        $select->setSelected('1');

        $options = array(
            '0' => 'Hidden',
            '1' => 'Visible',
        );
        $select = $mform->addElement('select', 'config_enrol_btn', get_string('config_enrol_btn', 'theme_edumy'), $options);
        $select->setSelected('0');

        $mform->addElement('text', 'config_enrol_btn_text', get_string('config_enrol_btn_text', 'theme_edumy'));
        $mform->setDefault('config_enrol_btn_text', 'Buy Now');
        $mform->setType('config_enrol_btn_text', PARAM_RAW);

        // $mform->addElement('html', '<a class="btn btn-secondary mt20 mb30 " href="'.$CFG->wwwroot.'/blocks/cocoon_course_grid/cocoon_course_grid.php">Select Featured Courses</a>');

        $options = array(
            'multiple' => true,
            'noselectionstring' => get_string('select_from_dropdown_multiple', 'theme_edumy'),
        );
        $mform->addElement('course', 'config_courses', get_string('courses'), $options);

        $options = array(
            '0' => 'No',
            '1' => 'Yes',
        );
        $select = $mform->addElement('select', 'config_group', get_string('config_group_courses_filter', 'theme_edumy'), $options);
        $select->setSelected('1');

        include($CFG->dirroot . '/theme/edumy/ccn/block_handler/edit.php');
    }
}
