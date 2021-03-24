<?php

class block_cocoon_myorders_edit_form extends block_edit_form
{
    protected function specific_definition($mform)
    {

        global $CFG;

        // Section header title according to language file.
        $mform->addElement('header', 'config_header', get_string('blocksettings', 'block'));

        // Title
        $mform->addElement('text', 'config_title', get_string('config_title', 'theme_edumy'));
        $mform->setDefault('config_title', 'My Orders');
        $mform->setType('config_title', PARAM_RAW);

        // Section header title according to language file.
        $mform->addElement('header', 'config_receipt', get_string('config_receipt', 'block_cocoon_myorders'));

        $mform->addElement('text', 'config_address_line_1', get_string('config_address_line_1', 'block_cocoon_myorders'));
        $mform->setDefault('config_address_line_1', '1 Trafalgar Square');
        $mform->setType('config_address_line_1', PARAM_RAW);

        $mform->addElement('text', 'config_address_line_2', get_string('config_address_line_2', 'block_cocoon_myorders'));
        $mform->setDefault('config_address_line_2', 'Westminster');
        $mform->setType('config_address_line_2', PARAM_RAW);

        $mform->addElement('text', 'config_address_line_3', get_string('config_address_line_3', 'block_cocoon_myorders'));
        $mform->setDefault('config_address_line_3', 'Central London');
        $mform->setType('config_address_line_3', PARAM_RAW);

        $mform->addElement('text', 'config_zip_code', get_string('config_zip_code', 'block_cocoon_myorders'));
        $mform->setDefault('config_zip_code', 'SW1 3EJ');
        $mform->setType('config_zip_code', PARAM_RAW);

        $mform->addElement('text', 'config_phone', get_string('config_phone', 'block_cocoon_myorders'));
        $mform->setDefault('config_phone', '+133-424-481-500');
        $mform->setType('config_phone', PARAM_RAW);

        $mform->addElement('text', 'config_email', get_string('config_email', 'block_cocoon_myorders'));
        $mform->setDefault('config_email', 'orders@edumylearning.edu');
        $mform->setType('config_email', PARAM_RAW);

        include($CFG->dirroot . '/theme/edumy/ccn/block_handler/edit.php');

    }

}
