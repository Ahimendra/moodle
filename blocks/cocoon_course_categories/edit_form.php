<?php

class block_cocoon_course_categories_edit_form extends block_edit_form
{
    protected function specific_definition($mform)
    {
      global $CFG;

      if (!empty($this->block->config) && is_object($this->block->config)) {
          $data = $this->block->config;
      } else {
          $data = new stdClass();
          $data->items = 0;
      }

        // Section header title according to language file.
        $mform->addElement('header', 'config_header', get_string('blocksettings', 'block'));

        // Title
        $mform->addElement('text', 'config_title', get_string('config_title', 'block_cocoon_course_categories'));
        $mform->setDefault('config_title', 'Via School Categories Courses');
        $mform->setType('config_title', PARAM_RAW);

        // Subtitle
        $mform->addElement('text', 'config_subtitle', get_string('config_subtitle', 'block_cocoon_course_categories'));
        $mform->setDefault('config_subtitle', 'Cum doctus civibus efficiantur in imperdiet deterruisset.');
        $mform->setType('config_subtitle', PARAM_RAW);

        // // Button Link
        // $mform->addElement('text', 'config_button_link', get_string('config_arrow_link', 'theme_edumy'));
        // $mform->setDefault('config_button_link', '#our-courses');
        // $mform->setType('config_button_link', PARAM_RAW);

        // Button Text
        $mform->addElement('text', 'config_button_text', get_string('config_button_text', 'theme_edumy'));
        $mform->setDefault('config_button_text', 'View All Courses');
        $mform->setType('config_button_text', PARAM_RAW);

        $options = array(
            '0' => 'Category description',
            '1' => 'Category course count',
            '2' => 'None',
        );
        $select = $mform->addElement('select', 'config_body', get_string('config_body', 'theme_edumy'), $options);
        $select->setSelected('0');

        $items_range = range(0, 16);
        $mform->addElement('select', 'config_items', get_string('config_items', 'theme_edumy'), $items_range);
        $mform->setDefault('config_items', $data->items);

        // Style
        $radioarray=array();
        $radioarray[] = $mform->createElement('radio', 'config_style', '', 'Hide arrow', 0, $attributes);
        $radioarray[] = $mform->createElement('radio', 'config_style', '', 'Show arrow', 1, $attributes);
        $mform->addGroup($radioarray, 'config_style', 'Arrow', array(' '), false);

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
        file_prepare_draft_area($draftitemid, $this->block->context->id, 'block_cocoon_course_categories', 'content', 0,
            array('subdirs' => true));
        $entry->attachments = $draftitemid;
        parent::set_data($defaults);
        if ($data = parent::get_data()) {
            file_save_draft_area_files($data->config_image, $this->block->context->id, 'block_cocoon_course_categories', 'content', 0,
                array('subdirs' => true));
        }
        // END CCN Image Processing



        if (!empty($this->block->config) && is_object($this->block->config)) {
            $text = $this->block->config->bio;
            $draftid_editor = file_get_submitted_draft_itemid('config_bio');
            if (empty($text)) {
                $currenttext = '';
            } else {
                $currenttext = $text;
            }
            $defaults->config_bio['text'] = file_prepare_draft_area($draftid_editor, $this->block->context->id, 'block_cocoon_course_categories', 'content', 0, array('subdirs'=>true), $currenttext);
            $defaults->config_bio['itemid'] = $draftid_editor;
            $defaults->config_bio['format'] = $this->block->config->format;
        } else {
            $text = '';
        }


    }
}
