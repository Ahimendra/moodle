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
 * Block Mootivated settings.
 *
 * @package    block_mootivated
 * @copyright  2019 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configcheckbox(
        'block_mootivated/instantredemption',
        get_string('configinstantredemption', 'block_mootivated'),
        get_string('configinstantredemption_help', 'block_mootivated'),
        0
    ));

    $settings->add(new admin_setting_configcheckbox(
        'block_mootivated/enablecart',
        get_string('configenablecart', 'block_mootivated'),
        get_string('configenablecart_help', 'block_mootivated'),
        0
    ));

    $settings->add(new admin_setting_configcheckbox(
        'block_mootivated/showavatar',
        get_string('configshowavatar', 'block_mootivated'),
        get_string('configshowavatar_help', 'block_mootivated'),
        0
    ));
}
