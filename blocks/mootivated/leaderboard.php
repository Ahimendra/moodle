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
 * Leaderboard.
 *
 * @package    block_mootivated
 * @copyright  2018 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');

require_login(null, false);

$switch = optional_param('switch', false, PARAM_BOOL);
$url = new moodle_url('/blocks/mootivated/leaderboard.php');

$PAGE->set_url($url);
$PAGE->set_pagelayout('standard');
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_title(get_string('leaderboard', 'local_mootivated'));
$PAGE->set_heading(get_string('leaderboard', 'local_mootivated'));
$PAGE->navigation->override_active_url(new moodle_url('/user/profile.php', ['id' => $USER->id]));
$PAGE->navbar->add(get_string('leaderboard', 'local_mootivated'));

$manager = new block_mootivated\manager($USER, $PAGE->context, local_mootivated\helper::get_school_resolver());
$manager->require_view();
$manager->require_school();
$manager->clear_user_coins_cache();

// Find out what leaderboard to show.
$school = $manager->get_school();
$mode = 'school';
$svsleadeboard = local_mootivated\helper::is_section_vs_section_leaderboard_enabled();
$schoolleaderboard = $school->is_leaderboard_enabled();
$canswitch = $svsleadeboard && $schoolleaderboard;

if (!$svsleadeboard && !$schoolleaderboard) {
    // Nothing is enabled, let's make sure the settings reflect that.
    block_mootivated_tmp_disabled_svs_leaderboard();
    block_mootivated_tmp_disabled_school_leaderboard($school);
    throw new moodle_exception('leaderboarddisabled', 'block_mootivated');

} else if (!$schoolleaderboard) {
    // The school leaderboard is disabled, so we must be viewing the svs one.
    $mode = 'svs';
}

// Check if we need to switch.
if ($canswitch && $switch) {
    $mode = $mode == 'school' ? 'svs' : 'school';
}

$client = $manager->get_client_user();
$renderer = $PAGE->get_renderer('block_mootivated');

echo $OUTPUT->header();
echo $renderer->navigation_in_block($manager, 'leaderboard');

// If we can switch, print the switch button.
if ($canswitch) {
    $switchurl = new moodle_url($url, ['switch' => !$switch]);
    $switchstr = ($mode === 'svs') ? 'switchtoleaderboard' : 'switchtosectionleaderboard';
    echo html_writer::div(
        $renderer->single_button($switchurl, get_string($switchstr, 'block_mootivated'), 'get'),
        'mb-3',
        ['style' => !right_to_left() ? 'text-align: right;' : 'text-align: left;']   // TODO Handle RTL
    );
}

// Display the leaderboard.
switch ($mode) {
    case 'svs':
        try {
            // By default the leaderboard is enabled, that is to copy the default value from the server.
            // However, it is possible that the leaderboard was enabled, then the private key changed, or
            // that the leaderboard was disabled for the section prior to setting the private key. Or that
            // the section was created after disabling the leaderboard.
            $leaderboard = $client->get_svs_leaderboard();
            echo $renderer->svs_leaderboard($manager, $leaderboard);

        } catch (local_mootivated\client_exception $e) {
            if ($e->get_server_error() == 'LEADERBOARD_NOT_ENABLED') {
                echo $renderer->notification(get_string('leaderboarddisabled', 'block_mootivated'));

                // Set leaderboard as disabled.
                block_mootivated_tmp_disabled_svs_leaderboard();

            } else {
                throw $e;
            }
        }
        break;

    case 'school':
    default:
        try {
            // By default the leaderboard is enabled, that is to copy the default value from the server.
            // However, it is possible that the leaderboard was enabled, then the private key changed, or
            // that the leaderboard was disabled for the section prior to setting the private key. Or that
            // the section was created after disabling the leaderboard.

            $leaderboard = $client->get_leaderboard();
            echo $renderer->leaderboard($manager, $leaderboard);

        } catch (\local_mootivated\client_exception $e) {
            if ($e->get_server_error() == 'LEADERBOARD_NOT_ENABLED') {
                echo $renderer->notification(get_string('leaderboarddisabled', 'block_mootivated'));

                // Set leaderboard as disabled.
                block_mootivated_tmp_disabled_school_leaderboard($school);

            } else {
                throw $e;
            }
        }
        break;
}

echo $OUTPUT->footer();

/**
 * Disable the school leaderboard.
 *
 * @param school $school The school
 */
function block_mootivated_tmp_disabled_school_leaderboard($school) {
    $school->set_from_record((object) ['leaderboardenabled' => false]);
    $school->save();
}

/**
 * Disable the global leaderboard.
 */
function block_mootivated_tmp_disabled_svs_leaderboard() {
    local_mootivated\helper::set_section_vs_section_leaderboard_enabled(false);
}
