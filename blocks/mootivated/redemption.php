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
 * Redemption request.
 *
 * @package    block_mootivated
 * @copyright  2018 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');

require_login(null, false);
require_sesskey();

$purchaseid = required_param('purchaseid', PARAM_RAW);
$returnto = new moodle_url(required_param('returnto', PARAM_LOCALURL));

$PAGE->set_url('/blocks/mootivated/redemption.php');
$PAGE->set_pagelayout('standard');
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_title(get_string('purchases', 'block_mootivated'));
$PAGE->set_heading(get_string('purchases', 'block_mootivated'));
$PAGE->navigation->override_active_url(new moodle_url('/user/profile.php', ['id' => $USER->id]));
$PAGE->navbar->add(get_string('purchases', 'block_mootivated'));

$manager = new block_mootivated\manager($USER, $PAGE->context, local_mootivated\helper::get_school_resolver());
$manager->require_view();
$manager->require_school();

try {
    $client = $manager->get_client_user();
    $url = $client->get_redemption_url($purchaseid, $returnto);
    if (empty($url)) {
        throw new moodle_exception('unknownredemptionurl');
    }
    redirect($url);
} catch (moodle_exception $e) {
    redirect($returnto, get_string('errorwhileredeemingitem', 'block_mootivated'));
}
