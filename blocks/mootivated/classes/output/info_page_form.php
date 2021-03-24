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
 * Info page form.
 *
 * @package    block_mootivated
 * @copyright  2019 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_mootivated\output;
defined('MOODLE_INTERNAL') || die();

use coding_exception;
use context;
use moodle_exception;
use stdClass;

require_once($CFG->libdir . '/formslib.php');

/**
 * Info page form class.
 *
 * Why do we need this? Because the block config is set against the block instance
 * and is not easily readable from our internal pages. To remedy this, new config values
 * will use the block's edit form, but save the data using this class.
 *
 * @package    block_mootivated
 * @copyright  2017 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class info_page_form extends \moodleform {

    /**
     * Definition.
     */
    public function definition() {
        $mform = $this->_form;
        $editoroptions = $this->_customdata['editoroptions'];

        $mform->addElement('editor', 'infopagecontent_editor', get_string('infopagecontent', 'block_mootivated'),
            null, $editoroptions);

        $this->add_action_buttons(false);
    }

}
