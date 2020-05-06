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
 * This file is needed when restoring a course.
 * See https://docs.moodle.org/dev/Restore_API
 * See https://docs.moodle.org/dev/Restore_2.0_for_developers
 *
 * @package    block_acclaim
 * @copyright  2020 Credly, Inc. <http://youracclaim.com>
 * @license    https://opensource.org/licenses/MIT
 */
class restore_acclaim_block_structure_step extends restore_structure_step {
    /**
     * Restore from XML created by backup_acclaim_structure_step.
     *
     * @return restore_path_element[]
     */
     protected function define_structure() {
        // 'course' here causes the restore process to call process_course().
        return [new restore_path_element('course', '/block/acclaim_course')];
    }

    /**
     * Restore a row to the block_acclaim_courses table. This method is triggered by the call to
     * restore_path_element() in define_structure(), and it decodes XML created by backup_acclaim_structure_step.
     *
     * @param object $data - Data from the XML.
     */
    protected function process_course($data) {
        global $DB;
        $courseid = $this->task->get_courseid();

        $data = (array)$data;
        $oldid = $data['id'];
        unset($data['id']);
        $data['courseid'] = $courseid;

        $newid = $DB->get_field('block_acclaim_courses', 'id', ['courseid' => $courseid]);
        if ($newid) {
            $DB->update_record('block_acclaim_courses', ['id' => $newid] + $data);
        } else {
            // Data already exists for this course (the course is being overwritten). Update it.
            $newid = $DB->insert_record('block_acclaim_courses', $data);
        }

        // Save id mapping for restoring associated events.
        $this->set_mapping('block_acclaim_courses', $oldid, $newid);
    }
}
