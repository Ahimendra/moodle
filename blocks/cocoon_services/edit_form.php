<?php

class block_cocoon_services_edit_form extends block_edit_form {
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
        $mform->setDefault('config_title', 'Why Choose Us');
        $mform->setType('config_title', PARAM_TEXT);

        $mform->addElement('text', 'config_subtitle', get_string('config_subtitle', 'theme_edumy'));
        $mform->setDefault('config_subtitle', 'Cum doctus civibus efficiantur in imperdiet deterruisset.');
        $mform->setType('config_subtitle', PARAM_TEXT);

        $options = array(
            '0' => 'Border',
            '1' => 'Borderless',
        );
        $select = $mform->addElement('select', 'config_style', get_string('config_style', 'theme_edumy'), $options);
        $select->setSelected('0');


        // $radioarray=array();
        // $radioarray[] = $mform->createElement('radio', 'config_style', '', 'Standard', 0, $attributes);
        // $radioarray[] = $mform->createElement('radio', 'config_style', '', 'Fullsize', 1, $attributes);
        // $mform->addGroup($radioarray, 'config_style', 'Slider Size', array(' '), false);

        $slidesrange = range(0, 12);
        $mform->addElement('select', 'config_slidesnumber', get_string('config_items', 'theme_edumy'), $slidesrange);
        $mform->setDefault('config_slidesnumber', $data->slidesnumber);

        for($i = 1; $i <= $data->slidesnumber; $i++) {
            $mform->addElement('header', 'config_header' . $i , get_string('config_item', 'theme_edumy') . $i);

            $mform->addElement('text', 'config_title' . $i, get_string('config_title', 'theme_edumy', $i));
            $mform->setDefault('config_title' .$i , 'Trusted by Thousands');
            $mform->setType('config_title' . $i, PARAM_TEXT);

            $mform->addElement('textarea', 'config_body' . $i, get_string('config_body', 'theme_edumy', $i));
            $mform->setDefault('config_body' .$i , 'Aliquam dictum elit vitae mauris facilisis at dictum urna dignissim donec vel lectus vel felis.');
            $mform->setType('config_body' . $i, PARAM_TEXT);

            $mform->addElement('text', 'config_link' . $i, get_string('config_link', 'theme_edumy', $i));
            $mform->setDefault('config_link' .$i , '#');
            $mform->setType('config_link' . $i, PARAM_TEXT);

            $select = $mform->addElement('select', 'config_icon' .$i, get_string('config_icon_class', 'theme_edumy'), $ccnFontList, array('class'=>'ccn_icon_class'));
            $select->setSelected('flaticon-student-3');

        }

        include($CFG->dirroot . '/theme/edumy/ccn/block_handler/edit.php');

    }

    function set_data($defaults) {
        if (!empty($this->block->config) && is_object($this->block->config)) {

            for($i = 1; $i <= $this->block->config->slidesnumber; $i++) {
                $field = 'file_slide' . $i;
                $conffield = 'config_file_slide' . $i;
                $draftitemid = file_get_submitted_draft_itemid($conffield);
                file_prepare_draft_area($draftitemid, $this->block->context->id, 'block_cocoon_services', 'slides', $i, array('subdirs'=>false));
                $defaults->$conffield['itemid'] = $draftitemid;
                $this->block->config->$field = $draftitemid;
            }
        }

        parent::set_data($defaults);
    }
}
