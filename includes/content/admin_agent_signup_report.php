<?php
error_reporting(0);
$page_action = tep_fill_variable('page_action', 'get');
$mID='';
$mID = tep_fill_variable('mID', 'get');
$yR = tep_fill_variable('yR', 'get');
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td width="100%" valign="top">
			<?php
				if ($page_action == '') {
			?>
			<label>Select a Year</label>
                    <select name="yR" id="yR">
                        <option value="">All</option>
                        <?php for($i=2006;$i<=2018;$i++) {
                            if ($yR == $i) {
                                $selected = 'selected';
                            } else {
                                $selected = '';
                            }
                            echo '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
                        }
                        ?>
                    </select>
				<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
				
				<script>
					
					$('#yR').on('change', function() {
						if (this.value != '') {
							window.location.href = "/admin_agent_signup_report.php?page_action=viewmonth&yR="+this.value;	
						} else {
							window.location.href = "/admin_agent_signup_report.php";	
						}
					});
				</script>
				
				<div id="chart_div"></div>
				
				
				
				<!--<table width="80%" class="pageBox" cellspacing="0" cellpadding="2" border="0">
					<tr>
						<td width="40%" class="pageBoxHeading" align="center">Month</td>
						<td width="40%" class="pageBoxHeading" align="center">New Agents</td>
						
					</tr>-->
					<?php

					$query_test=$database->query("SELECT t1.date_created, t2.user_group_id FROM users AS t1, users_to_user_groups AS t2 WHERE t2.user_group_id =1 AND t1.user_id =t2.user_id");
					foreach($database->fetch_array($query_test) as $qry_res_test){
						if($qry_res_test['date_created']!='0'){
							$date_test[date('Y/m',$qry_res_test['date_created'])]+=1;
							
						}
					}

					ksort($date_test);
					reset($date_test);
					while(list($key, $val) = each($date_test)) {
						$exp=explode("/",$key);

							if($exp[1]=='01'){$mon="January"; }if($exp[1]=='02'){$mon="February"; }if($exp[1]=='03'){$mon="March"; }if($exp[1]=='04'){$mon="April"; }if($exp[1]=='05'){$mon="May"; }if($exp[1]=='06'){$mon="June"; }if($exp[1]=='07'){$mon="July"; }if($exp[1]=='08'){$mon="August"; }if($exp[1]=='09'){$mon="September"; }if($exp[1]=='10'){$mon="October"; }if($exp[1]=='11'){$mon="November"; }if($exp[1]=='12'){$mon="December"; }
							
							/*$data = array( 
										 array('Date', 'Sales'),  
										 array('June 25', 12.25),  
										 array('June 26', 8.00) 
							);*/
							$data[] = array($mon." ".$exp[0], $val);

							//json_encode($data);
							
							?>
							<!-- --<tr>
								<td width="40%" class="pageBoxContent" align="center"><a href="<?php echo FILENAME_ADMIN_AGENT_SIGNUP_REPORT?>?mID=<?=$exp[1]?>&yR=<?=$exp[0]?>&page_action=view"><?php echo $mon?>&nbsp;&nbsp;<?php echo $exp[0]?></a></td>
								<td width="40%" class="pageBoxContent" align="center"><?php echo $val?></td>
								
							</tr> -->
							<?php

					}
					//$data = array($data);
					$data_all = array_reverse($data);
					$data_start = array('Date', 'Agents');
					$data = array_merge(array($data_start), $data_all);
					$data = json_encode($data);
					//echo $data;
					?>
				<!--</table>-->
				
				<script>
				google.charts.load('current', {packages: ['corechart', 'bar']});
				google.charts.setOnLoadCallback(drawMultSeries);

				function drawMultSeries() {
					  var data = google.visualization.arrayToDataTable(<?php echo $data ?>);

					  var options = {
						title: 'Signed Up Agents per Month',
						chartArea: { top: '4%', width: "80%", height: "80%" },
						height: 1800,
						hAxis: {
						  title: 'Signed Up Agents',
						  minValue: 0
						},
						legend: {position: 'none', textStyle: {fontSize: 12}},
						vAxis: {
						  title: 'Month',
						  slantedText: true,
						  showTextEvery:1,
						  textStyle: {fontSize: 11}
						}
					  };

					  var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
					  chart.draw(data, options);
									  
					}
				</script>
				
				
			<?php
				}
				else if ($page_action == 'viewmonth') 
				{?>
					<label>Select a Year</label>
					<select name="yR" id="yR">
					<option value="">All</option>
					<? for($i=2006;$i<=2016;$i++) { ?>
					<option <? if ($yR==$i) { ?> selected <? } ?> value="<?=$i?>"><?=$i?></option>
					<?					
					}
					?>
					
				</select>
					<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
					<script>
					
					$('#yR').on('change', function() {
						if (this.value != '') {
							window.location.href = "/admin_agent_signup_report.php?page_action=viewmonth&yR="+this.value;	
						} else {
							window.location.href = "/admin_agent_signup_report.php";	
						}
						
					});
				</script>
					<div id="chart_div"></div>
					
						<?php
						
						//$data[] = array('Date', 'Agents');
						
						$year=$yR;
						//echo $year;
						$day_from=mktime(0, 0, 0, '01', '01', $year);
						//echo $day_from;
						$day_to=mktime(0, 0, 0, '12', '30', $year);

						
						$query_test=$database->query("SELECT t1.date_created, t2.user_group_id FROM users AS t1, users_to_user_groups AS t2 WHERE t2.user_group_id =1 AND t1.user_id =t2.user_id and t1.date_created between $day_from and $day_to");
						foreach($database->fetch_array($query_test) as $qry_res_test){
							if($qry_res_test['date_created']!='0'){
								$date_test[date('Y/m',$qry_res_test['date_created'])]+=1;
								
							}
						}

						ksort($date_test);
						reset($date_test);
						while(list($key, $val) = each($date_test)) {
							$exp=explode("/",$key);

								if($exp[1]=='01'){$mon="January"; }if($exp[1]=='02'){$mon="February"; }if($exp[1]=='03'){$mon="March"; }if($exp[1]=='04'){$mon="April"; }if($exp[1]=='05'){$mon="May"; }if($exp[1]=='06'){$mon="June"; }if($exp[1]=='07'){$mon="July"; }if($exp[1]=='08'){$mon="August"; }if($exp[1]=='09'){$mon="September"; }if($exp[1]=='10'){$mon="October"; }if($exp[1]=='11'){$mon="November"; }if($exp[1]=='12'){$mon="December"; }
								
								$data[] = array($mon." ".$exp[0], $val);

						}
						$data_all = array_reverse($data);
						$data_start = array('Date', 'Agents');
						$data = array_merge(array($data_start), $data_all);
						$data = json_encode($data);
						//$data = json_encode($data);
						//echo $data;
						?>
					<!--</table>-->
					
					<script>
					google.charts.load('current', {packages: ['corechart', 'bar']});
					google.charts.setOnLoadCallback(drawMultSeries);

					function drawMultSeries() {
						  var data = google.visualization.arrayToDataTable(<?php echo $data ?>);

						  var options = {
							title: 'Signed Up Agents per Month',
							chartArea: { top: '4%', width: "80%", height: "80%" },
							height: 600,
							hAxis: {
							  title: 'Signed Up Agents',
							  minValue: 0
							},
							legend: {position: 'none', textStyle: {fontSize: 12}},
							vAxis: {
							  title: 'Month',
							  slantedText: true,
							  showTextEvery:1,
							  textStyle: {fontSize: 11}
							}
						  };

						  var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
						  chart.draw(data, options);
										  
						}
					</script>
				<?
				}
				
				else if ($page_action == 'view') 
				{
				//$mon=$mID;
				$year=$yR;
				//echo $year;
				$day_from=mktime(0, 0, 0, '01', '01', $year);
				//echo $day_from;
				$day_to=mktime(0, 0, 0, '12', '30', $year);
				//echo 
				$query_day="SELECT t1.date_created,t1.email_address,t2.user_group_id FROM users AS t1, users_to_user_groups AS t2 WHERE t2.user_group_id =1 AND t1.user_id =t2.user_id and t1.date_created between $day_from and $day_to";
				$query_day_res=$database->query($query_day);

				    foreach($database->fetch_array($query_day_res) as $query_day_final_res)
					{
					$date_new[]=date('m/d/Y',$query_day_final_res['date_created']);
					}
				$day_arr=array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30");
					$l=0;$q=0;$n=0;
					for($l=0;$l<sizeof($day_arr);$l++)
					{
					for($q=0;$q<sizeof($date_new);$q++)
					{
					$date_created_res=explode("/",$date_new[$q]);
					if($day_arr[$l]==$date_created_res[1])
					{
					$n++;
					}				
					}
					$d[]=$day_arr[$l].",".$n.",".$date_created_res[2];
				
					$n=0;
					}
			?>
			<table width="80%" class="pageBox" cellspacing="0" cellpadding="2" border="0">
					<tr>
						<td width="40%" class="pageBoxHeading" align="center">Date </td>
						<td width="40%" class="pageBoxHeading" align="center">New Agents</td><td><form action="<?php echo FILENAME_ADMIN_AGENT_SIGNUP_REPORT?>" method="post"><?php echo tep_create_button_submit('back', 'Cancel','submit_value=""'); ?></form></td>
						
					</tr>
					<?php
					for($r=0;$r<sizeof($d);$r++)
					{
					$expd=explode(",",$d[$r]);
					if($expd[1]!='0'){
					?>
					<tr>
						<td width="40%" class="pageBoxContent" align="center"><?=date("l  dS  F  Y",mktime(0, 0, 0, '01', $expd[0], $year))?></td>
						<td width="40%" class="pageBoxContent" align="center" colspan="2"><?=$expd[1]?></td>
						
					</tr>
					<?php
					}
					}
					?>
				</table>
				
			<?php
				}
			?>
		</td>
		
	</tr>
</table>
