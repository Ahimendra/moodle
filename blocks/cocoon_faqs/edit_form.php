<?php

class block_cocoon_faqs_edit_form extends block_edit_form {
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

        // Title
        $mform->addElement('text', 'config_title', get_string('config_title', 'theme_edumy'));
        $mform->setDefault('config_title', 'Payments');
        $mform->setType('config_title', PARAM_RAW);

        $slidesrange = range(0, 100);
        $mform->addElement('select', 'config_slidesnumber', get_string('config_items', 'theme_edumy'), $slidesrange);
        $mform->setDefault('config_slidesnumber', $data->slidesnumber);



        for($i = 1; $i <= $data->slidesnumber; $i++) {
            $mform->addElement('header', 'config_header' . $i , 'FAQ ' . $i);

            $mform->addElement('text', 'config_faq_title' . $i, get_string('config_faq_title', 'block_cocoon_faqs', $i));
            $mform->setDefault('config_faq_title' .$i , 'Why won\'t my payment go through?');
            $mform->setType('config_faq_title' . $i, PARAM_TEXT);

            $mform->addElement('text', 'config_faq_subtitle' . $i, get_string('config_faq_subtitle', 'block_cocoon_faqs', $i));
            $mform->setDefault('config_faq_subtitle' .$i , 'Course Description');
            $mform->setType('config_faq_subtitle' . $i, PARAM_TEXT);

            $options = array(
                '0' => 'Plain text',
                '1' => 'HTML editor',
            );
            $select = $mform->addElement('select', 'config_body_type' .$i, get_string('config_body_type', 'theme_edumy'), $options);
            $select->setSelected('0');


            $mform->addElement('textarea', 'config_faq_body' . $i, get_string('config_body_plain', 'theme_edumy', $i));
            $mform->disabledIf('config_faq_body' .$i, 'config_body_type' .$i, 'eq', 1);
            $mform->setDefault('config_faq_body' .$i , 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.');
            $mform->setType('config_faq_body' . $i, PARAM_TEXT);

            $editoroptions = array('maxfiles' => EDITOR_UNLIMITED_FILES, 'noclean'=>true, 'context'=>$this->block->context, 'subdirs' => 0);
            $mform->addElement('editor', 'config_faq_html' .$i , get_string('config_body_html', 'theme_edumy'), null, $editoroptions);
            $mform->disabledIf('config_faq_html' .$i , 'config_body_type' .$i, 'neq', 1);
            // $mform->setDefault('config_faq_html' .$i , 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.');
            $mform->setType('config_faq_html' . $i, PARAM_RAW); // XSS is prevented when printing the block contents and serving files


        }

        include($CFG->dirroot . '/theme/edumy/ccn/block_handler/edit.php');

    }

    function set_data($defaults) {
        if (!empty($this->block->config) && is_object($this->block->config)) {

            for($i = 1; $i <= $this->block->config->slidesnumber; $i++) {
                $field = 'file_slide' . $i;
                $conffield = 'config_file_slide' . $i;
                $draftitemid = file_get_submitted_draft_itemid($conffield);
                file_prepare_draft_area($draftitemid, $this->block->context->id, 'block_cocoon_faqs', 'slides', $i, array('subdirs'=>false));
                $defaults->$conffield['itemid'] = $draftitemid;
                $this->block->config->$field = $draftitemid;




                // if (!empty($this->block->config) && is_object($this->block->config)) {
                    $text = $this->block->config->faq_html . $i;
                    $conffield = 'config_faq_html' . $i;
                    $draftid_editor = file_get_submitted_draft_itemid($conffield);
                    if (empty($text)) {
                        $currenttext = '';
                    } else {
                        $currenttext = $text;
                    }
                    $defaults->$conffield['text'] = file_prepare_draft_area($draftid_editor, $this->block->context->id, 'block_cocoon_faqs', 'content', $i, array('subdirs'=>false), $currenttext);
                    $defaults->$conffield['itemid'] = $draftid_editor;
                    $defaults->$conffield['format'] = $this->block->config->format . $i ;
                // } else {
                //     $text = '';
                // }

            }
        }

        parent::set_data($defaults);
    }
}
