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


// This file keeps track of upgrades to the birthday block
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installtion to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the functions defined in lib/ddllib.php

function xmldb_block_birthday_upgrade($oldversion=0) {

    global $CFG, $THEME;

    $result = true;

    if ($result && $oldversion < 2008041601) { //New version in version.php
        // cleanup previous data stored in mdl_config table
        // data is now stored in mdl_config_plugins
        // first get the data if it exists
        $fieldname = get_config(null, 'block_birthday_fieldname');
        $dateformat = get_config(null, 'block_birthday_dateformat');
        $visible = get_config(null, 'block_birthday_visible');
        // then delete the data from mdl_config
        $result = (set_config('block_birthday_fieldname', null)
            && set_config('block_birthday_dateformat', null)
            && set_config('block_birthday_visible', null));
        // finally if there is data, then add to mdl_config_plugin
        if (!empty($fieldname)) {
            set_config('block_birthday_fieldname', $fieldname, 'block/birthday');
        }
        if (!empty($dateformat)) {
            set_config('block_birthday_dateformat', $dateformat, 'block/birthday');
        }
        if (!empty($visible)) {
            set_config('block_birthday_visible', $visible, 'block/birthday');
        }
    }

    return $result;
}
