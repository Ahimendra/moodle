<?php
global $CFG;

class block_cocoon_course_details extends block_base
{
    // Declare first
    public function init()
    {
        $this->title = get_string('cocoon_course_details', 'block_cocoon_course_details');
    }

    // Declare second
    public function specialization()
    {
      global $CFG;
      include($CFG->dirroot . '/theme/edumy/ccn/block_handler/specialization.php');
    }

    function applicable_formats() {
        return array(
          'all' => true,
          'my' => false,
        );
    }



    public function get_content()
    {
        global $CFG, $DB, $COURSE, $USER, $PAGE;

        if (isset($PAGE->theme->settings->course_enrolment_payment) && ($PAGE->theme->settings->course_enrolment_payment == 1)) {
          $paymentForced = false;
        } else {
          $paymentForced = true;
        }

        $context = context_course::instance($COURSE->id, MUST_EXIST);
        $enrolinstances = enrol_get_instances($COURSE->id, true);
        $ccnmethods = array_column($enrolinstances, null, "enrol");


        // Paypal
        $ccnpp = $ccnmethods['paypal'];
        $cost = $ccnpp->cost;
        $currency = $ccnpp->currency;
        $enrol = $ccnpp->enrol;
        // Stripe
        $ccnstripe = $ccnmethods['stripepayment'];
        $cost_stripe = $ccnstripe->cost;
        $currency_stripe = $ccnstripe->currency;
        $enrol_stripe = $ccnstripe->enrol;

        $price_matches = false;
        if($cost == $cost_stripe) {
          $price_matches = true;
        } else {
          $price_matches = false;
        }

        $enrolment_link = $CFG->wwwroot . '/enrol/index.php?id=' . $COURSE->id;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content =  new stdClass;

        if (!empty($this->config) && is_object($this->config)) {
            $data = $this->config;
            $data->items = is_numeric($data->items) ? (int)$data->items : 0;
        } else {
            $data = new stdClass();
            $data->items = 0;
        }

        if(!empty($this->config->price_title)){$this->content->price_title = $this->config->price_title;}
        if(!empty($this->config->currency)){$this->content->currency = $this->config->currency;}
        if(!empty($this->config->price)){$this->content->price = $this->config->price;}
        if(!empty($this->config->full_price)){$this->content->full_price = $this->config->full_price;}
        if(!empty($this->config->add_to_cart_text)){$this->content->add_to_cart_text = $this->config->add_to_cart_text;}
        if(!empty($this->config->add_to_cart_link)){$this->content->add_to_cart_link = $this->config->add_to_cart_link;}
        if(!empty($this->config->buy_now_text)){$this->content->buy_now_text = $this->config->buy_now_text;}
        if(!empty($this->config->buy_now_link)){$this->content->buy_now_link = $this->config->buy_now_link;}
        if(!empty($this->config->video_text)){$this->content->video_text = $this->config->video_text;}
        if(!empty($this->config->download_text)){$this->content->download_text = $this->config->download_text;}
        if(!empty($this->config->access_text)){$this->content->access_text = $this->config->access_text;}
        if(!empty($this->config->devices_text)){$this->content->devices_text = $this->config->devices_text;}
        if(!empty($this->config->assignments_text)){$this->content->assignments_text = $this->config->assignments_text;}
        if(!empty($this->config->certificate_text)){$this->content->certificate_text = $this->config->certificate_text;}

        $this->content->text = '<div class="instructor_pricing_widget">';
        if (is_enrolled($context, $USER, '', true)) {
          $this->content->text .='
                                  <div class="price"> '. get_string('course_enrolled', 'theme_edumy') .' </div>
                                  <p class="mt10">'. get_string('course_enrolled_text', 'theme_edumy') .'</p>';
        } elseif($enrolinstances && ($ccnmethods['paypal'] || $ccnmethods['stripepayment'])) {
          if($ccnmethods['paypal'] && $ccnmethods['stripepayment'] && $price_matches) {
            $this->content->text .='
            <div class="price">
              <i class="fa fa-credit-card"></i><span>'. get_string('course_price', 'theme_edumy') .'</span> '. get_string('course_currency', 'theme_edumy') . $cost_stripe .' <small>'. $currency_stripe .'</small>
            </div>';
          } else {
            if($ccnmethods['paypal']) {
              $this->content->text .='
              <div class="price">
                <i class="fa fa-paypal"></i><span>'. get_string('course_price', 'theme_edumy') .'</span> '. get_string('course_currency', 'theme_edumy') . $cost .' <small>'. $currency .'</small>
              </div>';
            }
            if($ccnmethods['stripepayment']) {
              $this->content->text .='
              <div class="price">
                <i class="fa fa-credit-card"></i><span>'. get_string('course_price', 'theme_edumy') .'</span> '. get_string('course_currency', 'theme_edumy') . $cost_stripe .' <small>'. $currency_stripe .'</small>
              </div>';
            }
          }
           $this->content->text .='
          <a href="'. $enrolment_link .'" class="cart_btnss">'. get_string('course_enrolment', 'theme_edumy') .'</a>
          <div class="ccn-buy-access">'. get_string('course_buy_access', 'theme_edumy') .'</div>';
          if ($data->items > 0) {
            $this->content->text .='<ul class="price_quere_list text-left">';

            for ($i = 1; $i <= $data->items; $i++) {
              $item_title = 'item_title' . $i;
              $item_icon = 'item_icon' . $i;
              $this->content->text .='<li><div class="ccn-course-details-item"><span class="'.format_text($data->$item_icon, FORMAT_HTML, array('filter' => true)).'"></span> '.format_text($data->$item_title, FORMAT_HTML, array('filter' => true)).'</div></li>';
            }

            $this->content->text .='</ul>';

          }
        } elseif($paymentForced == false){
          $this->content->text .= '   <div class="price">
            <i class="fa fa-credit-card"></i><span>'. get_string('course_free_accent', 'theme_edumy') .'</span> '. get_string('course_free', 'theme_edumy') .' <small>'. get_string('course_free_to_join', 'theme_edumy') .'</small>
          </div>
          <a href="'. $enrolment_link .'" class="cart_btnss">'. get_string('course_enrolment_free', 'theme_edumy') .'</a>
          <div class="ccn-buy-access">'. get_string('course_free_access', 'theme_edumy') .'</div>';
          if ($data->items > 0) {
            $this->content->text .='<ul class="price_quere_list text-left">';

            for ($i = 1; $i <= $data->items; $i++) {
              $item_title = 'item_title' . $i;
              $item_icon = 'item_icon' . $i;
              $this->content->text .='<li><div class="ccn-course-details-item"><span class="'.format_text($data->$item_icon, FORMAT_HTML, array('filter' => true)).'"></span> '.format_text($data->$item_title, FORMAT_HTML, array('filter' => true)).'</div></li>';
            }

            $this->content->text .='</ul>';

          }
        } else {
          $this->content->text .= '<div class="price">'. get_string('course_error_title', 'theme_edumy') .'</div>
                                   <p class="mt10">'. get_string('course_error_text', 'theme_edumy') .'</p>';
        }
        $this->content->text .= '</div>';
        return $this->content;
    }
    public function html_attributes() {
      global $CFG;
      $attributes = parent::html_attributes();
      include($CFG->dirroot . '/theme/edumy/ccn/block_handler/attributes.php');
      return $attributes;
    }
}
