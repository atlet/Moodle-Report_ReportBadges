<?php

/**
 * ReportBadges report renderable.
 *
 * @package    report_reportbadges
 * @copyright  2014 Andraž Prinčič <atletek@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

class report_reportbadges implements renderable {
    
    /** @var moodle_url url of report page */
    public $url;
    
    /** @var string selected report list to display */
    public $reporttype = null;
    
    public $courseid;
    
    
    /**
     * Constructor.
     *
     * @param moodle_url|string $url (optional) page url.
     * @param string $reporttype (optional) which report list to display.
     */
    public function __construct($courseid = NULL, $url = "", $reporttype = "") {

        global $PAGE;                
        
        $this->courseid = $courseid;
        
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
        
        $this->reporttype = $reporttype;
        
    }
    
    public function getAvailablereports() {
        return array(
        1 => get_string('listusersandbadgescount', 'report_reportbadges'),
        2 => get_string('listusersandbadges', 'report_reportbadges'),
        3 => get_string('listbadgesanduserscount', 'report_reportbadges'), 
        4 => get_string('listubadgesandusers', 'report_reportbadges'),
        5 => get_string('listbyyear', 'report_reportbadges'),
    );
    }
    
}