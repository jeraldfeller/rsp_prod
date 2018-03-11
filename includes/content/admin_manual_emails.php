<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$mID = tep_fill_variable('mID', 'get');
	$submit_value = tep_fill_variable('submit_value', 'post');

	$message = '';
	$emails = tep_fill_variable('emails', 'post', array());
	$user_group_id = tep_fill_variable('user_group_id', 'post');
	$order_status = tep_fill_variable('order_status', 'post');

		
			$manual_email_id = tep_fill_variable('manual_email_id', 'post');
			$user_group_id = tep_fill_variable('user_group_id', 'post');
			$subject = tep_fill_variable('subject', 'post');
			$content = tep_fill_variable('content', 'post');
			$from = tep_fill_variable('from', 'post');
			$to = tep_fill_variable('to', 'post');
			$date_sent_time_stamp = mktime();

			
		if($page_action == 'add_confirm') {
				if(!empty($from)) {
					$from_time_stamp = strtotime($from);
				}
				if(!empty($to)) {
					$to_time_stamp = strtotime($to);
				}
	
							switch ($order_status) {
							case 0:
								$query = $database->query("select u.email_address, ud.firstname, ud.lastname from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug where utug.user_group_id = '" . $user_group_id . "' and utug.user_id = u.user_id and u.user_id = ud.user_id");
								break;
							case 1:
								$query = $database->query("select u.email_address, ud.firstname, ud.lastname from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug, " . TABLE_ORDERS . " o where o.user_id = u.user_id and u.user_id = ud.user_id and utug.user_group_id = " . $user_group_id . " and utug.user_id = u.user_id" . ((isset($from_time_stamp)) ? (" and o.date_added > '" . $from_time_stamp . "'") : '') . ((isset($to_time_stamp)) ? (" and o.date_added < '" . $to_time_stamp . "'") : '') . " group by u.user_id"); 
								break;
							case 2:
								$query = $database->query("select u.email_address, ud.firstname, ud.lastname from " . TABLE_USERS . " u left join "	. TABLE_ORDERS . " o on (u.user_id = o.user_id" . ((isset($from_time_stamp)) ? (" and o.date_added > '" . $from_time_stamp . "'") : '') . ((isset($to_time_stamp)) ? (" and o.date_added < '" . $to_time_stamp . "'") : '') . "), " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug where u.user_id = ud.user_id and utug.user_group_id = " . $user_group_id . " and utug.user_id = u.user_id and o.order_id IS NULL group by u.user_id");
								break;
							case 3:
								$query = $database->query("select u.email_address, ud.firstname, ud.lastname from " . TABLE_USERS . " u, " . TABLE_USERS_DESCRIPTION . " ud, " . TABLE_USERS_TO_USER_GROUPS . " utug where utug.user_group_id = '" . $user_group_id . "' and utug.user_id = u.user_id". ((isset($from_time_stamp)) ? (" and u.date_created > '" . $from_time_stamp . "'") : '') . ((isset($to_time_stamp)) ? (" and u.date_created < '" . $to_time_stamp . "'") : '') . " and u.user_id = ud.user_id");
								break;
							}
							if(isset($query)) {
								$email = '';
								$email_content = $content;
								$email_subject = $subject;
								while($result = $database->fetch_array($query)) {
									$firstname = $result['firstname'];
									$lastname = $result['lastname'];
									$email_address = $result['email_address'];
										if(!empty($email)) {
											$email .= ', ';
										}
									$email .= $result['email_address'];	

										     $email_template = new email_template('', false);
											 $email_template->set_email_template($email_content);
											 $email_template->set_template_command('SUBJECT', $email_subject);
											 $email_template->set_email_template_variable('USER_FIRSTNAME', $firstname);
											 $email_template->set_email_template_variable('USER_LASTNAME', $lastname);
											 $email_template->parse_template();
											 $email_template->send_email($email_address, $firstname.', '.$lastname);
								}
								
							}
							
						$database->query("insert into " . TABLE_MANUAL_EMAILS . " (date_sent, subject, content, sent_to) values ('" . $date_sent_time_stamp . "', '" . $subject . "', '" . $content . "', '" . $email . "')");					
						
						$message = 'Your email has been successfully sent';
		}
		if ($page_action == 'view_email') {
			$email_query = $database->query("select manual_email_id, date_sent, content, user_group_id, subject from " . TABLE_MANUAL_EMAILS . " where manual_email_id = '" . $mID . "' limit 1");
			$email_result = $database->fetch_array($email_query);
		}
			
			

		if ($page_action == 'delete_confirm') {
			$database->query("delete from " . TABLE_MANUAL_EMAILS . " where manual_email_id = '" . $mID . "'");
		}		
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td width="100%" valign="top">
			<?php
if ($page_action == 'view') { 
					$email_query = $database->query("select sent_to, date_sent, subject, content from " . TABLE_MANUAL_EMAILS . " where manual_email_id = '" . $mID . "' limit 1");
					$email_result = $database->fetch_array($email_query)
			?>
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td class="pageBoxContent" width="130">Send to</td><td class="pageBoxContent"><?php echo $email_result['sent_to']; ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Subject</td><td class="pageBoxContent"><?php echo $email_result['subject']; ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Date</td><td class="pageBoxContent"><?php echo date("m/d/Y", $email_result['date_sent']); ?></td>
					</tr>
					<tr>
						<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent" colspan="2"><?php echo $email_result['content']; ?></td>
					</tr>
				</table>
		<?php
		 } elseif ($page_action == 'add') {
		?>
				<form method="post" action="<?php echo FILENAME_ADMIN_MANUAL_EMAILS . '?page_action=add_confirm'; ?>">
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2" border="1">
					<tr>
						<td class="pageBoxContent" width="130">Send to</td><td class="pageBoxContent"><?php echo tep_draw_group_pulldown('user_group_id'); ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent" width="130"></td><td class="pageBoxContent"><?php echo tep_draw_user_status_pulldown('order_status'); ?></td>
					</tr>
					<tr>
						<td class="pageBoxContent" width="130" colspan="2">If applicable specify date below.</td>
					</tr>
					<tr>
						<td	class="pageBoxContent">From: <input type="text" name="from" value=""/></td><td class="pageBoxContent">To: <input type="text" name="to" value=""/></td>
					</tr>
					<tr>
						<td class="pageBoxContent">Subject: </td><td class="pageBoxContent"><input type="text" name="subject" value="" /></td>
					<?php
							$content = '';
					?>
					<tr>
						<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent" colspan="2"></td>
					</tr>
					<tr>
						<td colspan="2"><?php
						$sBasePath = 'editor/';
						//$sBasePath = substr( $sBasePath, 0, strpos( $sBasePath, "_samples" ) ) ;
						$oFCKeditor = new FCKeditor('content') ;
						$oFCKeditor->BasePath = $sBasePath ;
						$oFCKeditor->Value	= $content;
						$oFCKeditor->Height	= '400';
						$oFCKeditor->Create() ;
						?></td>
					</tr>
				</table>
			<?php
			} else {
			?>
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td class="pageBoxHeading">Email ID</td>
						<td class="pageBoxHeading">Subject</td>
						<td class="pageBoxHeading">Date Sent</td>
						<td class="pageBoxHeading"width="75">Action</td>
						<td width="10" class="pageBoxHeading"></td>
					</tr>
				<?php
					$query = $database->query("select manual_email_id, date_sent, subject from " . TABLE_MANUAL_EMAILS . " order by manual_email_id DESC");
						while($result = $database->fetch_array($query)) {
						
				?>
					<tr>
						<td class="pageBoxContent" align="center"><?php echo $result['manual_email_id'];?></td>
						<td class="pageBoxContent" align="center"><?php echo $result['subject']; ?></td>
						<td class="pageBoxContent" align="center"><?php echo date("m/d/Y", $result['date_sent']); ?></td>
						<td class="pageBoxContent" align="center"><a href="<?php echo FILENAME_ADMIN_MANUAL_EMAILS . '?mID='.$result['manual_email_id'].'&page_action=view'; ?>">View</a><?php // if (tep_page_can_be_deleted($result['page_url'])) { ?> | <a href="<?php echo FILENAME_ADMIN_MANUAL_EMAILS . '?mID='.$result['manual_email_id'].'&page_action=delete'; ?>">Delete</a><?php } ?></td>
						<td width="10" class="pageBoxContent"></td>
					</tr>
			<?php
				}
			?>
				</table>
		<td width="15"><img src="images/pixel_trans.gif" height="1" width="10"></td>
		<td width="250" valign="top">
			<table width="100%" cellspacing="0" cellpadding="0">
				<?php
					if(!empty($message)) {
				?>
				<tr>
					<td class="mainSuccess"><?php echo $message; ?></td>
				</tr>
				<?php
					}
				?>
				<tr>
					<td width="100%">
					<?php
					if ($page_action == 'add') {
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox" border="0">
							<tr>
								<td class="pageBoxHeading"><b>New Email</b></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td class="pageBoxContent">Click Send to Send the email or click Cancel to exit.</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td height="5"><input type="submit" value="Send" /></td>
							</tr>
							</form>
							<form action="<?php echo FILENAME_ADMIN_MANUAL_EMAILS; ?>" method="post">
							<tr>
								<td height="5"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></td>
							</tr>
							</form>
						</table>
					<?php
						} elseif ($page_action == 'delete') {
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">Are you sure you wish to delete this Email?</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right"><form action="<?php echo FILENAME_ADMIN_MANUAL_EMAILS; ?>?mID=<?php echo $mID; ?>&page_action=delete_confirm" method="post"><input type="submit" value="Delete Confirm" /></form><form action="<?php echo FILENAME_ADMIN_MANUAL_EMAILS; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form</td>
							</tr>
						</table>
					<?php
					} elseif ($page_action == 'view') {
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent"></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%" align="right"><form action="<?php echo FILENAME_ADMIN_MANUAL_EMAILS; ?>" method="post"><input type="submit" value="Back" /></form></td>
							</tr>
						</table>
			<?php
						} else {
			?>
			<table width="250" cellspacing="0" celpadding="0" class="pageBox">
				<tr>
					<td class="pageBoxHeading"><b>Manual Email Options</b></td>
				</tr>
				<tr>
					<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
				</tr>
				<tr>
					<td class="pageBoxContent">Click on Create to create and send an email.</td>
				</tr>
				<tr>
					<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
				</tr>
				<form action="<?php echo FILENAME_ADMIN_MANUAL_EMAILS; ?>?page_action=add" method="post">
				<tr>
					<td height="5"><input type="submit" value="Create" /></td>
				</tr>
				</form>
			</table>
		<?php
			}
		?>
		</td>
	</tr>
</table>
		</td>
	</tr>
</table>