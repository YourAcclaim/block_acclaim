<?php
// This file is part of Credly's Acclaim Moodle Block Plugin
//
// Credly's Acclaim Moodle Block Plugin is free software: you can redistribute it
// and/or modify it under the terms of the MIT license as published by
// the Free Software Foundation.
//
// This script is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// MIT License for more details.
//
// You can find the GNU General Public License at <https://opensource.org/licenses/MIT>.

/**
 * Credly's Acclaim Moodle Block Plugin
 * Credly: http://youracclaim.com
 * Moodle: http://moodle.org/
 *
 * Issue badge observer.
 *
 * @package    block_acclaim
 * @copyright  2020 Credly, Inc. <http://youracclaim.com>
 * @license    https://opensource.org/licenses/MIT
 */
namespace block_acclaim\event;
defined('MOODLE_INTERNAL') || die();

class block_acclaim_create_pending_badge extends \core\event\base {
    /**
     * Initialize the block.
     */
    protected function init() {
        $this->data['crud'] = 'c'; // c(reate), r(ead), u(pdate), d(elete)
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'issued_badge';
    }

    /**
     * Get the display name for the block
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventbadgeissued', 'acclaim');
    }
 
    /**
     * Get the description of a badge issue event.
     *
     * @return string
     */
    public function get_description() {
        return "User {$this->userid} has been issued a badge with id {$this->objectid}.";
    }

    /**
     * Get the URL for this block.
     *
     * @return string
     */
    public function get_url() {
        return new \moodle_url('/block/acclaim', array('parameter' => 'id', $this->objectid));
    }
}
