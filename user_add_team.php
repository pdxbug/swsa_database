<?php  session_start(); //start the session 

/* Created by Kerry Mraz for SWSA
Created 6/19/2013
Updated 3/9/2014

Contact kerry@fossil-bug.com

Page Information:
Page created for SWSA to allow players/admins to update the teams. 
*/

//include server url string
include	'server_url.php';

//check to see if the user is logged in, else redirect them to the login page if not
if ((empty($_SESSION['user_id']))||($_SESSION['login_fail']!=0)){
	$_SESSION['login_fail']=5;
	header("Location: http://$server_url/user_login.php?page=admin_updatehtm");
	//print_r($_SESSION);
	exit;
}	

//include database connect file
include 'db_connection_scottb.php';

if(isset($_POST['whichLeague'])){
	$query = "SELECT id FROM team_list WHERE team_name = '$_POST[name]' AND league = $_POST[whichLeague]";
	$result = mysql_query($query,$conn);
	$num_results = mysql_num_rows($result);
	//echo $query;
	if ($num_results==0){
		$post = "INSERT INTO team_list (team_name,league,color,rep_1) VALUES ('$_POST[name]','$_POST[whichLeague]','$_POST[color]','$_POST[rep_1]')";
		//echo $post;
		if(mysql_query($post,$conn)){
			$query = "SELECT id,league FROM team_list WHERE team_name = '$_POST[name]'";
			$result = mysql_query($query,$conn);
			if(!$result){ // add this check.
				die('Invalid query: ' . mysql_error());
				exit;
			}
			$row = mysql_fetch_array($result,MYSQL_ASSOC);
			header("location: http://$server_url/user_cpt_add_player.php?id=$_SESSION[user_id]&email=$_SESSION[email]&whichLeague=$row[league]&whichTeam=$row[id]");
			exit;
		} else {
			die(mysql_error());
		}
	} else {
		$_SESSION['update_error']=1;
		header('Location: ' . $_SERVER['HTTP_REFERER']);//should be user_add_team.htm
		exit;
	}
}
if(isset($_POST['whichTeam'])){
	if(($_SESSION['card_id']!=$_POST['rep_1'])&&($_SESSION['card_id']!=$_POST['rep_2'])){//checking to verify person is a team captain for deletion or modification
		header("location: http://$server_url/user_main.htm?error=1");
		exit;
	}
	if(isset($_POST['delete'])){
		if($_POST['delete']=="false"){
			echo '<font color="#FF0000">Are you sure you want to remove this team? It will be permanent...</font>
			<form action="user_add_team.php" method="POST" name="remove_team">
			<input type="hidden" name="whichTeam" value="' . $_POST['whichTeam'] . '" />
			<input type="hidden" name="whichLeague" value="' . $_POST['whichLeague'] . '" />
			<input type="hidden" name="rep_1" value="' . $_POST['rep_1'] . '" />
			<input type="hidden" name="rep_2" value="' . $_POST['rep_2'] . '" />
			<input type="hidden" name="delete" value="true" />
			<input type="submit" name="submit" value="Yes" />
			</form>
			<a href="user_main.htm">No</a>';
		} else {
			$query = "DELETE FROM team_list WHERE id = '$_POST[whichTeam]'";
			if(mysql_query($query,$conn)){
				$query = "SELECT id,team FROM player_list WHERE team LIKE '%$_POST[whichTeam]%'";
				//echo $query;
				$result = mysql_query($query, $conn);
				if(!$result){
					die(mysql_error());
				}
				$team = "";
				$teams = array();
				while($row = mysql_fetch_array($result)){
					$teams = explode(',',$row['team']);
					if(count($teams)==1){
						$team = "";
					} else {
						foreach($teams as $value){
							if($_POST['whichTeam']!=$value){
								if($team == ""){
									$team = $value;
								} else {
									$team .= "," . $value;
								}
							}
						}
					}
					$update = "UPDATE player_list SET team = '$team' WHERE id = '$row[id]'";
					if(!mysql_query($update,$conn)){
						die(mysql_error());	
					}
				}
				header("location: http://$server_url/user_main.htm?success=2&whichTeam=$_POST[whichTeam]&whichLeague=$_POST[League]");
				exit;
			} else {
				die(mysql_error());
			}
		}
	} //closes if isset $_POST[delete]
	if($_POST['submit']=="Modify Team"){
		$query = "SELECT team_name,color FROM team_list WHERE id = $_POST[whichTeam]";
		//echo $query;
		$result = mysql_query($query, $conn);
		if(!$result){
			die(mysql_error());
		}
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		
		include('LookAndFeel.php');
		pagebegin('SWSA: Team Modification Page');
		echo '<link href="css/style.css" rel="stylesheet" type="text/css" />
		</head>
		
		<body>
		<div id="wrapper">
			<h3>Southwest Washington Soccer Association</h3> 
			<div id="menu_left">
				<p>Hello ' . $_SESSION['fname'] . '</p>';
				
				include 'user_left_menu.php'; 
				
			echo '</div><!-- closes menu_left div -->
			<div id="content_right">
				<p>You can modify the team name and team color. If you want to change leagues, it is best to create a new team to verify all the players are eligible.</p>
			<form name="modify_team" action="user_add_team.php" method="POST" />
			<table border="1">
			<tr><th><label for="team_name">Team Name</label></th><td><input type="text" name="team_name" value="' . $row['team_name'] . '" /></td></tr>
			<tr><th><label for="color">Team Color</label></th><td><input type="text" name="color" value="' . $row['color'] . '" /></td></tr>
			<tr><td colspan="2">
			<input type="hidden" name="rep_1" value="' . $_POST['rep_1'] . '" />
			<input type="hidden" name="rep_2" value="' . $_POST['rep_2'] . '" />
			<input type="hidden" name="whichTeam" value="' . $_POST['whichTeam'] . '" />
			<input type="hidden" name="League" value="' . $_POST['League'] . '" />
			<input type="submit" name="submit" value="Submit Modification" /></td><tr>
			</table>
			</form><br />
		</div><!-- This closes the content right div -->';
		pageend('2014/04/24 14:35:14');  
		echo '</div><!-- closes wrapper -->';
	}
	if($_POST['submit']=="Submit Modification"){
		$update = "UPDATE team_list SET team_name = '$_POST[team_name]', color = '$_POST[color]' WHERE id = $_POST[whichTeam]";
		if(!mysql_query($update,$conn)){
			die(mysql_error());	
		} else {
			header("location: http://$server_url/user_main.htm?success=2&whichTeam=$_POST[whichTeam]&whichLeague=$_POST[League]");
			exit;
		}
	}
} 
if(isset($_POST['team'])){
	if($_POST['submit']=="Remove Captain"){
		if(isset($_POST['rep_1'])){
			$rep = "rep_1";
		} else {
			$rep = "rep_2";
		}
		$update = "UPDATE team_list SET $rep = '' WHERE id = '$_POST[team]'";
	} else {
		//there are two places to add a captain. One is in user_cpt_add_player for selecting from a dropdown menu. The second is here if there are not assigned captains. 
		if(isset($_POST['rep_1'])){
			$rep = "rep_1";
		} else {
			$rep = "rep_2";
		}
		if(($_POST[$rep]=="")||(empty($_POST[$rep]))){
			$_SESSION['update_error']=1;
			header('Location: ' . $_SERVER['HTTP_REFERER']);//should be user_main.htm
			exit;
		}
		$update = "UPDATE team_list SET $rep = '$_POST[$rep]' WHERE id = '$_POST[team]'";
	}
	if(!mysql_query($update,$conn)){
		die(mysql_error());	
	} else {	
		//echo $update;	
		header("location: http://$server_url/user_main.htm?success=2&whichTeam=$_POST[team]&whichLeague=$_POST[league]");
		exit;
	}
}
			
//close the database connection
				mysql_close($conn);
?>