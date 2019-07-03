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

require_once(dirname(__FILE__).'/../../config.php');

function block_acclaim_query_acclaim_api($customUrl)
{
    $config = get_config('block_acclaim');
    $url="{$config->url}/organizations/{$config->org}/badge_templates?sort=name&filter=state::active";
    if (!is_null($customUrl)) {
    	$url = $customUrl;
    }
    $username=$config->token;
    $password = "";
    $curl = new curl;
    return block_acclaim_return_json_badges($curl, $url, $username);
}

function block_acclaim_truncate($input)
{
    $max_length = 75;

    if(strlen($input) > $max_length){
        $len = strlen($input);
        $trim_amount = ($max_length - $len);
        return substr($input,0, $trim_amount)."...";
        }

    return $input;
}

function block_acclaim_get_badge_info($course_id,$field)
{
    global $DB;
    $return_val = "";

    $course = $DB->get_record('block_acclaim', array('courseid' => $course_id));

    if(!empty($course)){
        $return_val = $course->$field;
    }

    return $return_val;
}

function block_acclaim_get_block_course($course_id)
{
    global $DB;
    $course = $DB->get_record('block_acclaim', array('courseid' => $course_id), '*', MUST_EXIST);
    return $course;
}

function block_acclaim_write_badge_to_issue($fromform)
{
    global $DB;

    $fromform = block_acclaim_update_form_with_badge_name($fromform);
    $DB->delete_records('block_acclaim',  array('courseid' => $fromform->courseid));

    return $DB->insert_record('block_acclaim', $fromform);
}

function block_acclaim_get_issue_badge_url()
{
    $block_acclaim_config = get_config('block_acclaim');

    $base_url = $block_acclaim_config->url;
    $org_id = $block_acclaim_config->org;
    $request_url = "{$base_url}/organizations/{$org_id}/badges";
    return $request_url;
}

function block_acclaim_get_request_token()
{
    $block_acclaim_config = get_config('block_acclaim');
    return $block_acclaim_config->token;
}

function block_acclaim_return_user($user_id)
{
    global $DB;
    return $DB->get_record('user', array('id'=>$user_id), '*', MUST_EXIST);
}

function block_acclaim_convert_time_stamp($timestamp)
{
    if($timestamp){
        return gmdate("Y-m-d  h:i:s a", $timestamp);
    }

    return $timestamp;
}

function block_acclaim_update_form_with_badge_name($fromform)
{
    $all_badges_names = json_decode($fromform->badgename);
    $badge_id = $fromform->badgeid;

    if(isset($all_badges_names->$badge_id)){
        $badge_name =  $all_badges_names->$badge_id;
        $fromform->badgename = $badge_name;
    }

    return $fromform;
}

function block_acclaim_create_data_array($event,$badge_id,$timestamp)
{
    $user_id = $event->relateduserid;
    $course_id = $event->courseid;
    $user = block_acclaim_return_user($user_id);
    $firstname = $user->firstname;
    $lastname = $user->lastname;
    $email = $user->email;
    $expires_at = block_acclaim_convert_time_stamp($timestamp);
    $date_time = block_acclaim_convert_time_stamp(time());

    $data = array(
        'badge_template_id' => $badge_id,
        'issued_to_first_name' => $firstname,
        'issued_to_last_name' => $lastname,
        'expires_at' => $expires_at,
        'recipient_email' => $email,
        'issued_at' => $date_time
    );

    return $data;
}

function block_acclaim_issue_badge_request($data, $url, $username)
{
    $curl = new curl;
    $curl->post($url, $data, array( "CURLOPT_USERPWD" => $username . ":" ));
    return $curl->info["http_code"];
}

function block_acclaim_return_json_badges($curl, $url, $token)
{
    $params = array("sort" => "name", "filter" => "state::active");
    $options = array("CURLOPT_USERPWD" => $token . ":");

    $http = $curl->get($url, $params, $options);
    return $http;
}

function block_acclaim_build_radio_buttons($json, $badge_items)
{
    $arr = json_decode($json, true);
    if (isset($arr['data'])) {
        foreach ($arr['data'] as $item) {
            $friendly_name = block_acclaim_truncate($item["name"]);
            $badge_id = $item["id"];
            $badge_items[$badge_id] = $friendly_name;
        }
    } else {
        error_log("invalid api, token or unable to connect");
    }
    return $badge_items;
}

function block_acclaim_images()
{
    $badge_items = array();
	
    $json = block_acclaim_query_acclaim_api(null);
    $badge_items = block_acclaim_build_radio_buttons($json, $badge_items);
    
    $next_page_url = "";
    if (isset($json['metadata'])) {
    	$metadata = $json['metadata'];
    	$next_page_url = $metadata["next_page_url"];
    	
    	while (!is_null($next_page_url)) {
                $json = block_acclaim_query_acclaim_api($next_page_url."&sort=name&filter=state::active");
                $badge_items = block_acclaim_build_radio_buttons($json, $badge_items);
    		
    		if (isset($json['metadata'])) {
    			$metadata = $json['metadata'];
    			$next_page_url = $metadata["next_page_url"];
    		}
    	}
    }
    return $badge_items;
}
