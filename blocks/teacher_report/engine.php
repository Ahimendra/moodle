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
require_once('../../config.php');
require_once($CFG->libdir . '/grouplib.php');
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->libdir . '/enrollib.php');
require_once($CFG->libdir.'/completionlib.php');
require_once($CFG->dirroot.'/blocks/teacher_report/excellib.class.php');
require_once($CFG->dirroot.'/lib/filelib.php');
require_once($CFG->dirroot . '/lib/messagelib.php');
GLOBAL $DB, $CFG;
//print_r($_POST);
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
        if (file_exists($CFG->dirroot.'/blocks/teacher_report/repo/'.$filename)){
           unlink($CFG->dirroot.'/blocks/teacher_report/repo/'.$filename);
        }
        $objWriter->save($CFG->dirroot.'/blocks/teacher_report/repo/'.$filename);
        
    }
}
if (isset($_POST['cid'])){
	$courseobj = $DB->get_record('course', array('id' => $_POST['cid']));
	$groups = groups_get_all_groups($courseobj->id); 
	//
	if(isset($groups) && !empty($groups)){//print_r($groups);
		echo "<div class='groupset'><label for='group'>Choose a Group:</label>
		<select name='group' id='group' class='custom-select singleselect'>
		<option value=''>--Select--</option>";
		foreach ($groups as $key => $group) {
			echo "<option value='".$group->id."'>".$group->name."</option>";
		}
		echo "</select></div>";
	} else {
		$coursecontext = context_course::instance($courseobj->id);
		$enrolledusers = get_enrolled_users($coursecontext);
		//print_r($enrolledusers);
		if($enrolledusers){
		echo '<div class="userset"><label for="users">Choose a User(s):</label><select name="users[]" id="users" multiple class="custom-select">';
		echo "<option value='0'>All</option>";
		foreach ($enrolledusers as $key => $enrolleduser) {
			if(user_has_role_assignment($enrolleduser->id, 5, $coursecontext->id)) {
				echo "<option value='".$enrolleduser->id."'>".$enrolleduser->firstname ." " . $enrolleduser->lastname . "</option>";
			}
		}
		echo '</select></div>';
		} else {
			echo '<div class="userset">No Users Yet!</div>';
		}
	}
}
if(isset($_POST['gid'])) {
	//echo $_POST['gid'];
	$groupmembers = groups_get_members($_POST['gid']);
	
	if (isset($groupmembers) && !empty($groupmembers)) { //echo 111; print_r($groupmembers);
		echo '<div class="userset"><label for="users">Choose a User(s):</label><select name="users[]" id="users" multiple class="custom-select">';
		echo "<option value='0'>All</option>";
		foreach ($groupmembers as $key => $groupmember) {
			echo "<option value='".$groupmember->id."'>".$groupmember->firstname ." " . $groupmember->lastname . "</option>";
		}
		echo '</select></div>';
	}
}
if (isset($_POST['uid']) && isset($_POST['selectgid'])) {
	if ($_POST['uid'] == 0) {//all students
		$groupmembers = groups_get_members($_POST['selectgid']);
		//print_r($groupmembers);
	} else {
		$groupmembers = array();
		foreach ($_POST['uid'] as $key => $userid) {
			$userobj = $DB->get_record('user', array('id' => $userid));
			$groupmembers[$userid] = $userobj;
			//print_r($groupmembers);
		}
	}
	if ($groupmembers) {
		$courseobj = $DB->get_record('course', array('id' => $_POST['courseid']));
		$downloadfilename = clean_filename($courseobj->shortname.".xlsx");
		// Creating a workbook
		$workbook = new MoodleExcelWorkbook1("-");
		// Sending HTTP headers
		$workbook->send($downloadfilename);
		// Adding the worksheet
		;
		$format = $workbook->add_format();
		$format->set_align('vcenter');
		$info = new completion_info($courseobj);
		$html = '<h1>Report : '.$courseobj->fullname.'</h1>

		<table class="table">
		  	<tr style="background: #5EBBB8; color: #fff; text-align: center">
			    <th>Student Name</th>
			    <th>Activity</th>
			    <th>Course Progress / Time</th>
		  	</tr>
			<tr>';
		foreach ($groupmembers as $key => $groupmember) {
			$myxls = $workbook->add_worksheet($groupmember->firstname);//for teacher

			$format = $workbook->add_format();
			$format->set_align('vcenter');
			$myxls->write_string(0, 0, "Student Name", $format);
			$myxls->merge_cells(0, 0, 1, 0);
			$myxls->write_string(0, 1, "Activity", $format);
			$myxls->merge_cells(0, 1, 0, 4);
			$myxls->write_string(0, 5, "Course Progress / Time", $format);
			//$myxls->merge_cells(0, 5, 1, 0);
			/*for student*/
			$studentfilename = clean_filename($courseobj->shortname."_".$groupmember->firstname."_".$groupmember->lastname."_".$groupmember->id.".xlsx");
			// Creating a workbook
			$studentworkbook = new MoodleExcelWorkbook1("-");
			// Sending HTTP headers
			$studentworkbook->send($studentfilename);
			// Adding the worksheet
			$studformat = $studentworkbook->add_format();
			$studformat->set_align('vcenter');
			$studxls = $studentworkbook->add_worksheet($courseobj->fullname);
			$studxls->write_string(0, 0, "Student Name", $studformat);
			$studxls->merge_cells(0, 0, 1, 0);
			$studxls->write_string(0, 1, "Activity", $studformat);
			$studxls->merge_cells(0, 1, 0, 4);
			$studxls->write_string(0, 5, "Course Progress / Time", $studformat);
			/*for student*/
			
			$activities = get_all_course_modules($courseobj->id, $groupmember->id);
			$html .= '<tr><td>'.$groupmember->firstname." ".$groupmember->lastname.'</td>
			  <td>
			    <table class="innertable">
			      <tr style="background: #5EBBB8; color: #fff; text-align: center">
			      	<th>Activity name</th>
			        <th>Status</th>
			        <th>Score</th>
			        <th>Time</th>
			      </tr>
			      <tr>';
			$myxls->write_string(1, 1, "Activity name", $format);
			$myxls->write_string(1, 2, "Status", $format);
			$myxls->write_string(1, 3, "Score", $format);
			$myxls->write_string(1, 4, "Time", $format);
			$myxls->write_string(2, 0, $groupmember->firstname." ".$groupmember->lastname, $format);
			$studxls->write_string(1, 1, "Activity name", $format);
			$studxls->write_string(1, 2, "Status", $format);
			$studxls->write_string(1, 3, "Score", $format);
			$studxls->write_string(1, 4, "Time", $format);
			$studxls->write_string(2, 0, $groupmember->firstname." ".$groupmember->lastname, $format);
			$xlxcount = 2;
			$ctime = 0;
			foreach ($activities as $key => $activity) {
				$ctime += get_config('block_teacher_report', "activity_timespent_".$groupmember->id."_".$activity->id);
				$html .= '<tr>
			        <td>'.$activity->course_module_instance->name.'</td>';
			        $myxls->write_string($xlxcount, 1, $activity->course_module_instance->name, $format);
			        $studxls->write_string($xlxcount, 1, $activity->course_module_instance->name, $format);
			        if(isset($activity->completionstatus->completionstate)) {
			        	$html .= '<td style="color: #5EBBB8">Complete</td>';
			        	$myxls->write_string($xlxcount, 2, "Complete", $format);
			        	$studxls->write_string($xlxcount, 2, "Complete", $format);
			        } else {
						if (strtotime("-1 week") > $groupmember->lastaccess) {
			        		$html .= '<td style="color: red">Incomplete</td>';
			        	} else {
			        		$html .= '<td style="color: red">Incomplete</td>';
			        	}
						$myxls->write_string($xlxcount, 2, "Incomplete", $format);
						$studxls->write_string($xlxcount, 2, "Incomplete", $format);
			        }
			    $myxls->write_string($xlxcount, 3, $activity->obtainscore, $format);
			    $studxls->write_string($xlxcount, 3, $activity->obtainscore, $format);
			    $html .= '<td>'.$activity->obtainscore.'</td>
			        <td>'.secondsToTime(get_config('block_teacher_report', "activity_timespent_".$groupmember->id."_".$activity->id)).'</td>
			    </tr>';
			    $myxls->write_string($xlxcount, 4, secondsToTime(get_config('block_teacher_report', "activity_timespent_".$groupmember->id."_".$activity->id)), $format);
			    $studxls->write_string($xlxcount, 4, secondsToTime(get_config('block_teacher_report', "activity_timespent_".$groupmember->id."_".$activity->id)), $format);
			    $xlxcount = $xlxcount + 1;
			}
			$xlxcount--;
			$myxls->merge_cells(2, 0, $xlxcount, 0);
			$studxls->merge_cells(2, 0, $xlxcount, 0);
			$coursecomplete = $info->is_course_complete($groupmember->id);
			if($coursecomplete){
				$coursecompletestatus = 'Complete';
			} else {
				$coursecompletestatus = 'Incomplete';
			}
			$myxls->write_string(2, 5, $coursecompletestatus.' / '.secondsToTime($ctime), $format);
			$studxls->write_string(2, 5, $coursecompletestatus.' / '.secondsToTime($ctime), $format);
			//$myxls->merge_cells(2, 5, 1, 0);
			$html .= '</td>
    		</table>
    		<td>'.$coursecompletestatus.' / '.secondsToTime($ctime).'</td></tr>';
    		$studentworkbook->close();
    		$coursecontext = context_course::instance($courseobj->id);
			$PAGE->set_context($coursecontext);
			$userobj = $DB->get_record('user', array('id' => $groupmember->id));
			$message = new \core\message\message();
			$message->component = 'block_teacher_report';
			$message->name = 'notify';

			$message->userfrom = \core_user::get_support_user();
			$message->userto = $userobj;

			$message->subject = "Your Course " .$courseobj->fullname. " Notification";

			// Same than the subject.
			$message->contexturlname = $message->subject;
			$message->courseid = $courseobj->id;

			$message->fullmessage = "Your progress report downlaod from here : <a href=".$CFG->wwwroot."/blocks/teacher_report/repo/".$studentfilename." download>Progress Report</a>";
			$message->fullmessageformat = FORMAT_PLAIN;
			$message->fullmessagehtml = "Your progress report downlaod from here : <a href=".$CFG->wwwroot."/blocks/teacher_report/repo/".$studentfilename." download>Progress Report</a>";
			$message->smallmessage = "Your progress report downlaod from here : <a href=".$CFG->wwwroot."/blocks/teacher_report/repo/".$studentfilename." download>Progress Report</a>";
			$message->contexturl = $CFG->wwwroot;

			message_send($message);
		}
		$workbook->close();
		
		$html .= '</tr></table>';
	}
	echo $html;
} else if (isset($_POST['uid']) && isset($_POST['courseid'])) { 
	$coursecontext = context_course::instance($_POST['courseid']);
	if ($_POST['uid'] == 0) {//all students
		$enrolledusers = get_enrolled_users($coursecontext);
		//print_r($groupmembers);
	} else {
		$enrolledusers = array();
		foreach ($_POST['uid'] as $key => $userid) {
			$userobj = $DB->get_record('user', array('id' => $userid));
			$enrolledusers[$userid] = $userobj;
			//print_r($groupmembers);
		}
	}
	if ($enrolledusers) {
		$courseobj = $DB->get_record('course', array('id' => $_POST['courseid']));
		$info = new completion_info($courseobj);
		$downloadfilename = clean_filename($courseobj->shortname.".xlsx");
		// Creating a workbook
		$workbook = new MoodleExcelWorkbook1("-");
		// Sending HTTP headers
		$workbook->send($downloadfilename);
		// Adding the worksheet
		;
		$format = $workbook->add_format();
		$format->set_align('vcenter');
		$html = '<h1>Report : '.$courseobj->fullname.'</h1>

		<table class="table">
		  	<tr style="background: #5EBBB8; color: #fff; text-align: center">
			    <th>Student Name</th>
			    <th>Activity</th>
			    <th>Course Progress / Time</th>
		  	</tr>
			<tr>';
		foreach ($enrolledusers as $key => $enrolleduser) {
			$myxls = $workbook->add_worksheet($enrolleduser->firstname." ".$enrolleduser->lastname);//for teacher

			$format = $workbook->add_format();
			$format->set_align('vcenter');
			$myxls->write_string(0, 0, "Student Name", $format);
			$myxls->merge_cells(0, 0, 1, 0);
			$myxls->write_string(0, 1, "Activity", $format);
			$myxls->merge_cells(0, 1, 0, 4);
			$myxls->write_string(0, 5, "Course Progress / Time", $format);
			//$myxls->merge_cells(0, 5, 1, 0);
			/*for student*/
			$studentfilename = clean_filename($courseobj->shortname."_".$enrolleduser->firstname."_".$enrolleduser->lastname."_".$enrolleduser->id.".xlsx");
			// Creating a workbook
			$studentworkbook = new MoodleExcelWorkbook1("-");
			// Sending HTTP headers
			$studentworkbook->send($studentfilename);
			// Adding the worksheet
			$studformat = $studentworkbook->add_format();
			$studformat->set_align('vcenter');
			$studxls = $studentworkbook->add_worksheet($courseobj->fullname);
			$studxls->write_string(0, 0, "Student Name", $studformat);
			$studxls->merge_cells(0, 0, 1, 0);
			$studxls->write_string(0, 1, "Activity", $studformat);
			$studxls->merge_cells(0, 1, 0, 4);
			$studxls->write_string(0, 5, "Course Progress / Time", $studformat);
			/*for student*/
			$activities = get_all_course_modules($courseobj->id, $enrolleduser->id);
			$html .= '<tr><td>'.$enrolleduser->firstname.'</td>
			  <td>
			    <table class="innertable">
			      <tr style="background: #5EBBB8; color: #fff; text-align: center">
			      	<th>Activity name</th>
			        <th>Status</th>
			        <th>Score</th>
			        <th>Time</th>
			      </tr>
			      <tr>';
			$myxls->write_string(1, 1, "Activity name", $format);
			$myxls->write_string(1, 2, "Status", $format);
			$myxls->write_string(1, 3, "Score", $format);
			$myxls->write_string(1, 4, "Time", $format);
			$myxls->write_string(2, 0, $enrolleduser->firstname." ".$enrolleduser->lastname, $format);
			$studxls->write_string(1, 1, "Activity name", $format);
			$studxls->write_string(1, 2, "Status", $format);
			$studxls->write_string(1, 3, "Score", $format);
			$studxls->write_string(1, 4, "Time", $format);
			$studxls->write_string(2, 0, $enrolleduser->firstname." ".$enrolleduser->lastname, $format);
			$ctime = 0;
			$xlxcount = 2;
			foreach ($activities as $key => $activity) {
				$ctime += get_config('block_teacher_report', "activity_timespent_".$enrolleduser->id."_".$activity->id);
				$html .= '<tr>
			        <td>'.$activity->course_module_instance->name.'</td>';
			        $myxls->write_string($xlxcount, 1, $activity->course_module_instance->name, $format);
			        $studxls->write_string($xlxcount, 1, $activity->course_module_instance->name, $format);
			        if(isset($activity->completionstatus->completionstate)) {
			        	$html .= '<td style="color: #5EBBB8">Complete</td>';
			        	$myxls->write_string($xlxcount, 2, "Complete", $format);
			        	$studxls->write_string($xlxcount, 2, "Complete", $format);
			        } else {
			        	if (strtotime("-1 week") > $enrolleduser->lastaccess) {
			        		$html .= '<td style="color: red">Incomplete</td>';
			        	} else {
			        		$html .= '<td style="color: red">Incomplete</td>';
			        	}
						$myxls->write_string($xlxcount, 2, "Incomplete", $format);
						$studxls->write_string($xlxcount, 2, "Incomplete", $format);
			        }
			        
			    $html .= '<td>'.$activity->obtainscore.'</td>
			        <td>'.secondsToTime(get_config('block_teacher_report', "activity_timespent_".$enrolleduser->id."_".$activity->id)).'</td>
			    </tr>';
			}
			$xlxcount--;
			$coursecomplete = $info->is_course_complete($enrolleduser->id);
			if($coursecomplete){
				$coursecompletestatus = 'Complete';
			} else {
				$coursecompletestatus = 'Incomplete';
			}
			$myxls->write_string(2, 5, $coursecompletestatus.' / '.secondsToTime($ctime), $format);
			$studxls->write_string(2, 5, $coursecompletestatus.' / '.secondsToTime($ctime), $format);
			$html .= '</td>
    		</table>
    		<td>'.$coursecompletestatus.' / '.secondsToTime($ctime).'</td></tr>';
    		$studentworkbook->close();
    		$coursecontext = context_course::instance($courseobj->id);
			$PAGE->set_context($coursecontext);
			$userobj = $DB->get_record('user', array('id' => $enrolleduser->id));
			$message = new \core\message\message();
			$message->component = 'block_teacher_report';
			$message->name = 'notify';

			$message->userfrom = \core_user::get_support_user();
			$message->userto = $userobj;

			$message->subject = "Your Course " .$courseobj->fullname. " Notification";

			// Same than the subject.
			$message->contexturlname = $message->subject;
			$message->courseid = $courseobj->id;

			$message->fullmessage = "Your progress report downlaod from here : <a href=".$CFG->wwwroot."/blocks/teacher_report/repo/".$studentfilename." download>Progress Report</a>";
			$message->fullmessageformat = FORMAT_PLAIN;
			$message->fullmessagehtml = "Your progress report downlaod from here : <a href=".$CFG->wwwroot."/blocks/teacher_report/repo/".$studentfilename." download>Progress Report</a>";
			$message->smallmessage = "Your progress report downlaod from here : <a href=".$CFG->wwwroot."/blocks/teacher_report/repo/".$studentfilename." download>Progress Report</a>";
			$message->contexturl = $CFG->wwwroot;

			message_send($message);
		}
		$workbook->close();
		$html .= '</tr></table>';
	}
	echo $html;
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

if (isset($_POST['activity']) && isset($_POST['timespent'])) {
	$timespent = get_config('block_teacher_report', "activity_timespent_".$USER->id."_".$_POST['activity']);

	if(!$timespent){
		$timespent = 0;
		set_config("activity_timespent_".$USER->id."_".$_POST['activity'], $timespent + $_POST['timespent'], 'block_teacher_report');
	} else {
		$timespent += get_config('block_teacher_report', "activity_timespent_".$USER->id."_".$_POST['activity']);
		set_config("activity_timespent_".$USER->id."_".$_POST['activity'], $timespent + $_POST['timespent'], 'block_teacher_report');
	}
}

      
//
die;