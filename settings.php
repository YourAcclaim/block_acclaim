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
 * @package    block_acclaim
 * @copyright  2020 Credly, Inc. <http://youracclaim.com>
 * @license    https://opensource.org/licenses/MIT
 */
defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $urls = array(
        'https://api.youracclaim.com/v1' => get_string('production', 'block_acclaim'),
        'https://sandbox-api.youracclaim.com/v1' => get_string('sandbox', 'block_acclaim')
    );

    $settings->add(
        new admin_setting_configselect(
            'block_acclaim/url',
            get_string('setting_domain', 'block_acclaim'),
            null,
            0,
            $urls
        )
    );

    $settings->add(
        new admin_setting_configtext(
            'block_acclaim/org',
            get_string('setting_org_id', 'block_acclaim'),
            get_string('setting_org_help', 'block_acclaim', '6bb2e1c7-c66b-4d47-9301-4a6b9e792e2c'),
            null,
            PARAM_TEXT
        )
    );

    $settings->add(
        new admin_setting_configtext(
            'block_acclaim/token',
            get_string('setting_app_token', 'block_acclaim'),
            get_string('setting_app_token_help', 'block_acclaim', 'FZ9QZ4sDtEwNR7Tcv-Yi'),
            null,
            PARAM_TEXT
        )
    );
}
