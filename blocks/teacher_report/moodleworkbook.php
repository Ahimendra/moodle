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
 * teacher_report
 *
 * @package   blocks_teacher_report
 * @copyright 2020 Eruditiontec Innovations PVT LTD {contact.erulearn@gmail.com}{http://erulearn.com/}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
require_once($CFG->dirroot.'/blocks/teacher_report/excellib.class.php');
require_once($CFG->dirroot.'/lib/filelib.php');
require_once($CFG->libdir . '/gradelib.php');
class MoodleExcelWorkbook1 extends MoodleExcelWorkbook {
    /** @var PHPExcel */
    protected $objPHPExcel;

    /** @var string */
    protected $filename;

    /** @var string format type */
    protected $type;

    /**
     * Constructs one Moodle Workbook.
     *
     * @param string $filename The name of the file
     * @param string $type file format type used to be 'Excel5 or Excel2007' but now only 'Excel2007'
     */
    public function __construct($filename, $type = 'Excel2007') {
        global $CFG;
        require_once("$CFG->dirroot/blocks/teacher_report/phpexcel/PHPExcel.php");

        $this->objPHPExcel = new PHPExcel();
        $this->objPHPExcel->removeSheetByIndex(0);

        $this->filename = $filename;

        if (strtolower($type) === 'excel5') {
            debugging('Excel5 is no longer supported, using Excel2007 instead');
            $this->type = 'Excel2007';
        } else {
            $this->type = 'Excel2007';
        }
    }
    
    public function close() {
        global $CFG;
        $systemcontext = context_system::instance();
        foreach ($this->objPHPExcel->getAllSheets() as $sheet){
            $sheet->setSelectedCells('A1');
        }
        $this->objPHPExcel->setActiveSheetIndex(0);
        

        $filename = preg_replace('/\.xlsx?$/i', '', $this->filename);

        $filename = $filename.'.xlsx';

        if (core_useragent::is_ie()) {
            $filename = rawurlencode($filename);
        } else {
            $filename = s($filename);
        }
        $filename = str_replace(' ', '-', $filename);

        $objWriter = PHPExcel_IOFactory::createWriter($this->objPHPExcel, $this->type);
        //print_r($objWriter);die;
        if (file_exists($CFG->tempdir.'/'.$filename)){
           unlink($CFG->tempdir.'/'.$filename);
        }
        $objWriter->save($CFG->tempdir.'/'.$filename);
        
    }
}
function get_all_course_modules($course_id, $userid) {
    global $DB;
    $course_mods = get_course_mods($course_id);
    $result = array();
    if($course_mods) {
        foreach($course_mods as $course_mod) { 
            $course_mod->course_module_instance = $DB->get_record($course_mod->modname, array('id' =>$course_mod->instance ));
            $course_mod->completionstatus = $DB->get_record('course_modules_completion', array('coursemoduleid' => $course_mod->id, 'userid' => $userid));
            
            $grades = grade_get_grades($course_id, 'mod', $course_mod->modname, $course_mod->instance, $userid);
            /*echo $course_mod->modname."<br>".$course_mod->id;*/
            
            if(!empty($grades->items)) {//print_r($grades->items);
                $grade_item_grademax = $grades->items[0]->grademax;
                $user_final_grade = $grades->items[0]->grades[$userid]->grade;
                //echo $user_final_grade;
                $course_mod->obtainscore = ($user_final_grade/$grade_item_grademax*100)."%";
            } else {
                $course_mod->obtainscore = "-";
            }
            
            $result[$course_mod->id] = $course_mod;
            
        }
    }
    //print_r($result);
    return $result;
}
function secondsToTime($seconds) {
    $days = floor($seconds / 86400);
    $seconds -= ($days * 86400);

    $hours = floor($seconds / 3600);
    $seconds -= ($hours * 3600);

    $minutes = floor($seconds / 60);
    $seconds -= ($minutes * 60);

    $values = array(
        'day' => $days,
        'hour' => $hours,
        'minute' => $minutes,
        'second' => $seconds
    );
    
    $parts = array();
    
    foreach ($values as $text => $value) {
        if ($value > 0) {
            $parts[] = $value . ' ' . $text . ($value > 1 ? 's' : '');
        }
    }
    if(!empty($parts)){
        return implode(' ', $parts);
    } else {
        return '-';
    }
    
}