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
 * This block needs to be reworked.
 * The new roles system does away with the concepts of rigid student and
 * teacher roles.
 */
class block_birthday extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_birthday');
        $this->version = 2011061901;
    }

    public function has_config() {
        return true;
    }

    public function get_content() {
        global $USER, $CFG, $DB, $OUTPUT;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        if (empty($this->instance)) {
            return $this->content;
        }

        //initialize variables
        $cfg_birthday = get_config('block_birthday');

        if (!isset($cfg_birthday->fieldname)) { //fieldname is now an the user_info_field id
            $fieldname = 0;
        } else {
                $fieldname = $cfg_birthday->fieldname;
        }
        if (!isset($cfg_birthday->daysahead)) {
            $daysahead = O;
        } else {
            $daysahead = $cfg_birthday->daysahead;
        }
        if (!isset($cfg_birthday->visible)) {
            $visible = 'Hide';
        } else {
            $visible = $cfg_birthday->visible;
        }

        $timezone = empty($USER->timezone) ? $CFG->timezone : $USER->timezone;
        $users = array();

        //Calculate if we are in separate groups
        $isseparategroups = ($this->page->course->groupmode == SEPARATEGROUPS
            && $this->page->course->groupmodeforce
            && !has_capability('moodle/site:accessallgroups', $this->page->context));

        //Get the user current group
        $currentgroup = $isseparategroups ? groups_get_course_group($this->page->course) : null;

        $groupmembers = "";
        $groupselect  = "";
        $params = array();

        //Add this to the SQL to show only group users
        if ($currentgroup !== null) {
            $groupmembers = ", {groups_members} gm";
            $groupselect = "AND u.id = gm.userid AND gm.groupid = :currentgroup";
            $params['currentgroup'] = $currentgroup;
        }

        $userfields = user_picture::fields('u', array('username'));
        for ($i=0; $i <= $daysahead; $i++) {
              $userdate = usergetdate((time()+($i*86400)), $timezone);
            $usermonth = $userdate['mon'];
            $userday = $userdate['mday'];

            if ($this->page->course->id == SITEID
                or $this->page->context->contextlevel < CONTEXT_COURSE) {  // Site-level
                $sql = "SELECT u.id, u.username, u.firstname, u.lastname, u.picture, u.lastaccess,
                    u.imagealt, u.email, ud.data, month(from_unixtime(ud.data)) as month,
                    day(from_unixtime(ud.data)) as day
                FROM {user_info_data} ud, {user} u $groupmembers
                WHERE ud.userid = u.id
                    AND month(from_unixtime(ud.data)) = $usermonth
                    AND day(from_unixtime(ud.data))  = $userday
                    AND ud.fieldid = $cfg_birthday->fieldname
                    AND u.deleted = 0 $groupselect
                    AND ud.data > 0 
                GROUP BY u.id
                ORDER BY month, day, u.lastname, u.firstname ASC";
            } else {
                // Course level - show only enrolled users for now
                // TODO: add a new capability for viewing of all users (guests+enrolled+viewing)

                list($esqljoin, $eparams) = get_enrolled_sql($this->page->context);
                $params = array_merge($params, $eparams);

                $sql = "SELECT u.id, u.username, u.firstname, u.lastname, u.picture, u.lastaccess,
                u.imagealt, u.email, ud.data, month(from_unixtime(ud.data)) as month,
                day(from_unixtime(ud.data)) as day
                FROM {user_info_data} ud, {user} u $groupmembers
                JOIN ($esqljoin) euj ON euj.id = u.id
                WHERE ud.userid = u.id
                    AND month(from_unixtime(ud.data)) = $usermonth
                    AND day(from_unixtime(ud.data))  = $userday
                    AND ud.fieldid= $cfg_birthday->fieldname
                    AND u.deleted = 0 $groupselect
                    AND ud.data > 0 
                GROUP BY u.id
                ORDER BY month, day, u.lastname, u.firstname ASC";

                $params['courseid'] = $this->page->course->id;
            }

            if ($pusers = $DB->get_records_sql($sql, $params)) {
                foreach ($pusers as $puser) {
                    $users[$puser->id]=$puser;
                    $users[$puser->id]->fullname = fullname($puser);
                } //$users contains list of users with desired upcoming birthdays
            }
        }

        // Verify if we can see the list of users
        if (!has_capability('block/birthday:viewlist', $this->context)) {
            $this->content->text = '<div class="info">'
            .get_string('nocapabilitytousethisservice', 'error').'</div>';
            return $this->content;
        }

        if (!empty($users)) { //if there are no users than just print there are no birthdays to show
            if (isloggedin() && has_capability('moodle/site:sendmessage', $this->page->context)
                && !empty($CFG->messaging) && !isguestuser()) {
                  $canshowicon = true;
            } else {
                $canshowicon = false;
            }

            $this->content->text = '<div class="info">'
            .get_string('blocktitle', 'block_birthday').'</div>';
            $userdate = usergetdate(time(), $timezone);
            $usermonth = $userdate['mon'];
            $userday = $userdate['mday'];
            $usercount = 0;
            $this->content->text .= '<ul class="list">';
            foreach ($users as $user) {
                ++$usercount;
                if (!(($usermonth==$user->month) && ($userday== $user->day))) {
                    if ($usercount==1) {
                        $this->content->text .= '<li class="listentry">'
                        .get_string('nobirthdaystoday', 'block_birthday').'</li>';
                    }
                    $this->content->text .= '</ul><div class="clearer"><!-- --></div>'
                    .'<div class="info">'
                        .userdate($user->data, get_string('strftimedate', 'block_birthday'))
                        .'</div><ul class="list">';
                    $usermonth=$user->month;
                    $userday=$user->day;
                }
                $this->content->text .= '<li class="listentry">';
                if (isguestuser($user)) {
                    $this->content->text .= '<div class="user">'
                    .$OUTPUT->user_picture($user, array('size'=>16));
                    $this->content->text .= get_string('guestuser').'</div>';
                } else {
                    $this->content->text .= '<div class="user">'
                    .$OUTPUT->user_picture($user, array('size'=>16));
                    $this->content->text .= '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$user->id
                        .'&amp;course='.$this->page->course->id.'" title="'
                        .$user->fullname.'">'.$user->fullname.'</a></div>';
                }
                // Only when logged in and messaging active etc
                if ($canshowicon and ($USER->id != $user->id) and !isguestuser($user)) {
                    $anchortagcontents = '<img class="iconsmall" src="'
                    .$OUTPUT->pix_url('t/message')
                        . '" alt="'. get_string('messageselectadd') .'" />';
                        $anchortag = '<a href="'.$CFG->wwwroot.'/message/index.php?id='
                        .$user->id.'" title="'
                            .get_string('messageselectadd').'">'.$anchortagcontents .'</a>';
                        $this->content->text .= '<div class="message">'.$anchortag.'</div>';
                }
                $this->content->text .= '</li>';
            }
            $this->content->text .= '</ul><div class="clearer"><!-- --></div>';
        } else {
            $this->content->text = '<div class="info">'
            .get_string('nobirthdaystoday', 'block_birthday').'</div>';
            if (!empty($cfg_birthday->visible)) {
                if ($cfg_birthday->visible=='Hide') { //block is hidden when empty
                    $this->content->text = '';
                }
            }
        }
        return $this->content;
    }
}
