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
* @package    block_acclaim
* @copyright  2014 Yancy Ribbens <yancy.ribbens@gmail.com>
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace block_acclaim\event;
defined('MOODLE_INTERNAL') || die();

class block_acclaim_issue_badge extends \core\event\base {
    protected function init()
    {
        $this->data['crud'] = 'c'; // c(reate), r(ead), u(pdate), d(elete)
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'issued_badge';
    }

    public static function get_name()
    {
        return get_string('eventbadgeissued', 'acclaim');
    }
 
    public function get_description()
    {
        return "User {$this->userid} has been issued a badge with id {$this->objectid}.";
    }

    public function get_url()
    {
        return new \moodle_url('/block/acclaim', array('parameter' => 'id', $this->objectid));
    }
}
