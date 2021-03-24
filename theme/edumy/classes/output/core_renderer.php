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

namespace theme_edumy\output;

defined('MOODLE_INTERNAL') || die;
use action_link;
use action_menu;
use action_menu_filler;
use action_menu_link_secondary;
use block_contents;
use block_move_target;
use coding_exception;
use context_course;
use context_system;
use core_text;
use custom_menu;
use custom_menu_item;
use html_writer;
use moodle_page;
use moodle_url;
use navigation_node;
use pix_icon;
use stdClass;
require_once($CFG->dirroot."/course/format/lib.php");

class core_renderer extends \core_renderer {


  /**
   * Return the image URL, if any.
   *
   * Note that maximum sizes are not applied to the image.
   *
   * @param int $maxwidth The maximum width, or null when the maximum width does not matter.
   * @param int $maxheight The maximum height, or null when the maximum height does not matter.
   * @return moodle_url|false
   */
  public function get_theme_image_headerlogo1($maxwidth = null, $maxheight = 100) {
      global $CFG;
      if (!empty($this->page->theme->settings->headerlogo1)) {
          $url = $this->page->theme->setting_file_url('headerlogo1', 'headerlogo1');
          // Get a URL suitable for moodle_url.
          $relativebaseurl = preg_replace('|^https?://|i', '//', $CFG->wwwroot);
          $url = str_replace($relativebaseurl, '', $url);
          return new moodle_url($url);
          return parent::get_theme_image_headerlogo1($maxwidth, $maxheight);
      }

  }
  public function get_theme_image_headerlogo2($maxwidth = null, $maxheight = 100) {
      global $CFG;
      if (!empty($this->page->theme->settings->headerlogo2)) {
          $url = $this->page->theme->setting_file_url('headerlogo2', 'headerlogo2');
          // Get a URL suitable for moodle_url.
          $relativebaseurl = preg_replace('|^https?://|i', '//', $CFG->wwwroot);
          $url = str_replace($relativebaseurl, '', $url);
          return new moodle_url($url);
          return parent::get_theme_image_headerlogo2($maxwidth, $maxheight);
      }

  }
  public function get_theme_image_headerlogo3($maxwidth = null, $maxheight = 100) {
      global $CFG;
      if (!empty($this->page->theme->settings->headerlogo3)) {
          $url = $this->page->theme->setting_file_url('headerlogo3', 'headerlogo3');
          // Get a URL suitable for moodle_url.
          $relativebaseurl = preg_replace('|^https?://|i', '//', $CFG->wwwroot);
          $url = str_replace($relativebaseurl, '', $url);
          return new moodle_url($url);
          return parent::get_theme_image_headerlogo3($maxwidth, $maxheight);
      }

  }
  public function get_theme_image_headerlogo_mobile($maxwidth = null, $maxheight = 100) {
      global $CFG;
      if (!empty($this->page->theme->settings->headerlogo_mobile)) {
          $url = $this->page->theme->setting_file_url('headerlogo_mobile', 'headerlogo_mobile');
          // Get a URL suitable for moodle_url.
          $relativebaseurl = preg_replace('|^https?://|i', '//', $CFG->wwwroot);
          $url = str_replace($relativebaseurl, '', $url);
          return new moodle_url($url);
          return parent::get_theme_image_headerlogo_mobile($maxwidth, $maxheight);
      }

  }
  public function get_theme_image_footerlogo1($maxwidth = null, $maxheight = 100) {
      global $CFG;
      if (!empty($this->page->theme->settings->footerlogo1)) {
          $url = $this->page->theme->setting_file_url('footerlogo1', 'footerlogo1');
          // Get a URL suitable for moodle_url.
          $relativebaseurl = preg_replace('|^https?://|i', '//', $CFG->wwwroot);
          $url = str_replace($relativebaseurl, '', $url);
          return new moodle_url($url);
          return parent::get_theme_image_footerlogo1($maxwidth, $maxheight);
      }

  }
  public function get_theme_image_heading_bg($maxwidth = null, $maxheight = 100) {
      global $CFG;
      if (!empty($this->page->theme->settings->heading_bg)) {
          $url = $this->page->theme->setting_file_url('heading_bg', 'heading_bg');
          // Get a URL suitable for moodle_url.
          $relativebaseurl = preg_replace('|^https?://|i', '//', $CFG->wwwroot);
          $url = str_replace($relativebaseurl, '', $url);
          return new moodle_url($url);
          return parent::get_theme_image_heading_bg($maxwidth, $maxheight);
      }

  }
  public function get_theme_image_favicon($maxwidth = null, $maxheight = 100) {
      global $CFG;
      if (!empty($this->page->theme->settings->favicon)) {
          $url = $this->page->theme->setting_file_url('favicon', 'favicon');
          // Get a URL suitable for moodle_url.
          $relativebaseurl = preg_replace('|^https?://|i', '//', $CFG->wwwroot);
          $url = str_replace($relativebaseurl, '', $url);
          return new moodle_url($url);
          return parent::get_theme_image_favicon($maxwidth, $maxheight);
      }

  }

