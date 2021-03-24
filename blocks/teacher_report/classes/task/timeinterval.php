<?php
namespace block_teacher_report\task;
 
/**
 * An example of a scheduled task.
 */
class timeinterval extends \core\task\scheduled_task {
 
    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('timeinterval', 'block_teacher_report');
    }
 
    /**
     * Execute the task.
     */
    public function execute() {
        GLOBAL $CFG;
        require_once($CFG->libdir . '/enrollib.php');
        require_once($CFG->libdir.'/completionlib.php');
        require_once($CFG->dirroot . '/blocks/teacher_report/moodleworkbook.php');
        mtrace("check");
        $allcourses = get_courses();
        foreach ($allcourses as $ckey => $allcourse) {
            if($allcourse->format != 'site') {
                //mtrace($allcourse->id."<br>");
                $info = new \completion_info($allcourse);
                $coursecontext = \context_course::instance($allcourse->id);
                
                $teachers = get_enrolled_users($coursecontext, 'moodle/course:manageactivities');
                foreach ($teachers as $tkey => $teacher) {
                    $students = get_enrolled_users($coursecontext);
                    foreach ($students as $skey => $student) {
                        if ($teacher->id == $student->id){
                            unset($students[$student->id]);
                            //mtrace("Teacher here"."<br>");
                        }
                    }
                    if(isset($students) && !empty($students)){
                        
                        $filename = $allcourse->id."_weeklyreport_".$teacher->id.".xlsx";
                        //mtrace("We are here".$filename);
                        $downloadfilename = clean_filename($filename);
                        // Creating a workbook
                        $workbook = new \MoodleExcelWorkbook1("-");
                        // Sending HTTP headers
                        $workbook->send($downloadfilename);
                        // Adding the worksheet
                        ;
                        $format = $workbook->add_format();
                        $format->set_align('vcenter');
                        
                        
                        foreach ($students as $skey => $student) {
                            
                            $myxls = $workbook->add_worksheet($student->firstname."_".$student->lastname);//for teacher
                            $format = $workbook->add_format();
                            $format->set_align('vcenter');
                            $myxls->write_string(0, 0, "Student Name", $format);
                            $myxls->merge_cells(0, 0, 1, 0);
                            $myxls->write_string(0, 1, "Activity", $format);
                            $myxls->merge_cells(0, 1, 0, 4);
                            $myxls->write_string(0, 5, "Course Progress / Time", $format);
                            $activities = get_all_course_modules($allcourse->id, $student->id);
                            $myxls->write_string(1, 1, "Activity name", $format);
                            $myxls->write_string(1, 2, "Status", $format);
                            $myxls->write_string(1, 3, "Score", $format);
                            $myxls->write_string(1, 4, "Time", $format);
                            $myxls->write_string(2, 0, $student->firstname." ".$student->lastname, $format);
                            $xlxcount = 2;
                            $ctime = 0;
                            foreach ($activities as $key => $activity) {
                                $ctime += get_config('block_teacher_report', "activity_timespent_".$student->id."_".$activity->id);
                                $myxls->write_string($xlxcount, 1, $activity->course_module_instance->name, $format);
                                if(isset($activity->completionstatus->completionstate)) {
                                    $myxls->write_string($xlxcount, 2, "Complete", $format);
                                } else {
                                    $myxls->write_string($xlxcount, 2, "Incomplete", $format);
                                }
                                $myxls->write_string($xlxcount, 3, $activity->obtainscore, $format);
                                $myxls->write_string($xlxcount, 4, secondsToTime(get_config('block_teacher_report', "activity_timespent_".$student->id."_".$activity->id)), $format);
                                $xlxcount = $xlxcount + 1;
                            }
                            $xlxcount--;
                            $myxls->merge_cells(2, 0, $xlxcount, 0);
                            $coursecomplete = $info->is_course_complete($student->id);
                            if($coursecomplete){
                                $coursecompletestatus = 'Complete';
                            } else {
                                $coursecompletestatus = 'Incomplete';
                            }
                            $myxls->write_string(2, 5, $coursecompletestatus.' / '.secondsToTime($ctime), $format);
                            mtrace("student here"."<br>");
                        }
                        $workbook->close();
                    }
                    $attachment = $CFG->tempdir.'/'.$filename;
                    $subject = get_string('weeklyreport', 'block_teacher_report', $allcourse->id);
                    $data = new \stdClass();
                    $data->to = fullname($teacher);
                    $supportuser = \core_user::get_support_user();
                    
                    $data->form = $supportuser->email;
                    $message = get_string('weeklyreportbody', 'block_teacher_report', $data);
                    $from_email = $reply_to_email = $supportuser->email;
                    $recipient_email = $teacher->email;
                    $file_name = $filename;
                    $content = file_get_contents( $attachment);
                    $content = chunk_split(base64_encode($content));
                    $uid = md5(uniqid(time()));
                    $name = basename($attachment);
                    
                    $supportuser = \core_user::get_support_user();
                    $user = get_complete_user_data('id', $teacher->id);

                    email_to_user($user, $supportuser, $subject, $message, $message, $attachment, $filename);
                }
                
            }
        }
    }
}
