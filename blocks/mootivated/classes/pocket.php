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
 * Pocket.
 *
 * Small storage for little uses.
 *
 * @package    block_mootivated
 * @copyright  2019 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_mootivated;

use coding_exception;

/**
 * Pocket.
 *
 * @package    block_mootivated
 * @copyright  2019 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class pocket {

    /**
     * Constructor.
     *
     * @param array $redictions Redirections indexed by ID.
     */
    protected function __construct() {
        global $SESSION;
        if (!isset($SESSION->block_mootivated_pocket)) {
            $SESSION->block_mootivated_pocket = [];
        }
    }

    /**
     * Add a redirection.
     *
     * @param string $type The type.
     * @param mixed $args The args.
     * @return string The storage key.
     */
    public function add($type, $args = null) {
        global $SESSION;

        do {
            $key = random_string(5);
        } while (array_key_exists($key, $SESSION->block_mootivated_pocket));

        $SESSION->block_mootivated_pocket[$key] = ['type' => $type, 'args' => $args];

        return $key;
    }

    /**
     * Get the redirection.
     *
     * @param string $key Get the redirection key.
     * @return moodle_url
     */
    public function pop($key) {
        global $SESSION;

        if (!isset($SESSION->block_mootivated_pocket[$key])) {
            throw new coding_exception('Invalid redirection key');
        }
        $data = $SESSION->block_mootivated_pocket[$key];
        // unset($SESSION->block_mootivated_pocket[$key]);
        return $data;
    }

    /**
     * Get the redirector instance.
     *
     * @param int $userid The user ID.
     * @return self
     */
    public static function get($userid) {
        global $SESSION, $USER;
        if ($USER->id != $userid) {
            throw new coding_exception('Only the current user is supported');
        }
        return new static();
    }

}