  public function get_theme_image_preloader_image($maxwidth = null, $maxheight = 100) {
      global $CFG;
      if (!empty($this->page->theme->settings->preloader_image)) {
          $url = $this->page->theme->setting_file_url('preloader_image', 'preloader_image');
          // Get a URL suitable for moodle_url.
          $relativebaseurl = preg_replace('|^https?://|i', '//', $CFG->wwwroot);
          $url = str_replace($relativebaseurl, '', $url);
          return new moodle_url($url);
          return parent::get_theme_image_preloader_image($maxwidth, $maxheight);
      }

  }



  /**
   * Renders a custom menu object (located in outputcomponents.php)
   *
   * The custom menu this method produces makes use of the YUI3 menunav widget
   * and requires very specific html elements and classes.
   *
   * @staticvar int $menucount
   * @param custom_menu $menu
   * @return string
   */
  protected function render_custom_menu(custom_menu $menu) {
      global $CFG;

      $langs = get_string_manager()->get_list_of_translations();
      $haslangmenu = $this->lang_menu() != '';

      if (!$menu->has_children() && !$haslangmenu) {
          return '';
      }

      if ($haslangmenu) {
          $strlang = get_string('language');
          $currentlang = current_language();
          if (isset($langs[$currentlang])) {
              $currentlang = $langs[$currentlang];
          } else {
              $currentlang = $strlang;
          }
          $this->language = $menu->add($currentlang, new moodle_url('#'), $strlang, 10000);
          foreach ($langs as $langtype => $langname) {
              $this->language->add($langname, new moodle_url($this->page->url, array('lang' => $langtype)), $langname);
          }
      }

      $content = '';
      // print_object($menu->get_children());
      foreach ($menu->get_children() as $item) {
          // $context = $item->export_for_template($this);
          // $content .= $this->render_from_template('core/custom_menu_item', $context);
          $content .= $this->render_custom_menu_item($item);

          // foreach ($item->get_children() as $item2) {
          //     $context = $item2->export_for_template($this);
          //     $content .= $this->render_from_template('core/custom_menu_item', $context);
          // }

      }

      return $content;
  }

  /**
   * Renders a custom menu node as part of a submenu
   *
   * The custom menu this method produces makes use of the YUI3 menunav widget
   * and requires very specific html elements and classes.
   *
   * @see core:renderer::render_custom_menu()
   *
   * @staticvar int $submenucount
   * @param custom_menu_item $menunode
   * @return string
   */
  protected function render_custom_menu_item(custom_menu_item $menunode) {
      // Required to ensure we get unique trackable id's
      static $submenucount = 0;
      if ($menunode->has_children()) {

          // If the child has menus render it as a sub menu
          $submenucount++;
          $content = html_writer::start_tag('li');
          if ($menunode->get_url() !== null) {
              $url = $menunode->get_url();
          } else {
              $url = '#cm_submenu_'.$submenucount;
          }
          // $content .= html_writer::link($url, format_text($menunode->get_text(), FORMAT_HTML), array('class'=>'ccn-menu-item', 'title'=>$menunode->get_title()));
          $content .= html_writer::link($url, $menunode->get_text(), array('class'=>'ccn-menu-item', 'title'=>$menunode->get_title()));
          // $content .= html_writer::start_tag('div', array('id'=>'cm_submenu_'.$submenucount, 'class'=>'yui3-menu custom_menu_submenu'));
          // $content .= html_writer::start_tag('div', array('class'=>'yui3-menu-content'));
          $content .= html_writer::start_tag('ul');

          foreach ($menunode->get_children() as $menunode) {
              $content .= $this->render_custom_menu_item($menunode);
          }

          $content .= html_writer::end_tag('ul');
          // $content .= html_writer::end_tag('div');
          // $content .= html_writer::end_tag('div');
          $content .= html_writer::end_tag('li');
      } else {
          // The node doesn't have children so produce a final menuitem.
          // Also, if the node's text matches '####', add a class so we can treat it as a divider.
          $content = '';
          if (preg_match("/^#+$/", $menunode->get_text())) {

              // This is a divider.
              $content = html_writer::start_tag('li', array('class' => ''));
          } else {
              $content = html_writer::start_tag(
                  'li',
                  array(
                      'class' => ''
                  )
              );
              if ($menunode->get_url() !== null) {
                  $url = $menunode->get_url();
              } else {
                  $url = '#';
              }
              $content .= html_writer::link(
                  $url,
                  // format_text($menunode->get_text(), FORMAT_HTML),
                  $menunode->get_text(),
                  array('class' => '', 'title' => $menunode->get_title())
              );
          }
          $content .= html_writer::end_tag('li');
      }
      // Return the sub menu
      return $content;
  }


