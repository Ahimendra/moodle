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
 * Shopping cart.
 *
 * @package    block_mootivated
 * @copyright  2019 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_mootivated;

use coding_exception;

/**
 * Shopping cart.
 *
 * @package    block_mootivated
 * @copyright  2019 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class shopping_cart {

    /** @var bool Whether we loaded the cart. */
    protected $loaded = false;
    /** @var object[] The items. */
    protected $items = [];
    /** @var int The user ID. */
    protected $userid;
    /** @var int The maximum number of items. */
    protected $maxtotalquantity = 25;

    /**
     * Constructor.
     *
     * @param int $userid The user id.
     */
    public function __construct($userid) {
        $this->userid = $userid;
    }

    /**
     * Load the data.
     *
     * @return void
     */
    protected function load() {
        global $DB;
        if ($this->loaded) {
            return;
        }
        $items = $DB->get_records('block_mootivated_cart', ['userid' => $this->userid], 'id');
        $this->items = array_reduce($items, function($carry, $item) {
            $carry[$item->itemid] = $item;
            return $carry;
        }, []);
        $this->loaded = true;
    }

    /**
     * Add an item.
     *
     * @param string $itemid The item ID.
     * @param string $name The item name.
     * @param int $cost The cost.
     * @param string $imageurl The image URL.
     * @param int $quantity The quantity.
     */
    public function add_item($itemid, $name, $cost, $imageurl, $quantity = 1) {
        $this->load();

        $record = isset($this->items[$itemid]) ? $this->items[$itemid] : null;
        if (!$record) {
            $record = (object) [
                'itemid' => $itemid,
                'userid' => $this->userid,
                'quantity' => 0
            ];
        }

        $max = $this->get_available_quantity() + $record->quantity;
        $record->name = $name;
        $record->cost = $cost;
        $record->quantity = min($max, $record->quantity + $quantity);
        $record->imageurl = $imageurl;

        $this->items[$itemid] = $record;
        $this->commit_item($itemid);
    }

    /**
     * Commit an item.
     *
     * @param string $itemid The item ID.
     * @return void
     */
    protected function commit_item($itemid) {
        global $DB;
        if (!isset($this->items[$itemid])) {
            throw new coding_exception('Item not found in shopping cart');
        }
        $item = $this->items[$itemid];
        if (!empty($item->id)) {
            $DB->update_record('block_mootivated_cart', $item);
        } else {
            $item->id = $DB->insert_record('block_mootivated_cart', $item);
        }
    }

    /**
     * Empty the cart.
     *
     * @return void
     */
    public function empty() {
        global $DB;
        $DB->delete_records('block_mootivated_cart', ['userid' => $this->userid]);
        $this->items = [];
        $this->loaded = false;
    }

    /**
     * Get the available quantity of the cart, or the item.
     *
     * @return int
     */
    public function get_available_quantity() {
        return max(0, $this->get_max_total_quantity() - $this->get_total_quantity());
    }

    /**
     * Get the cart data.
     *
     * @return object
     */
    public function get_data() {
        return (object) [
            'maxquantity' => $this->get_max_total_quantity(),
            'total' => $this->get_total(),
            'lines' => $this->get_lines()
        ];
    }
    /**
     * Get an item.
     *
     * @param string $itemid The item ID.
     * @return object
     */
    public function get_item($itemid) {
        $this->load();
        if (!isset($this->items[$itemid])) {
            throw new coding_exception('Item not found in shopping cart');
        }
        return $this->items[$itemid];
    }

    /**
     * Get the items.
     *
     * @return array
     */
    public function get_items() {
        $this->load();
        return $this->items;
    }

    /**
     * Get the lines.
     *
     * @return object[] The lines.
     */
    public function get_lines() {
        return array_values(array_map(function($item) {
            return (object) [
                'itemid' => $item->itemid,
                'imageurl' => $item->imageurl,
                'name' => $item->name,
                'cost' => (int) $item->cost,
                'quantity' => (int) $item->quantity,
                'total' => $item->cost * $item->quantity,
            ];
        }, $this->get_items()));
    }

    /**
     * Get the maximum quantity for an item.
     *
     * @param string $itemid The item ID.
     * @return int
     */
    public function get_max_quantity($itemid) {
        $this->load();
        if (!isset($this->items[$itemid])) {
            throw new coding_exception('Item not found in shopping cart');
        }
        return $this->get_available_quantity() + $this->items[$itemid]->quantity;
    }

    /**
     * Get the maximum total combined quantity allowed in the cart.
     *
     * @return int
     */
    public function get_max_total_quantity() {
        return $this->maxtotalquantity;
    }

    /**
     * Get the total cost.
     *
     * @return int
     */
    public function get_total() {
        return array_sum(array_map(function($line) {
            return $line->total;
        }, $this->get_lines()));
    }

    /**
     * Get the total quantity of the cart.
     *
     * @return int
     */
    public function get_total_quantity() {
        return array_sum(array_map(function($item) {
            return $item->quantity;
        }, $this->get_items()));
    }

    /**
     * Whether the cart is full.
     *
     * @return bool
     */
    public function is_full() {
        return $this->get_total_quantity() >= $this->get_max_total_quantity();
    }

    /**
     * Remove an item from the cart.
     *
     * @param string $itemid The item ID.
     * @return void
     */
    public function remove_item($itemid) {
        global $DB;
        unset($this->items[$itemid]);
        $DB->delete_records('block_mootivated_cart', ['userid' => $this->userid, 'itemid' => $itemid]);
    }

    /**
     * Set the cost.
     *
     * @param string $itemid The item ID.
     * @param int $cost The cost.
     */
    public function set_cost($itemid, $cost) {
        $this->load();
        if (!isset($this->items[$itemid])) {
            throw new coding_exception('Item not found in shopping cart');
        }
        $this->items[$itemid]->cost = $cost;
        $this->commit_item($itemid);
    }

    /**
     * Set the info.
     *
     * @param string $itemid The item ID.
     * @param string $name The item name.
     * @param string $imageurl The image URL.
     */
    public function set_info($itemid, $name, $imageurl) {
        $this->load();
        if (!isset($this->items[$itemid])) {
            throw new coding_exception('Item not found in shopping cart');
        }
        $this->items[$itemid]->name = $name;
        $this->items[$itemid]->imageurl = $imageurl;
        $this->commit_item($itemid);
    }

    /**
     * Set the quantity.
     *
     * @param string $itemid The item ID.
     * @param int $qty The quantity.
     */
    public function set_quantity($itemid, $qty) {
        $this->load();
        if (!isset($this->items[$itemid])) {
            throw new coding_exception('Item not found in shopping cart');
        }

        $qty = (int) $qty;
        if ($qty <= 0) {
            $this->remove_item($itemid);
        } else {
            $max = $this->get_max_quantity($itemid);
            $this->items[$itemid]->quantity = min($max, $qty);
            $this->commit_item($itemid);
        }
    }

    /**
     * Get the cart of a user.
     *
     * @param int $userid The user ID.
     * @return shopping_cart
     */
    public static function get($userid) {
        return new shopping_cart($userid);
    }

}
