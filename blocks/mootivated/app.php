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
 * Entry point to go to the dasbhoard SSO-style.
 *
 * @package    local_mootivated
 * @copyright  2018 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_mootivated\helper;

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/tcpdf/tcpdf_barcodes_2d.php');

require_login(0, false);

$PAGE->set_url('/blocks/mootivated/app.php');
$PAGE->set_pagelayout('embedded');
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_title(get_string('applogin', 'block_mootivated'));
$PAGE->set_heading(get_string('applogin', 'block_mootivated'));
$PAGE->navigation->override_active_url(new moodle_url('/user/profile.php', ['id' => $USER->id]));
$PAGE->navbar->add(get_string('applogin', 'block_mootivated'));

$manager = new block_mootivated\manager($USER, $PAGE->context, local_mootivated\helper::get_school_resolver());
$manager->require_view();
$manager->require_school();

$client = $manager->get_client_user();
$renderer = $PAGE->get_renderer('block_mootivated');

echo $OUTPUT->header();

if (!helper::can_login($USER)) {
    throw new moodle_exception('cannotlogin', 'local_mootivated');
}

$school = $manager->get_school();
$data = $school->get_login_token();

$qrcodedata = array_merge((array) $data, [
    'host' => $school->get_host(),
    'moodle' => $CFG->wwwroot
]);
$creator = new TCPDF2DBarcode(json_encode($qrcodedata), 'QRCODE,L');
$qrcode = $creator->getBarcodePngData(5, 5);

echo html_writer::start_tag('div', ['style' => 'margin: 1em', 'class' => 'text-center']);
echo html_writer::tag('p', get_string('apploginintro', 'block_mootivated'));
echo html_writer::start_tag('p', ['class' => 'text-center']);
echo html_writer::empty_tag('img', ['src' => 'data:image/png;base64,' . base64_encode($qrcode)]);
echo html_writer::end_tag('p');

echo $OUTPUT->footer();
