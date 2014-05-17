<?php session_start(); //start the session 

/* Created by Kerry Mraz for the user of SWSA
Created 2/20/14

Page Information:
This page will verify the player is a captain for the team and then update the team with the new player
*/

//include server url string
include 'server_url.php';

//include database connect file
include 'db_connection_scottb.php';

//check to see if the user is logged in, else redirect them to the login page if not
if ((empty($_SESSION['user_id']))||($_SESSION['login_fail']!=0)){
	$_SESSION['login_fail']=5;
	header("Location: http://$server_url/user_login.php?page=user_cpt_add_plrphp");
	exit;
}
$whichTeam = $_GET['whichTeam'];
$whichLeague = $_GET['whichLeague'];
if($whichLeague == 0){//check to see if person is over 30
	$age = 30;
} else if($whichLeague == 1){//check to see if person is over 40
	$age = 40;
} else if($whichLeague == 2){//coed open, no age limit
	$age = 0;
}

date_default_timezone_set('America/Los_Angeles');

//if the captain is searching with a card id, this code will get the player_s database id; otherwise, we store the information in local variables
if(isset($_GET['card_id'])){
	$card_id = $_GET['card_id'];
	$player_query = "SELECT id,email,team,card_id FROM player_list WHERE card_id = '$card_id'";
	$player_result = mysql_query($player_query,$conn);
	if(!$player_result){ // add this check.
		die('Invalid query: ' . mysql_error());
		exit;
	}
	$id_row = mysql_fetch_array($player_result, MYSQL_NUM);
	$id = $id_row[0];
	$email = $id_row[1];
	$team = explode(',',$id_row[2]);
	foreach($team as &$value){
		$team_query = "SELECT id FROM team_list WHERE id = '$value' AND league = '$whichLeague'";
		$team_result = mysql_query($team_query,$conn);
		$team_numrows = mysql_num_rows($team_result);
		if($team_numrows > 0){
			header("Location: http://$server_url/user_cpt_add_player.htm?whichLeague=$whichLeague&whichTeam=$whichTeam&fail=2");
			exit;
		}
	}
	//check to make sure the player has the age requirements
	$player_dob = substr($id_row[3],0,-3);
	$player_dob = substr_replace($player_dob,'/',4,0);
	$player_dob = substr_replace($player_dob,'/',2,0);
	$player_dob = date('m-d-Y',strtotime($player_dob));
	$player_dob = explode('-', $player_dob);
	$date = date('m-d-Y');
	$date = explode('-',$date);
	$player_age = $date[2]-$player_dob[2];
	if($player_dob[0]>$date[0]){
		$player_age=$player_age-1;
	}
	if(($date[0]==$player_dob[0])&&($player_dob[1]>$date[1])){
		$player_age=$player_age-1;
	}
	if(($age!=0)&&($age>$player_age)){
		header("Location: http://$server_url/user_cpt_add_player.htm?whichLeague=$whichLeague&whichTeam=$whichTeam&fail=3");
		exit;
	}
} else if(isset($_GET['id'])){
	$id = $_GET['id'];
	$email = $_GET['email'];
	$remove = 0;
} else {
	$id = $_GET['rid'];
	$remove = 1;
	if($_GET['option']=="make_captain"){
		$query = "SELECT card_id FROM player_list WHERE id = $id";
		$result = mysql_query($query,$conn);
		if(!$result){ // add this check.
			die('Invalid query: ' . mysql_error());
			exit;
		}
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		$post = "UPDATE team_list SET $_GET[rep] = '$row[card_id]' WHERE id = $whichTeam";
		//echo $post;
		$result = mysql_query($post,$conn);
		if(!$result){ // add this check.
			die('Invalid query: ' . mysql_error());
			exit;
		} else {
			header("Location: http://$server_url/user_main.htm?whichLeague=$whichLeague&whichTeam=$whichTeam");
			exit;
		}
	}
}

