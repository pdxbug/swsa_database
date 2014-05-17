<?php session_start(); //start the session 

/* Created by Kerry Mraz for the user of SWSA
Created 3/9/2014

Page Information:
This page will update the database with the user's new information
*/

//include server url string
include '../../../docs/server_url.php';

//include database connect file
include '../../../docs/db_connection_scottb.php';

$id = $_SESSION['user_id'];
$date = date('Y-m-d');
$whichLeague = $_POST['whichLeague'];

//we don_t want the player posting too many times for the same league. Do a check to make sure they are not already on the list.
$query = "SELECT id FROM player_pickup WHERE player_id = '$id' AND league = '$whichLeague'";
$result = mysql_query($query,$conn);
if(!$result){ // add this check.
	die('Invalid query: ' . mysql_error());
	exit;
}
$result_num = mysql_num_rows($result);
if($result_num!=0){//the player is already on the player pickup list for this league
	header("Location: http://$server_url/user_main.htm?freeAgent=1&fail=1");//send the user back and let them know
	exit;
}

$query = "INSERT INTO player_pickup VALUES ('','$id','$date','$whichLeague')";

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