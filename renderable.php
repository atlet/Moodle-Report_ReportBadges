<?php

/**
 * ReportBadges report renderable.
 *
 * @package    report_reportbadges
 * @copyright  2014 Andraž Prinčič <atletek@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/report/reportbadges/classes/report_reportbadges_list_users_badges_number.php');
require_once($CFG->dirroot . '/report/reportbadges/classes/report_reportbadges_list_users_badges.php');
require_once($CFG->dirroot . '/report/reportbadges/classes/report_reportbadges_list_badges_users_number.php');

class report_reportbadges implements renderable {

    /** @var moodle_url url of report page */
    public $url;

    /** @var string selected report list to display */
    public $reporttype = null;
    public $courseid;
    public $table;
    public $reportyear;
    private $whereOptions = array();
    private $whereParameters = array();

    /**
     * Constructor.
     *
     * @param moodle_url|string $url (optional) page url.
     * @param string $reporttype (optional) which report list to display.
     */
    public function __construct($courseid = NULL, $url = "", $reporttype = "", $reportyear = "") {

        global $PAGE;

        $this->whereOptions[] = 't.criteriatype <> 0';

        $this->courseid = $courseid;

        $this->whereOptions[] = 'en.courseid = :courseid';
        $this->whereParameters['courseid'] = $courseid;
        
        $this->whereOptions[] = 'd.visible = 1';

        // Use page url if empty.
        if (empty($url)) {
            $this->url = new moodle_url($PAGE->url);
        } else {
            $this->url = new moodle_url($url);
        }

        if (empty($reporttype)) {
            $rtypes = $this->getAvailablereports();
            if (!empty($rtypes)) {
                reset($rtypes);
                $reporttype = key($rtypes);
            } else {
                $reporttype = null;
            }
        }

        if (empty($reportyear) || $reportyear == 0) {
            $this->reportyear = '';
        } else {
            $this->reportyear = $reportyear;

            $this->whereOptions[] = 'YEAR(FROM_UNIXTIME(d.dateissued)) = :reportyear';
            $this->whereParameters['reportyear'] = $reportyear;
        }

        $this->reporttype = $reporttype;
    }

    public function getAvailablereports() {
        return array(
            1 => get_string('listusersandbadgescount', 'report_reportbadges'),
            2 => get_string('listusersandbadges', 'report_reportbadges'),
            3 => get_string('listbadgesanduserscount', 'report_reportbadges'),
        );
    }

    public function show_table_list_badges_users_number() {
        global $CFG;
        
        $fields = 'b.id, b.name AS badgename, COUNT(u.username) AS userscount';
        
        if (file_exists($CFG->dirroot . '/local/badgecerts/lib.php')) {
            $fields .= ', b.certid';
        }
        
        $this->table = new list_badges_users_number('report_log');
        $this->table->set_sql($fields,
                "{badge_issued} AS d
          JOIN {badge} AS b ON d.badgeid = b.id
          JOIN {user} AS u ON d.userid = u.id
          JOIN {user_enrolments} AS ue ON ue.userid = u.id
          JOIN {enrol} AS en ON en.id = ue.enrolid
          JOIN {badge_criteria} AS t ON b.id = t.badgeid", implode(' AND ', $this->whereOptions),
                $this->whereParameters);
        $this->table->define_baseurl($this->url);
        $this->table->is_downloadable(false);
        $this->table->show_download_buttons_at(array(TABLE_P_BOTTOM));
        $this->table->out(25, true);
    }

    public function show_table_list_users_badges() {
        global $CFG;
        
        $fields = 'u.id, ' . get_all_user_name_fields(true, 'u') . ', CONCAT(u.firstname, \' \', u.lastname) AS fullname, u.username, GROUP_CONCAT(CONCAT(b.name) SEPARATOR \';\') AS badgename, GROUP_CONCAT(CONCAT(b.id) SEPARATOR \';\') AS badgenameid ';
        
        if (file_exists($CFG->dirroot . '/local/badgecerts/lib.php')) {
            $fields .= ', b.certid';
        }
        
        $this->table = new list_users_badges('list_users_badges');
        $this->table->set_sql($fields,
                "{badge_issued} AS d
          JOIN {badge} AS b ON d.badgeid = b.id
          JOIN {user} AS u ON d.userid = u.id
          JOIN {user_enrolments} AS ue ON ue.userid = u.id
          JOIN {enrol} AS en ON en.id = ue.enrolid
          JOIN {badge_criteria} AS t ON b.id = t.badgeid", implode(' AND ', $this->whereOptions), $this->whereParameters);
        $this->table->define_baseurl($this->url);
        $this->table->is_downloadable(false);
        $this->table->show_download_buttons_at(array(TABLE_P_BOTTOM));
        $this->table->out(25, true);
    }

    public function show_table_list_users_badges_number() {
        $this->table = new list_users_badges_number('report_log');
        $this->table->set_sql('u.id, ' . get_all_user_name_fields(true, 'u') . ', CONCAT(u.firstname, \' \', u.lastname) AS fullname, u.username, COUNT(*) AS badgescount',
                "{badge_issued} AS d
          JOIN {badge} AS b ON d.badgeid = b.id
          JOIN {user} AS u ON d.userid = u.id
          JOIN {user_enrolments} AS ue ON ue.userid = u.id
          JOIN {enrol} AS en ON en.id = ue.enrolid
          JOIN {badge_criteria} AS t ON b.id = t.badgeid", implode(' AND ', $this->whereOptions),
                $this->whereParameters);
        $this->table->define_baseurl($this->url);
        $this->table->is_downloadable(false);
        $this->table->show_download_buttons_at(array(TABLE_P_BOTTOM));
        $this->table->out(25, true);
    }

}
