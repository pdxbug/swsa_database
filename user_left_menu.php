<div id="menu_left">
		<p>Hello <?php echo $_SESSION['fname']; ?></p>

		<ul class="ulrt">
			<li class="top">Player Menu</li>
			<ul class="ulrt">
				<li class="sub"><a href="user_main.htm" title="Link to the main user page">Player Home</a></li>
				<li class="sub"><a href="user_main.htm?id=<?php echo $_SESSION['user_id']
; ?>&update=0" title="Link to the user stat page">My Info</a></li>
				<li class="sub"><a href="user_main.htm?id=<?php echo $_SESSION['user_id']
; ?>&update=1" title="Link to the user stat page">Update Info</a></li>
				<li class="sub"><a href="user_main.htm?id=all" title="Link to the user stat page">All Players</a></li>
				<li class="sub"><a href="user_main.htm?id=search" title="Link to the user stat page">Search Players/Teams</a></li>
			</ul>
			<li class="top">Captain Menu</li>
			<ul class="ulrt">
				<li class="sub"><a href="user_player_pickup.htm" title="Player Pickup List Page">Player Pickup List</a></li>
			</ul>
			<li class="top">Leagues</li>
					<?php
						$league_team_query = "SELECT leagues.id, leagues.league, team_list.team_name, team_list.league, team_list.id FROM leagues LEFT JOIN team_list ON (leagues.id = team_list.league)";
						//echo $league_team_query;
						$league_team_result = mysql_query($league_team_query,$GLOBALS['conn']);
						if(!$league_team_result){ // add this check.
							die('Invalid query: ' . mysql_error());
							exit;
						}
						echo '<ul class="ulrt">';
						$count = 0;
						$prev_row_league = '';
						while($row = mysql_fetch_array($league_team_result)){
							if($row[1]==$prev_row_league){
								if(isset($_GET['whichLeague'])&&($_GET['whichLeague']==$row[0])){
									echo '<li class="sub"><a href="user_main.htm?whichLeague=' . $row[0] . '&whichTeam=' . $row[4] . '">' . $row[2] . '</a></li>';
								}
							} else {
								if($count>0){
									echo '<li class="sub"><a href="user_add_team.htm?whichLeague=' . $_GET['whichLeague'] . '" title="Add a New Team to ' . $row[1] . ' League">Add a Team</a></li></ul>';
									$count=0;
								}
								echo '<li class="sub"><a href="user_main.htm?whichLeague=' . $row[0] . '">' . $row[1] . '</a>';
								if(isset($_GET['whichLeague'])&&($_GET['whichLeague']==$row[0])){
									echo '<ul class="ulrt"><li class="sub"><a href="user_main.htm?whichLeague=' . $row[0] . '&whichTeam=' . $row[4] . '">' . $row[2] . '</a></li>';
									$count++;
								}
							}
							$prev_row_league = $row[1];
						}
						if($count>0){
							echo '<li class="sub"><a href="user_add_team.htm?whichLeague=' . $_GET['whichLeague'] . '" title="Add a New Team to ' . $row[1] . ' League">Add a Team</a></li></ul>';
							$count=0;
						}
						echo '</ul>';
					if($_SESSION['admin']==1){
						echo '<li class="top">Admin Menu</li>
							<ul class="ulrt">
								<li class="sub"><a href="admin_update.htm?notification=update" title="Update notification">Update Notification</a></li>
								<li class="sub"><a href="user_main.htm?id=search&admin=make" title="Make admin">Make Admin</a></li>
							</ul>';
					}
				?>
				
			<li class="top"><a href="sd.php" title="Link to logout">Logout</a></li>
		</ul>
</div>