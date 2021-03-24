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

use block_mootivated\pocket;

require(__DIR__ . '/../../config.php');

require_login(null, false);
require_sesskey();

$itemid = required_param('itemid', PARAM_RAW);
$returnto = new moodle_url(required_param('returnto', PARAM_LOCALURL));
$cost = required_param('cost', PARAM_INT);
$instantredemption = optional_param('instantredemption', false, PARAM_BOOL);

$PAGE->set_url('/blocks/mootivated/purchase.php');
$PAGE->set_pagelayout('standard');
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_title(get_string('store', 'block_mootivated'));
$PAGE->set_heading(get_string('store', 'block_mootivated'));
$PAGE->navigation->override_active_url(new moodle_url('/user/profile.php', ['id' => $USER->id]));
$PAGE->navbar->add(get_string('store', 'block_mootivated'));

$manager = new block_mootivated\manager($USER, $PAGE->context, local_mootivated\helper::get_school_resolver());
$manager->require_view();
$manager->require_school();
$client = $manager->get_client_user();
$renderer = $PAGE->get_renderer('block_mootivated');

try {
    $purchase = $client->purchase_item($itemid);
} catch (moodle_exception $e) {
    redirect($returnto, get_string('errorwhilepurchasingitem', 'block_mootivated'));
}

$manager->substract_locally_spent_coins($cost);

if ($purchase && $instantredemption) {
    $pocket = pocket::get($USER->id);
    $key = $pocket->add('redemption_url', $purchase->id);
    redirect(new moodle_url('/blocks/mootivated/redirect.php', [
        'key' => $key,
        'sesskey' => sesskey(),
        'returnto' => $returnto
    ]));
}

redirect($returnto, get_string('itempurchasedsuccessfully', 'block_mootivated'));
