<div class="wrap">
 
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<style>.form-control{display: block;width: 100%;padding: .5rem 1rem;font-size: .875rem;font-weight: 400;line-height: 1.4rem;color: #495057;background-color: #fff;background-clip: padding-box;border: 1px solid #dedede;appearance: none;border-radius: 30px;transition: box-shadow .15s ease,border-color .15s ease;}</style>
	<form action="/wp-admin/admin.php?page=to-do" method="post">
	<input type="hidden" name="action" value="test"> 
	<div style="margin-bottom:10px;"><label >What you want to do?</label></div>
	<div style="display:flex;width:50%;"><input type="text" class="form-control" name="todo" placeholder="What you want to do?" required>
	<input type="submit" class="form-control" value="Add new to do"></div>
	</form>		
		<?php if(isset($_POST['action'])){			
			$todo = $_POST['todo'];
			if($wpdb->insert( 
			$tableName, 
			array( 
				'time' => current_time( 'mysql' ), 
				'todo' => $todo, 
			))){
				echo '</br><span style="color:green;">'.__('To do addeed.', 'todo').'</span>';  
			}
		}?>
</div><!-- .wrap -->