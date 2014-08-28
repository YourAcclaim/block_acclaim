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
 * Group observers.
 *
 * @package    mod_acclaim
 * @copyright  2014 Yancy Ribbens
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_acclaim;

defined('MOODLE_INTERNAL') || die();

//require_once($CFG->dirroot . '/mod/acclaim/locallib.php');
class group_observers {

    public static function issue_badge($event) {
	global $DB;
        $user_id = $event->userid;
        $course_id = $event->courseid;
	$user = $DB->get_record('user', array('id'=>$user_id));
        $firstname = $user->firstname;
        $lastname = $user->lastname;
	$email = $user->email;
        
//        $api = $DB->get_record('config', array('name'=>'api'));
//        $token = $DB->get_record('config', array('name'=>'token'));
//        $badge_id = $DB->get_record('config', array('name'=>'badge_template_id'));
//        $expires_at = $DB->get_record('config', array('name'=>'expires_at'));
//        $url = $api->value;

	//$block_record = $DB->get_record('block_acclaim', array('courseid' => '4');
	$course = $DB->get_record('block_acclaim', array('courseid' => '10'), '*', MUST_EXIST);
        //error_log('User Record: '.print_r($user,true));
        //error_log('url'.print_r($url,true));
  //      error_log('Event Data: ' . print_r($event,true));
	error_log('Block Record: ' . print_r($course,true));

//        $date_time = date('Y-m-d  h:i:s a', time());
//        $password = "";

//        $data = array(
//            'badge_template_id' => $badge_id->value,
//            'issued_to_first_name' => $user->firstname,
//            'issued_to_last_name' => $user->lastname,
//            'expires_at' => $expires_at->value,
//            'recipient_email' => $user->email,
//            'issued_at' => $date_time
//        );

 //       error_log('Data: '.print_r($data,true));
        //error_log('api_url: '.$api->value);

 //       $ch = curl_init();

//        $curlConfig = array(
//            CURLOPT_HTTPHEADER     => array('Accept: application/json'),
//            CURLOPT_CUSTOMREQUEST  => "POST",
//            CURLOPT_FOLLOWLOCATION => true,
//            CURLOPT_URL            => $api->value,
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_USERPWD        => $token->value . ":" . $password,
//            CURLOPT_POSTFIELDS     => $data,
//        );

//        error_log('curl_array: '. print_r($curlConfig,true));
//        curl_setopt_array($ch, $curlConfig);

//        $result = curl_exec($ch);
 //       $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  //      curl_close($ch);
    }
}

?>
