<?php

class block_cocoon_boxes_edit_form extends block_edit_form
{
    protected function specific_definition($mform)
    {
      global $CFG;
      $ccnFontList = include($CFG->dirroot . '/theme/edumy/ccn/font_handler/ccn_font_select.php');
      // include_once($CFG->dirroot . '/course/lib.php');
      // require_once($CFG->dirroot. '/course/renderer.php');
      // $topcategory = core_course_category::top();
      // $topcategorykids = $topcategory->get_children();
      // $searchareas = \core_search\manager::get_search_areas_list(true);
      // $areanames = array();
      // foreach ($topcategorykids as $areaid => $topcategorykids) {
      //     $areanames[$areaid] = $topcategorykids->get_formatted_name();
      //     // print_object($areaid->get_formatted_name());
      //     // print_object($areaid);
      //
      // }


      if (!empty($this->block->config) && is_object($this->block->config)) {
          $data = $this->block->config;
      } else {
          $data = new stdClass();
          $data->items = 0;
      }

        // Section header title according to language file.
        $mform->addElement('header', 'config_header', get_string('blocksettings', 'block'));

        // Title
        $mform->addElement('text', 'config_title', get_string('config_title', 'block_cocoon_boxes'));
        $mform->setDefault('config_title', 'What We Do');
        $mform->setType('config_title', PARAM_RAW);

        // Subtitle
        $mform->addElement('text', 'config_subtitle', get_string('config_subtitle', 'block_cocoon_boxes'));
        $mform->setDefault('config_subtitle', 'Cum doctus civibus efficiantur in imperdiet deterruisset.');
        $mform->setType('config_subtitle', PARAM_RAW);

        // // Button Link
        // $mform->addElement('text', 'config_button_link', get_string('config_arrow_link', 'theme_edumy'));
        // $mform->setDefault('config_button_link', '#our-courses');
        // $mform->setType('config_button_link', PARAM_RAW);

        // // Button Text
        // $mform->addElement('text', 'config_button_text', get_string('config_button_text', 'theme_edumy'));
        // $mform->setDefault('config_button_text', 'View All Courses');
        // $mform->setType('config_button_text', PARAM_RAW);

        // $options = array(
        //     '0' => 'Category description',
        //     '1' => 'Category course count',
        // );
        // $select = $mform->addElement('select', 'config_body', get_string('config_body', 'theme_edumy'), $options);
        // $select->setSelected('0');

        $items_range = range(0, 16);
        $mform->addElement('select', 'config_items', get_string('config_items', 'theme_edumy'), $items_range);
        $mform->setDefault('config_items', $data->items);

        // Style
        // $radioarray=array();
        // $radioarray[] = $mform->createElement('radio', 'config_style', '', 'Hide arrow', 0, $attributes);
        // $radioarray[] = $mform->createElement('radio', 'config_style', '', 'Show arrow', 1, $attributes);
        // $mform->addGroup($radioarray, 'config_style', 'Arrow', array(' '), false);

        for($i = 1; $i <= $data->items; $i++) {
            $mform->addElement('header', 'config_header' . $i , get_string('config_item', 'theme_edumy') . $i);

            $mform->addElement('text', 'config_title' . $i, get_string('config_title', 'theme_edumy', $i));
            $mform->setDefault('config_title' .$i , 'Create Account');
            $mform->setType('config_title' . $i, PARAM_TEXT);

            $mform->addElement('text', 'config_body' . $i, get_string('config_body', 'theme_edumy', $i));
            $mform->setDefault('config_body' .$i , 'Sed cursus turpis vitae tortor donec eaque ipsa quaeab illo.');
            $mform->setType('config_body' . $i, PARAM_TEXT);

            $mform->addElement('text', 'config_color' . $i, get_string('config_color', 'theme_edumy', $i), array('class'=>'ccn_spectrum_class'));
            $mform->setDefault('config_color' .$i , 'rgb(240, 208, 120)');
            $mform->setType('config_color' . $i, PARAM_TEXT);

            // $options = array(
            //     'multiple' => false,
            //     'noselectionstring' => get_string('select_from_dropdown', 'theme_edumy'),
            // );
            // $mform->addElement('autocomplete', 'config_category' . $i, get_string('category'), $areanames, $options);

            $select = $mform->addElement('select', 'config_icon' .$i, get_string('config_icon_class', 'theme_edumy'), $ccnFontList, array('class'=>'ccn_icon_class'));
            $select->setSelected('flaticon-student-3');



            // $options = array('multiple' => false, 'includefrontpage' => false);
            // $mform->addElement('course', 'config_course' . $i, get_string('course'), $options);


            // $filemanageroptions = array('maxbytes'      => $CFG->maxbytes,
            //                             'subdirs'       => 0,
            //                             'maxfiles'      => 1,
            //                             'accepted_types' => array('.jpg', '.png', '.gif'));
            //
            // $f = $mform->addElement('filemanager', 'config_file_slide' . $i, get_string('config_image', 'theme_edumy', $i), null, $filemanageroptions);
        }


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
        file_prepare_draft_area($draftitemid, $this->block->context->id, 'block_cocoon_boxes', 'content', 0,
            array('subdirs' => true));
        $entry->attachments = $draftitemid;
        parent::set_data($defaults);
        if ($data = parent::get_data()) {
            file_save_draft_area_files($data->config_image, $this->block->context->id, 'block_cocoon_boxes', 'content', 0,
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
            $defaults->config_bio['text'] = file_prepare_draft_area($draftid_editor, $this->block->context->id, 'block_cocoon_boxes', 'content', 0, array('subdirs'=>true), $currenttext);
            $defaults->config_bio['itemid'] = $draftid_editor;
            $defaults->config_bio['format'] = $this->block->config->format;
        } else {
            $text = '';
        }


    }
}
