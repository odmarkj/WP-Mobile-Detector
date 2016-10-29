<?php
header('Content-type: text/css');
header("Cache-Control: must-revalidate");
$offset = 72000 ;
$ExpStr = "Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
header($ExpStr);
$websitez_custom_options = [];
try{
	$filePath = urldecode($_REQUEST['path']);
	$contents = '';
	$handle = fopen($filePath, "r");
	if (FALSE !== $handle) {
		while (!feof($handle)) {
		    $contents .= fread($handle, 8192);
		}
	}
	
	fclose($handle);
	if(!empty($contents)) {
	    $json = json_decode($contents, true);
	    if($json && is_array($json)){
		    $websitez_custom_options = $json;
	    }
	}
} catch (Exception $e) {
    // $e->getMessage()
}
//Default options
$websitez_default_options['general'] = array(
	"font_family" => "Helvetica Neue"
);
$websitez_default_options['colors'] = array(
	"custom_color_light"=>"afbbcb",
	"custom_color_medium_light"=>"abbdce",
	"custom_color_dark"=>"6e86a4",
	"default_link_color"=>"556b00",
	"custom_post_background"=>"ffffff",
	"custom_header_logo"=>"f5f5f5"
);
$websitez_options = array_merge($websitez_default_options,$websitez_custom_options);
?>
body{
	background: url("<?php echo $websitez_options['images']['custom_website_background'];?>") repeat;
	font-family: "<?php echo $websitez_options['general']['font_family'];?>", Helvetica, Arial, sans-serif;
}
a:link, a:visited{
	color: #<?php echo $websitez_options['colors']['default_link_color'];?>;
	text-decoration: none;
}
/*
Start Header
*/

#header{
	z-index: 50;
	min-height: 42px;
	background-color: #ccc;
	position: fixed;
	top: 0px;
	left: 0px;
	right: 0px;
	width: 100%;
	line-height: 37px;
  	border-bottom: 1px solid #22374a;
  	text-align: center;
  	background: -moz-linear-gradient(bottom, #<?php echo $websitez_options['colors']['custom_color_dark'];?>, #<?php echo $websitez_options['colors']['custom_color_light'];?>);
	background: -webkit-gradient(linear, center bottom, center top, from(#<?php echo $websitez_options['colors']['custom_color_dark'];?>), to(#<?php echo $websitez_options['colors']['custom_color_light'];?>));
}

#header .brand{
	margin: 0 50px;
	text-align: center;
	font-size: 20px;
	text-shadow: black 1px 1px 1px;
	font-weight: bold;
	font-family: Georgia;
	display: block;
	color: #<?php echo $websitez_options['colors']['custom_header_logo'];?>;
}

#header .brand a{
	color: #<?php echo $websitez_options['colors']['custom_header_logo'];?>;
}

#header .logo{
	padding: 6px 0px;
	display: block;
	overflow: hidden;
}

#header .lMenu{
	float: left;
}

#header .lMenu .bar{ 
  margin: 6px 0 0 12px;
  height: 26px;
  width: 26px;
  line-height: 25px;
  border: 1px solid rgba(0,0,0, 0.4);
  -webkit-border-radius: 5px;
  /*background: -webkit-gradient(linear, left top, left bottom, from(#9fb3cc), to(#5b80ab), color-stop(0.5, #6b8bb2), color-stop(0.51, #597eaa));*/
  	background: -moz-linear-gradient(bottom, #<?php echo $websitez_options['colors']['custom_color_light'];?>, #<?php echo $websitez_options['colors']['custom_color_dark'];?>);
	background: -webkit-gradient(linear, center bottom, center top, from(#<?php echo $websitez_options['colors']['custom_color_light'];?>), to(#<?php echo $websitez_options['colors']['custom_color_dark'];?>));
  -webkit-box-shadow: 0 1px 0 rgba(255,255,255, 0.25), inset 0 1px 1px rgba(0,0,0, 0.2);
}

#header .lMenu .bar i{ 
  margin: 5px auto 0px;
}

#header .rMenu{
	float: right;
}

#header .rMenu .bar{ 
  margin: 6px 12px 0 0;
  height: 26px;
  width: 26px;
  line-height: 25px;
  border: 1px solid rgba(0,0,0, 0.4);
  -webkit-border-radius: 5px;
  /*background: -webkit-gradient(linear, left top, left bottom, from(#9fb3cc), to(#5b80ab), color-stop(0.5, #6b8bb2), color-stop(0.51, #597eaa));*/
  background: -moz-linear-gradient(bottom, #<?php echo $websitez_options['colors']['custom_color_light'];?>, #<?php echo $websitez_options['colors']['custom_color_dark'];?>);
	background: -webkit-gradient(linear, center bottom, center top, from(#<?php echo $websitez_options['colors']['custom_color_light'];?>), to(#<?php echo $websitez_options['colors']['custom_color_dark'];?>));
  -webkit-box-shadow: 0 1px 0 rgba(255,255,255, 0.25), inset 0 1px 1px rgba(0,0,0, 0.2);
}

#header .rMenu .bar i{
  margin: 5px auto 0px;
}

/*
END HEADER
*/

/*
START CONTENT
*/

#content{
	z-index: 40;
	background-color: #fff;
	position: absolute;
	top: 0px;
	left: 0px;
	right: 0px;
	width: 100%;
	border-left: 1px solid #000;
}

#content #title{
	text-transform:uppercase;
	font-size: 14px;
	text-shadow: black 1px 1px 1px;
	color: #<?php echo $websitez_options['colors']['custom_color_dark'];?>;
	font-weight: bold;
	background-color: #333;
	padding: 5px;
	text-align: center;
	margin: 0px;
}

#content.full{
	margin-top: 0px;
	margin-bottom: 0px;
}

#content.part{
	margin-top: 42px;
}

/*
END CONTENT
*/

/*
START POSTS
*/

#posts_container{
	
}

#posts_container a{
	text-decoration: none;
}

#posts_container .the_post{
	/*background-color: #666;*/
	background-image:-webkit-gradient(linear, left top, left bottom, from(#585858), to(#555));
	position: relative;
}

#posts_container .no_image{
	border-top: 1px solid #767676;
	border-bottom: 1px solid #333;
}

#posts_container .the_post .content{
	padding: 10px;
}

#posts_container .the_post .image{
	position: absolute;
	display: inline-block;
	right: 0px;
	left: 0px;
	bottom: 0px;
}

#posts_container .the_post .content h2{
	margin: 0px 50px 5px 0px;
	padding: 0px;
	font-size: 18px;
	text-shadow: black 1px 1px 1px;
	color: #<?php echo $websitez_options['colors']['custom_color_dark'];?>;
	line-height: 25px;
}

#posts_container .the_post .content p{
	margin: 0px;
	padding: 0px;
}

#posts_container .the_post .content div.meta{
	text-transform:uppercase;
	font-size: 12px;
	text-shadow: black 1px 1px 1px;
	color: #fff;
	margin-right: 25px;
}

#posts_container .the_post .content div.comments{
	float: right;
	font-size: 12px;
	text-shadow: black 1px 1px 1px;
	color: #fff;
	font-weight: bold;
}

/*
END POSTS
*/