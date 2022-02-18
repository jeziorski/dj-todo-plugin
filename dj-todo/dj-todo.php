<?php
 
/*
 
Plugin Name: DJ - to do list
 
Plugin URI: https://danieljeziorski.pl
 
Description: To do for WP.
 
Version: 1.0
 
Author: Daniel Jeziorski
 
Author URI: https://danieljeziorski.pl
 
License: GPLv2 or later
 
Text Domain: Dj to do
 
*/

define('TO_DO_PATH', plugin_dir_path(__FILE__));

register_activation_hook(__FILE__, 'activateToDo');
function activateToDo()
{
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$tableName = $wpdb->prefix . 'dj_to_do';

	$sql = "CREATE TABLE $tableName (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time datetime DEFAULT '0000-00-00 00:00:00',
		todo text NOT NULL,
		status text NOT NULL DEFAULT 'NEW',
		UNIQUE KEY id (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}
 /**
 * Deactivate plugin - remove DB table
 */
register_deactivation_hook(__FILE__, 'deactivateToDo');
function deactivateToDo()
{
	global $wpdb;
    $tableName = $wpdb->prefix . 'dj_to_do';
	$wpdb->query("DROP TABLE $tableName;");	
}


/**
 * Admin page
*/
add_action('admin_menu', 'addToDoToAdmin');
function addToDoToAdmin()
{
    add_menu_page('DJ - to do', 'DJ To Do list', 'administrator', 'to-do', 'render', 'dashicons-editor-ul');
}

function render()
{
	global $wpdb;
	$tableName = $wpdb->prefix . 'dj_to_do';	
	include_once( 'view.php' );  
	
    echo '<h2>'.__('To do list', 'todo').'</h2>';
	
	
	
	if(isset($_POST['refresh'])){
		$wpdb->query("TRUNCATE TABLE $tableName");
	}
	if(isset($_POST['update-to-do']) AND isset($_POST['id']) AND isset($_POST['status'])){
		$todoid = $_POST['id'];
		$todostatus = $_POST['status'];
		$wpdb->query("UPDATE $tableName SET `status` = '$todostatus' WHERE `id` = '$todoid'");
	}
	
	$todos = $wpdb->get_results("SELECT * FROM $tableName");
	if(!empty($todos)){echo '<form method="post" action="/wp-admin/admin.php?page=to-do"><input style="background-color:#EF6D6D; color:#fff;border:none;box-shadow: 0 5px 10px 0 rgba(0,0,0,.08);border-radius: 1.875rem;padding: .75rem 1.5rem;font-size: .75rem;" type="submit" name="refresh" value="Refresh to do list"/></form>';}
    echo '<div class="wrap">';	
	
	
    if(!empty($todos)) {
        echo '<table class="wp-list-table widefat fixed posts">';        
        echo '<thead>';
        echo '<tr>';
        echo '<th>ID</th>';
        echo '<th>'.__('To do', 'todo').'</th>';
        echo '<th>'.__('Time', 'todo').'</th>';
        echo '<th>'.__('Status', 'todo').'</th>';
        echo '<th>'.__('Action', 'todo').'</th>';
        echo '</tr>';
        echo '</thead>';
        
        echo '<tbody id="the-list">';
		
		
        foreach($todos as $todo) {
			if($todo->status != 'DONE'){
				echo '<tr><td>'.$todo->id.'</td><td>'.$todo->todo.'</td><td>'.$todo->time.'</td><td>'.$todo->status.'</td><td><form method="post" action="/wp-admin/admin.php?page=to-do">
			<input type="hidden" value="'.$todo->id.'" name="id">
			<input type="hidden" name="status" value="DONE">
			<input name="update-to-do" style="background-color: #65C18C;padding: .75rem 1.5rem;font-size: .75rem;border:none;border-radius: 1.875rem;box-shadow: 0 5px 10px 0 rgba(0,0,0,.08); color:#fff;" type="submit" value="Change status to done"/>
			</form></td></tr>';
			}else{
			echo '<tr style="text-decoration:line-through"><td>'.$todo->id.'</td><td>'.$todo->todo.'</td><td>'.$todo->time.'</td><td>'.$todo->status.'</td><td><form method="post" action="/wp-admin/admin.php?page=to-do">
			<input type="hidden" value="'.$todo->id.'" name="id">
			<input type="hidden" name="status" value="DONE">
			<input disabled name="update-to-do" style="background-color: #fff;padding: .75rem 1.5rem;font-size: .75rem;border:#65C18C 1px solid;border-radius: 1.875rem;box-shadow: 0 5px 10px 0 rgba(0,0,0,.08); color:#65C18C;" type="submit" value="Done" />
			</form></td></tr>';
			}
               
        }
        echo '</tbody>';
 
        echo '</table>';    
    } else {
        echo __('No todos added', 'todo');
    }
    
    echo '</div>';
}
add_action('admin_post_toDoResponse','toDoResponse');  
function toDoResponse()
{   
    if(isset($_POST['todo_sent'])) {        
        $todo = $_POST['todo'];        
        if(empty($todo)) {
            echo '<span class="todo-error">'.__('To do field cannot be empty.', 'todo').'</span>';           
        }
		$tableName = $wpdb->prefix . 'dj_to_do';
        $wpdb->insert($tableName, 'todo', $todo); 
		
		$wpdb->insert( 
			$tableName, 
			array( 
				'time' => current_time( 'mysql' ), 
				'todo' => $todo, 
			) 
		);        
        echo '<span class="todo-success">'.__('To do addeed.', 'todo').'</span>';        
		exit();
    }
}
