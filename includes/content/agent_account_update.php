<?php
//this script needs to be rewritten
//error_reporting(0); dsf
$user_query = $database->query("select email_address, billing_method_id, service_level_id, agency_id,is_recieve_inventory from " . TABLE_USERS . " where user_id = '" . $user->fetch_user_id() . "' limit 1");
$user_result = $database->fetch_array($user_query);
#echo '<pre>'; print_r($user_result); die;
$description_query = $database->query("select firstname, lastname from " . TABLE_USERS_DESCRIPTION . " where user_id = '" . $user->fetch_user_id() . "' limit 1");
$description_result = $database->fetch_array($description_query);
/**/	
$alternate_email_query = $database->query("select * from emails_to_users where user_id = '" . $user->fetch_user_id() . "' order by email_to_user_id desc");
$email_res = array($database->fetch_array($alternate_email_query));
//print_r($email_res);
/**/

//$alternate_email_count_query = $database->query("select * from emails_to_users where user_id = '" . $user->fetch_user_id() . "'");
//$resnum=$database->num_rows($alternate_email_count_query);

//while($res=$database->fetch_array($alternate_email_count_query))
	
$resnum=$database->num_rows($alternate_email_query);
while($res=$database->fetch_array($alternate_email_query))
{
	$a=$res['email_to_user_id'];
}
//var_dump($a);
$update_success = false;

$update_account = tep_fill_variable('update_account', 'get');

$form['first_name'] = tep_fill_variable('first_name', 'post', $description_result['firstname']);
$form['last_name'] = tep_fill_variable('last_name', 'post', $description_result['lastname']);
$form['email_address'] = tep_fill_variable('email_address', 'post', $user_result['email_address']);
$form['is_recieve_inventory'] = tep_fill_variable('is_recieve_inventory', 'post', $user_result['is_recieve_inventory']);

$phone_numbers_array = array('1' => '', '2' => '', '3' => '', '4' => '');

$number_query = $database->query("select phone_number, order_id from " . TABLE_USERS_PHONE_NUMBERS . " where user_id = '" . $user->fetch_user_id() . "' order by order_id");
while($number_result = $database->fetch_array($number_query)) 
{
	$phone_numbers_array[$number_result['order_id']] = $number_result['phone_number'];
}
	
$form['phone_number'] = tep_fill_variable('phone_number', 'post',$phone_numbers_array['1']);
$form['second_phone_number'] = tep_fill_variable('second_phone_number', 'post',$phone_numbers_array['2']);
$form['optional_third_phone_number'] = tep_fill_variable('optional_third_phone_number', 'post',$phone_numbers_array['3']);
$form['optional_fourth_phone_number'] = tep_fill_variable('optional_fourth_phone_number', 'post',$phone_numbers_array['4']);
$form['agency_id'] = tep_fill_variable('agency_id', 'post', $user_result['agency_id']);
$form['create_agency'] = tep_fill_variable('create_agency');
$form['agency_name'] = tep_fill_variable('agency_name');
$form['agency_address'] = tep_fill_variable('agency_address');
$form['contact_name'] = tep_fill_variable('contact_name');
$form['contact_phone'] = tep_fill_variable('contact_phone');
$form['service_level_id'] = tep_fill_variable('service_level_id', 'post', $user_result['service_level_id']);
$form['billing_method_id'] = tep_fill_variable('billing_method_id', 'post', $user_result['billing_method_id']);

$submit_type = tep_fill_variable('submit_type');
$submit_type_x = tep_fill_variable('submit_type_x');
$submit_type_y = tep_fill_variable('submit_type_y');

