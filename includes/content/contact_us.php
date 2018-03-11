<?php
if($user->fetch_user_group_id()==1) {
	header('Location: agent_contact_us.php');
}
	$page_action = tep_fill_variable('page_action', 'get');

	$contact_name = tep_fill_variable('contact_name', 'post', '');
	$contact_company = tep_fill_variable('contact_company', 'post', '');
	$contact_email = tep_fill_variable('contact_emal', 'post', '');
	$contact_phone = tep_fill_variable('contact_phone', 'post', '');
	$contact_message = tep_fill_variable('contact_message', 'post', '');
	$contact_subject = tep_fill_variable('contact_subject', 'post', '');

  if ($page_action == 'submit') {

    if (empty($contact_name) && empty($contact_company)) {
      $error->add_error('contact_us', 'Please enter either your name or your company name.');
    }
    if (empty($contact_email) && empty($contact_phone)) {
      $error->add_error('contact_us', 'Please enter either your email address or your phone number.');
    }
    if (!empty($contact_email) && strpos($contact_email, '@') === FALSE)
      $error->add_error('contact_us', 'Please enter a valid email address.');

    if (empty($contact_message)) {
      $error->add_error('contact_us', 'Please enter your message.');
    }

    if (!$error->get_error_status('contact_us')) {
      $from_name = '';
      if (!empty($contact_name))
        $from_name = $contact_name;
      if (!empty($contact_company))
        $from_name .= (!empty($from_name)?' - ':'').$contact_company;

      if (SEND_EMAILS != 'true') return false;

      // Instantiate a new mail object
      $message = new email(array(MAILER_NAME));

      // Build the text version
      $message->add_text($contact_message."\n\n".$contact_phone);
      if (!empty($contact_email)) {
        $from_email = $contact_email;
      } else {
        $from_email = '';
      }
      // Send message
      $subject = 'From Website: '. (empty($contact_subject) ? '(no subject)' : $contact_subject);

      $message->build_message();

      $message->send('', INFO_EMAIL, $from_name, $from_email, $subject);


      $error->add_error('contact_us', 'Please enter your name.', 'success');
      tep_redirect(PAGE_URL.'?page_action=success');

    } else {
      $page_action = '';
    }
  }

?>
<table width="100%" cellspacing="0" cellpadding="0">
	<?php
		if (empty($page_action)) {
	?>
	<tr>
		<td class="style9">&PAGE_TEXT</td>
	</tr>
	<tr>
		<td height="5"><img src="images/pixel_trans.gif" height="10" width="1"></td>
	</tr>
	<tr>
		<td valign="top" width="100%" align="center">
			<table width="400" cellspacing="0" cellpadding="0">
				<?php
					if ($error->get_error_status('contact_us', 'all')) {
				?>
				<tr>
					<td class="mainError" width="100%" align="left"><?php echo $error->get_error_string('contact_us', 'all'); ?></td>
				</tr>
				<tr>
					<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
				</tr>
				<?php
					}
				?>
				<tr>
					<td width="100%" align="left">
						<table cellpadding="0" cellspacing="3" class="pageBox">

							<form id="contact" method="post" action="<?php echo PAGE_URL; ?>?page_action=submit">
							<tr>
								<td colspan="2" width="100%" align="right"><span class="requiredItem">* Indicates a Required Item</span></td>
							</tr>
							<tr>
								<td height="4"><img src="images/pixel_trans.gif" height="4" width="1"></td>
							</tr>
							<tr>
								<td class="main" align="left">Your Name: </td><td><input type="text" name="contact_name" value="<?php echo $contact_name; ?>" /><span class="requiredItem">&nbsp;*</span></td>
							</tr>
							<tr>
								<td class="main" align="left">Your Company Name: </td><td><input type="text" name="contact_company" value="<?php echo $contact_company; ?>" /></td>
							</tr>
							<tr>
								<td class="main" align="left">Your Email Address: </td><td><input type="text" name="contact_emal" value="<?php echo $contact_email; ?>" /><span class="requiredItem">&nbsp;*</span></td>
							</tr>
							<tr>
								<td class="main" align="left">Your Phone Number: </td><td><input type="text" name="contact_phone" value="<?php echo $contact_phone; ?>" /></td>
							</tr>
							<tr>
								<td height="3"><img src="images/pixel_trans.gif" height="3" width="1" /></td>
							</tr>
							<tr>
								<td colspan="2" class="main" align="left">Subject:</td>
							</tr>
							<tr>
								<td colspan="2" class="main" align="left"><input type="text" name="contact_subject" value="<?php echo $contact_subject; ?>" style="width:300px" /></td>
							</tr>
							<tr>
								<td colspan="2" class="main" align="left">Message:<span class="requiredItem">&nbsp;*</span></td>
							</tr>
							<tr>
								<td colspan="2" class="main" align="left"><textarea name="contact_message" style="width:300px; height:150px;"><?php echo $contact_message; ?></textarea></td>
							</tr>
							<tr>
								<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
							</tr>
							<tr>
								<td colspan="2" align="center">
									<table cellspacing="0" cellpadding="0">
										<tr>
											<td><?php echo tep_create_button_link('reset', 'Reset Form', ' onclick="document.getElementById(\'contact\').reset();"'); ?></td>
											<td width="40"><img src="images/pixel_trans.gif" height="1" width="40" /></td>
											<td><?php echo tep_create_button_submit('send', 'Send Message'); ?></td>
										</tr>
									</table>
								</td>
							</tr>
							</form>

						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<?php
		} elseif ($page_action == 'success') {
	?>
	<tr>
		<td class="main" class="style9">Thank you for your message.  We will contact you as soon as possible.</td>
	</tr>
	<?php
		}
	?>
</table>
