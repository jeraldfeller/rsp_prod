<?php
$alternateemail_address10 = null;
$alternateemail_address11 = null;
$alternateemail_address12 = null;
$alternateemail_address13 = null;
$chk10 = "";
$chk11 = "";
$chk12 = "";
$chk13 = "";
if($user->fetch_user_group_id()==1) {
	header('Location: agent_account_update.php');
}
$user_query = $database->query("select email_notification,email_address,page_url,default_order_by,display_view, billing_method_id, service_level_id, agency_id from " . TABLE_USERS . " where user_id = '" . $user->fetch_user_id() . "' limit 1");
$user_result = $database->fetch_array($user_query);

$description_query = $database->query("select firstname, lastname from " . TABLE_USERS_DESCRIPTION . " where user_id = '" . $user->fetch_user_id() . "' limit 1");
$description_result = $database->fetch_array($description_query);
//echo "<pre>";  print_r($user_result); echo "</pre>";
/**/	
$alternate_email_query = $database->query("select * from emails_to_users where user_id = '" . $user->fetch_user_id() . "' order by email_to_user_id desc");

/**/
if($user->fetch_user_group_id()==3) {
	//echo "select ug.*,p.page_url from ".TABLE_USER_GROUPS_TO_PAGES." ug INNER JOIN ".TABLE_PAGES." p ON ug.page_id = p.page_id where ug.user_group_id = '3'";
	 $pages_c = $database->query("select ug.*,p.page_url from ".TABLE_USER_GROUPS_TO_PAGES." ug INNER JOIN ".TABLE_PAGES." p ON ug.page_id = p.page_id where ug.user_group_id = '3'");
}

$alternate_email_count_query = $database->query("select * from emails_to_users where user_id = '" . $user->fetch_user_id() . "'");
$resnum = $database->num_rows($alternate_email_count_query);


foreach($alternate_email_count_query as $res){
	$a=$res['email_to_user_id'];
}

$update_success = false;

$update_account = tep_fill_variable('update_account', 'get');

$first_name = tep_fill_variable('first_name', 'post', $description_result['firstname']);
$last_name = tep_fill_variable('last_name', 'post', $description_result['lastname']);
$email_address = tep_fill_variable('email_address', 'post', $user_result['email_address']);
$phone_numbers_array = array('1' => '', '2' => '', '3' => '', '4' => '');

$number_query = $database->query("select phone_number, order_id from " . TABLE_USERS_PHONE_NUMBERS . " where user_id = '" . $user->fetch_user_id() . "' order by order_id");
foreach($number_query as $number_result)
{
	$phone_numbers_array[$number_result['order_id']] = $number_result['phone_number'];
}
	
$phone_number = tep_fill_variable('phone_number', 'post',$phone_numbers_array['1']);
$second_phone_number = tep_fill_variable('second_phone_number', 'post',$phone_numbers_array['2']);
$optional_third_phone_number = tep_fill_variable('optional_third_phone_number', 'post',$phone_numbers_array['3']);
$optional_fourth_phone_number = tep_fill_variable('optional_fourth_phone_number', 'post',$phone_numbers_array['4']);
$agency_id = tep_fill_variable('agency_id', 'post', $user_result['agency_id']);
$create_agency = tep_fill_variable('create_agency');
$agency_name = tep_fill_variable('agency_name');
$agency_address = tep_fill_variable('agency_address');
$contact_name = tep_fill_variable('contact_name');
$contact_phone = tep_fill_variable('contact_phone');
$service_level_id = tep_fill_variable('service_level_id', 'post', $user_result['service_level_id']);
$billing_method_id = tep_fill_variable('billing_method_id', 'post', $user_result['billing_method_id']);

