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
 * Redirect.
 *
 * This page serves as transition to avoid long requests and
 * many HTTP header redirects that leave the user waiting for long on
 * the same page as the initial one.
 *
 * Here we display something, and immediately redirect to the next URL.
 *
 * @package    block_mootivated
 * @copyright  2018 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use block_mootivated\pocket;

require(__DIR__ . '/../../config.php');

require_login(null, false);
require_sesskey();

$key = required_param('key', PARAM_ALPHANUMEXT);
$returnto = new moodle_url(required_param('returnto', PARAM_LOCALURL));
$do = optional_param('do', 0, PARAM_INT);

$pleasewait = get_string('pleasewait', 'block_mootivated');

$PAGE->set_url('/blocks/mootivated/redirect.php', ['key' => $key, 'returnto' => $returnto]);
$PAGE->set_pagelayout('embedded');
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_title($pleasewait);
$PAGE->navigation->override_active_url(new moodle_url('/user/profile.php', ['id' => $USER->id]));

if (!$do) {
    echo $OUTPUT->header();
    $icon = $OUTPUT->pix_icon('i/loading', '');
    $goto = new moodle_url($PAGE->url, ['do' => 1, 'sesskey' => sesskey()]);
    $urlencoded = $goto->out(false);
    echo html_writer::tag('div', "{$icon} {$pleasewait}", ['style' => 'margin: 1em;']);
    echo html_writer::script("window.location.href = '$urlencoded';");
    echo $OUTPUT->footer();
    die();
}

$manager = new block_mootivated\manager($USER, $PAGE->context, local_mootivated\helper::get_school_resolver());
$manager->require_view();
$manager->require_school();
$client = $manager->get_client_user();

$pocket = pocket::get($USER->id);
$data = $pocket->pop($key);
$type = $data['type'];
$args = $data['args'];

$url = null;
try {
    switch ($type) {
        case 'redemptions_url':
            $url = $client->get_redemptions_url($args, $returnto);
            break;

        case 'redemption_url':
            $url = $client->get_redemption_url($args, $returnto);
            break;
    }

    if (!$url) {
        throw new coding_exception('Unknown redirection type');
    }
} catch (moodle_exception $e)  {
    $PAGE->set_pagelayout('standard');
    throw $e;
}

redirect($url);
