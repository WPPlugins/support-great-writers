<?php
/*
Plugin Name: Amazon Book Store
Plugin URI: https://wordpress.org/plugins/support-great-writers/
Description: Sell Amazon products in sidebar widgets, unique to the individual POST or generically from a default pool of products that you define.
Author: HeyPublisher
Author URI: https://www.heypublisher.com
Version: 2.2.1

  Copyright 2009-2014 Loudlever (wordpress@loudlever.com)
  Copyright 2014-2017 Richard Luck (https://github.com/aguywithanidea/)
  Copyright 2017 HeyPublisher (https://www.heypublisher.com/)

  Permission is hereby granted, free of charge, to any person
  obtaining a copy of this software and associated documentation
  files (the "Software"), to deal in the Software without
  restriction, including without limitation the rights to use,
  copy, modify, merge, publish, distribute, sublicense, and/or sell
  copies of the Software, and to permit persons to whom the
  Software is furnished to do so, subject to the following
  conditions:

  The above copyright notice and this permission notice shall be
  included in all copies or substantial portions of the Software.

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
  EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
  OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
  NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
  HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
  WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
  FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
  OTHER DEALINGS IN THE SOFTWARE.

*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
	exit;
}

/*
---------------------------------------------------------------------------------
  OPTION SETTINGS
---------------------------------------------------------------------------------
*/

define('SGW_DEBUG',false);
define('SGW_PLUGIN_VERSION', '2.2.1');
define('SGW_PLUGIN_OPTTIONS', '_sgw_plugin_options');
define('SGW_BASE_URL', get_option('siteurl').'/wp-content/plugins/support-great-writers/');
define('SGW_DEFAULT_IMAGE', get_option('siteurl').'/wp-content/plugins/support-great-writers/images/not_found.gif');
define('SGW_POST_META_KEY','SGW_ASIN');
define('SGW_ADMIN_PAGE','amazon_bookstore');
define('SGW_ADMIN_PAGE_NONCE','sgw-save-options');
define('SGW_PLUGIN_ERROR_CONTACT','Please contact <a href="mailto:wordpress@heypublisher.com?subject=Amazon%20Bookstore%20Widget">wordpress@heypublisher.com</a> if you have any questions');
define('SGW_BESTSELLERS','1455570249,144947425X,1501164589,0692859055');
define('SGW_PLUGIN_FILE',plugin_basename(__FILE__));

require_once(dirname(__FILE__) . '/include/classes/SGW_Widget.class.php');
require_once(dirname( __FILE__ ) . '/include/classes/AMZNBS/Admin.class.php');
$sgw = new \AMZNBS\Admin;

// enable link to settings page
add_filter($sgw->plugin_filter(), array(&$sgw,'plugin_link'), 10, 2 );

function RegisterAdminPage() {
  global $sgw;
  // ensure our js and style sheet only get loaded on our admin page
  $page = add_options_page('Amazon Book Store', 'Amazon Book Store', 'manage_options', SGW_ADMIN_PAGE, array(&$sgw,'action_handler'));
  $sgw->help = $page;
  add_action("admin_print_scripts-$page", 'AdminInit');
  add_action("admin_print_styles-$page", array(&$sgw,'admin_stylesheets'));
}

function AdminInit() {
  wp_enqueue_script('sgw', WP_PLUGIN_URL . '/support-great-writers/include/js/sgw.js');
}
function RegisterWidgetStyle() {
	wp_enqueue_style( 'sgw_widget', SGW_BASE_URL . 'css/sgw_widget.css', array(), '1.2.2' );
}

if (class_exists("SupportGreatWriters")) {
  add_action('widgets_init', create_function('', 'return register_widget("SupportGreatWriters");'));
  add_action('admin_menu', 'RegisterAdminPage');
	add_action('wp_enqueue_scripts','RegisterWidgetStyle');
	add_filter('contextual_help', array(&$sgw,'configuration_screen_help'), 10, 3);

}
register_activation_hook( __FILE__, array(&$sgw,'activate_plugin'));
register_deactivation_hook( __FILE__, array(&$sgw,'deactivate_plugin'));
?>
