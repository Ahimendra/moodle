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
 * @copyright  2019 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([
        'jquery',
        'block_mootivated/appearance',
        'block_mootivated/place-auction-bid',
        'block_mootivated/image-popup',
        'block_mootivated/purchase-confirmation'
    ], function($, Appearance, PlaceBid, Popup, PurchaseConfirmation) {

    var Store = function(coins, returnto, cart) {
        this.coins = coins;
        this.returnto = returnto;
        this.cart = cart;

        this.storeNode = $('.block_mootivated-items-list');
        this.toolsNode = $('.block_mootivated-page-nav .nav-tools-wrapper');

        this.init = this.init.bind(this);
        this.handleAddToCartClick = this.handleAddToCartClick.bind(this);
        this.handleBuyClick = this.handleBuyClick.bind(this);
        this.updateUiCartBtn = this.updateUiCartBtn.bind(this);
        this.updateUiCartCount = this.updateUiCartCount.bind(this);
        this.updateUiLockPurchase = this.updateUiLockPurchase.bind(this);
    };

    Store.prototype.init = function() {
        // Observe click on buy, add to cart, bid, etc. buttons.
        this.storeNode.on('click', 'button', (e) => {
            e.preventDefault();
            var btn = $(e.currentTarget);
            var node = btn.closest('.item-wrapper');
            if (btn.data('action') === 'cart') {
                this.handleAddToCartClick(node);
            } else {
                this.handleBuyClick(node);
            }
        });

        // Expand images when they are clicked.
        this.storeNode.on('click', '.item-image a', (e) => {
            e.preventDefault();
            Popup.open($(e.currentTarget).attr('href'));
        });

        // On load, lock the buy button where variant is not selected.
        this.storeNode.find('.item-has-variants').each((i, el) => {
            var node = $(el);
            this.handleVariantChange(node);
        });

        // On load, update the add to cart buttons.
        this.storeNode.find('.item-wrapper').each((i, el) => {
            var node = $(el);
            this.updateUiCartBtn(node);
        });

        // Observe change in variant.
        this.storeNode.on('change', '.variant-select', (e) => {
            e.preventDefault();
            var node = $(e.target).closest('.item-wrapper');
            this.handleVariantChange(node);
        });
    };

    Store.prototype.handleAddToCartClick = function(node) {
        const epochmilli = (new Date()).getTime();
        const waitUntil = new Date(epochmilli + 1000);
        this.updateUiCartBtn(node, true);

        return this.cart.addFromNode(node).then(() => {
            const now = new Date();
            setTimeout(() => {
                this.updateUiCartBtn(node, false);
                this.updateUiCartCount();
            }, Math.max(0, waitUntil.getTime() - now.getTime()));
            return;
        });
    };

    Store.prototype.handleBuyClick = function(node) {
        if (node.hasClass('item-auction')) {
            let item = {
                id: node.data('item-id'),
                hasbid: parseInt(node.data('item-hasbid'), 10),
                cost: parseInt(node.data('item-cost'), 10),
                auction_bid: parseInt(node.data('item-auction_bid'), 10),
                minimum_bid: parseInt(node.data('item-minimum_bid'), 10),
                handling_fee: parseInt(node.data('item-handling_fee'), 10),
            };
            let dialogue = new PlaceBid(item, this.coins, this.returnto, Appearance.getSettings());
            dialogue.show();
        } else {
            // If we don't have an item ID, or buy URL, we ignore this.
            if (!node.data('item-id') || !node.data('item-buyurl')) {
                return;
            }
            var item = {
                id: node.data('item-id'),
                cost: parseInt(node.data('item-cost'), 10),
                name: node.data('item-name')
            };
            var dialogue = new PurchaseConfirmation(item, node.data('item-buyurl'));
            dialogue.show();
        }
    };

    Store.prototype.handleVariantChange = function(node) {
        const select = node.find('.variant-select');
        const itemId = select.val();
        const option = select.find(':selected');

        if (!itemId) {
            node.data('item-buyurl', '');
            node.data('item-id', '');
            node.data('item-name', '');
            node.data('item-numremaining', '999');
        } else {
            node.data('item-id', itemId);
            node.data('item-buyurl', option.data('item-buyurl'));
            node.data('item-name', option.data('item-name'));
            node.data('item-numremaining', option.data('item-numremaining'));
        }

        this.updateUiLockPurchase(node, !itemId);
    };

    Store.prototype.updateUiCartBtn = function(node, inprogress) {
        const itemId = node.data('item-id');
        const btn = node.find('.add-to-cart-btn');
        if (inprogress) {
            btn.find('.add-to-cart-label').hide();
            btn.find('.add-to-cart-loading').show();
        } else {
            btn.find('.add-to-cart-label').show();
            btn.find('.add-to-cart-loading').hide();

            const numRemaining = node.data('item-numremaining');
            const qty = this.cart.getQuantity(itemId);
            btn.prop('disabled', !itemId || (typeof numRemaining !== 'undefined' && qty >= numRemaining));
        }
    };

    Store.prototype.updateUiCartCount = function() {
        this.toolsNode.find('.shopping-cart-qty').text(this.cart.getQuantity());
    };

    Store.prototype.updateUiLockPurchase = function(node, locked) {
        const btns = node.find('button');
        if (locked) {
            btns.prop('disabled', true);
        } else {
            btns.prop('disabled', false);
            this.updateUiCartBtn(node);
        }
    };

    return Store;

});
