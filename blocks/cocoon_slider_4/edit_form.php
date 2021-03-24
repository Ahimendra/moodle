<?php

class block_cocoon_slider_4_edit_form extends block_edit_form {
    protected function specific_definition($mform) {
        global $CFG;

        if (!empty($this->block->config) && is_object($this->block->config)) {
            $data = $this->block->config;
        } else {
            $data = new stdClass();
            $data->slidesnumber = 0;
        }


        // Fields for editing HTML block title and contents.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        // Image
        $mform->addElement('filemanager', 'config_image', get_string('config_image', 'theme_edumy'), null,
                array('subdirs' => 0, 'maxbytes' => $maxbytes, 'areamaxbytes' => 10485760, 'maxfiles' => 1,
                'accepted_types' => array('.png', '.jpg', '.gif') ));

        $slidesrange = range(0, 12);
        $mform->addElement('select', 'config_slidesnumber', get_string('config_items', 'theme_edumy'), $slidesrange);
        $mform->setDefault('config_slidesnumber', $data->slidesnumber);

        for($i = 1; $i <= $data->slidesnumber; $i++) {
            $mform->addElement('header', 'config_header' . $i , 'Slide ' . $i);

            $mform->addElement('text', 'config_slide_title' . $i, get_string('config_title', 'theme_edumy', $i));
            $mform->setDefault('config_slide_title' .$i , 'We Can');
            $mform->setType('config_slide_title' . $i, PARAM_TEXT);

            $mform->addElement('text', 'config_slide_title_2' . $i, get_string('config_title_2', 'theme_edumy', $i));
            $mform->setDefault('config_slide_title_2' .$i , 'Teach You!');
            $mform->setType('config_slide_title_2' . $i, PARAM_TEXT);

            $options = array(
                '0' => 'No',
                '1' => 'Yes',
            );
            $select = $mform->addElement('select', 'config_slide_line_break' . $i, get_string('config_title', 'theme_edumy') . ' ' . strtolower(get_string('config_line_break', 'theme_edumy')), $options);
            $select->setSelected('0');

            $mform->addElement('text', 'config_slide_subtitle' . $i, get_string('config_subtitle', 'theme_edumy', $i));
            $mform->setDefault('config_slide_subtitle' .$i , 'Technology is brining a massive wave of evolution on learning things on different ways.');
            $mform->setType('config_slide_subtitle' . $i, PARAM_TEXT);

            $mform->addElement('text', 'config_slide_btn_text' . $i, get_string('config_button_text', 'theme_edumy', $i));
            $mform->setDefault('config_slide_btn_text' .$i , 'Join In Free');
            $mform->setType('config_slide_btn_text' . $i, PARAM_TEXT);

            $mform->addElement('text', 'config_slide_btn_url' . $i, get_string('config_button_link', 'theme_edumy', $i));
            $mform->setDefault('config_slide_btn_url' .$i , '#');
            $mform->setType('config_slide_btn_url' . $i, PARAM_TEXT);

            $options = array(
                '_self' => 'Self (open in same window)',
                '_blank' => 'Blank (open in new window)',
                '_parent' => 'Parent (open in parent frame)',
                '_top' => 'Top (open in full body of the window)',
            );
            $select = $mform->addElement('select', 'config_slide_btn_target' . $i, get_string('config_button_target', 'theme_edumy'), $options);
            $select->setSelected('_self');

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
        file_prepare_draft_area($draftitemid, $this->block->context->id, 'block_cocoon_slider_4', 'content', 0,
            array('subdirs' => true));
        $entry->attachments = $draftitemid;
        parent::set_data($defaults);
        if ($data = parent::get_data()) {
            file_save_draft_area_files($data->config_image, $this->block->context->id, 'block_cocoon_slider_4', 'content', 0,
                array('subdirs' => true));
        }
        // END CCN Image Processing

        parent::set_data($defaults);
    }
}
