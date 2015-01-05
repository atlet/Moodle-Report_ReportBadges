<?php

/**
 * ReportBadges lib file.
 *
 * @package    report_reportbadges
 * @copyright  2014 Andraž Prinčič <atletek@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function report_reportbadges_extend_navigation_course($navigation, $course, $context) {
    if (has_capability('report/reportbadges:view', $context)) {
        $url = new moodle_url('/report/reportbadges/index.php', array('id' => $course->id));
        $navigation->add(get_string('pluginname', 'report_reportbadges'), $url, navigation_node::TYPE_SETTING, null,
                null, new pix_icon('i/report', ''));
    }
}
