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
 * External.
 *
 * @package    block_mootivated
 * @copyright  2019 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_mootivated;
defined('MOODLE_INTERNAL') || die();

use context_system;
use external_api;
use external_function_parameters;
use external_single_structure;
use external_multiple_structure;
use external_value;
use moodle_exception;
use block_mootivated\manager;
use local_mootivated\helper;

/**
 * External.
 *
 * @package    block_mootivated
 * @copyright  2019 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external extends external_api {

    /**
     * External function parameters.
     *
     * @return extermal_function_parameters [<description>]
     */
    public static function add_to_cart_parameters() {
        return new external_function_parameters([
            'id' => new external_value(PARAM_ALPHANUMEXT, 'The item ID', VALUE_REQUIRED),
            'name' => new external_value(PARAM_RAW, 'The name of the item', VALUE_REQUIRED),
            'cost' => new external_value(PARAM_INT, 'The cost of the item', VALUE_REQUIRED),
            'imageurl' => new external_value(PARAM_RAW, 'The image URL', VALUE_REQUIRED),
        ]);
    }

    /**
     * Add to card.
     *
     * @param string $id The item ID.
     * @param string $name The name.
     * @param int $cost The cost, for display purposes.
     * @param string $imageurl The image URL.
     */
    public static function add_to_cart($id, $name, $cost, $imageurl) {
        global $USER;
        $params = static::validate_parameters(static::add_to_cart_parameters(), ['id' => $id, 'name' => $name,
            'cost' => $cost, 'imageurl' => $imageurl]);
        $context = context_system::instance();
        static::validate_context($context);

        $manager = new manager($USER, $context, helper::get_school_resolver());
        $canmanage = $manager->can_manage();
        $canview = $manager->can_view() || $canmanage;

        // Hide the block to non-logged in users, guests and those who cannot view the block.
        if (!$USER->id || isguestuser() || !$canview) {
            throw new moodle_exception('Invalid user, or does not belong to school, or lacks permissions.');
        }

        $cart = $manager->get_shopping_cart();
        if ($cart->is_full()) {
            throw new moodle_exception('shoppingcartfull', 'block_mootivated', '', [
                'maxtotalquantity' => $cart->get_max_total_quantity()
            ]);
        }
        $cart->add_item($params['id'], $params['name'], $params['cost'], $params['imageurl']);
        return $cart->get_data();
    }

    /**
     * External function returns.
     *
     * @return extermal_function_parameters The return value.
     */
    public static function add_to_cart_returns() {
        return static::get_cart_returns();
    }

    /**
     * External function parameters.
     *
     * @return extermal_function_parameters [<description>]
     */
    public static function get_cart_parameters() {
        return new external_function_parameters([]);
    }

    /**
     * Get cart.
     *
     * @param string $id The item ID.
     * @param string $name The name.
     * @param int $cost The cost, for display purposes.
     * @param string $imageurl The image URL.
     */
    public static function get_cart() {
        global $USER;
        $context = context_system::instance();
        static::validate_context($context);

        $manager = new manager($USER, $context, helper::get_school_resolver());
        $canmanage = $manager->can_manage();
        $canview = $manager->can_view() || $canmanage;

        // Hide the block to non-logged in users, guests and those who cannot view the block.
        if (!$USER->id || isguestuser() || !$canview) {
            throw new moodle_exception('Invalid user, or does not belong to school, or lacks permissions.');
        }

        $cart = $manager->get_shopping_cart();
        return $cart->get_data();
    }

    /**
     * External function returns.
     *
     * @return extermal_function_parameters The return value.
     */
    public static function get_cart_returns() {
        return new external_single_structure([
            'maxquantity' => new external_value(PARAM_INT, 'The maximum quantity allowed in the cart', VALUE_REQUIRED),
            'total' => new external_value(PARAM_INT, 'The total in the cart', VALUE_REQUIRED),
            'lines' => new external_multiple_structure(new external_single_structure([
                'itemid' => new external_value(PARAM_ALPHANUMEXT, 'The item ID', VALUE_REQUIRED),
                'quantity' => new external_value(PARAM_INT, 'The quantity', VALUE_REQUIRED),
                'name' => new external_value(PARAM_RAW, 'The name of the item', VALUE_REQUIRED),
                'cost' => new external_value(PARAM_INT, 'The cost of the item', VALUE_REQUIRED),
                'imageurl' => new external_value(PARAM_RAW, 'The image URL', VALUE_REQUIRED),
                'total' => new external_value(PARAM_INT, 'The total for the line', VALUE_REQUIRED),
            ]), VALUE_REQUIRED)
        ]);
    }
}
