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
 * This file keeps track of upgrades to the liqpay enrolment plugin
 *
 * @package    enrol_liqpay
 * @copyright  2020 Andrii Semenets
 * @author     Andrii Semenets
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installation to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the methods of database_manager class
//
// Please do not forget to use upgrade_set_timeout()
// before any action that may take longer time to finish.

defined('MOODLE_INTERNAL') || die();

function xmldb_enrol_liqpay_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2020020313) {

        // add userenrollmentid to table enrol_liqpay.
        $table = new xmldb_table('enrol_liqpay');
        $field = new xmldb_field('userenrollmentid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null, null);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // set userenrollmentid value for existing enrollments
        $sql = "SELECT id, userid, instanceid, timeupdated 
                FROM mdl_enrol_liqpay 
                WHERE timeupdated in (SELECT MAX(timeupdated) 
                                      FROM mdl_enrol_liqpay 
                                      WHERE payment_status = 'success' 
                                      GROUP BY userid, instanceid);";
        if ($liqpayrecords = $DB->get_records_sql($sql, array())) {
            foreach ($liqpayrecords as $lp_record) {
                if ($enrollmentid = $DB->get_field('user_enrolments', 'id', array('enrolid'=>$lp_record->instanceid, 'userid'=>$lp_record->userid))) {
                    $DB->set_field('enrol_liqpay', 'userenrollmentid', $enrollmentid, array('id' => $lp_record->id));
                }
            }
        }
        // liqpay savepoint reached.
        upgrade_plugin_savepoint(true, 2020020313, 'enrol', 'liqpay');
    }

    // Automatically generated Moodle v3.7.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.8.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