  /**
   * The standard tags (meta tags, links to stylesheets and JavaScript, etc.)
   * that should be included in the <head> tag. Designed to be called in theme
   * layout.php files.
   *
   * @return string HTML fragment.
   */
  public function standard_head_html() {
      global $CFG, $SESSION, $SITE, $PAGE;

      // Before we output any content, we need to ensure that certain
      // page components are set up.

      // Blocks must be set up early as they may require javascript which
      // has to be included in the page header before output is created.
      foreach ($this->page->blocks->get_regions() as $region) {
          $this->page->blocks->ensure_content_created($region, $this);
      }

      $output = '';

      // Give plugins an opportunity to add any head elements. The callback
      // must always return a string containing valid html head content.
      $pluginswithfunction = get_plugins_with_function('before_standard_html_head', 'lib.php');
      foreach ($pluginswithfunction as $plugins) {
          foreach ($plugins as $function) {
              $output .= $function();
          }
      }

      // Allow a url_rewrite plugin to setup any dynamic head content.
      if (isset($CFG->urlrewriteclass) && !isset($CFG->upgraderunning)) {
          $class = $CFG->urlrewriteclass;
          $output .= $class::html_head_setup();
      }

      $output .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . "\n";

      if(!empty($this->page->theme->settings->meta_keywords)){
        $output .= '<meta name="keywords" content="'.$this->page->theme->settings->meta_keywords.'" />' . "\n";
      } else {
        $output .= '<meta name="keywords" content="moodle, ' . $this->page->title . '" />' . "\n";
      }

      // This is only set by the {@link redirect()} method
      $output .= $this->metarefreshtag;

      // Check if a periodic refresh delay has been set and make sure we arn't
      // already meta refreshing
      if ($this->metarefreshtag=='' && $this->page->periodicrefreshdelay!==null) {
          $output .= '<meta http-equiv="refresh" content="'.$this->page->periodicrefreshdelay.';url='.$this->page->url->out().'" />';
      }

      // Set up help link popups for all links with the helptooltip class
      $this->page->requires->js_init_call('M.util.help_popups.setup');

      $focus = $this->page->focuscontrol;
      if (!empty($focus)) {
          if (preg_match("#forms\['([a-zA-Z0-9]+)'\].elements\['([a-zA-Z0-9]+)'\]#", $focus, $matches)) {
              // This is a horrifically bad way to handle focus but it is passed in
              // through messy formslib::moodleform
              $this->page->requires->js_function_call('old_onload_focus', array($matches[1], $matches[2]));
          } else if (strpos($focus, '.')!==false) {
              // Old style of focus, bad way to do it
              debugging('This code is using the old style focus event, Please update this code to focus on an element id or the moodleform focus method.', DEBUG_DEVELOPER);
              $this->page->requires->js_function_call('old_onload_focus', explode('.', $focus, 2));
          } else {
              // Focus element with given id
              $this->page->requires->js_function_call('focuscontrol', array($focus));
          }
      }

      // Get the theme stylesheet - this has to be always first CSS, this loads also styles.css from all plugins;
      // any other custom CSS can not be overridden via themes and is highly discouraged
      $urls = $this->page->theme->css_urls($this->page);
      foreach ($urls as $url) {
          $this->page->requires->css_theme($url);
      }

      // Get the theme javascript head and footer
      if ($jsurl = $this->page->theme->javascript_url(true)) {
          $this->page->requires->js($jsurl, true);
      }
      if ($jsurl = $this->page->theme->javascript_url(false)) {
          $this->page->requires->js($jsurl);
      }

      // Get any HTML from the page_requirements_manager.
      $output .= $this->page->requires->get_head_code($this->page, $this);

      // List alternate versions.
      foreach ($this->page->alternateversions as $type => $alt) {
          $output .= html_writer::empty_tag('link', array('rel' => 'alternate',
                  'type' => $type, 'title' => $alt->title, 'href' => $alt->url));
      }

      // Add noindex tag if relevant page and setting applied.
      $allowindexing = isset($CFG->allowindexing) ? $CFG->allowindexing : 0;
      $loginpages = array('login-index', 'login-signup');
      if ($allowindexing == 2 || ($allowindexing == 0 && in_array($this->page->pagetype, $loginpages))) {
          if (!isset($CFG->additionalhtmlhead)) {
              $CFG->additionalhtmlhead = '';
          }
          $CFG->additionalhtmlhead .= '<meta name="robots" content="noindex" />';
      }

      if (!empty($CFG->additionalhtmlhead)) {
          $output .= "\n".$CFG->additionalhtmlhead;
      }

      if ($PAGE->pagelayout == 'frontpage') {
          $summary = s(strip_tags(format_text($SITE->summary, FORMAT_HTML)));
          if(!empty($this->page->theme->settings->meta_description)){
            $output .= '<meta name="description" content="'.$this->page->theme->settings->meta_description.'" />' . "\n";
          } elseif (!empty($summary)) {
            $output .= "<meta name=\"description\" content=\"$summary\" />\n";
          }
      }
      if(!empty($this->page->theme->settings->meta_abstract)){
        $output .= '<meta name="abstract" content="'.$this->page->theme->settings->meta_abstract.'" />' . "\n";
      }

      return $output;
  }


