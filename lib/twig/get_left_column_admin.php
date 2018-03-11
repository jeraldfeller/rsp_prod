<?php
require_once('../../includes/application_top.php');
Global $database, $language;
$language_id = 1;
$userGroupId = $_POST['userGroupId'];
$dailyManagement = array();
$statisticsReports = array();
$manageUsers = array();
$manageEquipment = array();
$manageService = array();
$manageServiceArea = array();
$manageWebsite = array();
$expiremental = array();
$myAccount = array();
// Daily Mangement
    $query = $database->query("select p.page_id, p.page_url, pd.name from " . TABLE_PAGES . " p, " . TABLE_PAGES_DESCRIPTION . " pd, " . TABLE_USER_GROUPS_TO_PAGES . " ugtp, " . TABLE_PAGES_GROUPS . " pg where pg.file_name = '" . 'daily_management.php' . "' and pg.page_group_id = p.page_group_id and p.page_id = ugtp.page_id and ugtp.user_group_id = '" . $userGroupId . "' and p.page_id = pd.page_id and pd.language_id = '" . $language_id . "' order by pd.name ASC");
    $count = 0;
    foreach($query as $result){

        $count++;
        $dailyManagement[] = array(
          'count' => $count,
          'name' => $result['name'],
          'pageUrl' => $result['page_url']
        );
    }

// Statistics Reports
$query = $database->query("select p.page_id, p.page_url, pd.name from " . TABLE_PAGES . " p, " . TABLE_PAGES_DESCRIPTION . " pd, " . TABLE_USER_GROUPS_TO_PAGES . " ugtp, " . TABLE_PAGES_GROUPS . " pg where pg.file_name = '" . 'stats_and_reports.php' . "' and pg.page_group_id = p.page_group_id and p.page_id = ugtp.page_id and ugtp.user_group_id = '" . $userGroupId . "' and p.page_id = pd.page_id and pd.language_id = '" . $language_id . "' UNION select p.page_id, p.page_url, pd.name from " . TABLE_PAGES . " p, " . TABLE_PAGES_DESCRIPTION . " pd, " . TABLE_USER_GROUPS_TO_PAGES . " ugtp, " . TABLE_PAGES_GROUPS . " pg where p.page_id IN (126, 129) AND p.page_id = pd.page_id and pd.language_id = '" . $language_id . "' order by name ASC");
$count = 0;
    foreach($query as $result){
    $count++;
    $statisticsReports[] = array(
      'count' => $count,
      'name' => $result['name'],
      'pageUrl' => $result['page_url']
    );
  }

//Manage Users
$query = $database->query("select p.page_id, p.page_url, pd.name from " . TABLE_PAGES . " p, " . TABLE_PAGES_DESCRIPTION . " pd, " . TABLE_USER_GROUPS_TO_PAGES . " ugtp, " . TABLE_PAGES_GROUPS . " pg where pg.file_name = '" . 'manage_users.php' . "' and pg.page_group_id = p.page_group_id and p.page_id = ugtp.page_id and ugtp.user_group_id = '" . $userGroupId . "' and p.page_id = pd.page_id and pd.language_id = '" . $language_id . "' order by pd.name ASC");
$count = 0;
    foreach($query as $result){
    $count++;
    $manageUsers[] = array(
      'count' => $count,
      'name' => $result['name'],
      'pageUrl' => $result['page_url']
    );

  }

// Manage Equipment
$query = $database->query("select p.page_id, p.page_url, pd.name from " . TABLE_PAGES . " p, " . TABLE_PAGES_DESCRIPTION . " pd, " . TABLE_USER_GROUPS_TO_PAGES . " ugtp, " . TABLE_PAGES_GROUPS . " pg where pg.file_name = '" . 'equipment.php' . "' and pg.page_group_id = p.page_group_id and p.page_id = ugtp.page_id and ugtp.user_group_id = '" . $userGroupId . "' and p.page_id = pd.page_id and pd.language_id = '" . $language_id . "' order by pd.page_order ASC, pd.name ASC");
$count = 0;
    foreach($query as $result){
    $count++;
    $manageEquipment[] = array(
      'count' => $count,
      'name' => $result['name'],
      'pageUrl' => $result['page_url']
    );

  }

