<?php

/**
 * ReportBadges table for displaying list of users with number of badges.
 *
 * @package    report_reportbadges
 * @copyright  2014 Andraž Prinčič <atletek@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/tablelib.php");

class list_users_badges_number extends table_sql {

    public function __construct($uniqueid) {
        parent::__construct($uniqueid);

        $this->set_attribute('class', 'reportlog generaltable generalbox');

        $this->define_columns(array('fullname', 'username', 'badgescount'));
        $this->define_headers(array(
            get_string('fullname', 'report_reportbadges'),
            get_string('username', 'report_reportbadges'),
            get_string('badgescount', 'report_reportbadges')
                )
        );
        $this->collapsible(false);
        $this->sortable(true);
        $this->pageable(true);
    }

    function other_cols($colname, $value) {
        
    }

    function query_db($pagesize, $useinitialsbar = true) {
        global $DB;
        if (!$this->is_downloading()) {
            if ($this->countsql === NULL) {
                $this->countsql = 'SELECT COUNT(1) FROM (SELECT ' . $this->sql->fields . '  FROM ' . $this->sql->from . ' WHERE ' . $this->sql->where . ' GROUP BY u.id) AS sq; ';
                $this->countparams = $this->sql->params;
            }
            $grandtotal = $DB->count_records_sql($this->countsql, $this->countparams);
            if ($useinitialsbar && !$this->is_downloading()) {
                $this->initialbars($grandtotal > $pagesize);
            }

            list($wsql, $wparams) = $this->get_sql_where();
            if ($wsql) {
                $this->countsql = 'SELECT COUNT(1) FROM (SELECT ' . $this->sql->fields . '  FROM ' . $this->sql->from . ' WHERE ' . $this->sql->where . ' AND ' . $wsql . ' GROUP BY u.id) AS sq; ';
                $this->countparams = array_merge($this->countparams, $wparams);

                $this->sql->where .= ' AND ' . $wsql;
                $this->sql->params = array_merge($this->sql->params, $wparams);

                $this->sql->where .= ' GROUP BY u.id' ;
                
                $total = $DB->count_records_sql($this->countsql, $this->countparams);
            } else {
                $this->sql->where .= ' GROUP BY u.id' ;
                $total = $grandtotal;
            }

            $this->pagesize($pagesize, $total);
        }

        // Fetch the attempts
        $sort = $this->get_sql_sort();
        if ($sort) {
            $sort = "ORDER BY $sort";
        }
        $sql = "SELECT
                  {$this->sql->fields}
                  FROM {$this->sql->from}
                  WHERE {$this->sql->where}
                  {$sort}";

        if (!$this->is_downloading()) {
            $this->rawdata = $DB->get_records_sql($sql, $this->sql->params, $this->get_page_start(),
                    $this->get_page_size());
        } else {
            $this->rawdata = $DB->get_records_sql($sql, $this->sql->params);
        }
    }

}
