<?php

class block_cocoon_price_tables_edit_form extends block_edit_form {
    protected function specific_definition($mform) {
        global $CFG;
        $ccnFontList = include($CFG->dirroot . '/theme/edumy/ccn/font_handler/ccn_font_select.php');

        if (!empty($this->block->config) && is_object($this->block->config)) {
            $data = $this->block->config;
        } else {
            $data = new stdClass();
            $data->slidesnumber = 0;
        }


        // Fields for editing HTML block title and contents.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        $mform->addElement('text', 'config_title', get_string('config_title', 'theme_edumy'));
        $mform->setDefault('config_title', 'Choose a Package');
        $mform->setType('config_title', PARAM_TEXT);

        $mform->addElement('text', 'config_subtitle', get_string('config_subtitle', 'theme_edumy'));
        $mform->setDefault('config_subtitle', 'Cum doctus civibus efficiantur in imperdiet deterruisset.');
        $mform->setType('config_subtitle', PARAM_TEXT);




        $slidesrange = range(0, 12);
        $mform->addElement('select', 'config_slidesnumber', get_string('config_items', 'theme_edumy'), $slidesrange);
        $mform->setDefault('config_slidesnumber', $data->slidesnumber);

        for($i = 1; $i <= $data->slidesnumber; $i++) {
            $mform->addElement('header', 'config_header' . $i , get_string('config_item', 'theme_edumy') . $i);

            $mform->addElement('text', 'config_title' . $i, get_string('config_title', 'theme_edumy', $i));
            $mform->setDefault('config_title' .$i , 'Basic');
            $mform->setType('config_title' . $i, PARAM_TEXT);

            $mform->addElement('text', 'config_subtitle' . $i, get_string('config_subtitle', 'theme_edumy', $i));
            $mform->setDefault('config_subtitle' .$i , 'One Time Fee for one listing, highlighted in the search results.');
            $mform->setType('config_subtitle' . $i, PARAM_TEXT);

            $mform->addElement('text', 'config_featured_text' . $i, get_string('config_featured_text', 'theme_edumy', $i));
            $mform->setType('config_featured_text' . $i, PARAM_TEXT);

            $mform->addElement('text', 'config_price' . $i, get_string('config_price', 'theme_edumy', $i));
            $mform->setDefault('config_price' .$i , '$4.95');
            $mform->setType('config_price' . $i, PARAM_TEXT);

            $mform->addElement('textarea', 'config_body' . $i, get_string('config_body', 'theme_edumy', $i));
            $mform->setDefault('config_body' .$i , 'Looking for a cozy hotel to stay, a restaurant to eat, a museum to visit or a mall to do some shopping?');
            $mform->setType('config_body' . $i, PARAM_TEXT);

            $mform->addElement('text', 'config_button_text' . $i, get_string('config_button_text', 'theme_edumy', $i));
            $mform->setDefault('config_button_text' .$i , 'Get Started');
            $mform->setType('config_button_text' . $i, PARAM_TEXT);

            $mform->addElement('text', 'config_button_link' . $i, get_string('config_button_link', 'theme_edumy', $i));
            $mform->setDefault('config_button_link' .$i , '#');
            $mform->setType('config_button_link' . $i, PARAM_TEXT);

            $mform->addElement('textarea', 'config_features' . $i, get_string('config_features', 'theme_edumy', $i));
            $mform->setDefault('config_features' .$i , 'One Course
Unlimited Availability
Certification 100% Approval
24/7 Support');
            $mform->setType('config_features' . $i, PARAM_TEXT);
            $mform->addHelpButton('config_features' . $i, 'config_features', 'theme_edumy');



        }

        include($CFG->dirroot . '/theme/edumy/ccn/block_handler/edit.php');

    }

    function set_data($defaults) {
        // if (!empty($this->block->config) && is_object($this->block->config)) {
        //
        //     for($i = 1; $i <= $this->block->config->slidesnumber; $i++) {
        //         $field = 'file_slide' . $i;
        //         $conffield = 'config_file_slide' . $i;
        //         $draftitemid = file_get_submitted_draft_itemid($conffield);
        //         file_prepare_draft_area($draftitemid, $this->block->context->id, 'block_cocoon_price_tables', 'slides', $i, array('subdirs'=>false));
        //         $defaults->$conffield['itemid'] = $draftitemid;
        //         $this->block->config->$field = $draftitemid;
        //     }
        // }

        parent::set_data($defaults);
    }
}
