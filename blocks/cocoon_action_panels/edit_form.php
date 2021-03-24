<?php

class block_cocoon_action_panels_edit_form extends block_edit_form
{
    protected function specific_definition($mform)
    {
      global $CFG;

        // Section header title according to language file.
        $mform->addElement('header', 'config_header', get_string('blocksettings', 'block'));

        // Title
        $mform->addElement('text', 'config_title', get_string('config_title', 'block_cocoon_action_panels'));
        $mform->setDefault('config_title', 'Enhance your skills with best Online courses');
        $mform->setType('config_title', PARAM_RAW);

        // Panel Left
        $mform->addElement('header', 'config_panel_1', 'Panel Left');

        $mform->addElement('text', 'config_panel_1_title', get_string('config_panel_1_title', 'block_cocoon_action_panels'));
        $mform->setDefault('config_panel_1_title', 'Become an Instructor');
        $mform->setType('config_panel_1_title', PARAM_RAW);

        $mform->addElement('text', 'config_panel_1_text', get_string('config_panel_1_text', 'block_cocoon_action_panels'));
        $mform->setDefault('config_panel_1_text', 'Teach what you love. Dove Schooll gives you the tools to create an online course.');
        $mform->setType('config_panel_1_text', PARAM_RAW);

        $mform->addElement('text', 'config_panel_1_button_text', get_string('config_panel_1_button_text', 'block_cocoon_action_panels'));
        $mform->setDefault('config_panel_1_button_text', 'Start Teaching');
        $mform->setType('config_panel_1_button_text', PARAM_RAW);

        $mform->addElement('text', 'config_panel_1_button_url', get_string('config_panel_1_button_url', 'block_cocoon_action_panels'));
        $mform->setDefault('config_panel_1_button_url', '#');
        $mform->setType('config_panel_1_button_url', PARAM_RAW);

        // Panel Right
        $mform->addElement('header', 'config_panel_2', 'Panel Right');

        $mform->addElement('text', 'config_panel_2_title', get_string('config_panel_2_title', 'block_cocoon_action_panels'));
        $mform->setDefault('config_panel_2_title', 'Dove School For Business');
        $mform->setType('config_panel_2_title', PARAM_RAW);

        $mform->addElement('text', 'config_panel_2_text', get_string('config_panel_2_text', 'block_cocoon_action_panels'));
        $mform->setDefault('config_panel_2_text', 'Get unlimited access to 2,500 of Udemy’s top courses for your team.');
        $mform->setType('config_panel_2_text', PARAM_RAW);

        $mform->addElement('text', 'config_panel_2_button_text', get_string('config_panel_2_button_text', 'block_cocoon_action_panels'));
        $mform->setDefault('config_panel_2_button_text', 'Doing Business');
        $mform->setType('config_panel_2_button_text', PARAM_RAW);

        $mform->addElement('text', 'config_panel_2_button_url', get_string('config_panel_2_button_url', 'block_cocoon_action_panels'));
        $mform->setDefault('config_panel_2_button_url', '#');
        $mform->setType('config_panel_2_button_url', PARAM_RAW);

        include($CFG->dirroot . '/theme/edumy/ccn/block_handler/edit.php');

    }
}
