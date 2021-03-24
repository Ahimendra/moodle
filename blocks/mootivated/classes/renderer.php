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
 * Block Mootivated renderer.
 *
 * @package    block_mootivated
 * @copyright  2017 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use local_mootivated\helper;
use block_mootivated\external;
use block_mootivated\manager;
use block_mootivated\shopping_cart;

/**
 * Block Mootivated renderer class.
 *
 * Note: We CANNOT use namespaces here.
 *
 * @package    block_mootivated
 * @copyright  2017 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_mootivated_renderer extends plugin_renderer_base {

    /** @var bool Whether the page requirements for appearance were set. */
    protected $appearancepagerequirementset = false;

    /**
     * Render a user's avatar.
     *
     * @param manager $manager The manager.
     * @return string
     */
    public function avatar(manager $manager) {
        return $this->user_picture($manager->get_user(), [
            'size' => 512,
            'link' => false,
            'class' => 'mootivated-avatar',
            'alttext' => false
        ]);
    }

    /**
     * Buy url.
     *
     * @param manager $manager The manager.
     * @param object $item The item.
     * @param moodle_url $returnto The page to return to.
     * @return action_link
     */
    public function buy_url(manager $manager, $item, moodle_url $returnto) {
        return new moodle_url('/blocks/mootivated/purchase.php', [
            'itemid' => $item->id,
            'sesskey' => sesskey(),
            'returnto' => $returnto->out_as_local_url(false),
            'cost' => $item->cost,
            'instantredemption' => (bool) get_config('block_mootivated', 'instantredemption')
        ]);
    }

    /**
     * Coin amount.
     *
     * @return string
     */
    public function coin_amount($coins) {
        $thousandssep = get_string('thousandssep', 'langconfig');
        $decsep = get_string('decsep', 'langconfig');
        $decimals = helper::get_points_decimal_places();

        $amount = $decimals > 0 ? $coins / pow(10, $decimals) : $coins;
        return number_format($amount, $decimals, $decsep, $thousandssep);
    }

    /**
     * Coin count.
     *
     * @param manager $manager The manager.
     * @return string
     */
    public function coin_count(manager $manager) {
        return $this->render_from_template('block_mootivated/coin_count', [
            'coins' => $this->coin_amount($manager->get_coins()),
            'pointsterm' => helper::get_points_term(),
            'pointsimageurl' => helper::get_points_image_url()
        ]);
    }

    /**
     * Output the coin history.
     *
     * @param manager $manager The manager.
     * @param array $items The items.
     * @return string
     */
    public function coin_history_listing(manager $manager, array $items) {
        global $USER;

        $items = array_values(array_filter($items, function($item) {
            if (empty($item->data) || empty($item->data->coins_gained)) {
                return false;
            }
            return true;
        }));

        $items = array_map(function($item) use ($manager) {
            $row = (object) [
                'date' => userdate($item->timestamp),
                'coins' => $this->coin_amount($item->data->coins_gained),
                'reason' => '',
            ];

            $reason = '';
            $type = !empty($item->reason) && !empty($item->reason->type) ? $item->reason->type : null;
            $data = !empty($item->reason) && !empty($item->reason->data) ? $item->reason->data : new stdClass();

            if ($type === 'moodle_event') {
                $class = $data->eventname;
                if (!class_exists($class) || !is_subclass_of($class, 'core\event\base')) {
                    return $row;
                }

                if (is_a($class, 'core\event\course_module_completion_updated', true)) {
                    $context = context_module::instance($data->cmid, IGNORE_MISSING);
                    if ($context) {
                        $reason = get_string('completingmodule', 'block_mootivated', $context->get_context_name(false));
                    } else {
                        $reason = get_string('completingamodule', 'block_mootivated');
                    }

                } else if (is_a($class, 'core\event\course_completed', true)) {
                    $context = context_course::instance($data->courseid, IGNORE_MISSING);
                    if ($context) {
                        $reason = get_string('completingcourse', 'block_mootivated', $context->get_context_name(false));
                    } else {
                        $reason = get_string('completingacourse', 'block_mootivated');
                    }

                } else {
                    $reason = $class::get_name();
                }

            } else if ($type === 'manual') {
                $reason = get_string('manualaward', 'block_mootivated');
            }


            $row->reason = $reason;
            return $row;
        }, $items);

        return $this->render_from_template('block_mootivated/coin_history_listing', [
            'hasitems' => !empty($items),
            'items' => $items,
            'pointsimageurl' => helper::get_points_image_url(),
            'pointsterm' => helper::get_points_term(),
        ]);
    }

    /**
     * Get appearance PAGE requirements.
     *
     * Call this when you know that an AMD module will be using the appearance settings,
     * and append the returned value to tyour
     *
     * @return string
     */
    public function get_appearance_page_requirements() {
        if ($this->appearancepagerequirementset) {
            return '';
        }
        $this->appearancepagerequirementset = true;


        $id = html_writer::random_id();
        $data = $this->get_appearance_settings();
        $this->page->requires->js_amd_inline('
            require(["jquery", "block_mootivated/appearance"], function($, Appearance) {
                var data = JSON.parse($("#' . $id . '").text());
                Appearance.setup(data);
            });
        ');

        return html_writer::tag('script', json_encode($data), [
            'type' => 'application/json',
            'id' => $id
        ]);
    }

    /**
     * Get all the appearance settings.
     *
     * @return object
     */
    public function get_appearance_settings() {
        return (object) [
            'decimalplaces' => helper::get_points_decimal_places(),
            'decsep' => get_string('decsep', 'langconfig'),
            'thousandssep' => get_string('thousandssep', 'langconfig'),
            'pointsimageurl' => helper::get_points_image_url(),
            'pointsterm' => helper::get_points_term()
        ];
    }

    /**
     * Whether the plugin should show an info page.
     *
     * @param manager $manager The manager.
     * @return bool
     */
    protected function has_info_page(manager $manager, $strict = false) {
        return (!$strict && $manager->can_manage()) || (bool) get_config('block_mootivated', 'hasinfopage');
    }

    /**
     * Whether the plugin has a leaderboard page.
     *
     * @param manager $manager The manager.
     * @return bool
     */
    protected function has_leaderboard_page(manager $manager) {
        $school = $manager->has_school() ? $manager->get_school() : null;
        return ($school && $school->is_leaderboard_enabled()) || helper::is_section_vs_section_leaderboard_enabled();
    }

    /**
     * Whether the avatar should be shown.
     *
     * @param manager $manager The manager.
     * @return bool
     */
    protected function is_avatar_shown(manager $manager) {
        return (bool) get_config('block_mootivated', 'showavatar');
    }

    /**
     * Whether the plugin uses the dashboard rules.
     *
     * @param manager $manager The manager.
     * @return bool
     */
    protected function is_reward_method_dashboard_rules(manager $manager) {
        $school = $manager->has_school() ? $manager->get_school() : null;
        return $school ? $school->is_reward_method_dashboard_rules() : false;
    }

    /**
     * Render navigation.
     *
     * @param manager $manager The manager.
     * @param array|object $options Block config.
     * @return string
     */
    public function navigation_on_block(manager $manager, $config = []) {
        global $USER;

        $config = (object) $config;
        $school = $manager->has_school() ? $manager->get_school() : null;
        $actions = [];

        if ($this->has_info_page($manager)) {
            $actions[] = new action_link(
                new moodle_url('/blocks/mootivated/info.php'),
                get_string('infopagetitle', 'block_mootivated'),
                null,
                null,
                new pix_icon('info', '', 'block_mootivated')
            );
        }

        $actions[] = new action_link(
            new moodle_url('/blocks/mootivated/store.php'),
            get_string('store', 'block_mootivated'),
            null,
            null,
            new pix_icon('store', '', 'block_mootivated')
        );

        $actions[] = new action_link(
            new moodle_url('/blocks/mootivated/purchases.php'),
            get_string('purchases', 'block_mootivated'),
            null,
            null,
            new pix_icon('purchases', '', 'block_mootivated')
        );

        if ($this->has_leaderboard_page($manager)) {
            $actions[] = new action_link(
                new moodle_url('/blocks/mootivated/leaderboard.php'),
                get_string('leaderboard', 'local_mootivated'),
                null,
                null,
                new pix_icon('leaderboard', '', 'block_mootivated')
            );
        }

        if (helper::can_login($USER) && !empty($config->mobilelogin)) {
            $url = new moodle_url('/blocks/mootivated/app.php');
            $actions[] = new action_link(
                $url,
                get_string('mobilelogin', 'block_mootivated'),
                new popup_action('click', $url, 'block_mootivated_mobile_login', ['toolbar' => false]),
                ['id' => html_writer::random_id()],
                new pix_icon('mobile', '', 'block_mootivated')
            );
        }

        if (helper::is_sso_to_dashboard_enabled() && helper::can_sso_to_dashboard()) {
            $actions[] = new action_link(
                new moodle_url('/local/mootivated/dashboard.php'),
                get_string('dashboard', 'local_mootivated'),
                null,
                ['target' => '_blank'],
                new pix_icon('dashboard', '', 'block_mootivated')
            );
        }

        $o = '';
        $o .= html_writer::start_tag('nav');
        $o .= implode('', array_map(function(action_link $action) {
            if (!isset($action->attributes['id'])) {
                $action->attributes['id'] = html_writer::random_id();
            }

            $iconandtext = html_writer::div($this->render($action->icon));
            $iconandtext .= html_writer::div($action->text, 'nav-label');
            $content = html_writer::link($action->url, $iconandtext, array_merge($action->attributes, ['class' => 'nav-button']));

            $componentactions = !empty($action->actions) ? $action->actions : [];
            foreach ($componentactions as $componentaction) {
                $this->add_action_handler($componentaction, $action->attributes['id']);
            }

            return $content;
        }, $actions));

        return $o;
    }

    /**
     * Navigation in block's pages.
     *
     * @param manager $manager The manager.
     * @param string $page The page we are on.
     * @return string The navigation.
     */
    public function navigation_in_block(manager $manager, $page) {
        $links = [];

        if ($this->has_info_page($manager)) {
            $links[] = [
                'id' => 'info',
                'text' => $this->pix_icon('info', '', 'block_mootivated') . get_string('infopagetitle', 'block_mootivated'),
                'url' => new moodle_url('/blocks/mootivated/info.php')
            ];
        }

        $links[] = [
            'id' => 'store',
            'text' => $this->pix_icon('store', '', 'block_mootivated') . get_string('store', 'block_mootivated'),
            'url' => new moodle_url('/blocks/mootivated/store.php')
        ];

        $links[] = [
            'id' => 'purchases',
            'text' => $this->pix_icon('purchases', '', 'block_mootivated') . get_string('purchases', 'block_mootivated'),
            'url' => new moodle_url('/blocks/mootivated/purchases.php')
        ];

        if ($this->has_leaderboard_page($manager)) {
            $links[] = [
                'id' => 'leaderboard',
                'text' => $this->pix_icon('leaderboard', '', 'block_mootivated') . get_string('leaderboard', 'local_mootivated'),
                'url' => new moodle_url('/blocks/mootivated/leaderboard.php')
            ];
        }

        if ($this->is_reward_method_dashboard_rules($manager)) {
            $links[] = [
                'id' => 'history',
                'text' => $this->pix_icon('history', '', 'block_mootivated') . get_string('history', 'block_mootivated'),
                'url' => new moodle_url('/blocks/mootivated/history.php')
            ];
        }

        $tabs = array_map(function($link) {
            return new tabobject($link['id'], $link['url'], $link['text'], clean_param($link['text'], PARAM_NOTAGS));
        }, $links);

        return html_writer::div(
            html_writer::div(
                $this->shopping_cart_notice($manager) .
                $this->coin_count($manager),
                'nav-tools-wrapper'
            ) . $this->tabtree($tabs, $page),
            'block_mootivated-page-nav
        ');
    }

    /**
     * Full page avatar.
     *
     * The page which displays the avatar in full size.
     *
     * @param manager $manager The manager
     * @return string
     */
    public function fullpage_avatar(manager $manager) {
        $o = '';
        $o .= $this->avatar($manager);
        return $o;
    }

    /**
     * Output the leaderboard.
     *
     * @param manager $manager The manager.
     * @param stdClass $leaderboard The leaderboard.
     * @return string
     */
    public function leaderboard(manager $manager, stdClass $leaderboard) {
        global $DB, $USER;

        $isanon = (bool) $leaderboard->anonymous;
        $serveruserid = $manager->get_school()->get_server_user_id($USER);
        $guestuser = guest_user();

        // Load the other users at once.
        $users = [];
        if (!$isanon && !empty($leaderboard->ranking)) {
            $usernames = array_filter(array_reduce($leaderboard->ranking, function($carry, $entry) {
                $carry[] = $entry->item->external_id;
                return $carry;
            }, []));
            if (!empty($usernames)) {
                // The list is indexed by username.
                list($inusersql, $inuserparams) = $DB->get_in_or_equal($usernames, SQL_PARAMS_NAMED);
                $users = $DB->get_records_select('user', "username $inusersql", $inuserparams, '',
                    'username,' . user_picture::fields());
            }
        }

        return $this->render_from_template('block_mootivated/leaderboard', [
            // Flatten the ranking.
            'leaderboard' => array_map(function($entry) use ($serveruserid, $isanon, $guestuser, $USER, $users) {

                // Identify the user.
                $fullname = null;
                $withlinkandalt = true;
                if ($entry->item->id == $serveruserid) {
                    $user = $USER;

                } else if ($isanon || empty($entry->item->external_id) || empty($users[$entry->item->external_id])) {                    $user = $guestuser;
                    $withlinkandalt = false;
                    $fullname = get_string('someoneelse', 'block_mootivated');

                } else {
                    $user = $users[$entry->item->external_id];
                }

                // Make the final object.
                return [
                    'rank' => $entry->rank,
                    'name' => !empty($fullname) ? $fullname : fullname($user),
                    'coins' => $this->coin_amount($entry->item->value),
                    'pic' => $this->user_picture($user, ['link' => $withlinkandalt, 'alttext' => $withlinkandalt]),
                ];
            }, $leaderboard->ranking),
            'hasitems' => !empty($leaderboard->ranking),
            'coins' => $manager->get_coins(),
            'pointsimageurl' => helper::get_points_image_url(),
            'pointsterm' => helper::get_points_term(),
            'showavatar' => $this->is_avatar_shown($manager),
            'avatarhtml' => $this->avatar($manager), // We always render this for template designers.
        ]);
    }

    /**
     * Return the block's content.
     *
     * @param manager $manager The manager.
     * @param bool $unused Used to be whether to hide the avatar.
     * @param object $config The block config.
     * @return string
     */
    public function main_block_content(manager $manager, $unused, $config) {
        $o = '';

        if ($this->is_avatar_shown($manager)) {
            $o .= html_writer::start_div('text-center');
            $o .= html_writer::link(new moodle_url('/blocks/mootivated/myavatar.php'), $this->avatar($manager));
            $o .= html_writer::end_div();
        }

        $o .= html_writer::start_div();
        $o .= $this->wallet($manager);
        $o .= html_writer::end_div();

        $o .= $this->navigation_on_block($manager, $config);

        return $o;
    }

    /**
     * Return the block's content for managers.
     *
     * @param manager $manager The manager.
     * @param bool $unused Used to be whether to hide the avatar.
     * @param object $config The block config.
     * @return string
     */
    public function main_block_content_for_managers(manager $manager, $unused, $config) {
        return $this->main_block_content($manager, $unused, $config);
    }

    /**
     * Override pix_url to auto-handle deprecation.
     *
     * It's just simpler than having to deal with differences between
     * Moodle < 3.3, and Moodle >= 3.3.
     *
     * @param string $image The file.
     * @param string $component The component.
     * @return string
     */
    public function pix_url($image, $component = 'moodle') {
        if (method_exists($this, 'image_url')) {
            return $this->image_url($image, $component);
        }
        return parent::pix_url($image, $component);
    }

    /**
     * Output the purchased items listing.
     *
     * @param manager $manager The manager.
     * @param array $items The items.
     * @return string
     */
    public function purchased_items_listing(manager $manager, array $items) {
        global $USER;
        $returnto = new moodle_url('/blocks/mootivated/purchases.php');
        return $this->render_from_template('block_mootivated/purchased_items_listing', [
            'hasitems' => !empty($items),
            'items' => array_map(function($item) use ($manager, $returnto) {
                $item->canredeem = !$item->redeempending;
                $item->showquantity = $item->quantityowned > 1;
                $item->redeemurl = $this->redemption_request_url($manager, $item, $returnto)->out(false);
                return $item;
            }, $items)
        ]);
    }

    /**
     * Output the purchases.
     *
     * @param manager $manager The manager.
     * @param array $purchases The purchases.
     * @return string
     */
    public function purchases(manager $manager, array $purchases) {
        global $USER;
        $returnto = new moodle_url('/blocks/mootivated/purchases.php');
        return $this->render_from_template('block_mootivated/purchased_items_listing', [
            'hasitems' => !empty($purchases),
            'items' => array_map(function($purchase) use ($manager, $returnto) {
                $isredeemed = $purchase->state === 'redeemed';
                $redeemedon = $isredeemed ? userdate($purchase->made_on, get_string('strftimedate', 'langconfig')) : null;
                $redeemedonstr = get_string('redeemedon', 'block_mootivated', $redeemedon);
                return [
                    'showquantity' => false,
                    'hidedescription' => true,

                    'id' => $purchase->item->id,
                    'image_url' => $purchase->item->image_url,
                    'name' => $purchase->item->name,
                    'canredeem' => $purchase->state === 'made',
                    'isredeemed' => $isredeemed,
                    'self_redeem' => true,  // To have confirmation popup.
                    'redeempending' => $purchase->state === 'redeeming',
                    'redeemurl' => $this->purchase_redemption_url($manager, $purchase, $returnto)->out(false),

                    'madeon' => userdate($purchase->made_on, get_string('strftimedate', 'langconfig')),
                    'redeemedon' => $redeemedon,
                    'redeemedonstr' => $redeemedonstr,
                ];
            }, $purchases)
        ]);
    }

    /**
     * Redemption URL rquest.
     *
     * @param manager $manager The manager.
     * @param object $purchase The purchase.
     * @param moodle_url $returnto The page to return to.
     * @return moodle_url
     */
    public function purchase_redemption_url(manager $manager, $purchase, moodle_url $returnto) {
        return new moodle_url('/blocks/mootivated/redemption.php', [
            'purchaseid' => $purchase->id,
            'sesskey' => sesskey(),
            'returnto' => $returnto->out_as_local_url(false)
        ]);
    }

    /**
     * Redemption URL rquest.
     *
     * @param manager $manager The manager.
     * @param object $item The item.
     * @param moodle_url $returnto The page to return to.
     * @return moodle_url
     */
    public function redemption_request_url(manager $manager, $item, moodle_url $returnto) {
        return new moodle_url('/blocks/mootivated/redemption_request.php', [
            'itemid' => $item->id,
            'self' => !empty($item->self_redeem),
            'sesskey' => sesskey(),
            'returnto' => $returnto->out_as_local_url(false)
        ]);
    }

    /**
     * Shopping cart.
     *
     * @param manager $manager The manager.
     * @param shopping_cart $cart The card.
     * @return string
     */
    public function shopping_cart(manager $manager, shopping_cart $cart) {
        $storeurl = new moodle_url('/blocks/mootivated/store.php');
        $carturl = new moodle_url('/blocks/mootivated/shoppingcart.php');
        return $this->render_from_template('block_mootivated/shopping_cart', array_merge(
            (array) $this->get_appearance_settings(),
            [
                'coins' => $this->coin_amount($cart->get_total()),
                'shoppingcartnote' => get_string('shoppingcartnote', 'block_mootivated', [
                    'maxtotalquantity' => $cart->get_max_total_quantity()
                ]),
                'insufficientfunds' => $manager->get_coins() < $cart->get_total(),
                'isempty' => $cart->get_total() <= 0,
                'sesskey' => sesskey(),
                'storeurl' => $storeurl->out(false),
                'lines' => array_map(function($line) use ($carturl) {
                    $removeurl = new moodle_url($carturl, ['delete' => $line->itemid, 'sesskey' => sesskey()]);
                    return array_merge((array) $line, [
                        'cost' => $this->coin_amount($line->cost),
                        'total' => $this->coin_amount($line->total),
                        'removeurl' => $removeurl->out(false),
                    ]);
                }, $cart->get_lines()),
            ]
        ));
    }

    /**
     * Shopping cart notice.
     *
     * The small button displaying the current state of the shopping cart.
     *
     * @param manager $manager The manager.
     * @return string
     */
    public function shopping_cart_notice(manager $manager) {
        $cartenabled = get_config('block_mootivated', 'enablecart');
        if (!$cartenabled) {
            return '';
        }
        $cart = $manager->get_shopping_cart();
        $url = new moodle_url('/blocks/mootivated/shoppingcart.php');
        return $this->render_from_template('block_mootivated/shopping_cart_notice', [
            'quantity' => $cart->get_total_quantity(),
            'url' => $url->out(false),
        ]);
    }

    /**
     * Output the store items.
     *
     * @param manager $manager The manager.
     * @param array $items The items.
     * @return string
     */
    public function store_listing(manager $manager, array $items) {
        global $USER;

        $cart = $manager->get_shopping_cart();
        $cartenabled = get_config('block_mootivated', 'enablecart');
        $returnto = new moodle_url('/blocks/mootivated/store.php');
        $coincount = $manager->get_coins();
        $serveruserid = $manager->get_school()->get_server_user_id($USER);
        $entriescounter = function($entries) use ($serveruserid) {
            return array_reduce($entries, function($carry, $item) use ($serveruserid) {
                $mine = ($item->user_id == $serveruserid ? $item->num_entries : 0);
                return [$carry[0] + $item->num_entries, $carry[1] + $mine];
            }, [0, 0]);
        };

        $binomialcoeff = function($n, $k) {
            // Credit: https://rosettacode.org/wiki/Evaluate_binomial_coefficients#PHP
            if ($k == 0) return 1;
            $result = 1;
            foreach (range(0, $k - 1) as $i) {
                $result *= ($n - $i) / ($i + 1);
            }
            return $result;
        };

        $winchances = function($totalitems, $myentries, $totalentries) use ($binomialcoeff) {
            // Credit: https://math.stackexchange.com/a/92008
            if ($totalentries <= 0 || $myentries <= 0) {
                return 0;
            }
            $lose = $binomialcoeff($totalentries - $myentries, $totalitems);
            $total = $binomialcoeff($totalentries, $totalitems);
            if ($lose <= 0) {
                return 1;
            }
            return 1 - ($lose / $total);
        };

        $highestbidfinder = function($bids) use ($serveruserid) {
            return array_reduce($bids, function($carry, $bid) use ($serveruserid) {
                if ($bid->user_id == $serveruserid) {
                    return max($bid->bids);
                }
                return $carry;
            }, 0);
        };

        // Group products that are of type purchase.
        $grouped = array_reduce($items, function($carry, $item) {
            $id = !empty($item->product_id) && $item->type === 'purchase' ? $item->product_id : $item->id;
            $carry[$id] = !isset($carry[$id]) ? [] : $carry[$id];
            $carry[$id][] = $item;
            return $carry;
        }, []);

        // Construct items context.
        $listing = array_map(function($items) use ($coincount, $manager, $returnto, $entriescounter,
                $serveruserid, $highestbidfinder, $winchances) {
            $item = $items[0];

            $item->starts = userdate($item->start_time);
            $item->ends = userdate($item->end_time);
            $item->ispurchase = $item->type == 'purchase';
            $item->isauction = $item->type == 'auction';
            $item->israffle = $item->type == 'raffle';
            $item->timeleft = $this->time_left($item->end_time);

            $item->hasvariants = (count($items) > 1);
            if (!$item->hasvariants) {
                $item->isunlimited = $item->num_items <= 0;
                $item->num_remaining = max(0, $item->num_items - $item->num_purchased);
                $item->nremainingstr = get_string('nremaining', 'block_mootivated', $item->num_remaining);
                $item->issoldout = !$item->isunlimited && $item->num_remaining <= 0;
                $item->buyurl = $this->buy_url($manager, $item, $returnto)->out(false);

                if ($item->israffle) {
                    list($totalentries, $myentries) = $entriescounter(!empty($item->raffle_entries) ? $item->raffle_entries : []);
                    $item->myentries = $myentries;
                    $item->nentriesstr = get_string('nentries', 'block_mootivated', $item->myentries);
                    $item->ntowinstr = get_string('ntowin', 'block_mootivated', $item->num_items);
                    $item->winchances = round($winchances($item->num_items, $myentries, $totalentries) * 100);
                    $item->winchancesstr = get_string('winchances', 'block_mootivated', $item->winchances);
                }

                if ($item->isauction) {
                    $item->hasbid = !empty($item->auction_bid);
                    $item->auctioncost = $item->hasbid ? $item->auction_bid : $item->cost;
                    $item->mineishighest = $item->hasbid ? $serveruserid == $item->auction_bidder : false;
                    $item->myhighest = $item->hasbid ? $highestbidfinder($item->auction_bids) : 0;
                    $item->nbidsstr = get_string('nbids', 'block_mootivated', $item->auction_bid_count);
                }

            } else {
                $item->variants = array_map(function($variant) use ($manager, $returnto) {
                    $numremaining = max(0, $variant->num_items - $variant->num_purchased);
                    $isunlimited = $variant->num_items <= 0;
                    $issoldout = !$isunlimited && $numremaining <= 0;
                    return (object) [
                        'id' => $variant->id,
                        'buyurl' => $this->buy_url($manager, $variant, $returnto)->out(false),
                        'isunlimited' => $isunlimited,
                        'label' => $variant->product_variant,
                        'name' => $variant->name,
                        'num_remaining' => $numremaining,
                        'nleftstr' => get_string('nleft', 'block_mootivated', $numremaining),
                        'issoldout' => $issoldout,
                    ];
                }, $items);

                $item->issoldout = array_reduce($item->variants, function($carry, $variant) {
                    return $variant->issoldout && $carry;
                }, true);

                // Override these last.
                $item->name = $item->product_name;
                $item->item_id = $item->id;
                $item->id = $item->product_id;
                $item->buyurl = null;
            }

            $item->coins = $this->coin_amount($item->isauction ? $item->auctioncost : $item->cost);
            $item->canbuy = !$item->issoldout && $item->cost <= $coincount;

            return $item;
        }, $grouped);

        return $this->render_from_template('block_mootivated/store_listing', [
            'returnto' => $returnto->out_as_local_url(false),
            'cartenabled' => $cartenabled,
            'cartjson' => json_encode($cart->get_data()),
            'coins' => $coincount,
            'canmanage' => $manager->can_manage(),
            'decimalplaces' => helper::get_points_decimal_places(),
            'decsep' => get_string('decsep', 'langconfig'),
            'hasitems' => !empty($items),
            'pointsimageurl' => helper::get_points_image_url(),
            'pointsterm' => helper::get_points_term(),
            'soldouturl' => $this->pix_url('soldout', 'block_mootivated')->out(false),
            'items' => array_values($listing)
        ]) . $this->get_appearance_page_requirements();;
    }

    /**
     * Output the SVS leaderboard.
     *
     * @param manager $manager The manager.
     * @param stdClass $leaderboard The leaderboard.
     * @return string
     */
    public function svs_leaderboard(manager $manager, stdClass $leaderboard) {
        $isanon = (bool) $leaderboard->anonymous;
        $somegroup = get_string('anonngroup', 'block_mootivated');
        return $this->render_from_template('block_mootivated/leaderboard', [
            // Flatten the ranking.
            'leaderboard' => array_map(function($entry) use ($isanon, $somegroup) {
                // Make the final object.
                return [
                    'rank' => $entry->rank,
                    'name' => !empty($entry->item->name) ? $entry->item->name : $somegroup,
                    'coins' => $this->coin_amount($entry->item->value),
                ];
            }, $leaderboard->ranking),
            'hasitems' => !empty($leaderboard->ranking),
            'pointsimageurl' => helper::get_points_image_url(),
            'pointsterm' => helper::get_points_term(),
        ]);
    }

    /**
     * Time left.
     *
     * @param int $epoch The timestamp.
     * @return string
     */
    public function time_left($epoch) {
        $diff = $epoch - time();
        if ($diff < 60) {
            $time = get_string('lessthan1min', 'block_mootivated');
        } else if ($diff < 5000) {
            $time = get_string('tinymin', 'block_mootivated', ceil($diff / 60));
        } else if ($diff < 100000) {
            $time = get_string('tinyhours', 'block_mootivated', ceil($diff / 3660));
        } else {
            $time = get_string('tinydays', 'block_mootivated', ceil($diff / 86400));
        }
        return get_string('timeleft', 'block_mootivated', $time);
    }

    /**
     * Return the content of the user's wallet.
     *
     * @param manager $manager The manager.
     * @return string
     */
    public function wallet(manager $manager) {
        $coins = $manager->get_coins();
        return $this->render_from_template('block_mootivated/wallet', (object) array_merge(
            (array) $this->get_appearance_settings(),
            [
                'coins' => $this->coin_amount($coins),
            ]
        ));
    }

}
