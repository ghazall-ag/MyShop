<?php

add_shortcode('cardshop','create_card_shop');

function create_card_shop()
{
	
	global $wpdb;
	
	require_once(plugin_dir_path(__FILE__).'/lib/nusoap.php');

	if (!empty($_POST['submit']))
	{//post form

		$email=$_POST['email'];
		$mobile=$_POST['mobile'];
		
		
		$error="";
		
		if (!is_email($email))
		{
			$error.=__('Invalid email address .','my-shop')."<br/>";
		}
		
		if (!is_mobile($mobile))
		{
			$error.=__('Invalid mobile number.','my-shop')."<br/>";
		}
		
	
		if ($error=="")
		{//no error
	
		//start payment
		
	$MerchantID = get_option('merchant'); 
	$Amount = 1000; 
	$Description = 'Buy a card'; 
	$Email = '';
	$Mobile = '';
	$CallbackURL = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	
	$client=new nusoap_client('https://sandbox.zarinpal.com/pg/services/WebGate/wsdl','wsdl');
	$client->soap_defencoding = 'UTF-8';
	
	$result=$client->call('PaymentRequest',array(
	
	'MerchantID' => $MerchantID,
	'Amount' => $Amount,
	'Description' => $Description,
	'Email' => $Email,
	'Mobile' => $Mobile,
	'CallbackURL' => $CallbackURL
	
	
	)
	
	);
	
	$wpdb->query("insert into ".shop_order_table." (email,mobile,bank_key,date_time)values('$email','$mobile','".$result['Authority']."',now())");
	
	 $url="https://sandbox.zarinpal.com/pg/StartPay/".$result['Authority'];
	
	
	?>
	
	<script>document.location='<?php  echo $url ?>'</script>
	
	<?php
	
	
			
			
		}else
		{//error
	
		?>
		
			
	<div class="wrap">
	
	<h2><?php _e('Card Shop','my-shop'); ?></h2>
	<form action="" method="post">
	<br/><br/>
	
	<?php _e('Error','my-shop'); ?> : <br/><?php echo $error ;?>
	
	<br/>
	
	
	<?php _e('Email','my-shop'); ?> : <input type="text" name="email" value="<?php  echo $email ?>"/>
	<br/><br/>
	
	<?php _e('Mobile','my-shop'); ?> : <input type="text" name="mobile" value="<?php  echo $mobile ?>"/>
	<br/><br/>
	
	<br/>
	
	<input name="submit" type="submit" value="<?php _e('Buy','my-shop'); ?>"/>
	
	
	</form>
	
	</div>
		
		
		<?php
	
	
			
	}
		
	}
	else if(!empty($_GET['Authority']))
	{ //call back

		if ($_GET['Status']=="OK")
		{
			
			$order=$wpdb->get_row("select * from ".shop_order_table." where bank_key='".$_GET['Authority']."'",ARRAY_A);
			
			if ($order)
			{
				
				$client=new nusoap_client('https://sandbox.zarinpal.com/pg/services/WebGate/wsdl','wsdl');
				$client->soap_defencoding = 'UTF-8';
				
				$MerchantID = get_option('merchant'); ;
				$Amount = 1000; 
				$Authority = $_GET['Authority'];
				
				$result=$client->call('PaymentVerification',array(
				'MerchantID' => $MerchantID,
				'Authority' => $Authority,
				'Amount' => $Amount
				));
				
				
				if ($result['Status']==100)
				{//valid payment
			
			
					$card=$wpdb->get_row("select * from ".shop_card_table." where status=0 limit 1",ARRAY_A);
					
					$wpdb->query("update ".shop_card_table." set order_id=".$order['id'].",status=1 where id=".$card['id']);
					
					
					$wpdb->query("update ".shop_order_table." set status=1 where id=".$order['id']);
					
					
					
					
					_e('Your card : ','my-shop');
					echo "<br/><br/>";
					
					echo $card['code'];
					
					
					//send email
					wp_mail($order['email'],__('Your card','my-shop'),"Your card : ".$card['code']);
					
					
				}
				else
				{
					_e('Payment error .','my-shop');
				}
				
				
				
			}else
			{
				_e('Order not exist .','my-shop');
			}
			
			
		}else
		{
			_e('Payment error .','my-shop');
		}
		
		
	
		
	}else
	{//default form
	
	
	?>
	
	<div class="wrap">
	
	<h2><?php _e('Card Shop','my-shop'); ?></h2>
	<form action="" method="post">
	<br/><br/>
	
	
	<?php _e('Email','my-shop'); ?> : <input type="text" name="email" value=""/>
	<br/><br/>
	
	<?php _e('Mobile','my-shop'); ?> : <input type="text" name="mobile" value=""/>
	<br/><br/>
	
	<br/>
	
	<input name="submit" type="submit" value="<?php _e('Buy','my-shop'); ?>"/>
	
	
	</form>
	
	</div>
	
	
	<?php
	
	}
	
	
}



function is_mobile($mobile)
{
if(preg_match('/^(((\+|00)98)|0)?9[123]\d{8}$/', $mobile))
    return true;
else
    return false;
	
}

?>