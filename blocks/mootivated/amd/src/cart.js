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

define(['core/ajax', 'core/notification', 'core/str'], function(Ajax, Notification, Str) {
    var getStrings = Str.get_strings;

    var Cart = function(initData) {
        this.cart = initData || {
            maxquantity: 0,
            total: 0,
            lines: []
        };
        this.add = this.add.bind(this);
        this.addFromNode = this.addFromNode.bind(this);
        this.getQuantity = this.getQuantity.bind(this);
    };

    Cart.prototype.add = function(id, name, cost, imageurl) {
        return Ajax.call([{
            methodname: 'block_mootivated_add_to_cart',
            args: {id, name, cost, imageurl}
        }], false, true)[0].then(cart => {
            this.cart = cart;
            return cart;
        }).catch(err => {
            if (err.errorcode === 'shoppingcartfull') {
                const strPromise = getStrings([
                    {key: 'cartfull', component: 'block_mootivated'},
                    {key: 'shoppingcartfull', component: 'block_mootivated', param: {
                        maxtotalquantity: this.cart.maxquantity
                    }},
                    {key: 'ok', component: 'core'},
                ]);
                strPromise.then(strs => {
                    Notification.alert(strs[0], strs[1], strs[2]);
                    return;
                }).catch(Notification.exception);
            } else {
                Notification.exception(err);
            }
        });
    };

    Cart.prototype.addFromNode = function(node) {
        const id = node.data('item-id');
        const name = node.data('item-name');
        const cost = parseInt(node.data('item-cost'), 10);
        const imageurl = node.data('item-thumbnailurl');
        return this.add(id, name, cost, imageurl);
    };

    Cart.prototype.getQuantity = function(itemId) {
        var lines = this.cart.lines;
        if (itemId) {
            lines = lines.filter(l => l.itemid == itemId);
        }
        return lines.reduce((c, l) => (c + l.quantity), 0);
    };

    return Cart;
});
