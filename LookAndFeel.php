<?php
function pagebegin($sTitle) {
  $aINI = parse_ini_file("LookAndFeel.ini");
  $chunks = explode("/", strtolower($_SERVER['PHP_SELF']));
  foreach ($chunks as $chunk) {
    if (substr($chunk, -4) == ".php") {
      $currPage = $chunk;
    }
  }
  /*$sNavList = '';
  $sFileName = 'NavList.txt';
  $aLines = file($sFileName);
  foreach ($aLines as $sEntry) { 
    list($PageUrl, $PageName) = explode('|', $sEntry);
    if ($currPage == strtolower($PageUrl)) {
      $sWork = "<tr><td bgcolor=\"$aINI[textbg]\"> <font color=\"$aINI[stripe]\"><b>$PageName</b></font></td></tr>";
    } else {
      $sWork = "<tr><td> <a href=\"$PageUrl\">$PageName</a></td></tr>";
    }
  $sNavList = $sNavList . $sWork;
  }*/
echo <<<top
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>$sTitle</title>
  <link rel="shortcut icon" href="icon.ico" />
  <script language="JavaScript1.2" src="verify.js"></script>
  <style type="text/css">
    <!--
    body {font-family:"$aINI[font]"; color:$aINI[fontcolor]}
    A:hover {background-color:$aINI[hoverBg]; color:$aINI[hoverFont]}
    -->
  </style>
  </head>
  <body background="$aINI[bgimage]" vlink="$aINI[vlink]" link="$aINI[link]">
  <div style="position: absolute; z-index:1">
  <table width="809" bgcolor="$aINI[stripe]" cellspacing="4">
    <tr>
      <td bgcolor="$aINI[navcolor]" valign="top" colspan="2">
	    <a href="index.php"><img src="$aINI[banner]" border=0 width="803" height="109"></a>
	  </td>
    </tr><tr>
      <td width="150" bgcolor="$aINI[navcolor]" valign="top">
        <table width="100%" cellpadding="4">
		  <tr><td >
top;

		  if($sTitle!="SWSA: Player Login"){
		  	include 'user_left_menu.php';
		  };

echo <<<middle
			</td></tr>
			<tr><td><img src="images/sp1.gif" border=0 width="140"></td></tr>
        </table>
      </td>
      <td valign="top" bgcolor="$aINI[textbg]">
        <table cellpadding="6" width="100%"><tr><td> 
        <font size="2"><i>$aINI[tagline]</i></font>
middle;
}

function pageend($sUpdated) {
  $aINI = parse_ini_file("LookAndFeel.ini");
  $year = date("Y");
echo <<<bottom
       <img src="images/footer.jpg" border=0 width="650" height="30">
    </td></tr></table>
    <td width="1" bgcolor="$aINI[stripe]" valign="top"></td>
  </td></tr></table>
  <hr>
  <font size="1" color="#ffffff">
  Copyright &copy; $year - All Rights Reserved<br>
  Updated $sUpdated</font>
  <br><font size="1" color="$aINI[bgcolor]">...website by <a href="http://www.oly-wa.us">Scott Bishop</a> and <a href="http://fossil-bug.com" target="new">Kerry Mraz</a>...</font>
  </div> 
  <div style="position: absolute; z-index:1"><img src="images/Logo2.gif">
  </div> 
</body>
</html>
bottom;
}

?>