  /**
   * The standard tags (typically performance information and validation links,
   * if we are in developer debug mode) that should be output in the footer area
   * of the page. Designed to be called in theme layout.php files.
   *
   * @return string HTML fragment.
   */
  public function standard_footer_html() {
global $CFG, $SCRIPT;

$output = '';
if (during_initial_install()) {
    // Debugging info can not work before install is finished,
    // in any case we do not want any links during installation!
    return $output;
}

// Give plugins an opportunity to add any footer elements.
// The callback must always return a string containing valid html footer content.
$pluginswithfunction = get_plugins_with_function('standard_footer_html', 'lib.php');
foreach ($pluginswithfunction as $plugins) {
    foreach ($plugins as $function) {
        $output .= $function();
    }
}

// This function is normally called from a layout.php file in {@link core_renderer::header()}
// but some of the content won't be known until later, so we return a placeholder
// for now. This will be replaced with the real content in {@link core_renderer::footer()}.
$output .= $this->unique_performance_info_token;
if ($this->page->devicetypeinuse == 'legacy') {
    // The legacy theme is in use print the notification
    $output .= html_writer::tag('div', get_string('legacythemeinuse'), array('class'=>'legacythemeinuse'));
}

// Get links to switch device types (only shown for users not on a default device)
$output .= $this->theme_switch_links();

if (!empty($CFG->debugpageinfo)) {
    $output .= '<div class="performanceinfo pageinfo">' . get_string('pageinfodebugsummary', 'core_admin',
        $this->page->debug_summary()) . '</div>';
}
if (debugging(null, DEBUG_DEVELOPER) and has_capability('moodle/site:config', context_system::instance())) {  // Only in developer mode
    // Add link to profiling report if necessary
    if (function_exists('profiling_is_running') && profiling_is_running()) {
        $txt = get_string('profiledscript', 'admin');
        $title = get_string('profiledscriptview', 'admin');
        $url = $CFG->wwwroot . '/admin/tool/profiling/index.php?script=' . urlencode($SCRIPT);
        $link= '<a title="' . $title . '" href="' . $url . '">' . $txt . '</a>';
        $output .= '<div class="profilingfooter">' . $link . '</div>';
    }
    $purgeurl = new moodle_url('/admin/purgecaches.php', array('confirm' => 1,
        'sesskey' => sesskey(), 'returnurl' => $this->page->url->out_as_local_url(false)));
    $output .= '<li class="list-inline-item"><div class="purgecaches">' .
            html_writer::link($purgeurl, get_string('purgecaches', 'admin')) . '</div></li>';
}
if (!empty($CFG->debugvalidators)) {
    // NOTE: this is not a nice hack, $PAGE->url is not always accurate and $FULLME neither, it is not a bug if it fails. --skodak
    $output .= '<div class="validators"><ul class="list-unstyled ml-1">
      <li><a href="http://validator.w3.org/check?verbose=1&amp;ss=1&amp;uri=' . urlencode(qualified_me()) . '">Validate HTML</a></li>
      <li><a href="http://www.contentquality.com/mynewtester/cynthia.exe?rptmode=-1&amp;url1=' . urlencode(qualified_me()) . '">Section 508 Check</a></li>
      <li><a href="http://www.contentquality.com/mynewtester/cynthia.exe?rptmode=0&amp;warnp2n3e=1&amp;url1=' . urlencode(qualified_me()) . '">WCAG 1 (2,3) Check</a></li>
    </ul></div>';
}
return $output;


  }


