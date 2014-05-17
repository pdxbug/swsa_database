<?php session_start();

/*Created by Kerry Mraz for SWSA Player database
04/29/2014

Page Information:
This file is provided for Captains of the SWSA player database website. On it the Captains will be able to download a CSV file from user_main.htm.
*/

//include server url string
include	'server_url.php';

//check to see if the user is logged in, else redirect them to the login page if not
if ((empty($_SESSION['user_id']))||($_SESSION['login_fail']!=0)){
	$_SESSION['login_fail']=5;
	header("Location: http://$server_url/user_login.php?page=user_cpt_dwnl_rstr_php");
	exit;
}	

//include database connect file
include '../../../db_connection_scottb.php';

	$query_team = "SELECT team_name,rep_1,rep_2 FROM team_list WHERE id = '$_GET[whichTeam]'";
	$result_team = mysql_query($query_team, $conn);
	if(!$result_team){ // add this check.
		die('Invalid query: ' . mysql_error());
		exit;
	}
	$row_team = mysql_fetch_array($result_team, MYSQL_ASSOC);
	$query_players = "SELECT * FROM player_list WHERE team LIKE CONVERT( _utf8 '%,$_GET[whichTeam],%' USING latin1 ) OR `team` LIKE CONVERT( _utf8 '1,%' USING latin1 ) OR `team` LIKE CONVERT( _utf8 '%,$_GET[whichTeam]' USING latin1 )
OR `team` = '$_GET[whichTeam]'";
	$result_players = mysql_query($query_players, $conn);
	if(!$result_players){ // add this check.
		die('Invalid query: ' . mysql_error());
		exit;
	}
	if (mysql_num_rows($result_players) > 0) {//as long as there are results, do the following	
		while($row = mysql_fetch_array($result_players, MYSQL_ASSOC)){
			if(($_SESSION['card_id'] == $row_team['rep_1'])||($_SESSION['card_id'] == $row_team['rep_2'])){//check to make sure the player is a team captain for this team; otherwise, send them back.
				//begin start of the page to be written as excel file
				$date = date('Y-m-d');
				header('Content-Disposition: attachment; filename="SWSA_" . $row[team_name] . "_team_roster_" . $date . ".xls"');
				echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
					<html xmlns="http://www.w3.org/1999/xhtml">
					<head>
					<title>SWSA_' . $row[team_name] . '_team_roster_' . $date . '</title>
					</head>
					
					<body>
					
						<table>
							<tr><th></th></tr>
							<tr><td></td></tr>
						</table>
					
					</body>
					</html>';
				//end page to be written to excel
			} else { //player is not recognized as a team captain. 
				echo 'You are not recognized as a Team Captain. If this is in error, please use the contact us page for more information. Otherwise, please <a href="http://$server_url/user_main.htm">Go Back</a>';
			}
		}
	}
	
//close the database connection
mysql_close($conn);

?>