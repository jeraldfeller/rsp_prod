<?php
	$page_action = tep_fill_variable('page_action', 'get');
	$eID = tep_fill_variable('eID', 'get');
	$submit_value = tep_fill_variable('submit_value_y', 'post');

	$message = tep_fill_variable('message', 'get');
	
	$language_id = tep_fill_variable('language_id', 'post', tep_get_default_language());
		
	$language = tep_get_language_code($language_id);
	
		if (!empty($submit_value)) {
			
			$content = tep_fill_variable('content', 'post');
			
			$email_array = file(DIR_LANGUAGES . $language . '/email_templates/' . $eID);
			$count = count($email_array);
					
			$email_content = '';
					
			$n = 0;
					
			$commands = array();
					
				while($n < $count) {
					if (substr($email_array[$n], 0, 4) == '(<>)') {
							$explode = explode(', ', str_replace('(<>)', '', $email_array[$n]), 2);
							$commands[] = array('name' =>$explode[0], 'value' => tep_fill_variable($explode[0], 'post'));
						}
					$n++;
				}
			
			$count = count($commands);
			$n = 0;
				while($n < $count) {
							if (!empty($email_content)) {
								$email_content .= "\n";
							}
						$email_content .= '(<>)'.$commands[$n]['name'].', '.$commands[$n]['value'];
					$n++;
				}
				if (!empty($email_content)) {
					$email_content .= "\n";
				}

			$email_content .= $content;
			
			tep_write_file(DIR_LANGUAGES . $language . '/email_templates/' . $eID, stripslashes($email_content));
			
			$comments = tep_fill_variable('comments');
			tep_write_file(DIR_LANGUAGES . $language . '/email_templates/comments/'.substr($eID, 0, strpos($eID, '.')) . '.txt', $comments);

			$message = 'Email successfully updated.';
			tep_redirect(FILENAME_ADMIN_EMAILS.'?message='.urlencode($message));
			
			$page_action = '';
			
		}
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<?php
		if ($error->get_error_status('admin_emails')) {
	?>
	<tr>
		<td class="mainError" colspan="2"><?php echo $error->get_error_string('admin_emails'); ?></td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td width="100%" valign="top">
			<?php
				if (($page_action != 'edit')) {
			?>
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td class="pageBoxHeading">Email Page</td>
						<td class="pageBoxHeading">Comments</td>
						<td class="pageBoxHeading" align="center">Last Modified</td>
						<td class="pageBoxHeading" align="right">Action</td>
						<td width="10" class="pageBoxHeading"></td>
					</tr>
				<?php
					$dir = DIR_LANGUAGES . $language . '/email_templates';
					if (is_dir($dir)) {
						if ($dh = opendir($dir)) {
							$list = array();
							while (($file = readdir($dh)) !== false) {
								if (($file != '.') && ($file != '..') && (substr($file, -4) == '.php')) {
									$list[] = $file;
								}
							}
							sort($list);
							for ($n = 0, $m = count($list); $n < $m; $n++) {
								$file = $list[$n];
									$comments = '';
										if (is_file(DIR_LANGUAGES . $language . '/email_templates/comments/'.substr($file, 0, strpos($file, '.')) . '.txt')) {
											$comments = file_get_contents(DIR_LANGUAGES . $language . '/email_templates/comments/'.substr($file, 0, strpos($file, '.')) . '.txt');
										}
				?>
					<tr>
						<td class="pageBoxContent"><?php echo $file; ?></td>
						<td class="pageBoxContent"><?php echo $comments; ?></td>
						<td class="pageBoxContent" align="center"><?php echo date("n/d/Y", filemtime($dir . '/' . $file)); ?></td>
						<td class="pageBoxContent" align="right"><a href="<?php echo FILENAME_ADMIN_EMAILS . '?eID='.$file.'&page_action=edit'; ?>">Edit</a></td>
						<td width="10" class="pageBoxContent"></td>
					</tr>
				<?php
							}
						}
					}
				?>
				</table>
			<?php
				} else {
					//Edit
					$email_array = file(DIR_LANGUAGES . $language . '/email_templates/' . $eID);
					$count = count($email_array);
					
					$email_content = '';
					
					$n = 0;
					
					$commands = array();
					
						while($n < $count) {
							if (substr($email_array[$n], 0, 4) == '(<>)') {
									$explode = explode(', ', str_replace('(<>)', '', $email_array[$n]), 2);
									$commands[] = array('name' =>$explode[0], 'value' => $explode[1]);
								} else {
										//if (!empty($email_content)) {
											//$email_content .= "<br>";
										//}
									$email_content .= $email_array[$n];
								}
							$n++;
						}
						
					//Now we have an array of commands and values and a string of the email with <br> tags to seperate lines.
					$comments = '';
						if (is_file(DIR_LANGUAGES . $language . '/email_templates/comments/'.substr($eID, 0, strpos($eID, '.')) . '.txt')) {
							$comments = file_get_contents(DIR_LANGUAGES . $language . '/email_templates/comments/'.substr($eID, 0, strpos($eID, '.')) . '.txt');
						}
			?>
			<form name="admin_config" method="post" action="<?php echo FILENAME_ADMIN_EMAILS . '?page_action=edit&eID='.$eID; ?>">
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<tr>
						<td class="pageBoxContent" valign="top">Comments: </td><td class="pageBoxContent"><textarea cols="35" name="comments"><?php echo $comments; ?></textarea></td>
					</tr>
					<tr>
						<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
					</tr>
					<?php
						$count = count($commands);
						$n = 0;
							while($n < $count) {
								$convert_name = str_replace('_', '  ', $commands[$n]['name']);
								$convert_name = strtolower($convert_name);
								$convert_name = ucfirst($convert_name);
								?>
								
								<tr>
									<td class="pageBoxContent"><?php echo $convert_name; ?>: </td><td class="pageBoxContent"><input type="text" size="45" name="<?php echo $commands[$n]['name']; ?>" value="<?php echo $commands[$n]['value']; ?>" /></td>
								</tr>
								<?php
								$n++;
							}
					
					?>
					<tr>
						<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
					</tr>
					<tr>
						<td class="pageBoxContent" colspan="2">Email Content <i>(please note that variables are prefixed by a & sign)</i></td>
					</tr>
					<tr>
						<td colspan="2"><?php
						$sBasePath = 'editor/';
						//$sBasePath = substr( $sBasePath, 0, strpos( $sBasePath, "_samples" ) ) ;
						$oFCKeditor = new FCKeditor('content') ;
						$oFCKeditor->BasePath = $sBasePath ;
						$oFCKeditor->Value	= $email_content;
						$oFCKeditor->Height	= '400';
						$oFCKeditor->Create() ;
						?></td>
					</tr>
				</table>
			<?php
				}
			?>
		</td>
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
						if ($page_action == 'edit') {
							
					?>
						<table width="250" cellspacing="0" celpadding="0" class="pageBox">
							<tr>
								<td class="pageBoxContent">Make your required changes and press "Update" below or press "Cancel" to cancel your changes.</td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td class="main">View Translation: <?php echo tep_draw_language_pulldown('language_id', $language_id, ' onchange="this.form.submit();"'); ?></td>
							</tr>
							<tr>
								<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
							</tr>
							<tr>
								<td width="100%">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td align="left"><?php echo tep_create_button_submit('update', 'Update', ' name="submit_value"'); ?></form></td>
											<td align="right"><form action="<?php echo FILENAME_ADMIN_EMAILS; ?>" method="post"><?php echo tep_create_button_submit('cancel', 'Cancel'); ?></form></td>
										</tr>
									</table>	
								</td>
							</tr>
							
						</table>
			<?php
			} else {
			?>
			<table width="250" cellspacing="0" celpadding="0" class="pageBox">
				<tr>
					<td class="pageBoxHeading"><b>Page Options</b></td>
				</tr>
				<tr>
					<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
				</tr>
				<tr>
					<td class="pageBoxContent">Click edit to edit a page.</td>
				</tr>
				<tr>
					<td height="5"><img src="images/pixel_trans.gif" height="5" width="1"></td>
				</tr>
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
