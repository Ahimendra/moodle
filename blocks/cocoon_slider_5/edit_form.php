<?php

class block_cocoon_slider_5_edit_form extends block_edit_form {
    protected function specific_definition($mform) {
        global $CFG;
        // include_once($CFG->dirroot . '/course/lib.php');
        // // require_once($CFG->libdir . '/coursecatlib.php');
        // require_once($CFG->dirroot. '/course/renderer.php');
        // $topcategory = core_course_category::top();
        // $topcategorykids = $topcategory->get_children();

        if (!empty($this->block->config) && is_object($this->block->config)) {
            $data = $this->block->config;
        } else {
            $data = new stdClass();
            $data->slidesnumber = 0;
        }


        // Fields for editing HTML block title and contents.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        // $options = array(
        //     '0' => '2 lines (default)',
        //     '1' => '1 line',
        //     '2' => 'Hidden',
        // );
        // $select = $mform->addElement('select', 'config_arrow_style', get_string('config_arrow_style', 'theme_edumy'), $options);
        // $select->setSelected('0');
        //
        // $mform->addElement('text', 'config_prev_1', get_string('config_prev_1', 'theme_edumy'));
        // $mform->hideIf('config_prev_1', 'config_arrow_style', 'neq', 0);
        // $mform->setDefault('config_prev_1', 'PR');
        // $mform->setType('config_prev_1', PARAM_TEXT);
        //
        // $mform->addElement('text', 'config_prev_2', get_string('config_prev_2', 'theme_edumy'));
        // $mform->hideIf('config_prev_2', 'config_arrow_style', 'neq', 0);
        // $mform->setDefault('config_prev_2', 'EV');
        // $mform->setType('config_prev_2', PARAM_TEXT);
        //
        // $mform->addElement('text', 'config_prev', get_string('config_prev', 'theme_edumy'));
        // $mform->hideIf('config_prev', 'config_arrow_style', 'neq', 1);
        // $mform->setDefault('config_prev', 'PREV');
        // $mform->setType('config_prev', PARAM_TEXT);
        //
        // $mform->addElement('text', 'config_next_1', get_string('config_next_1', 'theme_edumy'));
        // $mform->hideIf('config_next_1', 'config_arrow_style', 'neq', 0);
        // $mform->setDefault('config_next_1', 'NE');
        // $mform->setType('config_next_1', PARAM_TEXT);
        //
        // $mform->addElement('text', 'config_next_2', get_string('config_next_2', 'theme_edumy'));
        // $mform->hideIf('config_next_2', 'config_arrow_style', 'neq', 0);
        // $mform->setDefault('config_next_2', 'XT');
        // $mform->setType('config_next_2', PARAM_TEXT);
        //
        // $mform->addElement('text', 'config_next', get_string('config_next', 'theme_edumy'));
        // $mform->hideIf('config_next', 'config_arrow_style', 'neq', 1);
        // $mform->setDefault('config_next', 'NEXT');
        // $mform->setType('config_next', PARAM_TEXT);

        $slidesrange = range(0, 12);
        $mform->addElement('select', 'config_slidesnumber', get_string('config_items', 'theme_edumy'), $slidesrange);
        $mform->setDefault('config_slidesnumber', $data->slidesnumber);
        $mform->addRule('config_slidesnumber', get_string('required'), 'required', null, 'client', false, false);

        // $searchareas = \core_search\manager::get_search_areas_list(true);
        // $areanames = array();
        // foreach ($topcategorykids as $areaid => $topcategorykids) {
        //     $areanames[$areaid] = $topcategorykids->get_formatted_name();
        //     // print_object($areaid->get_formatted_name());
        //     // print_object($areaid);
        //
        // }
        // var_dump($areanames);
        // $options = array(
        //     'multiple' => true,
        //
        //
        //     'noselectionstring' => get_string('allareas', 'search'),
        // );
        // $mform->addElement('autocomplete', 'areaids', get_string('categories'), $areanames, $options);



        for($i = 1; $i <= $data->slidesnumber; $i++) {
            $mform->addElement('header', 'config_header' . $i , 'Slide ' . $i);

            $mform->addElement('text', 'config_slide_title' . $i, get_string('config_title', 'theme_edumy', $i));
            $mform->setDefault('config_slide_title' .$i , 'Learn From');
            $mform->setType('config_slide_title' . $i, PARAM_TEXT);

            $mform->addElement('text', 'config_slide_title_2' . $i, get_string('config_title_2', 'theme_edumy', $i));
            $mform->setDefault('config_slide_title_2' .$i , 'Anywhere');
            $mform->setType('config_slide_title_2' . $i, PARAM_TEXT);

            $mform->addElement('text', 'config_slide_subtitle' . $i, get_string('config_subtitle', 'theme_edumy', $i));
            $mform->setDefault('config_slide_subtitle' .$i , 'Technology is bringing a massive wave of evolution for learning things in different ways.');
            $mform->setType('config_slide_subtitle' . $i, PARAM_TEXT);

            $mform->addElement('text', 'config_slide_btn_text' . $i, get_string('config_button_text', 'theme_edumy', $i));
            $mform->setDefault('config_slide_btn_text' .$i , 'Ready to Get Started?');
            $mform->setType('config_slide_btn_text' . $i, PARAM_TEXT);

            $mform->addElement('text', 'config_slide_btn_url' . $i, get_string('config_button_link', 'theme_edumy', $i));
            $mform->setDefault('config_slide_btn_url' .$i , '#');
            $mform->setType('config_slide_btn_url' . $i, PARAM_TEXT);

            $options = array('multiple' => false, 'includefrontpage' => false);
            $mform->addElement('course', 'config_course' . $i, get_string('course'), $options);
            $mform->addRule('config_course' . $i, get_string('required'), 'required', null, 'client', false, false);

            // $options = array('multiple' => false, 'includefrontpage' => false);
            // $mform->addElement('course', 'config_course' . $i, get_string('course'), $options);


            $filemanageroptions = array('maxbytes'      => $CFG->maxbytes,
                                        'subdirs'       => 0,
                                        'maxfiles'      => 1,
                                        'accepted_types' => array('.jpg', '.png', '.gif'));

            $f = $mform->addElement('filemanager', 'config_file_slide' . $i, get_string('config_image', 'theme_edumy', $i), null, $filemanageroptions);
            $mform->addRule('config_file_slide' . $i, get_string('required'), 'required', null, 'client', false, false);
        }

        include($CFG->dirroot . '/theme/edumy/ccn/block_handler/edit.php');

    }

    function set_data($defaults) {
        if (!empty($this->block->config) && is_object($this->block->config)) {

            for($i = 1; $i <= $this->block->config->slidesnumber; $i++) {
                $field = 'file_slide' . $i;
                $conffield = 'config_file_slide' . $i;
                $draftitemid = file_get_submitted_draft_itemid($conffield);
                file_prepare_draft_area($draftitemid, $this->block->context->id, 'block_cocoon_slider_5', 'slides', $i, array('subdirs'=>false));
                $defaults->$conffield['itemid'] = $draftitemid;
                $this->block->config->$field = $draftitemid;
            }
        }

        parent::set_data($defaults);
    }
}
