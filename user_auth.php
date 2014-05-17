<?php  session_start(); //start the session 

/* Created by Kerry Mraz for SWSA
Created 3/30/2012
Modified 5/29/2013

Contact kerry@fossil-bug.com

Page Information
This page will verify the user has an account and send them to the appropriate page if they check out. If the password, username, or other error occurs, it will redirect them back to the user_login page with the appropriate error message. The session will be locked out should the user fail to login five times. This can be resolved by:
1. resetting their own password
2. clearing cache and cookies
3. closing and opening their browser
It isn't a perfect security fix, but should stop the average user */

//This function was found at: http://www.folksonomy.org/2009/08/31/fatal-error-call-to-undefined-function-file_put_contents/
if(!function_exists('file_put_contents')) {//call function to work around file_put_contents error on server
function file_put_contents($filename, $data, $file_append = false) {
  $fp = fopen($filename, (!$file_append ? 'w+' : 'a+'));
	if(!$fp) {
	  trigger_error('file_put_contents cannot write in file.', E_USER_ERROR);
	  return;
	}
  fputs($fp, $data);
  fclose($fp);
}
}

//reset the login_fail variable
$_SESSION['login_fail'] = 0;

//include server url string
include '../../../server_url.php';

//include database connect file
include '../../../db_connection_scottb.php';//xxx change this before making pubic

//get the info from the user_login.htm form
$fname = $_POST["fname"];
$lname = $_POST["lname"];
$password = $_POST["password"];

