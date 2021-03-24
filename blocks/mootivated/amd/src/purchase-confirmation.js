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
     * @param {String} item The item.
     * @param {String} buyUrl The purchase URL.
     */
    function Dialogue(item, buyUrl) {
        this.item = item;
        this.buyUrl = buyUrl;
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
        return Str.get_strings([
                {key: 'confirmpurchase', component: 'block_mootivated'},
                {key: 'areyousuretopurchase', component: 'block_mootivated'},
                {key: 'areyousuretopurchaseitema', component: 'block_mootivated'},
            ]).then(function(strs) {
            this.setTitle(strs[0]);

            var message = strs[1];
            if (this.item && this.item.name) {
                message = M.util.get_string('areyousuretopurchaseitema', 'block_mootivated', this.item.name);
            }
            var context = {buyurl: this.buyUrl, message: message};

            return Templates.render('block_mootivated/purchase_confirmation', context)
            .then(function(html, js) {
                this._setDialogueContent(html);
                this.center();
                Templates.runTemplateJS(js);

                this.find('.block_mootivated-purchase-confirmation').on('cancel', function() {
                    this.close();
                }.bind(this));
            }.bind(this));
        }.bind(this));
    };

    return Dialogue;
});
