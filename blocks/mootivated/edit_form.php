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
 * Block Mootivated edit form.
 *
 * @package    block_mootivated
 * @copyright  2017 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Workaround code that would have been written in a way that does not load the form.
require_once($CFG->dirroot . '/blocks/edit_form.php');

/**
 * Block Mootivated edit form class.
 *
 * @package    block_mootivated
 * @copyright  2017 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_mootivated_edit_form extends block_edit_form {

    /**
     * Set data.
     *
     * We also load the global config in the form, see block_mootivated::instance_config_save()
     * for more information.
     *
     * @param object $defaults Defaults.
     */
    public function set_data($defaults) {
        foreach (block_mootivated::$globalconfig as $key) {
            $val = get_config('block_mootivated', $key);
            if ($val !== false) {
                $defaults->{'config_' . $key} = $val;
            }
        }
        parent::set_data($defaults);
    }

    /**
     * Form definition.
     *
     * @param moodleform $mform Moodle form.
     * @return void
     */
    protected function specific_definition($mform) {
        $mform->addElement('header', 'confighdr', get_string('appearance'));
        $mform->addElement('text', 'config_title', get_string('configtitle', 'block_mootivated'));
        $mform->setDefault('config_title', get_string('defaulttitle', 'block_mootivated'));
        $mform->setType('config_title', PARAM_TEXT);

        $mform->addElement('selectyesno', 'config_mobilelogin', get_string('configmobilelogin', 'block_mootivated'));
        $mform->addHelpButton('config_mobilelogin', 'configmobilelogin', 'block_mootivated');
        $mform->setDefault('config_mobilelogin', false);
    }

}