$submit_type = tep_fill_variable('submit_type');
$submit_type_x = tep_fill_variable('submit_type_x');
$submit_type_y = tep_fill_variable('submit_type_y');
if (!empty($update_account) && ($update_account == 'update') && (!empty($submit_type_x) && !empty($submit_type_y))) 
{
	/**/
	$uid=$user->fetch_user_id();	
	$alternateemail_address10 = tep_fill_variable('alternate_email_address10', 'post',$_POST['alternate_email_address10']);
	
	$alternateemail_address11=tep_fill_variable('alternate_email_address11', 'post',$_POST['alternate_email_address11']);
	$alternateemail_address12=tep_fill_variable('alternate_email_address12', 'post',$_POST['alternate_email_address12']);
	$alternateemail_address13=tep_fill_variable('alternate_email_address13', 'post',$_POST['alternate_email_address13']);

	$chk10= (isset($_POST['chkexpert10']) ? $_POST['chkexpert10'] : "");
	$chk11= (isset($_POST['chkexpert11']) ? $_POST['chkexpert11'] : "");
	$chk12= (isset($_POST['chkexpert12']) ? $_POST['chkexpert12'] : "");
	$chk13= (isset($_POST['chkexpert13']) ? $_POST['chkexpert13'] : "");
	if(!empty($alternateemail_address10)&&($chk10!=""))
	{
		$database->query("insert into emails_to_users(user_id,email_address,email_status) values($uid,'{$alternateemail_address10}',$chk1)") or die(mysql_error("error-$qr"));
	}
	if(!empty($alternateemail_address11)&&($chk11!=""))
	{
		$database->query("insert into emails_to_users (user_id,email_address,email_status) values('" . $user->fetch_user_id() . "','" . $alternateemail_address11 . "','" .$chk11. "')") or die(mysql_error(0));
	}
	if(!empty($alternateemail_address12)&&($chk12!=""))
	{
		$database->query("insert into emails_to_users (user_id,email_address,email_status) values('" . $user->fetch_user_id() . "','" . $alternateemail_address12 . "','" .$chk12. "')") or die(mysql_error(0));
	}
	if(!empty($alternateemail_address13)&&($chk13!=""))
	{
		$database->query("insert into emails_to_users (user_id,email_address,email_status) values('" . $user->fetch_user_id() . "','" . $alternateemail_address13 . "','" .$chk13. "')") or die(mysql_error(0));
	}
	
	if(!empty($alternateemail_address10)&&($chk10==""))
	{
		$database->query("insert into emails_to_users (user_id,email_address) values('" . $user->fetch_user_id() . "','" . $alternateemail_address10 . "')") or die(mysql_error(0));
	}
	if(!empty($alternateemail_address11)&&($chk11==""))
	{
		$database->query("insert into emails_to_users (user_id,email_address) values('" . $user->fetch_user_id() . "','" . $alternateemail_address11 . "')") or die(mysql_error(0));
	}
	if(!empty($alternateemail_address12)&&($chk12==""))
	{
		$database->query("insert into emails_to_users (user_id,email_address) values('" . $user->fetch_user_id() . "','" . $alternateemail_address12 . "')") or die(mysql_error(0));
	}
	if(!empty($alternateemail_address13)&&($chk13==""))
	{
		$database->query("insert into emails_to_users (user_id,email_address) values('" . $user->fetch_user_id() . "','" . $alternateemail_address13 . "')") or die(mysql_error(0));
	}
	
	/**/			//User has submitted form.  Now chech data and then insert into database.
	if (empty($first_name)) 
	{
		$error->add_error('account_update', 'Please enter a First Name.');
	}
	if (empty($last_name)) 
	{
		$error->add_error('account_update', 'Please enter a Last Name.');
	}
	if (empty($email_address) ) 
	{
		$error->add_error('account_update', 'Please enter an Email Address.');
	}
	if (!tep_validate_email_address($email_address)) 
	{
		$error->add_error('account_update', 'Please enter a valid Email Address.');
	}
	if(!empty($email_address) && tep_email_address_exists($email_address)) 
	{
		$error->add_error('account_update', 'That email address is already registered to another user.');
	}
	if ((empty($phone_number))) 
	{
		$error->add_error('account_update', 'Please enter a Phone Number.');
	}
	if ((!tep_validate_phone_number($phone_number))) 
	{
		$error->add_error('account_update', 'Please enter a valid Phone Number.');
	}
	if ((empty($second_phone_number))) 
	{
		$error->add_error('account_update', 'Please enter a Second Phone Number.');
	}
	if ((!tep_validate_phone_number($second_phone_number))) 
	{
		$error->add_error('account_update', 'Please enter a valid Second Phone Number.');
	}
	if ((!empty($optional_third_phone_number) && !tep_validate_phone_number($optional_third_phone_number))) 
	{
		$error->add_error('account_update', 'Please enter a valid Third Phone Number or leave blank.');
	}
	if ((!empty($optional_fourth_phone_number) && !tep_validate_phone_number($optional_fourth_phone_number))) 
	{
		$error->add_error('account_update', 'Please enter a valid Fourth Phone Number or leave blank.');
	}
	if (!empty($create_agency)) 
	{
		if (empty($agency_name) || empty($agency_address) || empty($contact_name) || empty($contact_phone)) 
		{
			$error->add_error('account_update', 'Please either select an Agency or enter all the Agency Information to add a new one.');
		}
	}
	if ((empty($service_level_id)) && $user->fetch_user_group_id() != 3) 
	{
		$error->add_error('account_update', 'Please select a Service Level.');
	}
	if ((empty($billing_method_id))) 
	{
		$error->add_error('account_update', 'Please select a Billing Method.');
	}
	if (!$error->get_error_status('account_update')) 
	{
		//No error. Add.
        if (!empty($create_agency)) {
		    //New Agency.  Add to database.
			$database->query("INSERT INTO " . TABLE_AGENCYS . " (name, address, contact_name, contact_phone, agency_status_id, billing_method_id, service_level_id) values ('" . $agency_name . "', '" . $agency_address . "', '" . $contact_name . "', '" . $contact_phone . "', '1', '1', '1')");
			//Set agency as new agency_id.
            $agency_id = $database->insert_id();
            $create_agency = '';
		}
        
        //start add 08.01.2014 DrTech76, hook teh user to agency change log
        $uID=$user->fetch_user_id();
		$sql="SELECT `agency_id` FROM `".TABLE_USERS."` WHERE `user_id`=".$uID;
		$old_agency_res=$database->query($sql);
		$old_agency=$database->fetch_array($old_agency_res);
		$old_agency=(int)$old_agency["agency_id"];
		//end add 08.01.2014 DrTech76, hook teh user to agency change log
        $page_url=isset($_POST['page_url'])?$_POST['page_url']:'';
        $display_view=isset($_POST['display_view'])?$_POST['display_view']:'';
        $default_order_by=isset($_POST['default_order_by'])?$_POST['default_order_by']:'';
        $email_notify=isset($_POST['email_notify'])?$_POST['email_notify']:'yes';
		
        $database->query("update " . TABLE_USERS . " set email_notification='".$email_notify."', page_url='".$page_url."',default_order_by='".$default_order_by."',display_view='".$display_view."',  last_modified = '" . mktime() . "', email_address = '" . $email_address . "', billing_method_id = '" . $billing_method_id . "', service_level_id = '" . $service_level_id . "', agency_id = '" . $agency_id . "' where user_id = '" . $user->fetch_user_id() . "' limit 1");

		
		//start add 08.01.2014 DrTech76, hook teh user to agency change log
		if($old_agency!=(int)$agency_id)
		{
			$sql="INSERT INTO `agencies_to_users`(`user_id`,`agency_id`,`action_date`,`account_action_type`,`account_action_from`) VALUES (".$user->fetch_user_id().",".$agency_id.",NOW(),'update','own')";
            $database->query($sql);
            $sql="UPDATE " . TABLE_ACCOUNTS . " SET agency_id = '{$agency_id}' WHERE user_id = '{$user->fetch_user_id()}' LIMIT 1";
            $database->query($sql);
		}
		//end add 08.01.2014 DrTech76, hook teh user to agency change log
		
		
//	MJP					} else {
//	MJP						$database->query("update " . TABLE_USERS . " set last_modified = '" . mktime() . "', email_address = '" . $email_address . "' where user_id = '" . $user->fetch_user_id() . "' limit 1");
//		MJP				}
		//Now add as a new user.
		
		$user_id = $database->insert_id();
		$database->query("update " . TABLE_USERS_DESCRIPTION . " set firstname = '" . $first_name . "', lastname = '" . $last_name . "' where user_id = '" . $user->fetch_user_id() . "' limit 1");
		
/**/
		$a=$_POST['alternate_email_address'];
		$email_status=$_POST['chkexpert'];
		for($i=1;$i<=4;$i++)
		{
			$email_status[]=$_POST["chkexpert$i"];
		}
		$alternateemail_address=$a;
		$i=0;
		
		foreach($alternate_email_query as $res){
		 	$estatus=$email_status[$i]!=""?1:0;
			$database->query("update emails_to_users set email_status='".$estatus."',email_address ='".$a[$i]."' where user_id = '" . $user->fetch_user_id() . "' and email_to_user_id='".$res['email_to_user_id']."'")or die(mysql_error(0));
			$i++;
		}


/**/
		//$database->query("insert into " . TABLE_USERS_TO_USER_GROUPS . " (user_id, user_group_id) values ('" . $user_id . "', '1')");
		$phone_numbers_array = array();
		$phone_numbers_array[] = $phone_number;
		$phone_numbers_array[] = $second_phone_number;
		if (!empty($optional_third_phone_number)) 
		{
			$phone_numbers_array[] = $optional_third_phone_number;
		}
		if (!empty($optional_fourth_phone_number)) 
		{
			$phone_numbers_array[] = $optional_fourth_phone_number;
		}
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
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="style9">&PAGE_TEXT</td>
	</tr>
	<tr>
		<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
	</tr>
	<tr>
		<td valign="top" width="100%" align="left">
			<form name="update_account" method="post" action="account_update.php?update_account=update">
				<table cellspacing="0" cellpadding="0" class="pageBox">
					<tr>
						<td>
							<table cellpadding="0" cellspacing="3">
								<tr>
									<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
								</tr>
                                <?php

                                if ($error->get_error_status('account_update')) {
                                    ?>
									<tr>
										<td class="mainError" colspan="2"><?php echo $error->get_error_string('account_update'); ?></td>
									</tr>
                                    <?php
                                }
                                ?>
                                <?php
                                if ($update_success) {
                                    ?>
									<tr>
										<td class="mainSuccess" colspan="2">Your Account has been successfully updated.</td>
									</tr>
                                    <?php
                                }
                                ?>
								<tr>
									<td class="mainLarge" colspan="2">Personal Information</td>
								</tr>
								<tr>
									<td height="3"><img src="images/pixel_trans.gif" height="3" width="1"></td>
								</tr>
								<tr>
									<td class="main">First Name: </td><td><input type="text" name="first_name" value="<?php echo $first_name; ?>" /></td>
								</tr>
								<tr>
									<td class="main">Last Name: </td><td><input type="text" name="last_name" value="<?php echo $last_name; ?>" /></td>
								</tr>
								<tr>
									<td class="main">Email Address: </td><td><input type="text" name="email_address" value="<?php echo $email_address; ?>" /></td>
								</tr>

                                <?php

                                if($resnum > 0){
                                	$i = 0;
                                    foreach($alternate_email_query as $alternate_result) {

                                        ?>
										<tr>
											<td class="main">Alternate Email: </td><td><input type="text" name="alternate_email_address[]" value="<?php echo $alternate_result['email_address']; ?>" />

												<input type="checkbox" name="chkexpert1<?php echo $i;?>"  <?php if($alternate_result['email_status']=='1'){?> checked="true" value=<?php echo $alternate_result['email_status']?><?php }?> >Receive emails on this Email Address

											</td>
										</tr>
                                        <?php
                                        $i++;
                                    }
                                    /**/
                                }else{ ?>

									<tr>
										<td class="main">Alternate Email 1: </td><td><input type="text" name="alternate_email_address10" value="<?php echo $alternateemail_address10; ?>" /><input type="checkbox" name="chkexpert10" value="1" <?php if($chk10!=""){?> checked="true" <?php  }?>>Receive emails on this Email Address</td>
									</tr>

									<tr>
										<td class="main">Alternate Email 2: </td><td><input type="text" name="alternate_email_address11" value="<?php echo $alternateemail_address11; ?>" /><input type="checkbox" name="chkexpert11" value="1" <?php  if($chk11!=""){?> checked="true" <?php  }?>>Receive emails on this Email Address</td>
									</tr>
									<tr>
										<td class="main">Alternate Email 3: </td><td><input type="text" name="alternate_email_address12" value="<?php echo $alternateemail_address12; ?>" /><input type="checkbox" name="chkexpert12" value="1"  <?php  if($chk12!=""){?> checked="true" <?php  }?>>Receive emails on this Email Address</td>
									</tr>
									<tr>
										<td class="main">Alternate Email 4: </td><td><input type="text" name="alternate_email_address13" value="<?php echo $alternateemail_address13; ?>" /><input type="checkbox" name="chkexpert13" value="1" <?php  if($chk13!=""){?> checked="true" <?php  }?>>Receive emails on this Email Address</td>
									</tr>
                                    <?php
                                }
                                ?>

								<tr>
									<td class="main">Cell Phone Number: </td><td><input type="text" name="phone_number" value="<?php echo $phone_number; ?>" /></td>
								</tr>
								<tr>
									<td class="main">Phone Number: </td><td><input type="text" name="second_phone_number" value="<?php echo $second_phone_number; ?>" /></td>
								</tr>
								<tr>
									<td class="main">Fax Number: </td><td><input type="text" name="optional_third_phone_number" value="<?php echo $optional_third_phone_number; ?>" /></td>
								</tr>
								<tr>
									<td class="main">Optional Phone Number: </td><td><input type="text" name="optional_fourth_phone_number" value="<?php echo $optional_fourth_phone_number; ?>" /></td>
								</tr>
                                <?php
                                if ($user->fetch_user_group_id() == 3) {
                                    echo '<tr>
								<td class="main">Landing Page: </td>
								<td><select name="page_url">';
                                    foreach($pages_c as $userpages)
                                    {
                                        if($user_result['page_url'] == $userpages['page_url']){
                                            $selected = 'selected';
                                        }else{
                                            $selected = '';
                                        }
                                        echo '<option value="'.$userpages['page_url'].'" '.$selected.'>'.$userpages['page_url'].'</option>';
                                    }
                                    echo "</td> </tr>";

                                    ?>
									<tr>
										<td class="main">Default show view: </td>
										<td>
											<select name="display_view">
												<option value="overview" <?php echo ($user_result['display_view']=='overview')?'selected':''?>>Overview</option>
												<option value="detailed" <?php echo ($user_result['display_view']=='detailed')?'selected':''?>>Detailed</option></select>
										</td>
									</tr>
									<tr>
										<td class="main">Default order by: </td>
										<td>
											<select  name="default_order_by">
												<option value="1" <?php echo ($user_result['default_order_by']==1)?'selected':''?>>Order</option>
												<option value="2" <?php echo ($user_result['default_order_by']==2)?'selected':''?>>Date Scheduled</option>
												<option value="3" <?php echo ($user_result['default_order_by']==3)?'selected':''?>>Date Ordered</option>
												<option value="4" <?php echo ($user_result['default_order_by']==4)?'selected':''?>>Date Accepted</option>
												<option value="5" <?php echo ($user_result['default_order_by']==5)?'selected':''?>>House Number</option>
												<option value="6" <?php echo ($user_result['default_order_by']==6)?'selected':''?>>Street Name</option>
											</select>
										</td>
									</tr>
									<tr>
										<td class="main">E-Mail Notifications for New Orders: </td>
										<td>
											<select  name="email_notify">
												<option value="yes" <?php echo ($user_result['email_notification']=='yes')?'selected':''?>>Yes</option>
												<option value="no" <?php echo ($user_result['email_notification']=='no')?'selected':''?>>No</option>

											</select>
										</td>
									</tr>
                                    <?php
                                }
                                ?>
                                <?php
                                if ($user->fetch_user_group_id() == 1) {
                                    ?>
									<tr>
										<td height="5"><img src="images/pixel_trans.gif" height="8" width="1"></td>
									</tr>
									<tr>
										<td class="mainLarge" colspan="2">Agency Information</td>
									</tr>
									<tr>
										<td height="3"><img src="images/pixel_trans.gif" height="3" width="1"></td>
									</tr>
									<tr>
										<td class="main" colspan="2">Select your agency below or check the box to create a new one.</td>
									</tr>
									<tr class="existingAgency<?php echo ($create_agency == '1' ? ' hidden' : ''); ?>">
										<td class="main">Select Agency: </td><td><?php echo tep_draw_agency_pulldown('agency_id', "{$agency_id}", ' onchange="this.form.submit();"', array(), '', true, false); ?></td>
									</tr>
									<script language="javascript">
                                        function toggle_fields_status() {
                                            if (fields_status) {
                                                fields_status = false;
                                                $(".newAgency").addClass("hidden");
                                                $(".existingAgency").removeClass("hidden");
                                            } else {
                                                fields_status = true;
                                                $(".newAgency").removeClass("hidden");
                                                $(".existingAgency").addClass("hidden");
                                            }
                                        }

                                        var fields_status = "<?php echo ($create_agency == '1' ? 'false' : 'true'); ?>";
                                        toggle_fields_status();
									</script>

                                <?php
                                if (!empty($agency_id) && is_numeric($agency_id)) {
                                $query = $database->query("select name, service_level_id, billing_method_id, address, contact_name, contact_phone from " . TABLE_AGENCYS . " where agency_id = '" . $agency_id . "' limit 1");
                                $result = $database->fetch_array($query);
                                if (empty($service_level_id)) {
                                    $service_level_id = $result['service_level_id'];
                                }
                                if (empty($billing_method_id)) {
                                    $billing_method_id = $result['billing_method_id'];
                                }
                                ?>
									<tr class="existingAgency<?php echo ($create_agency == '1' ? ' hidden' : ''); ?>">
										<td class="main">Agency Name: </td><td class="mainGrey"><?php echo $result['name']; ?></td>
									</tr>
									<tr class="existingAgency<?php echo ($create_agency == '1' ? ' hidden' : ''); ?>">
										<td class="main">Agency Address: </td><td class="mainGrey"><?php echo $result['address']; ?></td>
									</tr>
									<tr class="existingAgency<?php echo ($create_agency == '1' ? ' hidden' : ''); ?>">
										<td class="main">Contact Name: </td><td class="mainGrey"><?php echo $result['contact_name']; ?></td>
									</tr>
									<tr class="existingAgency<?php echo ($create_agency == '1' ? ' hidden' : ''); ?>">
										<td class="main">Contact Phone: </td><td class="mainGrey"><?php echo $result['contact_phone']; ?></td>
									</tr>
									<tr>
										<td class="main">Create New Agency: </td><td class="main"><input type="checkbox" name="create_agency" onclick="javascript:toggle_fields_status();" value="1" <?php echo ($create_agency == '1' ? 'CHECKED' : ''); ?> /></td>
									</tr>
									<tr class="newAgency<?php echo ($create_agency != '1' ? ' hidden' : ''); ?>">
										<td class="main">Agency Name: </td><td class="main"><input type="text" id="agency_name" name="agency_name" value="" /></td>
									</tr>
									<tr class="newAgency<?php echo ($create_agency != '1' ? ' hidden' : ''); ?>">
										<td class="main">Agency Address: </td><td class="main"><input type="text" id="agency_address" name="agency_address" value="" /></td>
									</tr>
									<tr class="newAgency<?php echo ($create_agency != '1' ? ' hidden' : ''); ?>">
										<td class="main">Managing Broker: </td><td class="main"><input type="text" id="contact_name" name="contact_name" value="" /></td>
									</tr>
									<tr class="newAgency<?php echo ($create_agency != '1' ? ' hidden' : ''); ?>">
										<td class="main">Contact Phone: </td><td class="main"><input type="text" id="contact_phone" name="contact_phone" value="" /></td>
									</tr>
                                <?php
                                }

                                $service_levels_array = array();
                                $query = $database->query("select service_level_id, name from " . TABLE_SERVICE_LEVELS . " order by service_level_id");
                                foreach($query as $result){
                                    $service_levels_array[] = array('id' => $result['service_level_id'], 'name' => $result['name']);
                                }

                                if (empty($billing_method_id)) {
                                    $billing_method_id = 1;
                                }
                                $billing_methods_array = array();
                                $query = $database->query("select billing_method_id, name from " . TABLE_BILLING_METHODS . " where billing_method_id <= '" . $billing_method_id . "' order by billing_method_id");
                                foreach($query as $result){
                                    $billing_methods_array[] = array('id' => $result['billing_method_id'], 'name' => $result['name']);
                                }

                                ?>
									<tr>
										<td height="5"><img src="images/pixel_trans.gif" height="8" width="1"></td>
									</tr>
									<tr>
										<td class="mainLarge" colspan="2">Billing Information</td>
									</tr>
									<tr>
										<td height="3"><img src="images/pixel_trans.gif" height="3" width="1"></td>
									</tr>
									<tr>
										<td class="main">Service Level: </td><td class="main"><?php echo tep_generate_pulldown_menu('service_level_id', $service_levels_array, $service_level_id); ?></td>
									</tr>
									<tr>
										<td class="main">Billing Method: </td><td class="main"><?php echo tep_generate_pulldown_menu('billing_method_id', $billing_methods_array, $billing_method_id); ?></td>
									</tr>
                                    <?php
                                }
                                ?>
								<tr>
									<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
								</tr>
								<tr>
									<td colspan="2" align="right"><a href="<?php echo FILENAME_ACCOUNT_OVERVIEW; ?>"><?php echo tep_create_button_link('cancel', 'Cancel'); ?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo tep_create_button_submit('update_account', 'Update Account', ' name="submit_type" value="Update Account"'); ?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
					</tr>
					<tr>
						<td class="mainSmall"></td>
					</tr>
				</table>
			</form>
		</td>
		<td valign="top" class="main"></td>
	</tr>
</table>