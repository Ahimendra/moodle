<?php

class block_cocoon_course_intro_edit_form extends block_edit_form
{
    protected function specific_definition($mform)
    {
      global $CFG;

        // Section header title according to language file.
        $mform->addElement('header', 'config_header', get_string('blocksettings', 'block'));

        $mform->addElement('text', 'config_teacher', get_string('config_teacher', 'block_cocoon_course_intro'));
        $mform->setDefault('config_teacher', 'Ali Tufan');
        $mform->setType('config_teacher', PARAM_RAW);

        // Image
        $mform->addElement('filemanager', 'config_image', get_string('config_image', 'block_cocoon_course_intro'), null,
                array('subdirs' => 0, 'maxbytes' => $maxbytes, 'areamaxbytes' => 10485760, 'maxfiles' => 1,
                'accepted_types' => array('.png', '.jpg', '.gif') ));

        $mform->addElement('text', 'config_accent', get_string('config_accent', 'block_cocoon_course_intro'));
        $mform->setDefault('config_accent', 'Best Seller');
        $mform->setType('config_accent', PARAM_RAW);

        $mform->addElement('text', 'config_video', get_string('config_video', 'block_cocoon_course_intro'));
        $mform->setDefault('config_video', '//www.youtube.com/embed/57LQI8DKwec');
        $mform->setType('config_video', PARAM_RAW);

        // $radioarray=array();
        // $radioarray[] = $mform->createElement('radio', 'config_style', '', 'In content', 0, $attributes);
        // $radioarray[] = $mform->createElement('radio', 'config_style', '', 'In breadcrumb', 1, $attributes);
        // $radioarray[] = $mform->createElement('radio', 'config_style', '', 'In breadcrumb fullwidth', 2, $attributes);
        // $mform->addGroup($radioarray, 'config_style', 'Block location', array(' '), false);

        // $radioarray=array();
        // $radioarray[] = $mform->createElement('radio', 'config_rating', '', 'Visible', 0, $attributes);
        // $radioarray[] = $mform->createElement('radio', 'config_rating', '', 'Hidden', 1, $attributes);
        // $mform->addGroup($radioarray, 'config_rating', 'Rating', array(' '), false);

        // $options = array(
        //     '0' => 'Hidden',
        //     '1' => 'Visible',
        // );
        // $select = $mform->addElement('select', 'config_updated', get_string('config_updated', 'theme_edumy'), $options);
        // $select->setSelected('1');

        $options = array(
            '0' => 'Hidden',
            '1' => 'Visible',
        );
        $select = $mform->addElement('select', 'config_show_teacher', get_string('config_teacher', 'theme_edumy'), $options);
        $select->setSelected('1');

        include($CFG->dirroot . '/theme/edumy/ccn/block_handler/edit.php');
    }

    function set_data($defaults)
    {

        // Begin CCN Image Processing
        if (empty($entry->id)) {
            $entry = new stdClass;
            $entry->id = null;
        }
        $draftitemid = file_get_submitted_draft_itemid('config_image');
        file_prepare_draft_area($draftitemid, $this->block->context->id, 'block_cocoon_course_intro', 'content', 0,
            array('subdirs' => true));
        $entry->attachments = $draftitemid;
        parent::set_data($defaults);
        if ($data = parent::get_data()) {
            file_save_draft_area_files($data->config_image, $this->block->context->id, 'block_cocoon_course_intro', 'content', 0,
                array('subdirs' => true));
        }
        // END CCN Image Processing


    }
}
