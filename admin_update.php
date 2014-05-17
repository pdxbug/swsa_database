<?php  session_start(); //start the session 

/* Created by Kerry Mraz for SWSA
Created 6/18/2013

Contact kerry@fossil-bug.com

Page Information:
Page created for SWSA to allow admins to update the notification text file and create/remove admins.  Information comes from admin_update.htm. The database or text file will be updated and the user returned to admin_update.htm with a success message.
*/

//check to see if the user is logged in, else redirect them to the login page if not
if ((empty($_SESSION['user_id']))||($_SESSION['login_fail']!=0)||($_SESSION['admin']!=1)){
	$_SESSION['login_fail']=5;
	header("Location: http://$server_url/user_login.php?page=admin_updatehtm");
	//print_r($_SESSION);
	exit;
}	

if(isset($_POST['notification'])){
	if(file_put_contents('alert_notification.txt',$_POST['notification'])){
		header("Location: http://$server_url/admin_update.htm?update=success");
		exit;
	} else {
		header("Location: http://$server_url/admin_update.htm?update=fail");
		exit;
	}
}
if((isset($_POST['admin']))&&(is_numeric($_POST['id']))){
	//include database connect file
	include '../../../docs/db_connection_scottb.php';
	if($_POST['admin']=="make"){
		$post = "UPDATE player_list SET admin=1 WHERE id='$_POST[id]'";
	} else {
		$post = "UPDATE player_list SET admin=0 WHERE id='$_POST[id]'";
	}
	$result = mysql_query($post,$conn);
	if (!$result) { // add this check.
		die('Invalid query: ' . mysql_error());
		exit;
	} else {
		header("Location: http://$server_urls/admin_update.htm?update=success");
		exit;
	}
	
	//close the database connection
	mysql_close($conn);
}

?>