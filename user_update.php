<?php session_start(); //start the session 

/* Created by Kerry Mraz for the user of SWSA
Created 6/5/13

Page Information:
This page will update the database with the user's new information
*/

//include server url string
include	'server_url.php';

//include database connect file
include 'db_connection_scottb.php';

/*print_r($_POST);
echo '<br />';
print_r($_GET);
echo '<br />';
print_r($_SESSION);
echo '<br />';*/

$query = "UPDATE player_list SET";

if(isset($_POST['card_exp'])){
	$query .= " card_exp = '$_POST[card_exp]'";
	$_SESSION['card_exp'] = $_POST['card_exp'];
}
if(isset($_POST['phone'])){
	$query .= ", phone = '$_POST[phone]'";
}
if(isset($_POST['email'])){
	$query .= ", email = '$_POST[email]'";
}
if(isset($_POST['pref_pos'])){
	$query .= ", pref_pos = '$_POST[pref_pos]'";
}
if(isset($_POST['teams'])){
	$teams = explode(',',$_POST['teams']);
	if(count($teams)==1){
		$query .= " team = ''";
		$_SESSION['team']='';
	} else {
		$query .= " team = '";
		$pos = array_search($_POST['team_remove'],$teams);
		unset($teams[$pos]);
		$update = implode(",",$teams);
		$_SESSION['team']=$update;
		$query .= "$update'";
	}
}

if(isset($_POST['id'])){
	$id = $_POST['id'];
} else {
	$id = $_SESSION['user_id'];
}

if(isset($_GET['whichTeam'])){
	$player_query = "SELECT team FROM player_list WHERE id  = '$id'";
	$player_result = mysql_query($player_query,$conn);
	if(!$player_result){ // add this check.
		die('Invalid query: ' . mysql_error());
		exit;
	}
	$row = mysql_fetch_array($player_result);
	if($row['team']==''){
		$query .= " team = '$_GET[whichTeam]'";
	} else {
		$query .= " team = '$row[team],$_GET[whichTeam]'";
	}
}

$query .= " WHERE id = '$id'";

//echo $query;
$result = mysql_query($query,$conn);
if(!$result){ // add this check.
	die('Invalid query: ' . mysql_error());
	exit;
}
header("Location: http://$server_url/user_main.htm?success=1");//send the user to the main user page
exit;

//close the database connection
mysql_close($conn);

?>