<?php

class block_cocoon_parallax_features_edit_form extends block_edit_form {
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
        $mform->setDefault('config_title', 'Included Features');
        $mform->setType('config_title', PARAM_TEXT);

        $mform->addElement('text', 'config_subtitle', get_string('config_subtitle', 'theme_edumy'));
        $mform->setDefault('config_subtitle', 'Cum doctus civibus efficiantur in imperdiet deterruisset.');
        $mform->setType('config_subtitle', PARAM_TEXT);

        // Image
        $mform->addElement('filemanager', 'config_image', get_string('config_image', 'theme_edumy'), null,
                array('subdirs' => 0, 'maxbytes' => $maxbytes, 'maxfiles' => 1,
                'accepted_types' => array('.png', '.jpg', '.gif') ));

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
            $mform->setDefault('config_title' .$i , 'Multimedia Center');
            $mform->setType('config_title' . $i, PARAM_TEXT);

            $mform->addElement('text', 'config_link' . $i, get_string('config_link', 'theme_edumy', $i));
            $mform->setDefault('config_link' .$i , '#');
            $mform->setType('config_link' . $i, PARAM_TEXT);

            $select = $mform->addElement('select', 'config_icon' .$i, get_string('config_icon_class', 'theme_edumy'), $ccnFontList, array('class'=>'ccn_icon_class'));
            $select->setSelected('flaticon-student-3');

        }

        include($CFG->dirroot . '/theme/edumy/ccn/block_handler/edit.php');

    }

    function set_data($defaults) {
      // Begin CCN Image Processing
        if (empty($entry->id)) {
            $entry = new stdClass;
            $entry->id = null;
        }
        $draftitemid = file_get_submitted_draft_itemid('config_image');
        file_prepare_draft_area($draftitemid, $this->block->context->id, 'block_cocoon_parallax_features', 'content', 0,
            array('subdirs' => true));
        $entry->attachments = $draftitemid;
        parent::set_data($defaults);
        if ($data = parent::get_data()) {
            file_save_draft_area_files($data->config_image, $this->block->context->id, 'block_cocoon_parallax_features', 'content', 0,
                array('subdirs' => true));
        }
      // END CCN Image Processing
    }
}
