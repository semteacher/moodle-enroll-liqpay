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
 * Privacy Subsystem implementation for enrol_liqpay.
 *
 * @package    enrol_liqpay
 * @category   privacy
 * @copyright  2018 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_liqpay\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

/**
 * Privacy Subsystem implementation for enrol_liqpay.
 *
 * @copyright  2018 Shamim Rezaie <shamim@moodle.com> (PayPal)
 * @copyright  2019 Andrii Semenets <semteacher@gmail.com> (PayPal)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        // Transactions store user data.
        \core_privacy\local\metadata\provider,

        // The liqpay enrolment plugin contains user's transactions.
        \core_privacy\local\request\plugin\provider,

        // This plugin is capable of determining which users have data within it.
        \core_privacy\local\request\core_userlist_provider {

    /**
     * Returns meta data about this system.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {
        $collection->add_external_location_link(
            'liqpay.com',
            [
                'os0'        => 'privacy:metadata:enrol_liqpay:liqpay_com:os0',
                'custom'     => 'privacy:metadata:enrol_liqpay:liqpay_com:custom',
                'first_name' => 'privacy:metadata:enrol_liqpay:liqpay_com:first_name',
                'last_name'  => 'privacy:metadata:enrol_liqpay:liqpay_com:last_name',
                'address'    => 'privacy:metadata:enrol_liqpay:liqpay_com:address',
                'city'       => 'privacy:metadata:enrol_liqpay:liqpay_com:city',
                'email'      => 'privacy:metadata:enrol_liqpay:liqpay_com:email',
                'country'    => 'privacy:metadata:enrol_liqpay:liqpay_com:country',
            ],
            'privacy:metadata:enrol_liqpay:liqpay_com'
        );

        // The enrol_liqpay has a DB table that contains user data.
        $collection->add_database_table(
                'enrol_liqpay',
                [
                    'business'            => 'privacy:metadata:enrol_liqpay:enrol_liqpay:business',
                    'receiver_email'      => 'privacy:metadata:enrol_liqpay:enrol_liqpay:receiver_email',
                    'receiver_id'         => 'privacy:metadata:enrol_liqpay:enrol_liqpay:receiver_id',
                    'item_name'           => 'privacy:metadata:enrol_liqpay:enrol_liqpay:item_name',
                    'courseid'            => 'privacy:metadata:enrol_liqpay:enrol_liqpay:courseid',
                    'userid'              => 'privacy:metadata:enrol_liqpay:enrol_liqpay:userid',
                    'instanceid'          => 'privacy:metadata:enrol_liqpay:enrol_liqpay:instanceid',
                    'description'         => 'privacy:metadata:enrol_liqpay:enrol_liqpay:description',
                    'commission_credit'   => 'privacy:metadata:enrol_liqpay:enrol_liqpay:commission_credit',
                    'currency_debit'      => 'privacy:metadata:enrol_liqpay:enrol_liqpay:currency_debit',
                    'payment_status'      => 'privacy:metadata:enrol_liqpay:enrol_liqpay:payment_status',
                    'pending_reason'      => 'privacy:metadata:enrol_liqpay:enrol_liqpay:pending_reason',
                    'reason_code'         => 'privacy:metadata:enrol_liqpay:enrol_liqpay:reason_code',
                    'payment_id'          => 'privacy:metadata:enrol_liqpay:enrol_liqpay:payment_id',
                    'parent_txn_id'       => 'privacy:metadata:enrol_liqpay:enrol_liqpay:parent_txn_id',
                    'payment_type'        => 'privacy:metadata:enrol_liqpay:enrol_liqpay:payment_type',
                    'timeupdated'         => 'privacy:metadata:enrol_liqpay:enrol_liqpay:timeupdated'
                ],
                'privacy:metadata:enrol_liqpay:enrol_liqpay'
        );

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        $contextlist = new contextlist();

        // Values of ep.receiver_email and ep.business are already normalised to lowercase characters by PayPal,
        // therefore there is no need to use LOWER() on them in the following query.
        $sql = "SELECT ctx.id
                  FROM {enrol_liqpay} ep
                  JOIN {enrol} e ON ep.instanceid = e.id
                  JOIN {context} ctx ON e.courseid = ctx.instanceid AND ctx.contextlevel = :contextcourse
                  JOIN {user} u ON u.id = ep.userid OR LOWER(u.email) = ep.receiver_email OR LOWER(u.email) = ep.business
                 WHERE u.id = :userid";
        $params = [
            'contextcourse' => CONTEXT_COURSE,
            'userid'        => $userid,
        ];

        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!$context instanceof \context_course) {
            return;
        }

        // Values of ep.receiver_email and ep.business are already normalised to lowercase characters by PayPal,
        // therefore there is no need to use LOWER() on them in the following query.
        $sql = "SELECT u.id
                  FROM {enrol_liqpay} ep
                  JOIN {enrol} e ON ep.instanceid = e.id
                  JOIN {user} u ON ep.userid = u.id OR LOWER(u.email) = ep.receiver_email OR LOWER(u.email) = ep.business
                 WHERE e.courseid = :courseid";
        $params = ['courseid' => $context->instanceid];

        $userlist->add_from_sql('id', $sql, $params);
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        // Values of ep.receiver_email and ep.business are already normalised to lowercase characters by PayPal,
        // therefore there is no need to use LOWER() on them in the following query.
        $sql = "SELECT ep.*
                  FROM {enrol_liqpay} ep
                  JOIN {enrol} e ON ep.instanceid = e.id
                  JOIN {context} ctx ON e.courseid = ctx.instanceid AND ctx.contextlevel = :contextcourse
                  JOIN {user} u ON u.id = ep.userid OR LOWER(u.email) = ep.receiver_email OR LOWER(u.email) = ep.business
                 WHERE ctx.id {$contextsql} AND u.id = :userid
              ORDER BY e.courseid";

        $params = [
            'contextcourse' => CONTEXT_COURSE,
            'userid'        => $user->id,
            'emailuserid'   => $user->id,
        ];
        $params += $contextparams;

        // Reference to the course seen in the last iteration of the loop. By comparing this with the current record, and
        // because we know the results are ordered, we know when we've moved to the PayPal transactions for a new course
        // and therefore when we can export the complete data for the last course.
        $lastcourseid = null;

        $strtransactions = get_string('transactions', 'enrol_liqpay');
        $transactions = [];
        $liqpayrecords = $DB->get_recordset_sql($sql, $params);
        foreach ($liqpayrecords as $liqpayrecord) {
            if ($lastcourseid != $liqpayrecord->courseid) {
                if (!empty($transactions)) {
                    $coursecontext = \context_course::instance($liqpayrecord->courseid);
                    writer::with_context($coursecontext)->export_data(
                            [$strtransactions],
                            (object) ['transactions' => $transactions]
                    );
                }
                $transactions = [];
            }

            $transaction = (object) [
                'receiver_id'         => $liqpayrecord->receiver_id,
                'item_name'           => $liqpayrecord->item_name,
                'userid'              => $liqpayrecord->userid,
                'description'         => $liqpayrecord->description,
                'commission_credit'   => $liqpayrecord->commission_credit,
                'amount_debit'        => $liqpayrecord->amount_debit,
                'currency_debit'      => $liqpayrecord->currency_debit,
                'paytype'             => $liqpayrecord->paytype,
                'action'              => $liqpayrecord->action,
                'payment_status'      => $liqpayrecord->payment_status,
                'pending_reason'      => $liqpayrecord->pending_reason,
                'reason_code'         => $liqpayrecord->reason_code,
                'payment_id'          => $liqpayrecord->payment_id,
                'parent_txn_id'       => $liqpayrecord->parent_txn_id,
                'payment_type'        => $liqpayrecord->payment_type,
                'timeupdated'         => \core_privacy\local\request\transform::datetime($liqpayrecord->timeupdated),
            ];
            if ($liqpayrecord->userid == $user->id) {
                $transaction->userid = $liqpayrecord->userid;
            }
            if ($liqpayrecord->business == \core_text::strtolower($user->email)) {
                $transaction->business = $liqpayrecord->business;
            }
            if ($liqpayrecord->receiver_email == \core_text::strtolower($user->email)) {
                $transaction->receiver_email = $liqpayrecord->receiver_email;
            }

            $transactions[] = $liqpayrecord;

            $lastcourseid = $liqpayrecord->courseid;
        }
        $liqpayrecords->close();

        // The data for the last activity won't have been written yet, so make sure to write it now!
        if (!empty($transactions)) {
            $coursecontext = \context_course::instance($liqpayrecord->courseid);
            writer::with_context($coursecontext)->export_data(
                    [$strtransactions],
                    (object) ['transactions' => $transactions]
            );
        }
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if (!$context instanceof \context_course) {
            return;
        }

        $DB->delete_records('enrol_liqpay', array('courseid' => $context->instanceid));
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();

        $contexts = $contextlist->get_contexts();
        $courseids = [];
        foreach ($contexts as $context) {
            if ($context instanceof \context_course) {
                $courseids[] = $context->instanceid;
            }
        }

        list($insql, $inparams) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED);

        $select = "userid = :userid AND courseid $insql";
        $params = $inparams + ['userid' => $user->id];
        $DB->delete_records_select('enrol_liqpay', $select, $params);

        // We do not want to delete the payment record when the user is just the receiver of payment.
        // In that case, we just delete the receiver's info from the transaction record.

        $select = "business = :business AND courseid $insql";
        $params = $inparams + ['business' => \core_text::strtolower($user->email)];
        $DB->set_field_select('enrol_liqpay', 'business', '', $select, $params);

        $select = "receiver_email = :receiver_email AND courseid $insql";
        $params = $inparams + ['receiver_email' => \core_text::strtolower($user->email)];
        $DB->set_field_select('enrol_liqpay', 'receiver_email', '', $select, $params);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist       $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();

        if ($context->contextlevel != CONTEXT_COURSE) {
            return;
        }

        $userids = $userlist->get_userids();

        list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);

        $params = ['courseid' => $context->instanceid] + $userparams;

        $select = "courseid = :courseid AND userid $usersql";
        $DB->delete_records_select('enrol_liqpay', $select, $params);

        // We do not want to delete the payment record when the user is just the receiver of payment.
        // In that case, we just delete the receiver's info from the transaction record.

        $select = "courseid = :courseid AND business IN (SELECT LOWER(email) FROM {user} WHERE id $usersql)";
        $DB->set_field_select('enrol_liqpay', 'business', '', $select, $params);

        $select = "courseid = :courseid AND receiver_email IN (SELECT LOWER(email) FROM {user} WHERE id $usersql)";
        $DB->set_field_select('enrol_liqpay', 'receiver_email', '', $select, $params);
    }
}
