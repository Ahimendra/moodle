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
 * Block Mootivated.
 *
 * @package    block_mootivated
 * @copyright  2017 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use block_mootivated\manager;
use local_mootivated\helper;

/**
 * Block Mootivated class.
 *
 * @package    block_mootivated
 * @copyright  2017 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_mootivated extends block_base {

    /** @var array Global config keys. */
    public static $globalconfig = [];

    /**
     * Applicable formats.
     *
     * @return array
     */
    public function applicable_formats() {
        return ['all' => true];
    }

    /**
     * The plugin has a settings.php file.
     *
     * @return boolean True.
     */
    public function has_config() {
        return true;
    }

    /**
     * Init.
     *
     * @return void
     */
    public function init() {
        $this->title = get_string('defaulttitle', 'block_mootivated');
    }

    /**
     * Get content.
     *
     * @return stdClass
     */
    public function get_content() {
        global $PAGE, $USER;

        if (isset($this->content)) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        $manager = new manager($USER, $PAGE->context, helper::get_school_resolver());
        $canmanage = $manager->can_manage();
        $canview = $manager->can_view() || $canmanage;

        // Hide the block to non-logged in users, guests and those who cannot view the block.
        if (!$USER->id || isguestuser() || !$canview) {
            return $this->content;
        }

        // Get the user's school.
        $school = $manager->get_school();
        if (!$school && !$canmanage) {
            return $this->content;
        }

        $renderer = $this->page->get_renderer('block_mootivated');
        $config = $this->config;
        if (!$school && $canmanage) {
            // Display content for managers.
            $this->content->text = $renderer->main_block_content_for_managers($manager, null, $config);
        } else {
            // Display content for everyone.
            $this->content->text = $renderer->main_block_content($manager, null, $config);
        }

        // Extend block content hook.
        $plugins = get_plugin_list_with_function('mootivatedaddon', 'extend_block_mootivated_content');
        foreach ($plugins as $pluginname => $functionname) {
            $this->content->text .= component_callback($pluginname, 'extend_block_mootivated_content',
                [$manager, $school, $renderer], '');
        }

        return $this->content;
    }

    /**
     * Serialize and store config data.
     *
     * We hijack this to save some config globally. Why? Because we initially added
     * configuration settings here, and as such users got used to changing them here.
     * However, it is difficult for inner pages of the block to obtain access to the
     * config when they are bound to an instance, so from this point on, we save most
     * of the config as admin settings and at some point we will retire the block's
     * settings and use the admin settings instead.
     *
     * @param stdClass $data The config data.
     * @param bool $nolongerused Not used.
     */
    public function instance_config_save($data, $nolongerused = false) {
        foreach (static::$globalconfig as $key) {
            if (!isset($data->{$key})) {
                continue;
            }
            $value = $data->{$key};
            unset($data->{$key});
            set_config($key, $value, 'block_mootivated');
        }
        parent::instance_config_save($data);
    }

    /**
     * Specialization.
     *
     * Happens right after the initialisation is complete.
     *
     * @return void
     */
    public function specialization() {
        parent::specialization();
        if (!empty($this->config->title)) {
            $this->title = $this->config->title;
        }
    }

    /**
     * Whether the user can edit the instance.
     *
     * We require the permission to add the block because we don't want
     * normal users to change the settings. Especially as some of the instance
     * settings are saved directly to admin settings.
     *
     * @return bool
     */
    public function user_can_edit() {
        return parent::user_can_edit() && has_capability('block/mootivated:addinstance', $this->page->context);
    }

}
