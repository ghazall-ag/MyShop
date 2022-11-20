<?php

/*
Plugin Name: MyShop
Version: 1.0
Author: ghazall_agoush
Description: فروشگاه کارت شارژ
License: GPLv2




global $wpdb;

define('shop_card_table',$wpdb-> prefix."shop_card");
define('shop_order_table',$wpdb-> prefix."shop_order");

include_once(plugin_dir_path(__FILE__)."\shop-shortcode.php");


add_action('plugins_loaded','add_textdomain');

function add_textdomain()
{
	
	load_plugin_textdomain('my-shop',false,dirname(plugin_basename(__FILE__)));
	
}

register_activation_hook(__FILE__,'activation_func');

function activation_func()
{
	
	
require_once(ABSPATH.'wp-admin/includes/upgrade.php');
	

if ($wpdb->get_var("show tables like '".shop_card_table."'")!=shop_card_table)
{
	

$sql='CREATE TABLE  '.shop_card_table.' (
`id` INT NOT NULL AUTO_INCREMENT ,
`code` VARCHAR( 50 ) NOT NULL ,
`order_id` INT NOT NULL ,
`status` INT NOT NULL ,
PRIMARY KEY (  `id` )
) ENGINE = MYISAM ;';

dbDelta($sql);

}


if ($wpdb->get_var("show tables like '".shop_order_table."'")!=shop_order_table)
{

$sql='CREATE TABLE  '.shop_order_table.' (
`id` INT NOT NULL AUTO_INCREMENT ,
`email` VARCHAR( 50 ) NOT NULL ,
`mobile` VARCHAR( 11 ) NOT NULL ,
`bank_key` VARCHAR( 100 ) NOT NULL ,
`date_time` DATETIME NOT NULL ,
`status` INT NOT NULL ,
PRIMARY KEY (  `id` )
) ENGINE = MYISAM ;';

dbDelta($sql);


}

	
	
}


add_action('admin_menu','create_shop_menu');

function create_shop_menu()
{
	
	
	add_menu_page(__('Card Shop','my-shop'),__('Card Shop','my-shop'),'manage_options',__FILE__,'card_list_page',plugins_url('/images/shop-icon.png',__FILE__));
	


function card_list_page()
{
	
	global $wpdb;
	
	
	if (!empty($_POST['card_code'])&&!empty($_POST['submit']))
	{ //insert card

		$code=trim($_POST['card_code']);
		
		if (strlen($code)>=5)
		{//true card code
	
	
		$wpdb->query("insert into ".shop_card_table." (code)values('$code')");
			
	
	
		?>
	
	<div id="message" class="updated" > <?php _e('Card saved .','my-shop'); ?> </div>
	
	<?php
			
	
			
		}else
		{//show error
	
	?>
	
	<div id="message" class="error" > <?php _e('Card code is not valid .','my-shop'); ?> </div>
	
	<?php
			
		}
		
		
	} else 	if (!empty($_POST['card_id'])&&!empty($_POST['submit2']))
	{ //remove card
		
		$id=$_POST['card_id'];
		
		$wpdb->query("delete from ".shop_card_table." where id=".$id);
		
			?>
	
	<div id="message" class="updated" > <?php _e('Card deleted .','my-shop'); ?> </div>
	
	<?php
			
		
		


	}
	
	
	?>
	
	<div class="wrap">
	<h2><?php _e('Card List','my-shop'); ?></h2>
	<form action="" method="post">
	<br/><br/>
	
	<input type="text" name="card_code"/> <input name="submit" type="submit" value="<?php _e('Add Card','my-shop'); ?>" class="button-secondary"/>
	
	
	
	</form>
	
	<br/>	<br/>
	<hr>
	<br/>	<br/>
	
	
	
	<?php
	
		$cards=$wpdb->get_results("select * from ".shop_card_table." where status=0");
		
		
		if ($cards)
		{
	?>
	
	
	<table class="widefat">
	<thead>
	<tr>
	
	<th><?php _e('Card Code','my-shop'); ?></th>
	<th><?php _e('Remove','my-shop'); ?></th>
	
	</tr>
	
	</thead>
	<tbody>
	
	
	<?php
		
		foreach($cards as $card)
		{
	
	?>
	
	<tr>
	<td><?php echo $card->code; ?></td>
	<td><form action="" method="post"><input type="hidden" name="card_id" value="<?php echo $card->id?>"/><input name="submit2" type="submit" value="<?php _e('Remove','my-shop'); ?>"  class="button-secondary" onclick="return confirm('<?php _e('Are you sure ?','my-shop'); ?>');"/></form></td>
	</tr>
	
	<?php
		}
	?>
	
	
		
	
	</tbody>
	
	
	</table>
	
	<?php
		}
		else
		{
			_e('Card not exist .','my-shop');
		}
	?>
	
	
	
	
	
	
	
	
	
	</div>
	
	
	
	<?php
}


add_submenu_page(__FILE__,__('Orders','my-shop'),__('Orders','my-shop'),'manage_options',__FILE__.'_orders','order_list_page');


function order_list_page()
{
	
	global $wpdb;
	
	
	
	?>
	
	<div class="wrap">
	<h2><?php _e('Order List','my-shop'); ?></h2>
	<br/><br/>
	<?php

	
		$orders=$wpdb->get_results("select * from ".shop_card_table.",".shop_order_table." where ".shop_order_table.".status=1 and ".shop_order_table.".id=".shop_card_table.".order_id order by ".shop_order_table.".id desc");
		
		
		if ($orders)
		{
	?>
	
	
	<table class="widefat">
	<thead>
	<tr>
	
	<th><?php _e('Email','my-shop'); ?></th>
	<th><?php _e('Mobile','my-shop'); ?></th>
	<th><?php _e('Date/Time','my-shop'); ?></th>
	<th><?php _e('Card Code','my-shop'); ?></th>
	</tr>
	
	</thead>
	<tbody>
	
	
	<?php
		
		foreach($orders as $order)
		{
	
	?>
	
	<tr>
	<td><?php echo $order->email; ?></td>
	<td><?php echo $order->mobile; ?></td>
	<td><?php echo $order->date_time; ?></td>
	<td><?php echo $order->code; ?></td>
	</tr>
	
	<?php
		}
	?>
	
	
		
	
	</tbody>
	
	
	</table>
	
	<?php
		}
		else
		{
			_e('Order not exist .','my-shop');
		}
	?>
	
	
	
	
	
	
	
	
	
	</div>
	
	
	
	<?php
}




add_submenu_page(__FILE__,__('Settings','my-shop'),__('Settings','my-shop'),'manage_options',__FILE__.'_settings','settings_page');


function settings_page()
{
	
	
	
	
	if (!empty($_POST['submit']))
	{
		
		
	$merchant = @($_POST['merchant']);
		
	update_option('merchant',$merchant);
	
	
	}
	
	$merchant = get_option('merchant');
	
	
	
	?>
	
	<div class="wrap">
	<h2><?php  _e('Plugin settings','my-shop') ?></h2>
	<form action="" method="post">
	<br/>	<br/>	<br/>
	
	<?php _e('Zarinpal merchant','my-shop') ?> :  <input type="text" name="merchant" value="<?php echo $merchant; ?>"/>
	<br/>	<br/>
	
	<br/>
			
			<input name="submit" type="submit" value="<?php _e('Save settings','my-shop') ?>" class="button-secondary"/>
	
	
	
	
	
	
	
	</form>
	
	
	</div>
	
	
	<?php
	
	
}





add_submenu_page(__FILE__,__('About','my-shop'),__('About','my-shop'),'manage_options',__FILE__.'_about','about_page');


function about_page()
{
	echo "<p><center><br/><br/><br/><br/><br/>";
	
	echo __('Card Shop Plugin','my-shop')."<br/><br/>";
	echo __('Version 1.1','my-shop')."<br/><br/>";
	echo __('Author : ghazall','my-shop')."<br/><br/>";
	
	
	echo "</p>";
	
}





}

?>