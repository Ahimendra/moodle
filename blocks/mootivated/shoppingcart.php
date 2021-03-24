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
 * Store.
 *
 * @package    block_mootivated
 * @copyright  2018 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use block_mootivated\pocket;
use core\output\notification;

require(__DIR__ . '/../../config.php');

require_login(null, false);
$delete = optional_param('delete', null, PARAM_ALPHANUMEXT);

$PAGE->set_url('/blocks/mootivated/shoppingcart.php');
$PAGE->set_pagelayout('standard');
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_title(get_string('shoppingcart', 'block_mootivated'));
$PAGE->set_heading(get_string('shoppingcart', 'block_mootivated'));
$PAGE->navigation->override_active_url(new moodle_url('/user/profile.php', ['id' => $USER->id]));
$PAGE->navbar->add(get_string('shoppingcart', 'block_mootivated'));

$manager = new block_mootivated\manager($USER, $PAGE->context, local_mootivated\helper::get_school_resolver());
$manager->require_view();
$manager->require_school();

if (!get_config('block_mootivated', 'enablecart')) {
    redirect(new moodle_url('/blocks/mootivated/store.php'));
}

$client = $manager->get_client_user();
$cart = $manager->get_shopping_cart();
$renderer = $PAGE->get_renderer('block_mootivated');

// Delete an item from the cart.
if (!empty($delete)) {
    require_sesskey();
    $cart->remove_item($delete);
    redirect($PAGE->url);
}

// Helper method.
$itemnames = function($items) {
    return implode(', ', array_map(function($item) {
        return $item->name;
    }, $items));
};

// Convenience function to get the form because we initialise it multiple times.
$getform = function($bare = true) use ($cart, $manager, $renderer) {
    return new block_mootivated\output\shopping_cart_form(null, [
        'bare' => $bare,
        'cart' => $cart,
        'manager' => $manager,
        'renderer' => $renderer
    ]);
};

$form = $getform();
if ($data = $form->get_data()) {
    // We use the form object to validate the form submission, but the output is generated
    // by ourselves in a template. The reason for doing this is to have a nice looking
    // form, while still using formslib to validate the data.
    foreach ($data->qty as $itemid => $qty) {
        if ($qty > $cart->get_max_quantity($itemid)) {
            $SESSION->block_mootivated_cart_message = get_string('cartitemquantitycapped', 'block_mootivated', [
                'maxtotalquantity' => $cart->get_max_total_quantity(),
                'name' => $cart->get_item($itemid)->name
            ]);
        }
        $cart->set_quantity($itemid, $qty);
    }
    redirect($PAGE->url);

} else if ($form->no_submit_button_pressed() && optional_param('checkout_and_pay', 0, PARAM_BOOL) && confirm_sesskey()) {
    // This is it, the user has requested that we proceed with their checkout.
    $items = array_values(array_map(function($item) {
        return ['id' => $item->itemid, 'quantity' => (int) $item->quantity];
    }, $cart->get_items()));

    $returnto = new moodle_url('/blocks/mootivated/store.php');
    try {
        $purchases = $client->purchase_items($items);
    } catch (moodle_exception $e) {
        redirect($PAGE->url, get_string('errorwhilepurchasingitems', 'block_mootivated'));
    }

    if (empty($purchases)) {
        redirect($PAGE->url, get_string('errorwhilepurchasingitems', 'block_mootivated'));

    }

    // Empty the cart.
    $cart->empty();

    if (get_config('block_mootivated', 'instantredemption')) {
        $pocket = pocket::get($USER->id);
        $key = $pocket->add('redemptions_url', array_map(function($p) {
            return $p->id;
        }, $purchases));
        redirect(new moodle_url('/blocks/mootivated/redirect.php', [
            'key' => $key,
            'sesskey' => sesskey(),
            'returnto' => $returnto
        ]));
    }

    redirect($returnto);
}
unset($form);

$manager->clear_user_coins_cache();

// Get the items, and sort them up.
$client = $manager->get_client_user();
$allitems = $client->get_store_items();
$items = array_reduce($allitems, function($carry, $item) {
    if ($item->type !== 'purchase') {
        return $carry;
    }
    $carry[$item->id] = (object) [
        'id' => $item->id,
        'cost' => $item->cost,
        'name' => $item->name,
        'imageurl' => $item->thumbnail_url,
        'isunlimited' => $item->num_items <= 0,
        'numremaining' => max(0, $item->num_items - $item->num_purchased),
    ];
    return $carry;
}, []);

$removed = [];
$priceupdated = [];
$quantitycapped = [];
foreach ($cart->get_items() as $cartitem) {

    // Item no longer available.
    if (!isset($items[$cartitem->itemid])) {
        $cart->remove_item($cartitem->itemid);
        $removed[] = $cartitem;
        continue;
    }

    // Quantity demanded exceeds quantity available.
    $item = $items[$cartitem->itemid];
    if (!$item->isunlimited && $cartitem->quantity > $item->numremaining) {
        $newqty = min($cartitem->quantity, $item->numremaining);
        $cart->set_quantity($item->id, $newqty);
        if ($newqty <= 0) {
            $removed[] = $cartitem;
            continue;
        } else {
            $quantitycapped[] = $cartitem;
        }
    }

    // The price has been updated.
    if ($item->cost != $cartitem->cost) {
        $cart->set_cost($item->id, $item->cost);
        $priceupdated[] = $cartitem;
    }

    // The name or image has changed.
    if ($item->name != $cartitem->name || $item->imageurl != $cartitem->imageurl) {
        $cart->set_info($item->id, $item->name, $item->imageurl);
    }
}

echo $OUTPUT->header();

echo $renderer->navigation_in_block($manager, 'cart');

$notifs = [];
if (!empty($SESSION->block_mootivated_cart_message)) {
    $message = $SESSION->block_mootivated_cart_message;
    unset($SESSION->block_mootivated_cart_message);
    $notifs[] = new notification($message, notification::NOTIFY_INFO);
}
if (!empty($removed)) {
    $notifs[] = new notification(get_string('cartitemsnolongeravailableremoved', 'block_mootivated', $itemnames($removed)),
        notification::NOTIFY_INFO);
}
if (!empty($priceupdated)) {
    $notifs[] = new notification(get_string('cartitemspricechanged', 'block_mootivated', $itemnames($priceupdated)),
        notification::NOTIFY_INFO);
}
if (!empty($quantitycapped)) {
    $notifs[] = new notification(get_string('cartitemsquantitychanged', 'block_mootivated', $itemnames($quantitycapped)),
        notification::NOTIFY_INFO);
}
foreach ($notifs as $notif) {
    echo $OUTPUT->render($notif);
}

// We reinitialise the form here as we may have changed the cart, and rendering may have already
// occurred, so getting a new instance and displaying it is best.
$form = $getform(false);
$form->display();

echo $OUTPUT->footer();
