<?php

	
		if (!$user->user_is_logged() || ($user->fetch_user_group_id() == 1) || $user->accounts_payable) {
			include(DIR_BOXES . 'orders.php');
		}
		if ($user->user_is_logged() && ($user->fetch_user_group_id() == 1)) {
			//include(DIR_BOXES . 'addresses.php');
		}
		if ($user->user_is_logged() && ($user->fetch_user_group_id() == 1)) {
			include(DIR_BOXES . 'my_equipment.php');
		}
		
		
		if ($user->user_is_logged() && ($user->fetch_user_group_id() == 2)) {
			//include(DIR_BOXES . 'admin_users_orders.php');
		}
		if ($user->user_is_logged() && ($user->fetch_user_group_id() == 2)) {
			//include(DIR_BOXES . 'installation_payments.php');
		}
		if ($user->user_is_logged() && ($user->fetch_user_group_id() == 2)) {
			include(DIR_BOXES . 'daily_management.php');
		}
		if ($user->user_is_logged() && ($user->fetch_user_group_id() == 2)) {
			include(DIR_BOXES . 'stats_and_reports.php');
		}
		if ($user->user_is_logged() && ($user->accounts_payable)) {
			include(DIR_BOXES . 'accounts_payable.php');
		}
		if ($user->user_is_logged() && ($user->fetch_user_group_id() == 2)) {
			include(DIR_BOXES . 'manage_users.php');
		}
		if ($user->user_is_logged() && ($user->fetch_user_group_id() == 2)) {
			include(DIR_BOXES . 'equipment.php');
		}
		if ($user->user_is_logged() && ($user->fetch_user_group_id() == 2)) {
			include(DIR_BOXES . 'manage_service.php');
		}
		if ($user->user_is_logged() && ($user->fetch_user_group_id() == 2)) {
			include(DIR_BOXES . 'manage_service_area.php');
		}
		if ($user->user_is_logged() && ($user->fetch_user_group_id() == 2)) {
			include(DIR_BOXES . 'manage_website.php');
		}
		if ($user->user_is_logged() && ($user->fetch_user_group_id() == 2)) {
			include(DIR_BOXES . 'experimental.php');
		}
		if ($user->user_is_logged() && ($user->fetch_user_group_id() == 4)) {
			include(DIR_BOXES . 'admin_users_orders.php');
		}
		if ($user->user_is_logged() && ($user->fetch_user_group_id() == 4)) {
			include(DIR_BOXES . 'manage_agents.php');
		}
		
		if ($user->user_is_logged() && ($user->fetch_user_group_id() == 3)) {
			include(DIR_BOXES . 'assigned_jobs.php');
		}
        include(DIR_BOXES . 'account.php');
		if ($user->user_is_logged() && ($user->fetch_user_group_id() == 2)) {
			//include(DIR_BOXES . 'admin_config.php');
		}
?>