//Check to see if the user exists and retreive their data from the database
if ($conn) { 
//query the database for the information	 
	$query = "SELECT id,fname,lname,card_id,card_exp,gender,team,phone,email,admin FROM player_list WHERE fname = '$fname' AND lname ='$lname'"; //creating the query for MYSQL to look for the user's handle
	//echo $query;
	$result = mysql_query($query, $conn); //sending the query to the database and capturing it in a variable
	if(!result){ // add this check.
		die('Invalid query: ' . mysql_error());
		exit;
	} else {
		$num_rows = mysql_num_rows($result); //set a variable to check if there are results
		if($num_rows==0) { //verifying that data was captured to make sure the username exists
			$_SESSION['login_fail'] = 1;//set the session login failure to username does not exist
			header("Location: http://$server_url/user_login.php");//send the user to the login page with corresponding error code
			exit;
		} else {
			while($row = mysql_fetch_array($result)){ //putting the captured data into variables
				$id = $row["id"]; //grabbing user information from the database
				$fname = $row["fname"]; //grabbing user information from the database
				$lname = $row["lname"]; //grabbing user information from the database
				$card_id = $row["card_id"]; //grabbing user information from the database
				$card_exp = $row["card_exp"]; //grabbing user information from the database
				$gender = $row["gender"]; //grabbing user information from the database
				$team = $row["team"]; //grabbing user information from the database
				$phone = $row["phone"]; //grabbing user information from the database
				$email = $row["email"]; //grabbing user information from the database
				$admin = $row["admin"]; //grabbing user information from the database
			}
			if($password!=$card_id){
				$_SESSION['login_fail'] = 2;//set the session login failure to passwords do not match
				
				//function to get the IP address of the user, if they fail to login properly. Will be used to log their ip and attempt
				//information obtained from http://thephpcode.blogspot.com/2009/01/php-getting-secondary-internet-protocol.html
				$IP = '';
				if (getenv('HTTP_CLIENT_IP')) {
					$IP =getenv('HTTP_CLIENT_IP');
				} elseif (getenv('HTTP_X_FORWARDED_FOR')) {
					$IP =getenv('HTTP_X_FORWARDED_FOR');
				} elseif (getenv('HTTP_X_FORWARDED')) {
					$IP =getenv('HTTP_X_FORWARDED');
				} elseif (getenv('HTTP_FORWARDED_FOR')) {
					$IP =getenv('HTTP_FORWARDED_FOR');
				} elseif (getenv('HTTP_FORWARDED')) {
					$IP = getenv('HTTP_FORWARDED');
				} else {
					$IP = $_SERVER['REMOTE_ADDR'];
				}
				
				$log='tmp/login_attempt_log.log';//declare the log file
				$update_log = 'Date: ' . date("m.d.y H:i:s") . ' - IP: ' . $IP . ' - Attempt #: ' . $_SESSION['login_attempts'] . ' - Name: ' . $fname . ' ' . $lname  . PHP_EOL;//information to update the log with
				//$_SESSION['update_log'] = $update_log;//store to provide feedback of what is being written
				// Log the more than five attempts
				file_put_contents($log, $update_log, FILE_APPEND);
				
				//echo 'password-database: ' . $password_check . '<br /> password-entered: ' . $password; //a check to verify we are getting to the right area for the right reason
				header("Location: http://$server_url/user_login.php");//send the user to the login page with corresponding error code
				exit;
			} else {//login successful, store the variables in Session variables and send the user to the correct page
				$_SESSION['user_id'] = $id; //putting user information into session variables
				$_SESSION['fname'] = $fname; //putting user information into session variables
				$_SESSION['lname'] = $lname; //putting user information into session variables
				$_SESSION['gender'] = $gender; //putting user information into session variables
				$_SESSION['card_id'] = $card_id; //grabbing user information from the database
				$_SESSION['card_exp'] = $card_exp; //grabbing user information from the database
				$_SESSION['team'] = $team; //grabbing user information from the database
				$_SESSION['phone ']= $phone; //grabbing user information from the database
				$_SESSION['email'] = $email; //grabbing user information from the database
				$_SESSION['admin'] = $admin; //grabbing user information from the database
				$_SESSION['login_fail'] = 0;//set session login_fail to 0
				
				//adding a check for player_pickup_list to remove players that have been on the list too long
				$date = date('Y-m-d');
				$date_diff = date('Y-m-d', strtotime($date. ' - 90 days'));
				$player_pickup_check = "SELECT player_id FROM player_pickup WHERE timestamp < '$date_diff'"; //currently set to 90 days old
				$results = mysql_query($player_pickup_check,$conn);
				if(!$results){ // add this check.
					die('Invalid query: ' . mysql_error());
					exit;
				} else {
					$num_results = mysql_num_rows($results);
					if ($num_results>0) {
						while($row = mysql_fetch_array($results, MYSQL_ASSOC)){
							$player = "SELECT email from player_list WHERE id = '$row[player_id]'";//getting the player_s email address to notify them of being removed from the player_pickup_list
							$player_result = mysql_query($player,$conn);
							if(!$player_result){ // add this check.
								die('Invalid query: ' . mysql_error());
								exit;
							} else {
								$email = mysql_fetch_array($player_result,MYSQL_NUM);
								$to = 'grsshppr_km@yahoo.com';//$email[0];
								$subject = "SWSA - Removed from Pickup Player List";
								$body = "You have been removed from the Player Pickup List on the SWSA website. \n
								The list is cleared every 90 days. Feel free to place yourself back on by logging in to the site.\r\n";
								$headers = "From: kerry@fossil-bug.com.com\r\n" . "X-Mailer: php";
								//echo $body;
								if(!mail($to, $subject, $body, $headers)){
									echo 'Player has been removed from the player_pickup_list; however, the website was unable to send an Email to the player. Click to continue to the <a href="http://$server_url/user_main.htm">main page</a>.';
									exit;
								}
							}
						}
						$player_pickup_check = "DELETE FROM player_pickup WHERE timestamp < '$date_diff'"; //currently set to 90 days old
						$results = mysql_query($player_pickup_check,$conn);
						if(!$results){ // add this check.
							die('Invalid query: ' . mysql_error());
							exit;
						}
					}
					header("Location: http://$server_url/user_main.htm");//send the user to the main user page
					exit;
				}
			}
		}
	}
}

//close the database connection
mysql_close($conn);
?>