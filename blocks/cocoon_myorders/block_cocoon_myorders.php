<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

include_once($CFG->dirroot . '/course/lib.php');

class block_cocoon_myorders extends block_list {
    function init() {
        $this->title = get_string('pluginname', 'block_cocoon_myorders');
    }

    function has_config() {
        return true;
    }

    function applicable_formats() {
        return array(
          'all' => false,
          'my' => true,
        );
    }

    function get_content() {
        global $CFG, $USER, $DB, $OUTPUT, $COURSE, $SITE;


        if($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';
        if(!empty($this->config->title)){$this->content->title = $this->config->title;} else {$this->content->title == $this->title;}
        if(!empty($this->config->address_line_1)){$this->content->address_line_1 = $this->config->address_line_1;}
        if(!empty($this->config->address_line_2)){$this->content->address_line_2 = $this->config->address_line_2;}
        if(!empty($this->config->address_line_3)){$this->content->address_line_3 = $this->config->address_line_3;}
        if(!empty($this->config->zip_code)){$this->content->zip_code = $this->config->zip_code;}
        if(!empty($this->config->phone)){$this->content->phone = $this->config->phone;}
        if(!empty($this->config->email)){$this->content->email = $this->config->email;}

        // $icon = $OUTPUT->pix_icon('i/course', get_string('course'));

        // $adminseesall = true;
        // if (isset($CFG->block_cocoon_myorders_adminview)) {
        //    if ( $CFG->block_cocoon_myorders_adminview == 'own'){
        //        $adminseesall = false;
        //    }
        // }

        // $allcourselink =
        //     (has_capability('moodle/course:update', context_system::instance())
        //     || empty($CFG->block_cocoon_myorders_hideallcourseslink)) &&
        //     core_course_category::user_top();

        // if (empty($CFG->disablemycourses) and isloggedin() and !isguestuser() and
        //   !(has_capability('moodle/course:update', context_system::instance()) and $adminseesall)) {    // Just print My Courses
        //     if ($courses = enrol_get_my_courses()) {
        //         foreach ($courses as $course) {
        //             $coursecontext = context_course::instance($course->id);
        //             $linkcss = $course->visible ? "" : " class=\"dimmed\" ";
        //             $this->content->items[]="<a $linkcss title=\"" . format_string($course->shortname, true, array('context' => $coursecontext)) . "\" ".
        //                        "href=\"$CFG->wwwroot/course/view.php?id=$course->id\">".$icon.format_string(get_course_display_name_for_list($course)). "</a>";
        //         }
        //         $this->title = get_string('mycourses');
        //     /// If we can update any course of the view all isn't hidden, show the view all courses link
        //         if ($allcourselink) {
        //             $this->content->footer = "<a href=\"$CFG->wwwroot/course/index.php\">".get_string("fulllistofcourses")."</a> ...";
        //         }
        //     }
        //     $this->get_remote_courses();
        //     if ($this->content->items) { // make sure we don't return an empty list
        //         return $this->content;
        //     }
        // }

        $order = '';
        $payment = '';
        $ccn_sum = 0;
        $ccn_courses_i = 0;
        if(isloggedin() and !isguestuser()){
            $courses = enrol_get_all_users_courses($USER->id, true);

            foreach ($courses as $course) {
              $course = new core_course_list_element($course);
              $coursecontext = context_course::instance($course->id);
              $enrolinstances = enrol_get_instances($course->id, true);
              $ccnmethods = array_column($enrolinstances, null, "enrol");

              //PayPal
              if(array_key_exists('paypal', $ccnmethods)){
                $ccnpp = $ccnmethods['paypal'];
                if($ccnpp){
                  $ccn_enrolment_method = $ccnpp->enrol;
                  $cost = $ccnpp->cost;
                  $currency = $ccnpp->currency;
                  $enrol = $ccnpp->enrol;
                }
              }
              //Stripe
              if(array_key_exists('stripepayment', $ccnmethods)){
                $ccnstripe = $ccnmethods['stripepayment'];
                if($ccnstripe){
                  $cost_stripe = $ccnstripe->cost;
                  $currency_stripe = $ccnstripe->currency;
                  $enrol_stripe = $ccnstripe->enrol;
                }
              }

              $enrolment_link = $CFG->wwwroot . '/enrol/index.php?id=' . $course->id;
              $ccn_course_id = $course->id;
              $ccn_course_title = $course->fullname;
              $ccn_course_start_date = $course->startdate;
              $ccn_course_link = $CFG->wwwroot . '/course/view.php?id=' . $course->id;
              $ccn_receipt_id = 'receiptID-' . $course->id;
              $ccn_site_name = $SITE->fullname;
              $ccn_site_url = $CFG->wwwroot;
              $ccn_customer_id = strtoupper(trim($SITE->fullname . $USER->id));
              $ccn_customer_username = $USER->username;
              $ccn_customer_email = $USER->email;

              $ccn_sum+= $cost;
              $ccn_courses_i++;

              $contentimages = $contentfiles = '';
              foreach ($course->get_course_overviewfiles() as $file) {
                $isimage = $file->is_valid_image();
                $url = file_encode_url("{$CFG->wwwroot}/pluginfile.php",
                  '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                  $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
                if ($isimage) {
                  $contentimages .= $url;
                } else {
                  $image = $this->output->pix_icon(file_file_icon($file, 24), $file->get_filename(), 'moodle');
                  $filename = html_writer::tag('span', $image, array('class' => 'fp-icon')).
                      html_writer::tag('span', $file->get_filename(), array('class' => 'fp-filename'));
                  $contentfiles .= html_writer::tag('span',
                      html_writer::link($url, $filename),
                      array('class' => 'coursefile fp-filename-icon'));
                }
              }


              $order .= '
              <tr>
					    	<th scope="row">
					    		<ul class="cart_list">
					    			<li class="list-inline-item pr20"><a href="'.$ccn_course_link.'"><img src="'.$contentimages.'" alt="'.$ccn_course_title.'"></a></li>
					    			<li class="list-inline-item"><a class="cart_title" href="'.$ccn_course_link.'">'.$ccn_course_title.'</a></li>
					    		</ul>
					    	</th>
					    	<td>'.userdate($ccn_course_start_date, get_string('strftimedatefullshort', 'langconfig'), 0).'</td>
					    	<td>'.get_string('completed', 'theme_edumy').'</td>
					    	<td class="cart_total">'. get_string('currency_symbol', 'theme_edumy') . $cost . get_string('currency', 'theme_edumy') .'</td>
					    	<td class="text-thm tdu"><a data-ccn-receipt-id="'.$ccn_receipt_id.'" href="#'.$ccn_receipt_id.'" class="ccn_receipt_handler">'.get_string('receipt', 'theme_edumy').'</a></td>
                <td id="'.$ccn_receipt_id.'" style="display:none;">
                  <style>
                    @import url(https://fonts.googleapis.com/css?family=Nunito:400,500,600,700|Open+Sans);
                    .ccnReceiptWrapper * {font-family: "Nunito", Arial, Helvetica, sans-serif;}
                    .ccnReceiptWrapper {padding: 15px 40px;}
                    .ccnReceiptWrapper table, .ccnReceiptWrapper th, .ccnReceiptWrapper td {padding: 6px 10px;border:1px solid #ddd;}
                    .ccnReceiptWrapper table {margin: 20px 0;}
                    .ccnReceiptWrapper .receiptProvider {line-height: 1.3;font-size: 12px;}
                    .ccnReceiptWrapper .receiptProviderName {font-weight: bold;font-size: 15px;}
                    .ccnReceiptWrapper .receiptProviderAddress, .ccnReceiptWrapper .receiptProviderDetails {line-height: 1.3;font-size: 12px;padding: 10px 0 0 0;border-top: 1px solid #ddd;margin: 10px 0 0 0;}
                  </style>
                  <div class="ccnReceiptWrapper" style="font-family: Arial, Helvetica, sans-serif;">
                    <h1>'.$ccn_site_name.'</h1>
                    <h2>'.get_string('your_order', 'theme_edumy').'</h2>
                    <table style="border-collapse:collapse; border: 1px solid #ddd;">
                      <tr>
                        <th style="border: 1px solid #ddd;padding: 6px 10px;">'.$ccn_course_title.'</th>
                        <td style="border: 1px solid #ddd;padding: 6px 10px;">'.get_string('currency_symbol', 'theme_edumy') . $cost . get_string('currency', 'theme_edumy').'</td>
                        <td style="text-transform:capitalize;border: 1px solid #ddd;padding: 6px 10px;">'.get_string('payment_method', 'theme_edumy') .' '. $ccn_enrolment_method .'</td>
                      </tr>
                    </table>
                    <div class="receiptProvider">
                    <div class="receiptProviderName">'.$ccn_site_name.'</div>
                    <div class="receiptProviderAddress">
                      '.$this->content->address_line_1.'<br>
                      '.$this->content->address_line_2.'<br>
                      '.$this->content->address_line_3.'<br>
                      '.$this->content->zip_code.'<br>
                    </div>
                    <div class="receiptProviderDetails">
                      '.$this->content->phone.'<br>
                      '.$this->content->email.'<br>
                    </div>
                    </div>
                  </div>
                </td>
					    </tr>';

              $payment .= '<tr>
					    	<th scope="row">
					    		<ul class="cart_list">
					    			<li class="list-inline-item"><a class="cart_title" href="'.$ccn_course_link.'">'.$ccn_course_title.'</a></li>
					    		</ul>
					    	</th>
					    	<td>'.userdate($ccn_course_start_date, get_string('strftimedatefullshort', 'langconfig'), 0).'</td>
					    	<td>'.$ccn_course_id.'</td>
                <td></td>
					    	<td class="cart_total">'. get_string('currency_symbol', 'theme_edumy') . $cost . get_string('currency', 'theme_edumy') .'</td>
					    	<td class="">'. get_string('currency_symbol', 'theme_edumy') . $cost . get_string('currency', 'theme_edumy') .'</td>
					    </tr>';
            }
          }
        $this->content->footer = '
          <div class="my_course_content_container mb30">
						<div class="my_setting_content">
							<div class="my_setting_content_header">
								<div class="my_sch_title">
									<h4 class="m0">'.$this->content->title.'</h4>
								</div>
							</div>
							<div class="my_setting_content_details pb0">
								<div class="cart_page_form style2">
									<form action="#">
										<table class="table table-responsive">
										  	<thead>
											    <tr class="carttable_row">
											    	<th class="cartm_title">'.get_string('item', 'theme_edumy').'</th>
											    	<th class="cartm_title">'.get_string('date', 'theme_edumy').'</th>
											    	<th class="cartm_title">'.get_string('status', 'theme_edumy').'</th>
											    	<th class="cartm_title">'.get_string('total', 'theme_edumy').'</th>
											    	<th class="cartm_title">'.get_string('action', 'theme_edumy').'</th>
											    </tr>
										  	</thead>
										  	<tbody class="table_body">
                        '.$order.'
										  	</tbody>
										</table>
									</form>
								</div>
							</div>
							<div class="my_setting_content_header pt0">
								<div class="my_sch_title">
									<h4 class="m0">Order Details</h4>
								</div>
							</div>
							<div class="my_setting_content_details">
								<ul class="order_key_status mb0">
									<li>'.get_string('customer_id', 'theme_edumy').' <span>'.$ccn_customer_id.'</span></li>
									<li>'.get_string('customer_username', 'theme_edumy').' <span>'.$ccn_customer_username.'</span></li>
                  <li>'.get_string('customer_email', 'theme_edumy').' <span>'.$ccn_customer_email.'</span></li>
								</ul>
							</div>
							<div class="my_setting_content_details">
								<div class="cart_page_form style3">
									<form action="#">
										<table class="table table-responsive">
										  	<thead>
											    <tr class="carttable_row">
											    	<th class="cartm_title">'.get_string('item', 'theme_edumy').'</th>
											    	<th class="cartm_title">'.get_string('date', 'theme_edumy').'</th>
											    	<th class="cartm_title">'.get_string('course_id', 'theme_edumy').'</th>
                            <th class="cartm_title"></th>
											    	<th class="cartm_title">'.get_string('price', 'theme_edumy').'</th>
											    	<th class="cartm_title">'.get_string('total', 'theme_edumy').'</th>
											    </tr>
										  	</thead>
										  	<tbody class="table_body">
											    '.$payment.'
											    <tr class="borderless_table_row">
											    	<th scope="row"></th>
											    	<td></td>
                            <td></td>
											    	<td></td>
											    	<td class="cart_total color-dark fz18 pb10">'.get_string('total_courses', 'theme_edumy').'</td>
											    	<td class="color-gray2 fz15 pb10">'.$ccn_courses_i.'</td>
											    </tr>
											    <tr class="borderless_table_row style2">
											    	<th scope="row"></th>
											    	<td></td>
                            <td></td>
											    	<td></td>
											    	<td class="cart_total color-dark fz18 pt10">'.get_string('total', 'theme_edumy').'</td>
											    	<td class="color-gray2 fz15 pt10">'. get_string('currency_symbol', 'theme_edumy') . $ccn_sum . get_string('currency', 'theme_edumy') .'</td>
											    </tr>
										  	</tbody>
										</table>
									</form>
								</div>
							</div>
						</div>
					</div>';
        return $this->content;
    }

    function get_remote_courses() {
        global $CFG, $USER, $OUTPUT;

        if (!is_enabled_auth('mnet')) {
            // no need to query anything remote related
            return;
        }

        $icon = $OUTPUT->pix_icon('i/mnethost', get_string('host', 'mnet'));

        // shortcut - the rest is only for logged in users!
        if (!isloggedin() || isguestuser()) {
            return false;
        }

        if ($courses = get_my_remotecourses()) {
            $this->content->items[] = get_string('remotecourses','mnet');
            $this->content->icons[] = '';
            foreach ($courses as $course) {
                $this->content->items[]="<a title=\"" . format_string($course->shortname, true) . "\" ".
                    "href=\"{$CFG->wwwroot}/auth/mnet/jump.php?hostid={$course->hostid}&amp;wantsurl=/course/view.php?id={$course->remoteid}\">"
                    .$icon. format_string(get_course_display_name_for_list($course)) . "</a>";
            }
            // if we listed courses, we are done
            return true;
        }

        if ($hosts = get_my_remotehosts()) {
            $this->content->items[] = get_string('remotehosts', 'mnet');
            $this->content->icons[] = '';
            foreach($USER->mnet_foreign_host_array as $somehost) {
                $this->content->items[] = $somehost['count'].get_string('courseson','mnet').'<a title="'.$somehost['name'].'" href="'.$somehost['url'].'">'.$icon.$somehost['name'].'</a>';
            }
            // if we listed hosts, done
            return true;
        }

        return false;
    }

    /**
     * Returns the role that best describes the course list block.
     *
     * @return string
     */
    public function get_aria_role() {
        return 'navigation';
    }

    public function html_attributes() {
      global $CFG;
      $attributes = parent::html_attributes();
      include($CFG->dirroot . '/theme/edumy/ccn/block_handler/attributes.php');
      return $attributes;
    }
}
