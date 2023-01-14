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
 * This file is needed when backing up a course.
 * See https://docs.moodle.org/dev/Backup_API
 * See https://docs.moodle.org/dev/Backup_2.0_for_developers
 *
 * @package    block_acclaim
 * @copyright  2020 Credly, Inc. <http://youracclaim.com>
 * @license    https://opensource.org/licenses/MIT
 */
class backup_acclaim_structure_step extends backup_block_structure_step {
    /**
     * Generate XML in this form:
     * <block id="29" blockname="acclaim" contextid="53">
     *   <acclaim_course id="2">
     *     <courseid>4</courseid>
     *     <badgeid>a11b6c16-7ee6-4d6e-422e-8f734afbe989</badgeid>
     *     <expiration>0</expiration>
     *     <badgename>My Badge</badgename>
     *   </acclaim_course>
     * </block>
     */
    protected function define_structure() {
        if (backup::VAR_COURSEID) {
            $acclaim = new backup_nested_element(
                'acclaim_course',
                array('id'),
                array('courseid', 'badgeid', 'expiration', 'badgename', 'badgeurl')
            );

            $acclaim->set_source_table('block_acclaim_courses', array('courseid' => backup::VAR_COURSEID));

            return $this->prepare_block_structure($acclaim);
        }
    }
}
