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

global $DB;
defined('MOODLE_INTERNAL') || die;

$cfg_birthday = get_config('block_birthday');
$options = array();
if ($ADMIN->fulltree) {
    //build options from available date/time user profile fields
    $conditions = array('datatype' => 'datetime');
    $fields = "id,name";
    $profilefields = $DB->get_records('user_info_field', $conditions, null , $fields);
    if (empty($profilefields)) {
        $options = array(0 => get_string('nouserprofilefields', 'block_birthday'));
    } else {
        foreach ($profilefields as $profilefield) {
            $options[$profilefield->id] = $profilefield->name;
        }
    }
    $settings->add(new admin_setting_configselect('block_birthday/fieldname',
    get_string('user_info_field_name', 'block_birthday'),
    get_string('user_info_field_name_info', 'block_birthday'), 0 , $options));

    $settings->add(new admin_setting_configtext('block_birthday/daysahead',
    get_string('daysahead', 'block_birthday'),
    get_string('daysahead_info', 'block_birthday'), '0', PARAM_INT, 3));

    $options = array('Show'=>get_string('blockshow', 'block_birthday'),
    'Hide'=>get_string('blockhide', 'block_birthday'));
    $settings->add(new admin_setting_configselect('block_birthday/visible',
    get_string('blockvisible', 'block_birthday'),
    get_string('blockvisible_info', 'block_birthday'), 'Hide', $options));
}
