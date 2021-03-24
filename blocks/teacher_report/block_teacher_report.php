<?php
/**Copyright (C) 2020 onwards Eruditiontec Innivations PVT LTD
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
/**
 * Teacher Report 
 *
 * @package   blocks_teacher_report
 * @copyright 2020 Eruditiontec Innovations PVT LTD {contact.erulearn@gmail.com}{http://erulearn.com/}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->libdir . '/grade/grade_item.php');
require_once($CFG->libdir . '/grade/grade_grade.php');
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->dirroot . '/grade/querylib.php');
class block_teacher_report extends block_base {
    public function init() {  
        $this->title = get_string('pluginname', 'block_teacher_report');
    }
    public function specialization() {
        if (!empty($this->config->text)) {
            $this->title = $this->config->text;
        } else {
            $this->title = get_string('pluginname', 'block_teacher_report');
        }
    }
    public function get_required_javascript() {
        GLOBAL $PAGE, $CFG;
        parent::get_required_javascript();
        $this->page->requires->jquery();
        $this->page->requires->jquery_plugin('engine', 'block_teacher_report');
    }
    public function applicable_formats() {
        return array('my' => true);
    }
    public function get_content() {
    	GLOBAL $DB, $USER, $CFG, $PAGE, $OUTPUT;
    	$this->content = new stdClass();
        
    	$this->content->footer = '';
        
        $allcourses = get_courses();
        $mycourses = enrol_get_my_courses('*', 'visible DESC, sortorder ASC');

        $html = '<input type="hidden" id="url" name="url" value="'.$CFG->wwwroot.'/blocks/teacher_report/engine.php">';
        $html .= '<label for="course">Choose a Course:</label>

        <select name="course" id="mycourse" class="custom-select singleselect">';
        $html .= '<option value="0">'.get_string('select', 'block_teacher_report').'</option>';
        $isstud = false;
        if (is_siteadmin()) {
            foreach ($allcourses as $key => $allcourse) {
                if($allcourse->format != 'site') {
                    $html .= '<option value="'.$allcourse->id.'">'.$allcourse->fullname.'</option>';
                }
                
            }
        } else { 
            foreach ($mycourses as $key => $mycourse) {
                $context = context_course::instance($mycourse->id); 
                if(user_has_role_assignment($USER->id, 3, $context->id)) {
                    //
                    $html .= '<option value="'.$mycourse->id.'">'.$mycourse->fullname.'</option>';
                } else {
                    $isstud = true;
                }
            }
        }
        
        
        
          
        $html .= '</select>';

        
        
        $html .= '<div class="actionholder"><input type="submit" class="btn
                        btn-primary
                    " name="submitbutton" id="getreport" value="Submit" disabled>
                    <!--<input type="submit" class="btn
                        btn-primary
                    " name="submitbutton" id="notifyme" value="Notify" disabled attr-url = "'.$CFG->wwwroot.'/blocks/teacher_report/notifyajax.php">--></div>';
        $html .= '<div class="table-responsive reportholder"></div>
        <style>
        .block_teacher_report #getreport, .block_teacher_report #notifyme {
            display: inline-block;
            background: #5EBBB8;
            color: #000;
            border-color: #5EBBB8;
        }
        .block_teacher_report .groupset, .block_teacher_report .userset{
            padding-left: 5px;
            display: inline-block;
        }
        .block_teacher_report .actionholder {
            margin-top: 5px;
        }
        .block_teacher_report tr:hover {
            background: #d9efee;
        }
        .block_teacher_report table, .block_teacher_report .innertable {
            background: #f7fcfc;
        }
        .block_teacher_report .custom-select {
            border: 1px solid #5EBBB8;
        }
        .block_teacher_report td {
            vertical-align: middle;
        }
        </style>
        ';

        //

        
        if($isstud) { 
            return false;
        } else {
            $this->content->text = $html;
            return $this->content;
        }
    }
    public function instance_allow_multiple() {
	  	return false;
	}
	/**
     * Allow the block to have a configuration page.
     *
     * @return boolean
     */
    public function has_config() {
        return true;
    }
}