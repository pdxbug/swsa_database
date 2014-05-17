<?php session_start(); // start up your PHP session!	

/*Created by Kerry Mraz for OLANpia LAN Party events
04/04/2012
updated 11/16/2013 KM

Page Information:
This page is provided to register new users to the SWSA Player List page. This page will check the database for errors in requested information from the user and provide feedback for errors, or post the information to the database if there are no errors. The values should be saved if there is an error to prevent users from having to re-enter their information. 
*/

//include server url string
include	'server_url.php';

//include database connect file
include '../../../db_connection_scottb.php';

//retrieve variables from user_registration.htm form
$to="";
$fname = $_POST['fname'];
$lname = $_POST['lname'];
$card_id = $_POST['card_id'];
$email = $_POST['email'];
$phone = $_POST['pnumber01'].'-'.$_POST['pnumber02'].'-'.$_POST['pnumber03']; //only allows the user to input a phone number in the format xxx-xxx-xxxx
$card_id = $_POST['card_id'];
$card_exp = isset($_REQUEST['date1']) ? $_REQUEST['date1'] : "";
$pref_pos = $_POST['pref_pos'];

//verify the CAPTCHA information was correct
include_once $_SERVER['DOCUMENT_ROOT'] . '/temp/scott/swsa/securimage/securimage.php';
$securimage = new Securimage();

if ($securimage->check($_POST['captcha_code']) == false) {
	// the CAPTCHA code was incorrect
	$_SESSION['registration_error']=1;//set registration error code to provide feedback to the user
	//store the variables to prevent having to re-enter them into the form
	$_SESSION['fname'] = $_POST['fname'];
	$_SESSION['lname'] = $_POST['lname'];
	$_SESSION['card_id'] = $_POST['card_id'];
	$_SESSION['email'] = $_POST['email'];
	$_SESSION['pnumber01'] = $_POST['pnumber01'];
	$_SESSION['pnumber02'] = $_POST['pnumber02'];
	$_SESSION['pnumber03'] = $_POST['pnumber03'];
	$_SESSION['card_exp'] = $card_exp;
	header("Location: http://$server_url/user_register.htm?registration_state=1"); //send the user to the registration page for re-verification that they are human
	exit;
}

//check to verify the username is not already taken, if not enter the information to the database
if ($conn) { 
//query the database for the information	
	$query = "SELECT * FROM player_list WHERE card_id = '$card_id'"; //creating the query for MYSQL to look for the volunteer's username
	$result = mysql_query($query, $conn); //sending the query to the database and capturing it in a variable
	if(!$result){
		die(mysql_error());
	}
	$num_rows = mysql_num_rows($result);
	if ($num_rows!=0){
		$_SESSION['registration_error']=2; //user already exists, set variable to 2
		//store the variables to prevent having to re-enter them into the form
		$_SESSION['fname'] = $_POST['fname'];
		$_SESSION['lname'] = $_POST['lname'];
		$_SESSION['card_id'] = $_POST['card_id'];
		$_SESSION['email'] = $_POST['email'];
		$_SESSION['pnumber01'] = $_POST['pnumber01'];
		$_SESSION['pnumber02'] = $_POST['pnumber02'];
		$_SESSION['pnumber03'] = $_POST['pnumber03'];
		$_SESSION['card_exp'] = $card_exp;
		header("Location: http://$server_url/user_register.htm?registration_state=1"); //send the user back to registration page
		exit;
	} else { 
		$_SESSION['registration_error']=0; //user does not exist, set variable to 0
		$post = "INSERT INTO player_list (fname, lname, card_id, card_exp, phone, email, pref_pos) VALUES ('".$fname."','".$lname."','".$card_id."','".$card_exp."','".$phone."','".$email."','".$pref_pos."')";
		if (mysql_query($post)) {//if the post is successful, we want to save some of the data for this current session
			$query = "SELECT id FROM player_list WHERE card_id = '$card_id'"; //creating the query for MYSQL to look for the user's handle
			$result = mysql_query($query, $conn); //sending the query to the database and capturing it in a variable
			if(!$result){
				die(mysql_error());
			}
			while($row = mysql_fetch_array($result)){ //putting the captured data into variables
				$_SESSION['user_id'] = $row["id"]; //grabbing user information from the database
			}
			$_SESSION['fname'] = $_POST['fname']; //grabbing user information from the database
			$_SESSION['lname'] = $_POST['lname']; //grabbing user information from the database
			$_SESSION['admin'] = 0; //grabbing user information from the database
			$to = "kerry@fossil-bug.com";
			$subject = "SWSA - New Player Registration";
			$body = "A user successfully registered on SWSA Player List\n
			User Details\n
			Name: " . $fname . " " . $lname . "\n
			Phone: " . $phone . "\n
			Email: " . $email . "\n";
			$headers = "From: kerry@fossil-bug.com.com\r\n" . "X-Mailer: php";
			if(mail($to, $subject, $body, $headers)){
				$to = $email;
				$subject = "SWSA Player Registration";
				$body = "You have successfully registered on the SWSA Player Page. You can feel free to login to the website using your First Name, Last Name and SWSA Card ID you used during registration.\n
				Below is the information we have stored in our records. If the information is not correct, you can login to the website and modify the information. The personal information you have provided will not be shared and is only used by the SWSA Player List.\n\n
				Player Details\n
				Name: " . $fname . " " . $lname . "\n
				Phone: " . $phone . "\n
				Email: kerry@fossil-bug.com\n
				SWSA Card ID: " . $card_id . "\n
				Card Expiration: " . $card_exp . "\n";
				//removed from body for testing purposes Email: " . $email . "\n
				$headers = "From: kerry@fossil-bug.com\r\n" . "X-Mailer: php";
				if(mail($to, $subject, $body, $headers)){
					header("Location: http://$server_url/user_main.htm"); //send the user to the volunteer's personal page
					exit;
				} else {
				 echo 'Confirmation Email was not sent.';
				}//close if line 152
			} else {
				echo 'Information was added to the database; however, unable to send an Email to the admin. Click to continue to the <a href="user_main.htm">SWSA Player Page</a>.';
			}//close if line 130
		}//close if line 110
	}//close if line 81
}//close if line 71

//close the database connection
mysql_close($conn);

?>