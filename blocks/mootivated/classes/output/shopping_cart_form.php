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
 * Shopping cart form.
 *
 * @package    block_mootivated
 * @copyright  2019 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_mootivated\output;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Shopping cart form.
 *
 * @package    block_mootivated
 * @copyright  2019 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class shopping_cart_form extends \moodleform {

    /**
     * Definition.
     *
     * @return void
     */
    public function definition() {

        $bare = $this->_customdata['bare'];
        $renderer = $this->_customdata['renderer'];
        $manager = $this->_customdata['manager'];
        $cart = $this->_customdata['cart'];
        $mform = $this->_form;

        foreach ($cart->get_lines() as $line) {
            $name = 'qty[' . $line->itemid . ']';
            $mform->addElement('hidden', $name);
            $mform->setType($name, PARAM_INT);
            $mform->setDefault($name, $line->quantity);
        }

        // We manually create the button checkout and pay. The button to update
        // quantities is the one that is the default submit action.
        $mform->registerNoSubmitButton('checkout_and_pay');

        // We don't want to render this unless it is actually going to be displayed on the page.
        // Otherwise we may end up with JavaScript running multiple times.
        if (!$bare) {
            $mform->addElement('html', $renderer->shopping_cart($manager, $cart));
        }
    }

}