  /**
   * Returns standard main content placeholder.
   * Designed to be called in theme layout.php files.
   *
   * @return string HTML fragment.
   */
  public function main_content() {
      // This is here because it is the only place we can inject the "main" role over the entire main content area
      // without requiring all theme's to manually do it, and without creating yet another thing people need to
      // remember in the theme.
      // This is an unfortunate hack. DO NO EVER add anything more here.
      // DO NOT add classes.
      // DO NOT add an id.
      return $this->unique_main_content_token;
  }





  public function user_menu($user = null, $withlinks = null) {
      global $USER, $CFG;
      require_once($CFG->dirroot . '/user/lib.php');

      if (is_null($user)) {
          $user = $USER;
      }

      // Note: this behaviour is intended to match that of core_renderer::login_info,
      // but should not be considered to be good practice; layout options are
      // intended to be theme-specific. Please don't copy this snippet anywhere else.
      if (is_null($withlinks)) {
          $withlinks = empty($this->page->layout_options['nologinlinks']);
      }

      // Add a class for when $withlinks is false.
      $usermenuclasses = 'usermenu';
      if (!$withlinks) {
          $usermenuclasses .= ' withoutlinks';
      }

      $returnstr = "";

      // If during initial install, return the empty return string.
      if (during_initial_install()) {
          return $returnstr;
      }

      $loginpage = $this->is_login_page();
      $loginurl = get_login_url();
      // If not logged in, show the typical not-logged-in string.
      if (!isloggedin()) {
          $returnstr = get_string('loggedinnot', 'moodle');
          if (!$loginpage) {
              $returnstr .= " (<a href=\"$loginurl\">" . get_string('login') . '</a>)';
          }
          return html_writer::div(
              html_writer::span(
                  $returnstr,
                  'login'
              ),
              $usermenuclasses
          );

      }

      // If logged in as a guest user, show a string to that effect.
      if (isguestuser()) {
          $returnstr = get_string('loggedinasguest');
          if (!$loginpage && $withlinks) {
              $returnstr .= " (<a href=\"$loginurl\">".get_string('login').'</a>)';
          }

          return html_writer::div(
              html_writer::span(
                  $returnstr,
                  'login'
              ),
              $usermenuclasses
          );
      }

      // Get some navigation opts.
      $opts = user_get_user_navigation_info($user, $this->page);

      $avatarclasses = "avatars";
      $avatarcontents = html_writer::span($opts->metadata['useravatar'], 'avatar current');
      $usertextcontents = $opts->metadata['userfullname'];

      // Other user.
      if (!empty($opts->metadata['asotheruser'])) {
          $avatarcontents .= html_writer::span(
              $opts->metadata['realuseravatar'],
              'avatar realuser'
          );
          $usertextcontents = $opts->metadata['realuserfullname'];
          $usertextcontents .= html_writer::tag(
              'span',
              get_string(
                  'loggedinas',
                  'moodle',
                  html_writer::span(
                      $opts->metadata['userfullname'],
                      'value'
                  )
              ),
              array('class' => 'meta viewingas')
          );
      }

      // Role.
      if (!empty($opts->metadata['asotherrole'])) {
          $role = core_text::strtolower(preg_replace('#[ ]+#', '-', trim($opts->metadata['rolename'])));
          $usertextcontents .= html_writer::span(
              $opts->metadata['rolename'],
              'meta role role-' . $role
          );
      }

      // User login failures.
      if (!empty($opts->metadata['userloginfail'])) {
          $usertextcontents .= html_writer::span(
              $opts->metadata['userloginfail'],
              'meta loginfailures'
          );
      }

      // MNet.
      if (!empty($opts->metadata['asmnetuser'])) {
          $mnet = strtolower(preg_replace('#[ ]+#', '-', trim($opts->metadata['mnetidprovidername'])));
          $usertextcontents .= html_writer::span(
              $opts->metadata['mnetidprovidername'],
              'meta mnet mnet-' . $mnet
          );
      }

      $returnstr .= html_writer::span(
          html_writer::span($usertextcontents, 'usertext mr-1') .
          html_writer::span($avatarcontents, $avatarclasses),
          'userbutton'
      );

      // Create a divider (well, a filler).
      $divider = new action_menu_filler();
      $divider->primary = false;

      $am = new action_menu();
      $am->set_menu_trigger(
          $returnstr
      );
      $am->set_action_label(get_string('usermenu'));
      $am->set_alignment(action_menu::TR, action_menu::BR);
      $am->set_nowrap_on_items();
      $ccn_nav_items = '';
      if ($withlinks) {
          $navitemcount = count($opts->navitems);
          $idx = 0;
          foreach ($opts->navitems as $key => $value) {
            $ccn_nav_items .= '<a class="dropdown-item" href="'. $value->url .'">'. $value->title .'</a>';


              switch ($value->itemtype) {
                  case 'divider':
                      // If the nav item is a divider, add one and skip link processing.
                      $am->add($divider);
                      break;

                  case 'invalid':
                      // Silently skip invalid entries (should we post a notification?).
                      break;

                  case 'link':
                      // Process this as a link item.
                      $pix = null;
                      if (isset($value->pix) && !empty($value->pix)) {
                          $pix = new pix_icon($value->pix, '', null, array('class' => 'iconsmall'));
                      } else if (isset($value->imgsrc) && !empty($value->imgsrc)) {
                          $value->title = html_writer::img(
                              $value->imgsrc,
                              $value->title,
                              array('class' => 'iconsmall')
                          ) . $value->title;
                      }

                      $al = new action_menu_link_secondary(
                          $value->url,
                          $pix,
                          $value->title,
                          array('class' => 'icon')
                      );
                      if (!empty($value->titleidentifier)) {
                          $al->attributes['data-title'] = $value->titleidentifier;
                      }
                      $am->add($al);
                      break;
              }

              $idx++;

          }
      }

     /* return html_writer::div(
          $this->render($am),
          $usermenuclasses
      );*/
      return $ccn_nav_items;
  }


