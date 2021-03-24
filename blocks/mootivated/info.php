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
 * Info page.
 *
 * The info data is stored globally and not in the block instance.
 *
 * @package    block_mootivated
 * @copyright  2019 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');

require_login(null, false);

$PAGE->set_url('/blocks/mootivated/info.php');
$PAGE->set_pagelayout('standard');
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_title(get_string('infopagetitle', 'block_mootivated'));
$PAGE->set_heading(get_string('infopagetitle', 'block_mootivated'));
$PAGE->navigation->override_active_url(new moodle_url('/user/profile.php', ['id' => $USER->id]));
$PAGE->navbar->add(get_string('infopagetitle', 'block_mootivated'));

$manager = new block_mootivated\manager($USER, $PAGE->context, local_mootivated\helper::get_school_resolver());
$manager->require_view();
$manager->require_school();
$renderer = $PAGE->get_renderer('block_mootivated');

$contentcontext = context_system::instance();
$contentoptions = ['maxfiles' => -1, 'trusttext' => true, 'context' => $contentcontext];

$content = get_config('block_mootivated', 'infopagecontent');
$format = get_config('block_mootivated', 'infopagecontentformat');
$hasinfopage = (bool) get_config('block_mootivated', 'hasinfopage');
$draftitemid = file_get_submitted_draft_itemid('badges');

if ($manager->can_manage()) {
    $data = (object) [
        'infopagecontent' => $content !== false ? $content : '',
        'infopagecontentformat' => $format !== false ? $format : FORMAT_HTML
    ];
    $data = file_prepare_standard_editor($data, 'infopagecontent', $contentoptions, $contentcontext,
        'block_mootivated', 'infopagecontent', 0);
    $form = new block_mootivated\output\info_page_form(null, ['editoroptions' => $contentoptions]);
    $form->set_data($data);

    if ($data = $form->get_data()) {
        $data = file_postupdate_standard_editor($data, 'infopagecontent', $contentoptions, $contentcontext,
            'block_mootivated', 'infopagecontent', 0);
        set_config('infopagecontent', $data->infopagecontent, 'block_mootivated');
        set_config('infopagecontentformat', $data->infopagecontentformat, 'block_mootivated');
        set_config('hasinfopage', !empty(trim(strip_tags($data->infopagecontent))), 'block_mootivated');
        redirect($PAGE->url, get_string('contentsaved', 'block_mootivated'));
    }

    echo $OUTPUT->header();
    echo $renderer->navigation_in_block($manager, 'info');
    if (!$hasinfopage) {
        echo html_writer::tag('div', markdown_to_html(get_string('infopagemanagerintronotvisible', 'block_mootivated')));
    }
    $form->display();
    echo $OUTPUT->footer();

} else {
    if (!$hasinfopage) {
        redirect(new moodle_url('/blocks/mootivated/store.php'));
    }
    echo $OUTPUT->header();
    echo $renderer->navigation_in_block($manager, 'info');
    $content = file_rewrite_pluginfile_urls($content, 'pluginfile.php', $contentcontext->id, 'block_mootivated', 'infopagecontent', 0);
    echo format_text($content, $format, ['context' => $contentcontext]);
    echo $OUTPUT->footer();
}
