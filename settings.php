<?php
global $DB;
defined('MOODLE_INTERNAL') || die;

$cfg_birthday = get_config('block_birthday');
$options = array();
if ($ADMIN->fulltree) {
	
    //build options from available date/time user profile fields
    $conditions = array('datatype' => 'datetime');
    $fields = "id,name";
    $profilefields = $DB->get_records('user_info_field',$conditions,NULL,$fields);
    if (empty($profilefields)) {
	$options = array(0 => get_string('nouserprofilefields','block_birthday'));
    } else { 
	foreach ($profilefields as $profilefield) {
			$options[$profilefield->id] = $profilefield->name;
		}
    }
    $settings->add(new admin_setting_configselect('block_birthday/fieldname', get_string('user_info_field_name','block_birthday'), get_string('user_info_field_name_info', 'block_birthday'),0 , $options));

//    $options = array('ISO'=>get_string('dateformatiso', 'block_birthday'), 'USA'=>get_string('dateformatusa', 'block_birthday'), 'EUR'=>get_string('dateformateur','block_birthday'), 'EUR_ES'=>get_string('dateformateures','block_birthday'));
//    $settings->add(new admin_setting_configselect('block_birthday/dateformat', get_string('dateformat', 'block_birthday'), get_string('dateformat_info', 'block_birthday'), 'ISO', $options));

    $settings->add(new admin_setting_configtext('block_birthday/daysahead', get_string('daysahead','block_birthday'), get_string('daysahead_info','block_birthday'), '0', PARAM_INT, 3));

    $options = array('Show'=>get_string('blockshow','block_birthday'),'Hide'=>get_string('blockhide','block_birthday'));
    $settings->add(new admin_setting_configselect('block_birthday/visible', get_string('blockvisible','block_birthday'),get_string('blockvisible_info','block_birthday'), 'Hide', $options));
}
