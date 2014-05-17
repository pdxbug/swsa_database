<?php  session_start(); //start the session 

/* Created by Kerry Mraz for the user of LAN party event organization and registration
Created 3/30/2012
Modified 4/23/2014

Contact kerry@fossil-bug.com

Page Information
This will be the landing page for anyone that wishes to login to the player site area. Anyone that access pages further down the tree will be redirected to here and provided an error and a chance to login.*/ 

include('LookAndFeel.php');
pagebegin('SWSA: Player Login');
echo '<link href="css/style.css" rel="stylesheet" type="text/css">';

echo "
<!-- Java Script -->
<!-- This javascript is to keep the login form labels inside the form fields for compactness. It was located at: http://www.alistapart.com/articles/makingcompactformsmoreaccessible -->

<script type='text/javascript'>

function initOverLabels () {
  if (!document.getElementById) return;  	
 
  var labels, id, field;
 
  // Set focus and blur handlers to hide and show 
  // LABELs with 'overlabel' class names.
  labels = document.getElementsByTagName('label');
  for (var i = 0; i < labels.length; i++) {
	
    if (labels[i].className == 'overlabel') {
 
      // Skip labels that do not have a named association
      // with another field.
      id = labels[i].htmlFor || labels[i].getAttribute('for');
      if (!id || !(field = document.getElementById(id))) {
        continue;
      }
 
      // Change the applied class to hover the label 
      // over the form field.
      labels[i].className = 'overlabel-apply';
 
      // Hide any fields having an initial value.
      if (field.value !== '') {
        hideLabel(field.getAttribute('id'), true);
      }
 
      // Set handlers to show and hide labels.
      field.onfocus = function () {
        hideLabel(this.getAttribute('id'), true);
      };
      field.onblur = function () {
        if (this.value === '') {
          hideLabel(this.getAttribute('id'), false);
        }
      };
 
      // Handle clicks to LABEL elements (for Safari).
      labels[i].onclick = function () {
        var id, field;
        id = this.getAttribute('for');
        if (id && (field = document.getElementById(id))) {
          field.focus();
        }
      };
 
    }
  }
};

function hideLabel (field_id, hide) {
  var field_for;
  var labels = document.getElementsByTagName('label');
  for (var i = 0; i < labels.length; i++) {
    field_for = labels[i].htmlFor || labels[i].getAttribute('for');
    if (field_for == field_id) {
      labels[i].style.textIndent = (hide) ? '-2000px' : '0px';
      return true;
    }
  }
}
 
window.onload = function () {
  setTimeout(initOverLabels, 50);
};
</script>
";

echo'
</head>

<body>
	<div id="wrapper">
		<h3>Southwest Washington Soccer Association</h3> 
		<div id="user_login">
			
		<!-This is the form for logging in registered users ---------------------------------------------------------------------------/>	
					<form action="user_auth.php" name="login" method="post">
						<table align="center">
							<tr><td align="center">
								<div id="fname">        
									<label accesskey="L" for="fname-field" class="overlabel">First Name:</label>
									<input name="fname" type="text" id="fname-field" size="15" value="" tabindex="1" />
								</div>
							</td></tr>
							<tr><td align="center">
								<div id="lname">        
									<label for="lname-field" class="overlabel">Last Name:</label>
									<input name="lname" type="text" id="lname-field" size="15" value="" tabindex="1" />
								</div>
							</td></tr>
							<tr><td align="center">
								<div id="password">    
									<label for="password-field" class="overlabel">SWSA Card ID Number:</label>
									<input name="password" type="text" id="password-field" size="15" maxlength="9" value="" tabindex="2"/>
								</div>
							</td></tr>
							<tr><td style="text-align:center;">
								<input name="Submit" type="submit" value="Login" tabindex="3" />
							</td></tr>
							<tr><td>';
							//provide the error codes for login failures or deletion of accounts
							if(isset($_SESSION['login_fail']))
							{
								if($_SESSION['login_fail'] == 1){
									echo'<font color=red>This player name does not exist.</font>';
									$_SESSION['login_fail'] = 0;//reset the login error code
								}
								if($_SESSION['login_fail'] == 2){
									echo"<font color=red>Card ID does not match the account on file.</font>";
									//echo "<br />" . $_SESSION['update_log'];//provide information about what is being logged
									$_SESSION['login_fail'] = 0;//reset the login error code
								}
								if($_SESSION['login_fail'] == 5){
									echo'<font color=red>You are either not logged in or do not have sufficient privileges to access page "' . $_GET['page'] . '". Please login and try again. If the error persists, please send this error message to kerry@fossil-bug.com</font>';
									$_SESSION['login_fail'] = 0;//reset the login error code
								}
							} else {
								$_SESSION['login_fail'] = 0;
							}
							echo'</td></tr>
							<tr><td style="text-align:center;">
								<p><a href="user_forgot_password.htm" title="Link to forgot password or username page">Forgot Card ID?</a><br />
				  				Don\'t have an account? <a href="user_register.htm?registration_state=0">Register for an account now.</a></p>
							</td></tr>
						</table>
					</form>
					<br />';
			
	echo'	  </div><!-- This closes the member_login div -->';
	pageend('2014/04/24 14:35:14'); 
	echo '</div><!-- This closes the wrapper div -->

</body>
</html>';

?>
