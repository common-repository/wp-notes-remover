<?php 

if (!defined('WP_UNINSTALL_PLUGIN')) {
	exit();
}

require_once dirname( __FILE__ ) . '/config.php';

$WebWeb_WP_NotesRemover_obj = WebWeb_WP_NotesRemover::get_instance();
$WebWeb_WP_NotesRemover_obj->on_uninstall();

