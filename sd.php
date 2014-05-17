<?php session_start();//start the session

/*Created by Kerry Mraz for OLANpia.com
created 04/05/2012
modified 05/29/2013

Page Information:
This file will destroy the user's session effectively logging them out. They will then be redirected to the lansite page
*/

//Destroy the session. 
session_destroy();

//include server url string
include '../../../docs/server_url.php';

//redirect the user to the login page
header("Location: http://$server_url/user_login.php");
exit;
 

?>