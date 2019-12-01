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
 * LiqPay utility script
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
$f = fopen("post.txt", "w");
fwrite($f, print_r($_POST, true));
fclose($f);

/// Keep out casual intruders
if (empty($_POST)) {
    http_response_code(400);
    throw new moodle_exception('invalidrequest', 'core_error');
}

$id = required_param('id', PARAM_INT);
var_dump($id);

$public_key = get_config('enrol_liqpay', 'publickey');
$private_key = get_config('enrol_liqpay', 'privatekey');
$liqpay = new \enrol_liqpay\liqpaysdk\LiqPay($public_key, $private_key);
//var_dump(base64_decode($_POST['data']));

$pdata = new stdClass();
//error_log('LiqPay ipn BEGIN:');
foreach ($_POST as $key => $value) {
//error_log(print_r($key, true));
//error_log(print_r($value, true));
    if ($key !== clean_param($key, PARAM_ALPHANUMEXT)) {
        throw new moodle_exception('invalidrequest', 'core_error', '', null, $key);
    }
    //$data->$key = fix_utf8($value);
    //$data->$key = base64_decode($value);
    $pdata->$key = $value;
}

var_dump($pdata);
//$pdata->data = base64_decode($pdata->data);
//var_dump('base64 decoded data:');
//var_dump($pdata->data);
//$sign = base64_encode( sha1( $private_key . $pdata->data . $private_key , 1 ));
//$sign2 = sha1( base64_encode( $private_key . $pdata->data . $private_key) , 1 );
//$sign3 = sha1( base64_encode( $private_key . $pdata->data . $private_key) );
//var_dump($pdata->data);
//var_dump($sign);
//var_dump($sign2);
//var_dump($sign3);
$pdata->data = $liqpay->decode_params($pdata->data);
//$reencoded = base64_encode(json_encode($pdata->data));
$sign = $liqpay->cnb_signature($pdata->data);
var_dump($sign);
//if ($sign != $data->signature) {
//    throw new moodle_exception('invalidrequest', 'core_error', '', null, 'Invalid signature!');
//}

//$pdata->data = json_decode($pdata->data);

var_dump('json decoded data:');
var_dump($pdata->data);

if (empty($pdata->data['order_id'])) {
    throw new moodle_exception('invalidrequest', 'core_error', '', null, 'Missing request param: order_id');
}

$order_id = explode('-', $pdata->data['order_id']);
//unset($data->custom);

if (empty($order_id) || count($order_id) < 3) {
    throw new moodle_exception('invalidrequest', 'core_error', '', null, 'Invalid value of the request param: order_id');
}

$data = new stdClass();
$data->userid           = (int)$order_id[0];
$data->courseid         = (int)$order_id[1];
$data->instanceid       = (int)$order_id[2];
$data->payment_gross    = $pdata->data['amount_credit'];
$data->payment_currency = $pdata->data['currency_credit'];
$data->timeupdated      = time();
var_dump($data);
$user = $DB->get_record("user", array("id" => $data->userid), "*", MUST_EXIST);
$course = $DB->get_record("course", array("id" => $data->courseid), "*", MUST_EXIST);
$context = context_course::instance($course->id, MUST_EXIST);


//var_dump($data);
if (!$course = $DB->get_record("course", array("id"=>$id))) {
    redirect($CFG->wwwroot);
}

$context = context_course::instance($course->id, MUST_EXIST);
$PAGE->set_context($context);

require_login();

if (!empty($SESSION->wantsurl)) {
    $destination = $SESSION->wantsurl;
    unset($SESSION->wantsurl);
} else {
    $destination = "$CFG->wwwroot/course/view.php?id=$course->id";
}

$fullname = format_string($course->fullname, true, array('context' => $context));

if (is_enrolled($context, NULL, '', true)) { // TODO: use real liqpay check
    redirect($destination, get_string('paymentthanks', '', $fullname));

} else {   /// Somehow they aren't enrolled yet!  :-(
    $PAGE->set_url($destination);
    echo $OUTPUT->header();
    $a = new stdClass();
    $a->teacher = get_string('defaultcourseteacher');
    $a->fullname = $fullname;
    notice(get_string('paymentsorry', '', $a), $destination);
}


