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
 * Generic dialogue base.
 *
 * This is originally a copy of block_stash/dialogue-base.
 *
 * @package    block_mootivated
 * @copyright  2018 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/templates', 'core/str', 'block_mootivated/dialogue-base'], function($, Templates, Str, DialogueBase) {

    /**
     * Constructor.
     *
     * @param {Object} item The item.
     * @param {Number} myCoins The number of coins we have.
     * @param {String} returnTo The URL to return to.
     * @param {Object} appearance The appareance settings (contains, decsep, decimalplaces, pointsterm, and pointsimageurl).
     */
    function Dialogue(item, myCoins, returnTo, appearance) {
        this.item = item;
        this.myCoins = myCoins;
        this.returnTo = returnTo;
        this.appearance = appearance;
        DialogueBase.prototype.constructor.apply(this, []);
    }
    Dialogue.prototype = Object.create(DialogueBase.prototype);
    Dialogue.prototype.constructor = Dialogue;

    /**
     * Render mechanics.
     *
     * @return {Promise}
     */
    Dialogue.prototype._render = function() {
        return Str.get_string('placebid', 'block_mootivated').then(function(placebid) {
            this.setTitle(placebid);
            var context = {
                hasbid: !this.item.hasbid,
                minbid: !this.item.hasbid ? this.item.cost : this.item.auction_bid + this.item.minimum_bid,
                handlingfee: this.item.handling_fee,
                mycoins: this.myCoins,

                decimalplaces: this.appearance.decimalplaces,
                decsep: this.appearance.decsep,
                pointsimageurl: this.appearance.pointsimageurl,
                pointsterm: this.appearance.pointsterm
            };
            return Templates.render('block_mootivated/place_auction_bid', context)
            .then(function(html, js) {
                this._setDialogueContent(html);
                this.center();
                Templates.runTemplateJS(js);

                this.find('.block_mootivated-place-auction-bid').on('cancel-bid', function() {
                    this.close();
                }.bind(this));

                this.find('.block_mootivated-place-auction-bid').on('place-bid', function(e, bid) {
                    var url = M.cfg.wwwroot + '/blocks/mootivated/bid.php';
                    url += '?sesskey=' + encodeURIComponent(M.cfg.sesskey);
                    url += '&itemid=' + encodeURIComponent(this.item.id);
                    url += '&bid=' + encodeURIComponent(bid);
                    url += '&returnto=' + encodeURIComponent(this.returnTo);
                    window.location.href = url;
                }.bind(this));
            }.bind(this));
        }.bind(this));
    };

    return Dialogue;
});
