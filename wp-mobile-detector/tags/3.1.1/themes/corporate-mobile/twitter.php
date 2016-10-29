<?php
if(isset($_GET['twitter_username'])){
	$stuff = file_get_contents("http://twitter.com/statuses/user_timeline/".$_GET['twitter_username'].".json");
	echo $stuff;
}
?>