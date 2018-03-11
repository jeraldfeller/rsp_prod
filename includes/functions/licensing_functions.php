<?php 
/*
 * Functions related to license level
 * Auther: Haresh Vidja 
 */

function get_active_installer($user_id=null)
{
	global $database;
	if($user_id>0)
	{
		$installar_query= "select u.* from " . TABLE_USERS_TO_USER_GROUPS . " ug, " . TABLE_USERS . " u where u.user_id = ug.user_id and (ug.user_group_id=3 or u.user_id=".$user_id.") and u.active_status= 1";
	}	
	else
	{	
		$installar_query= "select u.* from " . TABLE_USERS_TO_USER_GROUPS . " ug, " . TABLE_USERS . " u where u.user_id = ug.user_id and ug.user_group_id=3 and u.active_status= 1";
	}
	$query = $database->query($installar_query);
	return $database->num_rows($query);
}

function is_user_installer($user_id)
{
	global $database;
	$installar_query= "select u.user_id,ug.user_group_id from " . TABLE_USERS_TO_USER_GROUPS . " ug, " . TABLE_USERS . " u where u.user_id = ug.user_id and u.user_id=".$user_id;
	$query = $database->query($installar_query);
	$result = $database->fetch_array($query);
	return (isset($result['user_group_id']) && $result['user_group_id']==3); 
}

function get_default_billing_method()
{
	if(BILLING_METHOD==1 || BILLING_METHOD==3) return 1;
	else if(BILLING_METHOD==2) return 2;
	else return 1; 
}

function set_error_reporting_settings()
{
	if(getenv("SERVER_MODE") != "TEST")
	{
		error_reporting(0);
		ini_set('error_reporting', 0);
		ini_set('display_errors', 'Off');
	}
}

function parse_license_detail($content)
{
	$licence_contstants=array(
			"BUSINESS_NAME",
			"BUSINESS_NAME_FULL",
			"BUSINESS_PARTNER",
			"BUSINESS_TAG_LINE",
			"WEB_DOMAIN",
			"FAX_VOICE",
			"EMERGENCY_NUMBER",
			"LINKEDIN_URL",
			"BUSINESS_ADDRESS",
			"BUSINESS_AREA",
			"INFO_EMAIL"
	);
	foreach($licence_contstants as $constant_name)
	{
		if(defined($constant_name))
		{
			$content= str_replace("[[".$constant_name."]]", constant($constant_name), $content);
		}
	}	
	return $content;
	
}