<?php
	$page_action = tep_fill_variable('page_action', 'get');
	
		if($page_action == 'update_confirm') {
			//Delete old data.
			$database->query("delete from " . TABLE_AGENTS_TO_AGENT_PREFERENCES . " where user_id = '" . $user->fetch_user_id() . "'");
			$query = $database->query("select agent_preference_group_id from " . TABLE_AGENT_PREFERENCE_GROUPS);
			$items = tep_fill_variable('items', 'post', array());
				while($result = $database->fetch_array($query)) {
						if (is_array($items[$result['agent_preference_group_id']]) && !empty($items[$result['agent_preference_group_id']])) {
							$count = count($items[$result['agent_preference_group_id']]);
							$n = 0;
								while($n < $count) {
									$database->query("insert into " . TABLE_AGENTS_TO_AGENT_PREFERENCES . " (user_id, agent_preference_id) values ('" . $user->fetch_user_id() . "', '" . $items[$result['agent_preference_group_id']][$n] . "')");
									$n++;
								}
						}
				}
			$error->add_error('agent_preferences', 'Your preferences have been successfully updated.  These will show on all your orders from now on.');
		}

?>
<table width="100%" cellspacing="0" cellpadding="0">
	<?php
		if ($error->get_error_status('agent_preferences')) {
	?>
	<tr>
		<td class="mainSuccess" colspan="2"><?php echo $error->get_error_string('agent_preferences'); ?></td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td width="100%" valign="top">
				<table width="100%" class="pageBox" cellspacing="0" cellpadding="2">
					<form action="<?php echo FILENAME_AGENT_PREFERENCES; ?>?page_action=update_confirm" method="post">
					<?php
						$query = $database->query("select agent_preference_group_id, name, selectable from " . TABLE_AGENT_PREFERENCE_GROUPS . " order by name");
						$loop = 0;
							while($result = $database->fetch_array($query)) {
									if ($loop > 0) {
									?>
									<tr>
										<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
									</tr>
									<?php
									}
							?>
							<tr>
								<td class="pageBoxContent"><b><?php echo $result['name']; ?></b></td>
							</tr>
							<?php
								if ($result['selectable'] != '1') {
							?>
								<script language="javascript">
								function checkTwo_<?php echo $result['agent_preference_group_id']; ?>(theBox, message, limit){

								 boxName=theBox.name;
									
								 elm=theBox.form.elements;
										
								 count=0;
										
									 for(i=0;i<elm.length;i++) {
											
									   if(elm[i].name==boxName && elm[i].checked==true) {
										 count++
										  }
									  }
										 if(count > limit){
												
										   alert('Please select no more than two '+message+' items to be placed.')
												
											  theBox.checked=false;
												
										 }
											
									 }
											 
								</script>
							<?php
								}
							?>
							<tr>
								<td width="100%" align="left">
									<table width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td width="10"><img src="images/pixel_trans.gif" width="10" height="1" /></td>
											<td aling="left" width="100%">
												<table width="100%" cellspacing="0" cellpadding="0">
													<?php
														$items_query = $database->query("select p.agent_preference_id, p.name, atp.user_id as found from " . TABLE_AGENT_PREFERENCES . " p left join " . TABLE_AGENTS_TO_AGENT_PREFERENCES . " atp on (p.agent_preference_id = atp.agent_preference_id and atp.user_id = '" . $user->fetch_user_id() . "') where p.agent_preference_group_id = '" . $result['agent_preference_group_id'] . "' order by p.name");
															while($items = $database->fetch_array($items_query)) {
																if ($items['found'] != NULL) {
																	$checked = ' CHECKED ';
																} else {
																	$checked = '';
																}
																if ($result['selectable'] == '1') {
																	$check_box = '<input type="radio" name="items['.$result['agent_preference_group_id'].'][]" value="'.$items['agent_preference_id'].'"'.$checked.'>';
																} else {
																	$check_box = '<input type="checkbox" onclick="checkTwo_'.$result['agent_preference_group_id'].'(this, \''.$result['name'].'\', '.$result['selectable'].');" name="items['.$result['agent_preference_group_id'].'][]" value="'.$items['agent_preference_id'].'"'.$checked.'>';
																}
														?>
															<tr>
																<td class="main"><?php echo $check_box . ' ' . $items['name']; ?></td>
															</tr>
														<?php
															}
														?>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<?php
								$loop++;
							}
					?>
			</table>
		</td>
		<td width="15"><img src="images/pixel_trans.gif" height="1" width="10"></td>
		<td width="250" valign="top">
			<table width="250" cellspacing="0" celpadding="0" class="pageBox">
				<tr>
					<td class="pageBoxContent">&PAGE_TEXT</td>
				</tr>
				<tr>
					<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
				</tr>
				<tr>
					<td width="100%"><HR></td>
				</tr>
				<tr>
					<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
				</tr>
				<tr>
					<td width="100%">
						<table width="100%" cellspacing="0" cellpadding="0">
							<tr>
								<td align="left"><input type="reset" value="Clear All" /></td>
								<td align="right"><input type="submit" value="Update" /></form></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>