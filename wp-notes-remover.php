<?php
/*
  Plugin Name: WP Notes Remover
  Plugin URI: http://club.orbisius.com/products/wordpress-plugins/wp-notes-remover/
  Description: WP Notes Remover removes "You May Use These HTML tags and attributes" below the comments. No necessary theme hacks needed.
  Version: 1.0.6
  Author: Svetoslav Marinov (Slavi)
  Author URI: http://Orbisius.com
  License: GPL v2
 */

/*
  Copyright 2011-2020 Svetoslav Marinov (slavi@slavi.biz)

  This program ais free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; version 2 of the License.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

require_once dirname( __FILE__ ) . '/config.php';

// we can be called from the test script
if (empty($_ENV['WebWeb_WP_NotesRemover_TEST'])) {
    // Make sure we don't expose any info if called directly
    if (!function_exists('add_action')) {
        echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
        exit;
    }
    
	$WebWeb_WP_NotesRemover_obj = WebWeb_WP_NotesRemover::get_instance();
	
    add_action('init', array($WebWeb_WP_NotesRemover_obj, 'init'));

    register_activation_hook(__FILE__, array($WebWeb_WP_NotesRemover_obj, 'on_activate'));
    register_deactivation_hook(__FILE__, array($WebWeb_WP_NotesRemover_obj, 'on_deactivate'));    
}
