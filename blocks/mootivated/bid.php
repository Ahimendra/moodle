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
 * Purchase an item.
 *
 * @package    block_mootivated
 * @copyright  2018 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');

require_login(null, false);
require_sesskey();

$itemid = required_param('itemid', PARAM_RAW);
$returnto = new moodle_url(required_param('returnto', PARAM_LOCALURL));
$bid = required_param('bid', PARAM_INT);

$PAGE->set_url('/blocks/mootivated/bid.php');
$PAGE->set_pagelayout('standard');
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_title(get_string('placebid', 'block_mootivated'));
$PAGE->set_heading(get_string('placebid', 'block_mootivated'));

$manager = new block_mootivated\manager($USER, $PAGE->context, local_mootivated\helper::get_school_resolver());
$manager->require_view();
$manager->require_school();
$client = $manager->get_client_user();

try {
    $client->place_bid($itemid, $bid);
} catch (moodle_exception $e) {
    redirect($returnto, get_string('errorwhileplacingbid', 'block_mootivated'));
}

$manager->substract_locally_spent_coins($bid);
redirect($returnto, get_string('bidplacedsuccessfully', 'block_mootivated'));
