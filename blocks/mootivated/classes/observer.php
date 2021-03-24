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
 * Observer.
 *
 * @package    block_mootivated
 * @copyright  2017 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_mootivated;
defined('MOODLE_INTERNAL') || die();

use context_system;
use core_user;

/**
 * Observer class.
 *
 * @package    block_mootivated
 * @copyright  2017 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class observer {

    /**
     * Observe coins being earned.
     *
     * @param \local_mootivated\event\coins_earned $event The event.
     * @return void
     */
    public static function coins_earned(\local_mootivated\event\coins_earned $event) {
        $manager = new manager($event->relateduserid, context_system::instance(),
            \local_mootivated\helper::get_school_resolver());
        $manager->add_locally_earned_coins($event->other['amount']);
    }

}