  /**
   * Prints a nice side block with an optional header.
   *
   * @param block_contents $bc HTML for the content
   * @param string $region the region the block is appearing in.
   * @return string the HTML to be output.
   */
  public function block(block_contents $bc, $region) {
      $bc = clone($bc); // Avoid messing up the object passed in.
      if (empty($bc->blockinstanceid) || !strip_tags($bc->title)) {
          $bc->collapsible = block_contents::NOT_HIDEABLE;
      }

      $id = !empty($bc->attributes['id']) ? $bc->attributes['id'] : uniqid('block-');
      $context = new stdClass();
      $context->skipid = $bc->skipid;
      $context->blockinstanceid = $bc->blockinstanceid;
      $context->dockable = $bc->dockable;
      $context->id = $id;
      $context->hidden = $bc->collapsible == block_contents::HIDDEN;
      $context->skiptitle = strip_tags($bc->title);
      $context->showskiplink = !empty($context->skiptitle);
      $context->arialabel = $bc->arialabel;
      $context->ariarole = !empty($bc->attributes['role']) ? $bc->attributes['role'] : 'complementary';
      $context->class = $bc->attributes['class'];
      $context->type = $bc->attributes['data-block'];
      if(array_key_exists('ccn_style', $bc->attributes)){
        $context->ccn_style = $bc->attributes['ccn_style'];
      }
      $context->title = $bc->title;
      $context->content = $bc->content;
      $context->annotation = $bc->annotation;
      $context->footer = $bc->footer;
      $context->hascontrols = !empty($bc->controls);
      if ($context->hascontrols) {
          $context->controls = $this->block_controls($bc->controls, $id);
      }

      return $this->render_from_template('core/block', $context);
  }


  /**
   * Render the login signup form into a nice template for the theme.
   *
   * @param mform $form
   * @return string
   */
  // public function render_login_signup_form($form) {
  //     global $SITE;
  //
  //     $context = $form->export_for_template($this);
  //     $url = $this->get_logo_url();
  //     if ($url) {
  //         $url = $url->out(false);
  //     }
  //     $context['logourl'] = $url;
  //     $context['sitename'] = format_string($SITE->fullname, true,
  //             ['context' => context_course::instance(SITEID), "escape" => false]);
  //
  //     return $this->render_from_template('ccn_signup_form_layout', $context);
  // }


}
