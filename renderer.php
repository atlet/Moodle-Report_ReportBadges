<?php

/**
 * ReportBadges report renderer.
 *
 * @package    report_reportbadges
 * @copyright  2014 Andraž Prinčič <atletek@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

class report_reportbadges_renderer extends plugin_renderer_base {

    protected function render_report_reportbadges(report_reportbadges $reportbadges) {
        $this->report_selector_form($reportbadges);
    }

    /**
     * This function is used to generate and display selector form
     *
     * @param report_reportbadges $reportbadges reportbadges report.
     */
    public function report_selector_form(report_reportbadges $reportbadges) {

        global $DB;

        $yearsDB = $DB->get_records_sql('SELECT DISTINCT YEAR(FROM_UNIXTIME(d.dateissued)) as yearissued FROM {badge_issued} AS d JOIN {badge} AS b ON d.badgeid = b.id', array('courseid' => $reportbadges->courseid));

        $years[] = get_string('selecallyears', 'report_reportbadges');

        foreach ($yearsDB as $value) {
            $years[$value->yearissued] = $value->yearissued;
        }

        echo html_writer::start_tag('form',
                array('class' => 'reportbadgesselecform', 'action' => $reportbadges->url, 'method' => 'get'));
        echo html_writer::start_div();

        echo html_writer::empty_tag('input',
                array('type' => 'hidden', 'name' => 'id', 'value' => $reportbadges->courseid));

        echo html_writer::label(get_string('selectreporttype', 'report_reportbadges'), 'menureader', false);
        echo html_writer::select($reportbadges->getAvailablereports(), 'reporttype', $reportbadges->reporttype, false);

        echo html_writer::label(get_string('selectreportyear', 'report_reportbadges'), 'menureader', false);
        echo html_writer::select($years, 'reportyear', $reportbadges->reportyear, false);

        echo html_writer::empty_tag('input',
                array('type' => 'submit', 'value' => get_string('showreport', 'report_reportbadges')));

        echo html_writer::end_div();
        echo html_writer::end_tag('form');
    }

}
