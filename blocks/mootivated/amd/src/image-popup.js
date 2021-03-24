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
 * Image popup.
 *
 * @package    block_mootivated
 * @copyright  2019 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/templates', 'core/notification'], function($, Templates, Notification) {
  var body = $('body');
  var scrollLockClass = 'block_mootivated-scrolllock';

  /**
   * Open the popup.
   *
   * @param {String} url The URL.
   */
  function open(url) {
    Templates.render('block_mootivated/image_popup', {imageurl: url}).then(function(html, js) {
      var node = $(html);

      // Listen for escape key.
      var onEscape = function(e) {
        if (e.key === 'Escape') {
          close();
        }
      };
      $(document).on('keyup', onEscape);

      // The thing to do when we close the popup.
      var close = function() {
        body.removeClass(scrollLockClass);
        node.remove();
        $(document).off('keyup', onEscape);
      };

      // Listen for internal close events.
      node.on('close', close);

      // Add to the DOM.
      body.addClass(scrollLockClass);
      Templates.runTemplateJS(js);
      body.append(node);

      return; // To please eslint.
    }).fail(Notification.exception);
  }

  return {
    open: open
  };

});
