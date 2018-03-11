<?php

error_reporting(E_ALL);

function n($s) { return strtolower(trim($s)); }

$zipBlob = file_get_contents("database/ZIP_CODES.txt");
if ($zipBlob === FALSE) die('failed to open');

// "01002","+42.367092","-072.464571","AMHERST","MA","HAMPSHIRE","STANDARD"
//
$zips = explode("\n", $zipBlob);

$lookup = array();
foreach ($zips as $rec) {
  $fields = explode(',', $rec);
  if (count($fields) > 5) {
    $z = n(substr($fields[0],1,-1));
    $c = n(substr($fields[5],1,-1));
    $lookup[$z] = $c;
  }
}

include('includes/application_top.php');

$counties = array();
$sql = "SELECT county_id,name,state_id FROM countys";
$query = $database->query($sql);
foreach($database->fetch_array($query) as $res)
{
  if ($res['state_id'] > 0)
    $counties[$res['state_id']][n($res['name'])] = $res['county_id'];
}

echo '<pre>';

$fixes = array();
$misses = array();

$sql = "SELECT a.*,s.name AS state_name FROM addresses a, states s WHERE a.county_id=0 AND a.state_id=s.state_id";
$query = $database->query($sql);
foreach($database->fetch_array($query) as $addr)
{
  $aid = $addr['address_id'];
  $sid = $addr['state_id'];
  $zip = $addr['zip'];

  echo "$aid ";

  if (isset($lookup[$zip])) {
    $cname = n($lookup[$zip]);

    $cname = preg_replace('/ city$/', '', $cname);
    switch ($cname) {
      case 'district of columbia': $cname = 'dc'; break;
      case 'manassas':
      case 'manassas park':
        $cname = 'prince william'; break;
      case 'falls church': $cname = 'fairfax'; break;
      case 'fredericksburg': $cname = 'spotsylvania'; break;
      case 'winchester': $cname = 'frederick'; break;
    }

    if (isset($counties[$sid])) {
      if (isset($counties[$sid][$cname])) {
        $cid = $counties[$sid][$cname];

        echo "-> $cname, ". $addr['state_name'];
        //echo "county: $cname county_id: $cid"; // .'<br>';

        $fixes[$aid] = $cid;
        //$sql = "UPDATE addresses SET county_id=$cid WHERE address_id=$aid LIMIT 1";
        //echo "sql: $sql";


      } else {
        echo "county '$cname' not found";
      }
    } else {
      echo "state $sid <b>not found</b>";
    }

  } else {
    echo "zip $zip not found";
  }

  if (!isset($fixes[$aid]))
    $misses[$aid] = true;

  echo "\n"; //'<br>';
  
}
echo '</pre>';

echo 'fixes: '.count($fixes) ."<br>";
echo 'misses: '.count($misses) ."<br>";

foreach ($fixes as $aid => $cid) {

  if (!empty($aid) && $aid > 0)
    $database->query("UPDATE addresses SET county_id=$cid WHERE address_id=$aid LIMIT 1");

}
?>

