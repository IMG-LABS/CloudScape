<?php
require_once "$_SERVER[DOCUMENT_ROOT]/PHP_Code/__autoload.php";
/**
 * 
 * @author Vishnu T Suresh
 *
 */
class ThisPage{
	public static function getUser(){
		$token_no=isset($_COOKIE["token_no"])?$_COOKIE["token_no"]:NULL;
		$uuid=isset($_COOKIE["uuid"])?$_COOKIE["uuid"]:NULL;
		if(isset($token_no)){
			$mysql=MySQL::getInstance();
			$sql=new mysqli($mysql->domain, $mysql->username, $mysql->password,$mysql->database, $mysql->port);
			if (mysqli_connect_errno()) {
				printf("Connect failed: %s\n", mysqli_connect_error());
				exit();
			}
			$query="SELECT user_id FROM login WHERE token_no='$token_no' AND uuid='$uuid' AND expiry_time >'".date("Y-m-d H:i:s")."' ORDER BY token_no DESC LIMIT 1";
			$result=$sql->query($query) or die($sql->error.__LINE__);
			if($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					return User::withUserId($row["user_id"]);
				}
			}
			else {
				return NULL;
			}
		}
	}
	public static function renderTop($title){
		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title>ISW &rsaquo; <?php echo $title?></title>
		
		<link rel="stylesheet" href="/resources/style.css" type="text/css" media="screen, projection"/>
		<link type="text/css" href="/resources/jquery.jscrollpane.css" rel="stylesheet" media="all" />
		<link rel="stylesheet" href="/resources/style.layout.css" type="text/css" media="screen, projection"/>
		
		<script type="text/javascript" src="/resources/script/jquery-1.9.1.min.js"></script>
		<script type="text/javascript" src="/resources/script/jquery.mousewheel.js"></script>
		<script type="text/javascript" src="/resources/script/mwheelIntent.js"></script>
		<script type="text/javascript" src="/resources/script/jquery.jscrollpane.min.js"></script>
		<script type="text/javascript" src="/resources/script/init.js"></script>
 		<script type="text/javascript" src="/resources/dynamiclayout.js"></script>
		
		</head>
		<body>
		<div id="wrapper">
		<div id="header">
        <div id="logo"><a href="/Home">Information Services Website</a></div>
        <?php $user=ThisPage::getUser();
        if($user){
        ?>
        <div class="dropdown">
		<a class="account" ><?php 
        echo $user->getFirstName()." ";
        echo $user->getLastName();
        ?></a>
		
		<div class="submenu">
		<ul class="root">
		<li ><a href=/Profiles">Profiles</a></li>
      	<li ><a href=/Preferences">Preferences</a></li>
      	<li ><a href=/RegistrationDetails">Registration Details</a></li>
      	<li ><a href=/ChangePassword">Change Password</a></li>
      	<li ><a href="/Authentication/Logout">Logout</a></li>
		</ul>
		</div>
		
		</div>
        <?php }?>
		</div><!-- #header-->
		<div id="middle">
		<div id="container">
		<div id="content">	
		<?php 
	}
	public static function renderBottom(){
		?>
		</div><!-- #content-->
		</div><!-- #container-->
		<div class="sidebar scroll-pane" id="sideLeft">
		<?php 
		$layout=simplexml_load_file("$_SERVER[DOCUMENT_ROOT]/resources/layout.xml");
		foreach($layout->leftbar->group as $group ){
		?>
		<div class="groupwrapper">
		<div class="group"><?php echo $group["name"]?></div>
		<?php 
			foreach ($group->entry as $entry){
				$credentials=array_unique(array_map('intval', explode(',', $entry["credentials"])));
				$display=FALSE;
				$user=NULL;
				$user_is_loggedin=!is_null($user=ThisPage::getUser());
				$link_is_public=in_array(1,$credentials);
				$link_requires_login=in_array(2,$credentials);
				$user_has_credential=(!is_null($user))?$user->hasCredential($credentials):FALSE;
				if($link_is_public){
					$display=TRUE;
				}
				else if($link_requires_login&&$user_is_loggedin){
					$display=TRUE;
				}
				else if($user_has_credential){
					$display=TRUE;
				}
				if($display==TRUE){
					?>
					<div class="entry"><a href="<?php echo $entry["url"]?>" ><span class="text"><?php echo $entry?></span></a></div>
					<?php 
				}
			}
		?>
		</div>
		<?php
		}
		?>
		</div><!-- .sidebar#sideLeft -->
		<div class="sidebar" id="sideRight">
		<div id="AppBar" class="scroll-pane">
		<div id="AppBarTitle">Application Bar</div>
		</div>
		
		</div><!-- .sidebar#sideRight -->
	
		</div><!-- #middle-->
	
		
	
		</div><!-- #wrapper -->
		</body>
		</html>
		<?php 
	}
}?>