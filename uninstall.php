<?php
/*
* Uninstall plugin
*/

if (!defined('WP_UNINSTALL_PLUGIN'))
	exit();

if (is_multisite())
{
	
	$sites=wp_get_sites();
		
		foreach($sites as $site)
		{
			
			switch_to_blog($site['blog_id']);
			
			uninstall();
			
		}
		
		restore_current_blog();
	
	
}else
{
	
		uninstall();
	
}


function uninstall()
{
	global $wpdb;
	
	delete_option('merchant');
	
	
	define('shop_card_table',$wpdb-> prefix."shop_card");
	define('shop_order_table',$wpdb-> prefix."shop_order");
	
	
  global $wpdb;
   
	$sql="DROP TABLE IF EXISTS ".shop_card_table;
	

	 $wpdb->query( $sql);
	
	
	$sql="DROP TABLE IF EXISTS ".shop_order_table;
	
 $wpdb->query( $sql);
	
	
	
}

?>