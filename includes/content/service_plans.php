<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="main">Service Plans</td>
	</tr>
	<tr>
		<td height="5"><img src="images/pixel_trans.gif" height="5" width="1" /></td>
	</tr>
	<tr>
		<td width="100%">
			<table cellspacing="0" cellpadding="0" class="pageBox" width="100%">
				<tr>
					<td>
						<table cellpadding="0" cellspacing="3">
							<?php
							$loop = 0;
								$query = $database->query("select sl.cost, sld.name, sld.description from " . TABLE_SERVICE_LEVELS . " sl, " . TABLE_SERVICE_LEVELS_DESCRIPTION . " sld where sl.service_level_id = sld.service_level_id and sld.language_id = '" . $language_id . "' order by sl.service_level_id");
                                    foreach($database->fetch_array($query) as $result){
											if ($loop > 0) {
											?>
											<tr>
												<td height="10"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
											</tr>
											<?php
											}
										?>
										<tr>
											<td width="100%">
												<table width="100%" cellspacing="0" cellpadding="0">
													<tr>
														<td class="pageSubHeading"><?php echo $result['name']; ?></td>
													</tr>
													<tr>
														<td class="main">&nbsp;&nbsp;Cost: $<?php echo number_format($result['cost']); ?></td>
													</tr>
													<tr>
														<td height="3"><img src="images/pixel_trans.gif" height="10" width="1" /></td>
													</tr>
													<tr>
														<td class="main"><?php echo $result['description']; ?></td>
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
				</tr>
			</table>
		</td>
	</tr>
</table>