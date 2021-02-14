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
 * Strings for component 'enrol_liqpay', language 'uk'.
 *
 * @package    enrol_liqpay
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['assignrole'] = 'Надати роль';
$string['publickey'] = 'LiqPay public key';
$string['publickey_desc'] = 'The public key of your business LiqPay account';
$string['privatekey'] = 'LiqPay private key';
$string['privatekey_desc'] = 'The private key of your business LiqPay account';
$string['cost'] = 'Вартість зарахування';
$string['costerror'] = 'Вартість зарахування не є числом';
$string['costorkey'] = 'Будь-ласа, виберіть один з наступних методів зарахування на курс.';
$string['currency'] = 'Валюта';
$string['defaultrole'] = 'Роль по-замовчуванню';
$string['defaultrole_desc'] = 'Виберіть роль що буде надана користувачу під час зарахування засобами LiqPay';
$string['enrolenddate'] = 'Кінцева дата';
$string['enrolenddate_help'] = 'Якщо увімкнено, користувачі можуть бути зараховані лише до цієї дати';
$string['enrolenddaterror'] = 'Кінцева дата зарахування не може бути раніше початкової дати';
$string['enrolperiod'] = 'Тривальість зарахування';
$string['enrolperiod_desc'] = 'Тривальість по-замовчуванню, впродовж якого зарахування є дійсним. Якщо вказано нуль - немає обмеження тривалості зарахування.';
$string['enrolperiod_help'] = 'Length of time that the enrolment is valid, starting with the moment the user is enrolled. If disabled, the enrolment duration will be unlimited.';
$string['enrolstartdate'] = 'Дата початку';
$string['enrolstartdate_help'] = 'Якщо увімкнено - користувачі можуть бути зараховані лише починаючи з вказаної дати.';
$string['errdisabled'] = 'The LiqPay enrolment plugin is disabled and does not handle payment notifications.';
$string['erripninvalid'] = 'Повідомлення про миттєві полати не були верифіковані LiqPay.';
$string['errliqpayconnect'] = 'Неможлиов зєднатися з {$a->url} щоб перевірити статус миттєвої оплати : {$a->result}';
$string['expiredaction'] = 'Дія якщо термін зарахування добігає кінця';
$string['expiredaction_help'] = 'Select action to carry out when user enrolment expires. Please note that some user data and settings are purged from course during course unenrolment.';
$string['mailadmins'] = 'Повідомити адміна';
$string['mailstudents'] = 'Повідомити студентів';
$string['mailteachers'] = 'Повідомити викладачів';
$string['messageprovider:liqpay_enrolment'] = 'Повідомлення про зарахування через LiqPay';
$string['nocost'] = 'Не вказано вартість зарахування в цьому курсі!';
$string['liqpay:config'] = 'Налаштувати зарахування через LiqPay';
$string['liqpay:manage'] = 'Керування зарахованими користувачами';
$string['liqpay:unenrol'] = 'Відрахувати користувачів з курсу';
$string['liqpay:unenrolself'] = 'Відрахувати себе з курсу';
$string['liqpayaccepted'] = 'LiqPay платіж зараховано';
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
$string['sendpaymentbutton'] = 'Надіслати оплату через LiqPay';
$string['status'] = 'Дозволити зарахування через LiqPay';
$string['status_desc'] = 'Allow users to use LiqPay to enrol into a course by default.';
$string['transactions'] = 'Транзакції LiqPay';
$string['unenrolselfconfirm'] = 'Чи ви дійсно бажаєте відрахувати себе з курсу "{$a}"?';
$string['currencydoesnotmatch'] = 'LiqPay: currency does not match course settings, received (currency_debit): "{$a}"';
$string['repeatedtransaction'] = 'LiqPay: транзакцію {$a} буде повторено!';
$string['nouser'] = 'LiqPay: користувач {$a} не існує';
$string['nocourse'] = 'LiqPay: курс {$a} не існує';
$string['paidnotenough'] = 'LiqPay: Оплачена сума є недостатньою {$a}';
$string['unsuccesspayment'] = 'LiqPay: спроба оплати була неуспішною. Підстава: {$a} Спробуйте ще раз!';