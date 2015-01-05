<?php

/**
 * ReportBadges index file.
 *
 * @package    report_reportbadges
 * @copyright  2014 Andraž Prinčič <atletek@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot.'/report/reportbadges/lib.php');
require_once($CFG->dirroot.'/report/reportbadges/renderable.php');

$id = optional_param('id', 0, PARAM_INT);// Course ID.
$reporttype = optional_param('reporttype', '', PARAM_INT); // Which report list to display.

$params = array();
if ($id !== 0) {
    $params['id'] = $id;
}
if ($reporttype !== 0) {
    $params['reporttype'] = $reporttype;
}

$url = new moodle_url("/report/reportbadges/index.php", $params);

$PAGE->set_url('/report/reportbadges/index.php', $params);
$PAGE->set_pagelayout('report');

$course = null;
if ($id) {
    $course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
    require_login($course);
    $context = context_course::instance($course->id);
} else {
    require_login();
    $context = context_system::instance();
    $PAGE->set_context($context);
}

require_capability('report/reportbadges:view', $context);

$output = $PAGE->get_renderer('report_reportbadges');
$submissionwidget = new report_reportbadges($id, $url, $reporttype);

echo $output->header();
echo $output->render($submissionwidget);
echo $output->footer();