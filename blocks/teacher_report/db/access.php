<?php
/**Copyright (C) 2020 onwards Eruditiontec Innivations PVT LTD
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
/**
 * blocks_teacher_report capabilities
 *
 * @package   blocks_teacher_report
 * @copyright 2020 Eruditiontec Innovations PVT LTD {contact.erulearn@gmail.com}{http://erulearn.com/}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
    $capabilities = array(
 
    'block/teacher_report:myaddinstance' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'manager' => CAP_ALLOW
        ),
 
        'clonepermissionsfrom' => 'moodle/my:manageblocks'
    ),
 
    'block/teacher_report:addinstance' => array(
        'riskbitmask' => RISK_SPAM | RISK_XSS,
 
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'manager' => CAP_ALLOW
        ),
 
        'clonepermissionsfrom' => 'moodle/site:manageblocks'
    ),
    'block/teacher_report:viewnotification' => array(
        'riskbitmask' => RISK_SPAM | RISK_XSS,
 
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'editingteacher' => CAP_ALLOW,
            'student' => CAP_ALLOW,
        ),
 
        'clonepermissionsfrom' => 'moodle/site:manageblocks'
    ),
    'block/teacher_report:view' => array(
        'riskbitmask' => RISK_SPAM | RISK_XSS,
 
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'editingteacher' => CAP_ALLOW,
            
        ),
 
        'clonepermissionsfrom' => 'moodle/site:manageblocks'
    ),
);