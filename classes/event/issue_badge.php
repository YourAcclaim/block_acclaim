<?php

namespace block_acclaim\event;
defined('MOODLE_INTERNAL') || die();

class issue_badge extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'c'; // c(reate), r(ead), u(pdate), d(elete)
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'issued_badge';
    }

    public static function get_name() {
        return get_string('eventbadgeissued', 'acclaim');
    }
 
    public function get_description() {
        return "User {$this->userid} has been issued a badge with id {$this->objectid}.";
    }

    public function get_url() {
        return new \moodle_url('/block/acclaim', array('parameter' => 'id', $this->objectid));
    }
 
    //public function get_legacy_logdata() {
      // Override if you are migrating an add_to_log() call.
      //  return array($this->courseid, 'acclaim', 'LOGACTION',
        //    '...........',
          //  $this->objectid, $this->contextinstanceid);
//    }
 
    //public static function get_legacy_eventname() {
        // Override ONLY if you are migrating events_trigger() call.
      //  return 'MYPLUGIN_OLD_EVENT_NAME';
    //}
 
   // protected function get_legacy_eventdata() {
        // Override if you migrating events_trigger() call.
     //   $data = new \stdClass();
     //   $data->id = $this->objectid;
     //   $data->userid = $this->relateduserid;
     //   return $data;
    //}
}
