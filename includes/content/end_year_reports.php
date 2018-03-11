<?php
// Created 1/10/2013 brad@brgr2.com
// Updated 1/14/2013 brad@brgr2.com
/*
 * Prints a list of orders broken down by year, with year total for *all* users if admin, individual user if not.
 */
$user_id = 0;
if(isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id'])) 
{
    $user_id = (int)$_REQUEST['user_id'];
}

$menu = $script = null;
if(!$user_id) 
{
    $menu = "<div class='row-fluid'><label class='span1' for='user_id'>User</label><span class='span4'>".tep_draw_agent_pulldown('user_id',$user_id) . "</span></div>";
}

// Admins
if(isset($_SESSION['user_group_id']) && $_SESSION['user_group_id'] == 2) 
{ // Admin
    $menu = "<div class='row-fluid'><label class='span1' for='user_id'>User: </label><span class='span4'>".tep_draw_agent_pulldown('user_id',$user_id) . "</span></div>";
} 
elseif(isset($_SESSION['user_group_id']) && $_SESSION['user_group_id'] == 4) 
{ // Agency order manager
    $menu = "<div class='row-fluid'><label class='span1' for='user_id'>User: </label><span class='span4'>".tep_draw_aom_agent_pulldown('user_id',$user_id,$_SESSION['user_id']) . "</span></div>";
} 
else 
{
    $script = "google.setOnLoadCallback(getOrderReport);";
}
$vars['script'] = $script;
$vars['menu'] = $menu;
//echo $vars['menu'];
?>
<?php echo $twig->render('agent/end_of_year.html.twig', array('user' => $user, 'page' => $page, 'vars'=>$vars, 'error'=>$error)); ?>
