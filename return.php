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

/**
 * LiqPay return script
 *
 * @package    enrol_liqpay
 * @copyright  2004 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require("../../config.php");
require_once("$CFG->dirroot/enrol/liqpay/lib.php");

// Make sure we are enabled in the first place.
if (!enrol_is_enabled('liqpay')) {
    http_response_code(503);
    throw new moodle_exception('errdisabled', 'enrol_liqpay');
}

/// Keep out casual intruders
if (empty($_POST)) {
    http_response_code(400);
    throw new moodle_exception('invalidrequest', 'core_error');
}

$public_key = get_config('enrol_liqpay', 'publickey');
$private_key = get_config('enrol_liqpay', 'privatekey');
$liqpay = new \enrol_liqpay\liqpaysdk\LiqPay($public_key, $private_key);

$pdata = new stdClass();
foreach ($_POST as $key => $value) {
    if ($key !== clean_param($key, PARAM_ALPHANUMEXT)) {
        throw new moodle_exception('invalidrequest', 'core_error', '', null, $key);
    }
    $pdata->$key = $value;
}

if (empty($pdata->data) || empty($pdata->signature)) {
    throw new moodle_exception('invalidrequest', 'core_error', '', null, 'Missing LiqPay data or signature');
}

$localsign = base64_encode( sha1( $private_key . $pdata->data . $private_key , 1 ));
$pdata->data = $liqpay->decode_params($pdata->data);

// verification if signature does passed or not
if ($localsign != $pdata->signature) {
    throw new moodle_exception('invalidrequest', 'core_error', '', null, 'Invalid signature!');
}

if (empty($pdata->data['order_id'])) {
    throw new moodle_exception('invalidrequest', 'core_error', '', null, 'Missing request param: order_id');
}

$order_id = explode('-', $pdata->data['order_id']);

if (empty($order_id) || count($order_id) < 3) {
    throw new moodle_exception('invalidrequest', 'core_error', '', null, 'Invalid value of the request param: order_id');
}

// date fixing
if (empty($pdata->data['create_date'])) {$pdata->data['create_date']= time();}
if (empty($pdata->data['end_date'])) {$pdata->data['end_date']= $pdata->data['create_date'];}

$data = new stdClass();
$data->userid           = (int)$order_id[0];
$data->courseid         = (int)$order_id[1];
$data->instanceid       = (int)$order_id[2];
$data->action           = $pdata->data['action'];   // pay - платеж , hold - блокировка средств на счету отправителя,
                                                    // subscribe - регулярный платеж, paydonate - пожертвование, 
                                                    // auth - предавторизация карты
$data->payment_status   = $pdata->data['status'];   // "success" (go to www.liqpay.ua/documentation/api/callback for more)
$data->publickey        = $public_key;                       // receiver's ID: public_key
$data->liqpay_order_id  = $pdata->data['liqpay_order_id'];   // LiqPay internal order_id
$data->payment_type     = $pdata->data['type'];              // payment type
$data->err_code         = !empty($pdata->data['err_code'])? $pdata->data['err_code'] : ''; // Transaction error code
$data->description      = $pdata->data['description'];
$data->timeupdated      = time();

$course = $DB->get_record("course", array("id" => $data->courseid), "*", MUST_EXIST);
$context = context_course::instance($data->courseid, MUST_EXIST);
$destination = "$CFG->wwwroot/course/view.php?id=$data->courseid";
$fullname = format_string($course->fullname, true, array('context' => $context));

$PAGE->set_context($context);

$plugin_instance = $DB->get_record("enrol", array("id" => $data->instanceid, "enrol" => "liqpay", "status" => 0), "*", MUST_EXIST);
$plugin = enrol_get_plugin('liqpay');

// TODO: perform second verification via LiqPay API?

if ((strlen($pdata->data["action"]) > 0) && (strlen($pdata->data["status"]) > 0)) {
    if ((strcmp($pdata->data["action"], "pay") == 0) && (strcmp($pdata->data["status"], "success") == 0)) { // VALID PAYMENT!

        // Fill rest of transaction data - only if more or less success
        $data->payment_id        = $pdata->data['payment_id'];        // ==transaction_id
        $data->amount            = $pdata->data['amount'];            // ==course's price
        $data->currency          = $pdata->data['currency'];          // ==currency of price
        $data->amount_credit     = $pdata->data['amount_credit'];     // reveived by seller (receiver)
        $data->currency_credit   = $pdata->data['currency_credit'];   // currency of receiver's account
        $data->commission_credit = $pdata->data['commission_credit']; // commission from receiver
        $data->amount_debit      = $pdata->data['amount_debit'];      // payed by customer
        $data->currency_debit    = $pdata->data['currency_debit'];    // currency of customer's payment
        $data->commission_debit  = $pdata->data['commission_debit'];  // commission from customer
        $data->acq_id            = $pdata->data['acq_id'];            // An Equirer ID
        $data->end_date          = $pdata->data['end_date'];          // Transaction end date
        $data->create_date       = $pdata->data['create_date'];       // Transaction create date
        $data->paytype           = $pdata->data['paytype']; // card - оплата картой, liqpay - через кабинет liqpay,
                                                            // privat24 - через кабинет приват24, 
                                                            // masterpass - через кабинет masterpass, 
                                                            // moment_part - рассрочка, cash - наличными, 
                                                            // invoice - счет на e-mail, qr - сканирование qr-кода
        // PayPal code compability
        $data->payment_gross    = $data->amount_credit;
        $data->payment_currency = $data->currency_credit;

        // If currency is incorrectly set then someone maybe trying to cheat the system
        // if ($pdata->data['currency'] != $plugin_instance->currency) { // Merchant account No need to mutch currency - potential problems!!
        if ($pdata->data['currency_credit'] != $plugin_instance->currency) { // Merchant account must match currency
            \enrol_liqpay\util::message_liqpay_error_to_admin(
                get_string('currencydoesnotmatch', 'enrol_liqpay', $data->currency_credit),
                $data);
            //die;
            redirect($CFG->wwwroot, get_string('currencydoesnotmatch', 'enrol_liqpay', $data->currency_credit));
        }

        // At this point we only proceed with a status of completed or pending with a reason of echeck

        // Make sure this transaction doesn't exist already.
        if ($existing = $DB->get_record("enrol_liqpay", array("payment_id" => $data->payment_id), "*", IGNORE_MULTIPLE)) {
            \enrol_liqpay\util::message_liqpay_error_to_admin(
                get_string('repeatedtransaction', 'enrol_liqpay', $data->payment_id), 
                $data);
            //die;
            redirect($CFG->wwwroot, get_string('repeatedtransaction', 'enrol_liqpay', $data->payment_id));
        }

        if (!$user = $DB->get_record('user', array('id'=>$data->userid))) {   // Check that user exists
            \enrol_liqpay\util::message_liqpay_error_to_admin(get_string('nouser', 'enrol_liqpay', $data->userid), $data);
            //die;
            redirect($CFG->wwwroot, get_string('nouser', 'enrol_liqpay', $data->userid));
        }

        if (!$course = $DB->get_record('course', array('id'=>$data->courseid))) { // Check that course exists
            \enrol_liqpay\util::message_liqpay_error_to_admin(get_string('nocourse', 'enrol_liqpay', $data->courseid), $data);
            //die;
            redirect($CFG->wwwroot, get_string('nocourse', 'enrol_liqpay', $data->courseid));
        }

        // Check that amount paid is the correct amount
        if ( (float) $plugin_instance->cost <= 0 ) {
            $cost = (float) $plugin->get_config('cost');
        } else {
            $cost = (float) $plugin_instance->cost;
        }

        // Use the same rounding of floats as on the enrol form.
        $cost = format_float($cost, 2, false);

        if ($data->payment_gross < $cost) {
            \enrol_liqpay\util::message_liqpay_error_to_admin(
                get_string('paidnotenough', 'enrol_liqpay', "($data->payment_gross < $cost)"), 
                $data);
            //die;
            redirect($CFG->wwwroot, 
                get_string('paidnotenough', 'enrol_liqpay', "($data->payment_gross ($data->payment_currency) < $cost)"));
        }

        // Use the queried course's full name for the item_name field.
        $data->item_name = $course->fullname;

        // ALL CLEAR !

        $liqpayid = $DB->insert_record("enrol_liqpay", $data);

        if ($plugin_instance->enrolperiod) {
            $timestart = time();
            $timeend   = $timestart + $plugin_instance->enrolperiod;
        } else {
            $timestart = 0;
            $timeend   = 0;
        }

        // Enrol user
        $plugin->enrol_user($plugin_instance, $user->id, $plugin_instance->roleid, $timestart, $timeend);
        
        // get enrollmentid
        if ($enrollmentid = $DB->get_field('user_enrolments', 'id', array('enrolid'=>$data->instanceid, 'userid'=>$data->userid))) {
            $DB->set_field('enrol_liqpay', 'userenrollmentid', $enrollmentid, array('id' => $liqpayid));
        }

        // Pass $view=true to filter hidden caps if the user cannot see them
        if ($users = get_users_by_capability($context, 'moodle/course:update', 'u.*', 'u.id ASC',
                                             '', '', '', '', false, true)) {
            $users = sort_by_roleassignment_authority($users, $context);
            $teacher = array_shift($users);
        } else {
            $teacher = false;
        }

        $mailstudents = $plugin->get_config('mailstudents');
        $mailteachers = $plugin->get_config('mailteachers');
        $mailadmins   = $plugin->get_config('mailadmins');
        $shortname = format_string($course->shortname, true, array('context' => $context));


        if (!empty($mailstudents)) {
            $a = new stdClass();
            $a->coursename = format_string($course->fullname, true, array('context' => $context));
            $a->profileurl = "$CFG->wwwroot/user/view.php?id=$user->id";

            $eventdata = new \core\message\message();
            $eventdata->courseid          = $course->id;
            $eventdata->modulename        = 'moodle';
            $eventdata->component         = 'enrol_liqpay';
            $eventdata->name              = 'liqpay_enrolment';
            $eventdata->userfrom          = empty($teacher) ? core_user::get_noreply_user() : $teacher;
            $eventdata->userto            = $user;
            $eventdata->subject           = get_string("enrolmentnew", 'enrol', $shortname);
            $eventdata->fullmessage       = get_string('welcometocoursetext', '', $a);
            $eventdata->fullmessageformat = FORMAT_PLAIN;
            $eventdata->fullmessagehtml   = '';
            $eventdata->smallmessage      = '';
            message_send($eventdata);

        }

        if (!empty($mailteachers) && !empty($teacher)) {
            $a->course = format_string($course->fullname, true, array('context' => $context));
            $a->user = fullname($user);

            $eventdata = new \core\message\message();
            $eventdata->courseid          = $course->id;
            $eventdata->modulename        = 'moodle';
            $eventdata->component         = 'enrol_liqpay';
            $eventdata->name              = 'liqpay_enrolment';
            $eventdata->userfrom          = $user;
            $eventdata->userto            = $teacher;
            $eventdata->subject           = get_string("enrolmentnew", 'enrol', $shortname);
            $eventdata->fullmessage       = get_string('enrolmentnewuser', 'enrol', $a);
            $eventdata->fullmessageformat = FORMAT_PLAIN;
            $eventdata->fullmessagehtml   = '';
            $eventdata->smallmessage      = '';
            message_send($eventdata);
        }

        if (!empty($mailadmins)) {
            $a->course = format_string($course->fullname, true, array('context' => $context));
            $a->user = fullname($user);
            $admins = get_admins();
            foreach ($admins as $admin) {
                $eventdata = new \core\message\message();
                $eventdata->courseid          = $course->id;
                $eventdata->modulename        = 'moodle';
                $eventdata->component         = 'enrol_liqpay';
                $eventdata->name              = 'liqpay_enrolment';
                $eventdata->userfrom          = $user;
                $eventdata->userto            = $admin;
                $eventdata->subject           = get_string("enrolmentnew", 'enrol', $shortname);
                $eventdata->fullmessage       = get_string('enrolmentnewuser', 'enrol', $a);
                $eventdata->fullmessageformat = FORMAT_PLAIN;
                $eventdata->fullmessagehtml   = '';
                $eventdata->smallmessage      = '';
                message_send($eventdata);
            }
        }
    
    } elseif (strcmp($pdata->data["status"], "success") != 0) {
        if (strcmp($pdata->data["err_code"], "cancel") != 0) {
            // Fill rest of transaction data - for discovering of errors (but not canceled by user)
            $data->payment_id        = $pdata->data['payment_id'];        // ==transaction_id
            $data->amount            = $pdata->data['amount'];            // ==course's price
            $data->currency          = $pdata->data['currency'];          // ==currency of price
            $data->amount_credit     = 0;                                 // reveived by seller (receiver)
            $data->currency_credit   = $pdata->data['currency_credit'];   // currency of receiver's account
            $data->commission_credit = 0;                                 // commission from receiver
            $data->amount_debit      = 0;                                 // payed by customer
            $data->currency_debit    = $pdata->data['currency_debit'];    // currency of customer's payment
            $data->commission_debit  = 0;                                 // commission from customer
            $data->acq_id            = $pdata->data['acq_id'];            // An Equirer ID
            $data->end_date          = $pdata->data['end_date'];          // Transaction end date
            $data->create_date       = $pdata->data['create_date'];       // Transaction create date
            $data->paytype           = $pdata->data['paytype'];
            $data->item_name         = $course->fullname;
            $DB->insert_record("enrol_liqpay", $data);
        }
        $PAGE->set_url($destination);
        $PAGE->set_course($course);
        $PAGE->set_title($course->fullname);
        $PAGE->set_heading($course->fullname);
        $PAGE->set_context($context);
        echo $OUTPUT->header();
        notice(get_string('unsuccesspayment', 'enrol_liqpay', $data->payment_status.' - '.$data->err_code), $destination);        
    }
}

// Final redirect of user:
require_login();

if (!empty($SESSION->wantsurl)) {
    $destination = $SESSION->wantsurl;
    unset($SESSION->wantsurl);
}

if (is_enrolled($context, NULL, '', true)) {
    redirect($destination, get_string('paymentthanks', '', $fullname));
} else {   /// Somehow they aren't enrolled yet!  :-(
    $PAGE->set_url($destination);
    $PAGE->set_course($course);
    $PAGE->set_title($course->fullname);
    $PAGE->set_heading($course->fullname);
    $PAGE->set_context($context);
    echo $OUTPUT->header();
    $a = new stdClass();
    $a->teacher = get_string('defaultcourseteacher');
    $a->fullname = $fullname;
    notice(get_string('paymentsorry', '', $a), $destination);
}