if (!empty($update_account) && ($update_account == 'update')) 
{
	//die();
	/**/
	$uid=$user->fetch_user_id();
	
	//foreach($_POST[])
	#echo '<pre>';print_r($_POST); die;
	
	
	
	$alternateemail_address10 = tep_fill_variable('alternate_email_address10', 'post', $_POST['alternate_email_address'][0]);//tep_fill_variable('alternate_email_address10', 'post',$_POST['alternate_email_address10']);
	
	if (isset($_POST['alternate_email_address'][1])) {$alternateemail_address11= tep_fill_variable('alternate_email_address11', 'post',$_POST['alternate_email_address'][1]); } //tep_fill_variable('alternate_email_address11', 'post',$_POST['alternate_email_address11']);
	if (isset($_POST['alternate_email_address'][2])) {$alternateemail_address12= tep_fill_variable('alternate_email_address12', 'post',$_POST['alternate_email_address'][2]); } //tep_fill_variable('alternate_email_address12', 'post',$_POST['alternate_email_address12']);
	if (isset($_POST['alternate_email_address'][3])) {$alternateemail_address13= tep_fill_variable('alternate_email_address13', 'post',$_POST['alternate_email_address'][3]);} //tep_fill_variable('alternate_email_address13', 'post',$_POST['alternate_email_address13']);
	if (isset($_POST['chkexpert'][0])) { $chk10 = $_POST['chkexpert'][0]; } //$_POST['chkexpert10'];
	if (isset($_POST['chkexpert'][1])) { $chk11=$_POST['chkexpert'][1]; }//$_POST['chkexpert11'];
	if (isset($_POST['chkexpert'][2])) { $chk12=$_POST['chkexpert'][2]; }//$_POST['chkexpert12'];
	if (isset($_POST['chkexpert'][3])) { $chk13=$_POST['chkexpert'][3]; }//$_POST['chkexpert13'];
	
	
	((isset($_POST['is_recieve_inventory']) && $_POST['is_recieve_inventory'] ==1) ? $is_recieve_inventory=1 : $is_recieve_inventory=0);
	$form['is_recieve_inventory'] = $is_recieve_inventory;
	if(!empty($alternateemail_address10)&&($chk10!=""))
	{
		$database->query("insert into emails_to_users(user_id,email_address,email_status) values($uid,'{$alternateemail_address10}',$chk10)");
	}
	if(!empty($alternateemail_address11)&&($chk11!=""))
	{
		$database->query("insert into emails_to_users (user_id,email_address,email_status) values('" . $user->fetch_user_id() . "','" . $alternateemail_address11 . "','" .$chk11. "')");
	}
	if(!empty($alternateemail_address12)&&($chk12!=""))
	{
		$database->query("insert into emails_to_users (user_id,email_address,email_status) values('" . $user->fetch_user_id() . "','" . $alternateemail_address12 . "','" .$chk12. "')");
	}
	if(!empty($alternateemail_address13)&&($chk13!=""))
	{
		$database->query("insert into emails_to_users (user_id,email_address,email_status) values('" . $user->fetch_user_id() . "','" . $alternateemail_address13 . "','" .$chk13. "')");
	}
	
	if(!empty($alternateemail_address10)&&($chk10==""))
	{
		$database->query("insert into emails_to_users (user_id,email_address) values('" . $user->fetch_user_id() . "','" . $alternateemail_address10 . "')");
	}
	if(!empty($alternateemail_address11)&&($chk11==""))
	{
		$database->query("insert into emails_to_users (user_id,email_address) values('" . $user->fetch_user_id() . "','" . $alternateemail_address11 . "')");
	}
	if(!empty($alternateemail_address12)&&($chk12==""))
	{
		$database->query("insert into emails_to_users (user_id,email_address) values('" . $user->fetch_user_id() . "','" . $alternateemail_address12 . "')");
	}
	if(!empty($alternateemail_address13)&&($chk13==""))
	{
		$database->query("insert into emails_to_users (user_id,email_address) values('" . $user->fetch_user_id() . "','" . $alternateemail_address13 . "')");
	}
	
	/**/			//User has submitted form.  Now chech data and then insert into database.
	//echo $form['first_name'];
	if (empty($form['first_name'])) 
	{
		//die();
		$error->add_error('account_update', 'Please enter a First Name.');
	}
	if (empty($form['last_name'])) 
	{
		$error->add_error('account_update', 'Please enter a Last Name.');
	}
	if (empty($form['email_address']) ) 
	{
		$error->add_error('account_update', 'Please enter an Email Address.');
	}
	if (!tep_validate_email_address($form['email_address'])) 
	{
		$error->add_error('account_update', 'Please enter a valid Email Address.');
	}
	if(!empty($form['email_address']) && tep_email_address_exists($form['email_address'])) 
	{
		$error->add_error('account_update', 'That email address is already registered to another user.');
	}
	if ((empty($form['phone_number']))) 
	{
		$error->add_error('account_update', 'Please enter a Phone Number.');
	}
	if ((!tep_validate_phone_number($form['phone_number']))) 
	{
		$error->add_error('account_update', 'Please enter a valid Phone Number.');
	}
	if ((empty($form['second_phone_number']))) 
	{
		$error->add_error('account_update', 'Please enter a Second Phone Number.');
	}
	if ((!tep_validate_phone_number($form['second_phone_number']))) 
	{
		$error->add_error('account_update', 'Please enter a valid Second Phone Number.');
	}
	if ((!empty($form['optional_third_phone_number']) && !tep_validate_phone_number($form['optional_third_phone_number']))) 
	{
		$error->add_error('account_update', 'Please enter a valid Third Phone Number or leave blank.');
	}
	if ((!empty($form['optional_fourth_phone_number']) && !tep_validate_phone_number($form['optional_fourth_phone_number']))) 
	{
		$error->add_error('account_update', 'Please enter a valid Fourth Phone Number or leave blank.');
	}
	if (!empty($form['create_agency'])) 
	{
		if (empty($form['agency_name']) || empty($form['agency_address']) || empty($form['contact_name']) || empty($form['contact_phone'])) 
		{
			$error->add_error('account_update', 'Please either select an Agency or enter all the Agency Information to add a new one.');
		}
	}
	if ((empty($form['service_level_id']))) 
	{
		$error->add_error('account_update', 'Please select a Service Level.');
	}
	if ((empty($form['billing_method_id']))) 
	{
		$error->add_error('account_update', 'Please select a Billing Method.');
	}
	if (!$error->get_error_status('account_update')) 
	{
		//No error. Add.
        if (!empty($form['create_agency'])) {
		    //New Agency.  Add to database.
			$database->query("INSERT INTO " . TABLE_AGENCYS . " (name, address, contact_name, contact_phone, agency_status_id, billing_method_id, service_level_id) values ('" . $form['agency_name'] . "', '" . $form['agency_address'] . "', '" . $form['contact_name'] . "', '" . $form['contact_phone'] . "', '1', '1', '1')");
			//Set agency as new agency_id.
            $form['agency_id'] = $database->insert_id();
            $form['create_agency'] = '';
		}
        
        //start add 08.01.2014 DrTech76, hook teh user to agency change log
        $uID=$user->fetch_user_id();
		$sql="SELECT `agency_id` FROM `".TABLE_USERS."` WHERE `user_id`=".$uID;
		$old_agency_res=$database->query($sql);
		$old_agency=$database->fetch_array($old_agency_res);
		$old_agency=(int)$old_agency["agency_id"];
		//end add 08.01.2014 DrTech76, hook teh user to agency change log
        
        $database->query("update " . TABLE_USERS . " set last_modified = '" . time() . "', email_address = '" . $form['email_address'] . "', billing_method_id = '" . $form['billing_method_id'] . "', service_level_id = '" . $form['service_level_id'] . "', agency_id = '" . $form['agency_id'] . "', is_recieve_inventory='".$is_recieve_inventory."' where user_id = '" . $user->fetch_user_id() . "' limit 1");

		//start add 08.01.2014 DrTech76, hook teh user to agency change log is_recieve_inventory
		if($old_agency!=(int)$form['agency_id'])
		{
			$sql="INSERT INTO `agencies_to_users`(`user_id`,`agency_id`,`action_date`,`account_action_type`,`account_action_from`) VALUES (".$user->fetch_user_id().",".$form['agency_id'].",NOW(),'update','own')";
            $database->query($sql);
            $sql="UPDATE " . TABLE_ACCOUNTS . " SET agency_id = '{$form['agency_id']}' WHERE user_id = '{$user->fetch_user_id()}' LIMIT 1";
            $database->query($sql);
		}
		//end add 08.01.2014 DrTech76, hook teh user to agency change log
		
		
//	MJP					} else {
//	MJP						$database->query("update " . TABLE_USERS . " set last_modified = '" . time() . "', email_address = '" . $form['email_address'] . "' where user_id = '" . $user->fetch_user_id() . "' limit 1");
//		MJP				}
		//Now add as a new user.
		
		$user_id = $database->insert_id();
		$database->query("update " . TABLE_USERS_DESCRIPTION . " set firstname = '" . $form['first_name'] . "', lastname = '" . $form['last_name'] . "' where user_id = '" . $user->fetch_user_id() . "' limit 1");
		
/**/
		$a=$_POST['alternate_email_address'];
		//$email_status=isset($_POST['chkexpert']) ? $_POST['chkexpert'] : '';
		for($i=0;$i<=3;$i++)
		{
			if(isset($_POST["chkexpert"][$i])) {
				$email_status[]=$_POST["chkexpert"][$i];
			}	
		}
		$alternateemail_address=$a;
		$i=0;
		
		
		while($res=$database->fetch_array($alternate_email_query))
		{ 
			echo "update emails_to_users set email_status='".$estatus."',email_address ='".$a[$i]."' where user_id = '" . $user->fetch_user_id() . "' and email_to_user_id='".$res['email_to_user_id']."'";
		 	$estatus=$email_status[$i]!=""?1:0;
			$database->query("update emails_to_users set email_status='".$estatus."',email_address ='".$a[$i]."' where user_id = '" . $user->fetch_user_id() . "' and email_to_user_id='".$res['email_to_user_id']."'");
			$i++;
		}

	//	die();
/**/

		//$database->query("insert into " . TABLE_USERS_TO_USER_GROUPS . " (user_id, user_group_id) values ('" . $user_id . "', '1')");
		$phone_numbers_array = array();
		$phone_numbers_array[] = $form['phone_number'];
		$phone_numbers_array[] = $form['second_phone_number'];
		if (!empty($form['optional_third_phone_number'])) 
		{
			$phone_numbers_array[] = $form['optional_third_phone_number'];
		}
		if (!empty($form['optional_fourth_phone_number'])) 
		{
			$phone_numbers_array[] = $form['optional_fourth_phone_number'];
		}
		//print_r($phone_numbers_array);
		$count = count($phone_numbers_array);
		$n = 0;
		$database->query("delete from " . TABLE_USERS_PHONE_NUMBERS . "  where user_id = '" . $user->fetch_user_id() . "'");
		while($n < $count) 
		{
			$database->query("insert into " . TABLE_USERS_PHONE_NUMBERS . " (user_id, phone_number, order_id) values ('" . $user->fetch_user_id() . "', '" . $phone_numbers_array[$n] . "', '" . ($n + 1) . "')");
			$n++;
		}
		
		
		$update_success = true;
	}
}
if (!empty($form['agency_id']) && is_numeric($form['agency_id'])) {
		$query = $database->query("select name, service_level_id, billing_method_id, address, contact_name, contact_phone from " . TABLE_AGENCYS . " where agency_id = '" . $form['agency_id'] . "' limit 1");					$result = $database->fetch_array($query);	
		$vars['agency']['result'] = $result;
		
		if (empty($form['service_level_id'])) {											
			$form['service_level_id'] = $result['service_level_id'];		
		}
		if (empty($form['billing_method_id'])) {											
			$form['agency']['billing_method_id'] = $result['billing_method_id'];	
		}
	}						
	$service_levels_array = array();	
	$query = $database->query("select service_level_id, name from " . TABLE_SERVICE_LEVELS . " order by service_level_id");				
	while($result = $database->fetch_array($query)) {										
	$service_levels_array[] = array('id' => $result['service_level_id'], 'name' => $result['name']);								
	}
	if (empty($form['billing_method_id'])) {										
			$form['billing_method_id']= get_default_billing_method();		
		}
		$billing_methods_array = array();			
		$query = $database->query("select billing_method_id, name from " . TABLE_BILLING_METHODS . " where billing_method_id <= '" . $form['billing_method_id'] . "' order by billing_method_id");								
		while($result = $database->fetch_array($query)) {										
			$billing_methods_array[] = array('id' => $result['billing_method_id'], 'name' => $result['name']);							
		}
	$agency_id = $form['agency_id'];
	
	$vars['form'] = $form;
	$vars['agency']['pulldown'] = tep_draw_agency_pulldown_bgdn('agency_id', "{$agency_id}", 'change-submit', array(), '', true, false);
	
	#$vars['form']['is_recieve_inventory'] =  $user_result['is_recieve_inventory'];
	//print_r($email_res);
	$vars['form']['alternate_result'] = $email_res;
	//print_r($vars['form']['alternate_result']);
	$vars['pulldowns']['service_level'] = array('name'=>'service_level_id', 'contents'=> $service_levels_array, 'selected'=> $form['service_level_id']);
	$vars['pulldowns']['billing_method'] = array('name'=>'billing_method_id', 'contents'=> $billing_methods_array, 'selected'=> $form['billing_method_id']);
	echo $twig->render('agent/account_update.html.twig', array('user' => $user, 'page' => $page, 'error'=>$error, 'vars'=>$vars));
	
?>