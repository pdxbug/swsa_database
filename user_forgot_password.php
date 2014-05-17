<?php session_start(); // start up your PHP session

/*Created by Kerry Mraz for OLANpia LAN Party events
04/05/2012

Page Information:
This file will check to verify the user exists with their Name or Email Address and provide them with information about their account.
*/

//include server url string
include '../../../server_url.php';

//include database connect file
include '../../../db_connection_scottb.php';


if($_GET['reset']==0){//the user is just looking for their username or information about resetting a password for an account, do this function
	include_once $_SERVER['DOCUMENT_ROOT'] . '/temp/scott/swsa/securimage/securimage.php';
	$securimage = new Securimage();
	
	if ($securimage->check($_POST['captcha_code']) == false) {
		// the CAPTCHA code was incorrect
		$_SESSION['captcha_error']=1;	
		header("Location: http://$server_url/user_forgot_password.htm"); //send the user to the registration02 page for re-verification that they are human
		exit;
	}
	
	//retreive the information provided by user_forgot_password.htm form fields
	$fname = $_POST["fname"];
	$lname = $_POST["lname"];
	$dob = $_POST["dob"];
	
	//check to see if the user entered a Name or an Email Address
	if(($fname!="")&&($lname!="")){ //check to see if fname and lname are entered
		//set the query here to check for information
		$query = "SELECT id,card_id,email FROM player_list WHERE fname = '$fname' AND lname = '$lname'";
		if ($conn) { 
		//query the database for the information	 
			$result = mysql_query($query, $conn); //sending the query to the database and capturing it in a variable
			$num_rows = mysql_num_rows($result); //set a variable to check if there are results
			if($num_rows > 0){//the user exists and has been located by the phone number
				$row = mysql_fetch_array($result); //putting the captured data into variables
				$dob_check = substr($row['card_id'],0,6);
				if($dob_check == $dob){
					$_SESSION['reset_fname']=$fname;
					$_SESSION['reset_id']=$row['id'];
					$_SESSION['reset_email']=$row['email'];
					$_SESSION['reset_card_id']=$row['card_id'];
					$_SESSION['check_bit_01']=1; //we found the username and set a bit
				} else { //dob does not match, return with error
					$_SESSION['check_bit_01']=5; //we did not find a username with the first and last name and set a bit
				}
			} else {
				$_SESSION['check_bit_01']=2; //we did not find a username with the first and last name and set a bit
			}
		}
	} else {
		$_SESSION['check_bit_01']=3;//no information was entered in the required fields so we set a bit
	}
	
	header("Location: http://$server_url/user_forgot_password.htm");
	exit;
} elseif($_GET['reset']==1){//the user has requested to send the card id to their registered Email address
	
	//store the session variables
	$id = $_SESSION['reset_id'];
	$fname = $_SESSION['reset_fname'];
	$email = $_SESSION['reset_email'];	
	$card_id = $_SESSION['reset_card_id'];  
	
	if ($conn) { 
	//send the user an Email with their card id	
		$to = $email;
		$subject = "SWSA - Player Information Request ";
		$body = "Hello " . $fname . "\n
		A request for your player card information was requested from the SWSA website. Your SWSA Card ID on file is:\n
		" . $card_id . "\n\n";
		$headers = "From: scottb@oly-wa.us\r\n" . "X-Mailer: php";
		if(mail($to, $subject, $body, $headers)){
			$_SESSION['check_bit_01']=4;
			header("Location: http://$server_url/user_forgot_password.htm"); //send the user to the volunteer forgot password page
			exit;
		}
	}

}
//close the database connection
mysql_close($conn);

?>