//check to see that the player doing the search is a team captain for this team
$team_query = "SELECT team_name,rep_1,rep_2 FROM team_list WHERE id = '$whichTeam'";
$team_result = mysql_query($team_query,$conn);
if(!$team_result){ // add this check.
	die('Invalid query: ' . mysql_error());
	exit;
}
$team_row = mysql_fetch_array($team_result, MYSQL_ASSOC);
//print_r($team_row);
//echo '</ br>' . $_SESSION['card_id'];
if(($team_row['rep_1']==$_SESSION['card_id'])||($team_row['rep_2']==$_SESSION['card_id'])){
	$player_query = "SELECT team FROM player_list WHERE id = '$id'";
	//echo $player_query;
	$player_result = mysql_query($player_query,$conn);
	if(!$player_result){ // add this check.
		die('Invalid query: ' . mysql_error());
		exit;
	}
	unset($player_team);
	while($player_row = mysql_fetch_array($player_result, MYSQL_NUM)){
		if(empty($player_row[0])){
			$player_team = $whichTeam;
		} else {
			$team_explode = explode(',',$player_row[0]);
			$value_set = 0; //setting a variable so that we know when we have already placed the value
			foreach($team_explode as &$value){
				//ADD or REMOVE?
				if($remove == 0){//we are adding the player to a team
					if($whichTeam < $value){//we are going to half hearted attempt to sort the teams from low to high. this is not necessary, but sometimes OCD takes over
						if($value_set == 0){
							if(!isset($player_team)){//player team variable does not exist, so we do not want a comma first
								$player_team = $whichTeam . ',' . $value;
								$value_set = 1;
							} else {//player team variable does exist, so we do want a comma to come first
								$player_team = $player_team . ',' . $whichTeam . ',' . $value;
								$value_set = 1;
							}
						} else {
							$player_team = $player_team . ',' . $value;
						} 
					} else if($whichTeam == $value){//player is already on the team
						header("Location: http://$server_url/user_cpt_add_player.htm?whichLeague=$whichLeague&whichTeam=$whichTeam&fail=1");
						exit;
					} else {
						if(!isset($player_team)){//player team variable does not exist, so we don't want a comma first
							$player_team = $value;
						} else {//player team variable does exist, so we do want a comma to come first
							$player_team = $player_team . ',' . $value;
						}
					}
				} else { //we are removing a player from the team
					$value_set = 1;
					if($whichTeam != $value){
						if(!isset($player_team)){
							$player_team = $value;
						} else {
							$player_team = $player_team . ',' . $value;
						}
					}
				}
			}
			if($value_set == 0){
				$player_team = $player_team . ',' . $whichTeam;
			}
			unset($value);
		}			
	}
	//echo $player_team;
	$player_team_post = "UPDATE player_list SET team='$player_team' WHERE id='$id'";
	$pickup_player_check = "DELETE FROM player_pickup WHERE player_id = '$id' AND league = '$whichLeague'";//we want to remove the player from the player pickup list if a captain picks them up via another option
	$result_pickup_player_check = mysql_query($pickup_player_check,$conn);
	//echo $pickup_player_check;
	if($_SESSION['user_id']==$id){
		$_SESSION['team']=$player_team;//if the captain is updating their own info, we need to update the session variable
	}
	//echo $player_team_post;
	$player_result = mysql_query($player_team_post,$conn);
	if(!$player_result){ // add this check.
		die('Invalid query: ' . mysql_error());
		exit;
	} else {
		if(isset($_GET['pickup'])){
			$pickup_query = "DELETE FROM player_pickup WHERE id = '$_GET[pickup]'";
			//echo $pickup_query;
			$pickup_result = mysql_query($pickup_query,$conn);
			if(!$pickup_result){ // add this check.
				die('Invalid query: ' . mysql_error());
				exit;
			}
		}
		$captain_query = "SELECT fname,lname,phone,email FROM player_list WHERE card_id ='$team_row[rep_1]' OR card_id ='$team_row[rep_2]'";
		//echo $captain_query;
		$captain_result = mysql_query($captain_query,$conn);
		if(!$captain_result){ // add this check.
			die('Invalid query: ' . mysql_error());
			exit;
		}
		$captain_nums = mysql_num_rows($captain_result);
		if($captain_nums > 1){
			$i = 1;
			$captain_info = '';
			while($captain_row = mysql_fetch_array($captain_result, MYSQL_ASSOC)){
				$captain_info = $captain_info . 'Captain ' . $i . ': ' . $captain_row[fname] . ' ' . $captain_row[lname] . ' ' .  $captain_row[phone] . ' ' .  $captain_row[email] . '/n';
			}
		} else {
			$captain_row = mysql_fetch_array($captain_result, MYSQL_ASSOC);
			$captain_info = $captain_info . 'Captain: ' . $captain_row[fname] . ' ' . $captain_row[lname] . ' ' .  $captain_row[phone] . ' ' .  $captain_row[email];
		}
		$to = 'grsshppr_km@yahoo.com';//$email;
		$subject = "SWSA - Added to Soccer Team";
		$body = "A team captain has added you to their soccer team on the SWSA website. \n
		Team Details\r
		Team Name: " . $team_row['team_name'] . "\n
		Team Captain Information:\n" . $captain_info . "\n";
		$headers = "From: kerry@fossil-bug.com.com\r\n" . "X-Mailer: php";
		//echo $body;
		if(mail($to, $subject, $body, $headers)){
			header("Location: http://$server_url/user_main.htm?whichLeague=$whichLeague&whichTeam=$whichTeam&success=2");
			exit;
		} else {
			echo 'Player has been added to team; however, the website was unable to send an Email to the player. Please inform them they have been added. Click to continue to the <a href="http://$server_url/user_main.htm?whichLeague=$whichLeague&whichTeam=$whichTeam&success=2">main page</a>.';
		}
	}
} else {
	echo 'You are not listed as a team captain and are not authorized to add or remove players.';
}

//close the database connection
mysql_close($conn);
?>