// Manage service
$query = $database->query("select p.page_id, p.page_url, pd.name from " . TABLE_PAGES . " p, " . TABLE_PAGES_DESCRIPTION . " pd, " . TABLE_USER_GROUPS_TO_PAGES . " ugtp, " . TABLE_PAGES_GROUPS . " pg where pg.file_name = '" . 'manage_service.php' . "' and pg.page_group_id = p.page_group_id and p.page_id = ugtp.page_id and ugtp.user_group_id = '" . $userGroupId . "' and p.page_id = pd.page_id and pd.language_id = '" . $language_id . "' order by pd.name ASC");
$count = 0;
    foreach($query as $result){
    $count++;
    $manageService[] = array(
      'count' => $count,
      'name' => $result['name'],
      'pageUrl' => $result['page_url']
    );

  }

//Manage service area
$query = $database->query("select p.page_id, p.page_url, pd.name from " . TABLE_PAGES . " p, " . TABLE_PAGES_DESCRIPTION . " pd, " . TABLE_USER_GROUPS_TO_PAGES . " ugtp, " . TABLE_PAGES_GROUPS . " pg where pg.file_name = '" . 'manage_service_area.php' . "' and pg.page_group_id = p.page_group_id and p.page_id = ugtp.page_id and ugtp.user_group_id = '" . $userGroupId . "' and p.page_id = pd.page_id and pd.language_id = '" . $language_id . "' order by pd.name ASC");
$count = 0;
    foreach($query as $result){
    $count++;
    $manageServiceArea[] = array(
      'count' => $count,
      'name' => $result['name'],
      'pageUrl' => $result['page_url']
    );

  }

// Manage Website
$query = $database->query("select p.page_id, p.page_url, pd.name from " . TABLE_PAGES . " p, " . TABLE_PAGES_DESCRIPTION . " pd, " . TABLE_USER_GROUPS_TO_PAGES . " ugtp, " . TABLE_PAGES_GROUPS . " pg where pg.file_name = '" . 'manage_website.php' . "' and pg.page_group_id = p.page_group_id and p.page_id = ugtp.page_id and ugtp.user_group_id = '" . $userGroupId . "' and p.page_id = pd.page_id and pd.language_id = '" . $language_id . "' order by pd.name ASC");
$count = 0;
foreach($query as $result){
    $count++;
    $manageWebsite[] = array(
      'count' => $count,
      'name' => $result['name'],
      'pageUrl' => $result['page_url']
    );

  }

// Expiremental
$query = $database->query("select p.page_id, p.page_url, pd.name from " . TABLE_PAGES . " p, " . TABLE_PAGES_DESCRIPTION . " pd, " . TABLE_USER_GROUPS_TO_PAGES . " ugtp, " . TABLE_PAGES_GROUPS . " pg where pg.file_name = '" . 'experimental.php' . "' and pg.page_group_id = p.page_group_id and p.page_id = ugtp.page_id and ugtp.user_group_id = '" . $userGroupId . "' and p.page_id = pd.page_id and pd.language_id = '" . $language_id . "' order by pd.name ASC");
$count = 0;
foreach($query as $result){
    $count++;
    $experimental[] = array(
      'count' => $count,
      'name' => $result['name'],
      'pageUrl' => $result['page_url']
    );

  }

// My account
$myAccount = array(
  'isLoggedIn' => $user->user_is_logged(),
  'errorString' => $error->get_error_status('login_box'),
  'httpPrefix' => HTTPS_PREFIX,
  'pageUrl' => PAGE_URL,
  'pages' => array(
    array(
      'count' => 1,
      'name' => 'Update Account Information',
      'pageUrl' => FILENAME_ACCOUNT_UPDATE
    ),
    array(
      'count' => 2,
      'name' => 'Change Password',
      'pageUrl' => FILENAME_ACCOUNT_CHANGE_PASSWORD
    ),
    array(
      'count' => 3,
      'name' => 'Account Overview',
      'pageUrl' => FILENAME_ACCOUNT_OVERVIEW
    ),
    array(
      'count' => 4,
      'name' => 'Logoff',
      'pageUrl' => PAGE_URL
    )
  )

);

$pages = array(
  'dailyManagement' => $dailyManagement,
  'statisticsReports' => $statisticsReports,
  'manageUsers' => $manageUsers,
  'manageEquipment' => $manageEquipment,
  'manageService' => $manageService,
  'manageServiceArea' => $manageServiceArea,
  'manageWebsite' => $manageWebsite,
  'experimental' => $experimental,
  'myAccount' => $myAccount
);
echo json_encode($pages);
?>
