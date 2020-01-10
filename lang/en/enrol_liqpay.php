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
 * Strings for component 'enrol_liqpay', language 'en'.
 *
 * @package    enrol_liqpay
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['assignrole'] = 'Assign role';
$string['publickey'] = 'LiqPay public key';
$string['publickey_desc'] = 'The public key of your business LiqPay account';
$string['privatekey'] = 'LiqPay private key';
$string['privatekey_desc'] = 'The private key of your business LiqPay account';
$string['cost'] = 'Enrol cost';
$string['costerror'] = 'The enrolment cost is not numeric';
$string['costorkey'] = 'Please choose one of the following methods of enrolment.';
$string['currency'] = 'Currency';
$string['defaultrole'] = 'Default role assignment';
$string['defaultrole_desc'] = 'Select role which should be assigned to users during LiqPay enrolments';
$string['enrolenddate'] = 'End date';
$string['enrolenddate_help'] = 'If enabled, users can be enrolled until this date only.';
$string['enrolenddaterror'] = 'Enrolment end date cannot be earlier than start date';
$string['enrolperiod'] = 'Enrolment duration';
$string['enrolperiod_desc'] = 'Default length of time that the enrolment is valid. If set to zero, the enrolment duration will be unlimited by default.';
$string['enrolperiod_help'] = 'Length of time that the enrolment is valid, starting with the moment the user is enrolled. If disabled, the enrolment duration will be unlimited.';
$string['enrolstartdate'] = 'Start date';
$string['enrolstartdate_help'] = 'If enabled, users can be enrolled from this date onward only.';
$string['errdisabled'] = 'The LiqPay enrolment plugin is disabled and does not handle payment notifications.';
$string['erripninvalid'] = 'Instant payment notification has not been verified by LiqPay.';
$string['errliqpayconnect'] = 'Could not connect to {$a->url} to verify the instant payment notification: {$a->result}';
$string['expiredaction'] = 'Enrolment expiry action';
$string['expiredaction_help'] = 'Select action to carry out when user enrolment expires. Please note that some user data and settings are purged from course during course unenrolment.';
$string['mailadmins'] = 'Notify admin';
$string['mailstudents'] = 'Notify students';
$string['mailteachers'] = 'Notify teachers';
$string['messageprovider:liqpay_enrolment'] = 'LiqPay enrolment messages';
$string['nocost'] = 'There is no cost associated with enrolling in this course!';
$string['liqpay:config'] = 'Configure LiqPay enrol instances';
$string['liqpay:manage'] = 'Manage enrolled users';
$string['liqpay:unenrol'] = 'Unenrol users from course';
$string['liqpay:unenrolself'] = 'Unenrol self from the course';
$string['liqpayaccepted'] = 'LiqPay payments accepted';
$string['pluginname'] = 'LiqPay';
$string['pluginname_desc'] = 'The LiqPay module allows you to set up paid courses.  If the cost for any course is zero, then students are not asked to pay for entry.  There is a site-wide cost that you set here as a default for the whole site and then a course setting that you can set for each course individually. The course cost overrides the site cost.';
$string['privacy:metadata:enrol_liqpay:enrol_liqpay'] = 'Information about the LiqPay transactions for LiqPay enrolments.';
$string['privacy:metadata:enrol_liqpay:enrol_liqpay:business'] = 'Email address or LiqPay account ID of the payment recipient (that is, the merchant).';
$string['privacy:metadata:enrol_liqpay:enrol_liqpay:courseid'] = 'The ID of the course that is sold.';
$string['privacy:metadata:enrol_liqpay:enrol_liqpay:instanceid'] = 'The ID of the enrolment instance in the course.';
$string['privacy:metadata:enrol_liqpay:enrol_liqpay:item_name'] = 'The full name of the course that its enrolment has been sold.';
$string['privacy:metadata:enrol_liqpay:enrol_liqpay:description'] = 'A note that was entered by the buyer in LiqPay website payments note field.';
$string['privacy:metadata:enrol_liqpay:enrol_liqpay:currency_debit'] = 'Currency of the buyer payment.';
$string['privacy:metadata:enrol_liqpay:enrol_liqpay:liqpay_order_id'] = 'LiqPay inetrnal order ID.';
$string['privacy:metadata:enrol_liqpay:enrol_liqpay:payment_status'] = 'The status of the payment.';
$string['privacy:metadata:enrol_liqpay:enrol_liqpay:payment_type'] = 'Holds whether the payment was funded with an eCheck (echeck), or was funded with LiqPay balance, credit card, or instant transfer (instant).';
$string['privacy:metadata:enrol_liqpay:enrol_liqpay:end_date'] = 'Transaction end date.';
$string['privacy:metadata:enrol_liqpay:enrol_liqpay:create_date'] = 'Transaction create date.';
$string['privacy:metadata:enrol_liqpay:enrol_liqpay:err_code'] = 'Transaction error code.';
$string['privacy:metadata:enrol_liqpay:enrol_liqpay:acq_id'] = 'An Equirer ID.';
$string['privacy:metadata:enrol_liqpay:enrol_liqpay:receiver_email'] = 'Primary email address of the payment recipient (that is, the merchant).';
$string['privacy:metadata:enrol_liqpay:enrol_liqpay:receiver_id'] = 'Unique LiqPay account ID of the payment recipient (i.e., the merchant).';
$string['privacy:metadata:enrol_liqpay:enrol_liqpay:commission_credit'] = 'Amount of money charged on payment from receiver.';
$string['privacy:metadata:enrol_liqpay:enrol_liqpay:timeupdated'] = 'The time of Moodle being notified by LiqPay about the payment.';
$string['privacy:metadata:enrol_liqpay:enrol_liqpay:payment_id'] = 'The LiqPay payment ID (equal to transaction ID)';
$string['privacy:metadata:enrol_liqpay:enrol_liqpay:userid'] = 'The ID of the user who bought the course enrolment.';
$string['privacy:metadata:enrol_liqpay:liqpay_com'] = 'The LiqPay enrolment plugin transmits user data from Moodle to the LiqPay website.';
$string['privacy:metadata:enrol_liqpay:liqpay_com:address'] = 'Address of the user who is buying the course.';
$string['privacy:metadata:enrol_liqpay:liqpay_com:city'] = 'City of the user who is buying the course.';
$string['privacy:metadata:enrol_liqpay:liqpay_com:country'] = 'Country of the user who is buying the course.';
$string['privacy:metadata:enrol_liqpay:liqpay_com:custom'] = 'A hyphen-separated string that contains ID of the user (the buyer), ID of the course, ID of the enrolment instance.';
$string['privacy:metadata:enrol_liqpay:liqpay_com:email'] = 'Email address of the user who is buying the course.';
$string['privacy:metadata:enrol_liqpay:liqpay_com:first_name'] = 'First name of the user who is buying the course.';
$string['privacy:metadata:enrol_liqpay:liqpay_com:last_name'] = 'Last name of the user who is buying the course.';
$string['privacy:metadata:enrol_liqpay:liqpay_com:os0'] = 'Full name of the buyer.';
$string['processexpirationstask'] = 'LiqPay enrolment send expiry notifications task';
$string['sendpaymentbutton'] = 'Send payment via LiqPay';
$string['status'] = 'Allow LiqPay enrolments';
$string['status_desc'] = 'Allow users to use LiqPay to enrol into a course by default.';
$string['transactions'] = 'LiqPay transactions';
$string['unenrolselfconfirm'] = 'Do you really want to unenrol yourself from course "{$a}"?';
$string['currencydoesnotmatch'] = 'LiqPay: currency does not match course settings, received (currency_debit): "{$a}"';
$string['repeatedtransaction'] = 'LiqPay: transaction {$a} is being repeated!';
$string['nouser'] = 'LiqPay: user {$a} doesn\'t exist';
$string['nocourse'] = 'LiqPay: course $data->courseid doesn\'t exist';
$string['paidnotenough'] = 'LiqPay: Amount paid is not enough {$a}';
$string['unsuccesspayment'] = 'LiqPay: payment attempt was unsuccessfull. Reason: {$a} Try again!';