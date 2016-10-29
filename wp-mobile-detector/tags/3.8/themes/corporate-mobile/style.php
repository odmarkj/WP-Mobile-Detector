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
	"custom_color_light"=>"4f7498",
	"custom_color_medium_light"=>"abbdce",
	"custom_color_dark"=>"3c5975",
	"default_link_color"=>"556b00",
	"custom_post_background"=>"ffffff",
	"custom_header_logo"=>"f5f5f5"
);
$websitez_options = array_merge($websitez_default_options,$websitez_custom_options);
?>
*{
	padding: 0px;
	margin: 0px;
}

body {
	margin: 0;
	padding: 0;
	font-size: .9em;
	font-family: "<?php echo $websitez_options['general']['font_family'];?>", Helvetica, Arial, sans-serif;
	-webkit-text-size-adjust: none;
}

ul {
	margin: 0;
	padding: 0 20px 0 20px;
	list-style-type: circle;
	list-style-position: outside;
}

ol {
	margin: 0;
	padding: 0 25px 0 20px;
	list-style-type: decimal;
	list-style-position: outside;
}

li {
	margin-bottom: 5px;
	color: #555;
	list-style-type: disc;
	text-align: left;
	padding-bottom: 5px;
	font-size: 12px;
	margin-right: -15px;
	padding-top: 0;
}

h1, h2, h3, h4{
	padding: 5px 0px 10px;
}

p{
	padding: 5px 0px;
}

label{
	margin: 0px 0px 10px;
}

input {

}

code {
	font-family: Courier, "Courier New", mono;
	color: red;
}

blockquote {
	text-align: left;
	padding: 1px 10px 1px 15px;
	font-size: 90%;
	border-left: 2px solid #ccc;
	margin: 5px 15px;
}

.websitez-header{
	position: relative;
	text-align: center;
	margin: 0 auto;
}

.websitez-header-left{
	position: absolute;
	top: 0px;
	left: 0px;
	z-index: 100;
}

.websitez-header-right{
	position: absolute;
	top: 0px;
	right: 0px;
	z-index: 100;
}

.websitez-menu{

}

.websitez-menu-content{
	display: none;
}

.websitez-container{

}

.websitez-navigation{
	text-align: center;
	margin: 0 auto;
}

.websitez-footer{
	text-align: center;
	margin: 0 auto;
}

.websitez-footer-mobile{
	text-align: center;
	margin: 0 auto;
}

.rounded-corners{
	-moz-border-radius: 20px;
	-webkit-border-radius: 20px;
	-khtml-border-radius: 20px;
	border-radius: 20px;
}

.hidden{
	display: none;
}

/*
WordPress Required CSS
*/

.wp-caption, .wp-caption-text, .gallery-caption{
	font-size: .7em;
}

.aligncenter{
	text-align: center;
}

.alignleft{
	text-align: left;
}

.alignright{
	text-align: right;
}

/*
Start of Custom CSS
*/

body{
	background: url("<?php echo $websitez_options['images']['custom_website_background'];?>") repeat;
}

a:link, a:visited{
	color: #<?php echo $websitez_options['colors']['default_link_color'];?>;
	text-decoration: none;
}

.websitez-header{
	background: -moz-linear-gradient(bottom, #<?php echo $websitez_options['colors']['custom_color_dark'];?>, #<?php echo $websitez_options['colors']['custom_color_light'];?>);
	background: -webkit-gradient(linear, center bottom, center top, from(#<?php echo $websitez_options['colors']['custom_color_dark'];?>), to(#<?php echo $websitez_options['colors']['custom_color_light'];?>));
	-webkit-box-shadow:#333333 1px 1px 3px;
	border-bottom: 1px solid #000000;
	min-height: 42px;
}

.websitez-header-right{
	height: 100%;
	width: 40px;
	background: url("<?php echo $websitez_options['images']['header_left_icon'];?>") no-repeat;
	background-position: center center;
}

.websitez-header .logo{
	text-align: left;
	margin: 0px 40px 0px 10px;
	padding: 20px 0px;
	display: block;
	overflow: hidden;
	font-weight: bold;
	font-size: 1.4em;
	color: #<?php echo $websitez_options['colors']['custom_header_logo'];?>;
	text-shadow: #333333 1px 1px 1px;
}

.websitez-header-right{

}

.websitez-menu{
	<?php
	if(strlen($websitez_options['images']['custom_background_image']) > 0){
	?>
	background-image: url('<?php echo $websitez_options['images']['custom_background_image'];?>');
	<?php
	}else{
	?>
	background-image: url('http://www.decoratingideaslivingroom4u.com/images/Living%20Paint%20Colors.jpg');
	<?php
	}
	?>
	padding-top: 130px;
	background-repeat: no-repeat;
}

.websitez-view{
	background: none repeat scroll 0 0 rgba(0, 0, 0, 0.7);
	padding:  5px 10px;
	margin: 0;
	z-index: 500;
	color: #ffffff;
}

.websitez-view h3{
	margin: 0;
	padding: 0;
}

.websitez-view p{
	font-size: 13px;
	line-height: 18px;
}

.websitez-menu-previous{
	float: left;
	position: relative;
	width: 40px;
	background-color: #000000;
	background-position: center center;
	background-image: url('images/left-arrow-black.jpg');
	background-repeat: no-repeat;
}

.websitez-menu-count{
	background-color: #000000;
	margin: 0px 40px;
	color: #ffffff;
	text-align: center;
}

.websitez-menu-count-content{
	padding-top: 8px;
}

a.websitez-page-links:link { padding: 0px 5px; font: bold 16px Georgia; color: #cccccc; font-style:italic; }
a.websitez-page-links:active { padding: 0px 5px; font: bold 16px Georgia; color: #cccccc; font-style:italic; }
a.websitez-page-links:visited { padding: 0px 5px; font: bold 16px Georgia; color: #cccccc; font-style:italic; }
a.websitez-page-links:hover { padding: 0px 5px; font: bold 16px Georgia; color: #ffffff; text-decoration: underline; font-style:italic; }

a.websitez-page-links-active:link { padding: 0px 5px; font: bold 16px Georgia; color: #ffffff; text-decoration: underline; font-style:italic; }
a.websitez-page-links-active:active { padding: 0px 5px; font: bold 16px Georgia; color: #ffffff; text-decoration: underline; font-style:italic; }
a.websitez-page-links-active:visited { padding: 0px 5px; font: bold 16px Georgia; color: #ffffff; text-decoration: underline; font-style:italic; }
a.websitez-page-links-active:hover { padding: 0px 5px; font: bold 16px Georgia; color: #ffffff; text-decoration: underline; font-style:italic; }

.websitez-menu-height{
	height: 40px;
}

.websitez-menu-next{
	float: right;
	position: relative;
	width: 40px;
	background-color: #000000;
	background-position: center center;
	background-image: url('images/right-arrow-black.jpg');
	background-repeat: no-repeat;
}

.websitez-nav-button li{
	display: inline;
}
.websitez-nav-button li.current a{
	background: url("img/slider_nav_dots.png") no-repeat scroll -10px top transparent;
	height: 20px;
	top: 2px;
	width: 25px;
}

.websitez-nav-button li a{
	background: url("img/slider_nav_dots.png") no-repeat scroll left top transparent;
	height: 15px;
	width: 10px;
}

.websitez-menu-content{
	text-align: center;
	margin: 0 auto;
}

.websitez-menu-button{
	text-align: center;
	padding: 5px 0px 3px;
}

.websitez-container{
	padding: 0px;
}

.websitez-container h4{
	text-align: center;
	color: #333333;
	padding: 0px 0px 10px;
}

.websitez-container .post{
	<?php if(strlen($websitez_options['colors']['custom_post_background']) > 0){ ?>
	background: #<?php echo $websitez_options['colors']['custom_post_background'];?>;
	<?php } ?>
	margin-bottom: 0px;
	border-bottom: 2px solid #666666;
	/*border-top: 1px solid #cccccc;*/
}

.websitez-container .post-wrapper{
	padding: 10px 10px 5px;
}

.websitez-container .post-title{
	color: #<?php echo $websitez_options['colors']['custom_color_dark'];?>;
	font-size: 1.3em;
	font-weight: bold;
	word-wrap: break-word;
}

.websitez-container .post-author{
	color: #000000;
	font-size: .8em;
}

.websitez-container .post-tags{
	color: #000000;
	font-size: .8em;
	margin-bottom: 5px;
}

.websitez-container .post-more{
	color: #000000;
	font-size: .7em;
	<?php if(strlen($websitez_options['colors']['custom_color_medium_light']) > 0){ ?>
	background-color: #<?php echo $websitez_options['colors']['custom_color_medium_light'];?>;
	<?php } ?>
}

.websitez-container .post-view-more{
	text-align: right;
	padding: 5px 5px;
	float: right;
	width: 60%;
}

.websitez-container .post-read-more{
	text-align: left;
	padding: 3px 5px;
	float: left;
	width: 30%;
	text-transform: uppercase;
	color: #333333;
}

.calendar{
	text-align:center;
	position:relative;
	margin-bottom:5px;
	margin-right:10px;
	margin-top:0;
	border:1px solid #<?php echo $websitez_options['colors']['custom_color_dark'];?>;
	top:0px;
	float:left;
	-webkit-border-top-left-radius:9px;
	-webkit-border-top-right-radius:0px;
	-webkit-border-bottom-left-radius:0px;
	-webkit-border-bottom-right-radius:9px;
	-webkit-box-shadow:#999999 1px 1px 4px;
}
.calendar-month{
	font-size:11px;
	font-weight:bold;
	color:#fff;
	letter-spacing:0;
	text-shadow:#000000 1px 1px 0px;
	text-transform:uppercase;
	padding:3px 10px;
	-webkit-border-top-left-radius:7px;
	-webkit-border-top-right-radius:0px;
	background-color: #<?php echo $websitez_options['colors']['custom_color_dark'];?>;
}
.calendar-day{
	color:#111;
	background-color:#e9e9e9;
	text-shadow:#ffffff 1px 1px 1px;
	-webkit-border-bottom-left-radius:0px;
	-webkit-border-bottom-right-radius:9px;
	font:bold 18px "Arial Rounded MT Bold", Helvetica, Geneva, sans-serif;
	padding:2px 0 3px;
	text-align:center;
}

.websitez-navigation a{
	font-size: 1.3em;
	color: #333333;
	font-weight: bold;
}

.websitez-footer{
	padding: 5px 0px;
	font-size: .7em;
}

/*
Search
*/

.websitez-search{
	padding: 10px 3px 10px;
	<?php if(strlen($websitez_options['colors']['custom_post_background']) > 0){ ?>
	background: #<?php echo $websitez_options['colors']['custom_post_background'];?>;
	<?php } ?>
	text-align: center;
}

.websitez-search h3{
	margin: 0;
	font-size: 14px;
	padding: 2px 0px;
	color: #333333;
	text-align: center;
	float: left;
}

.websitez-search-input{
	width: 85%;
	margin: 0 auto;
	text-align: left;
	padding: 4px 0px 4px 25px;
	font-size: .8em;
	-moz-border-radius: 10px;
	-webkit-border-radius: 10px;
	-khtml-border-radius: 10px;
	border-radius: 10px;
	background-image: url('images/search_20.png');
	background-repeat: no-repeat;
	background-position: 3px center;
	border: 1px solid #<?php echo $websitez_options['colors']['custom_color_dark'];?>;
}

/*
Social Media
*/

#twitter_feed{
	background-color: #ffffff;
}

#twitter_feed ul{
	list-style: none;
	margin: 0;
	padding: 0;
}

#twitter_feed ul li{
	margin: 0;
	padding: 10px;
	background: -moz-linear-gradient(bottom, #<?php echo $websitez_options['colors']['custom_color_dark'];?>, #<?php echo $websitez_options['colors']['custom_color_light'];?>);
	background: -webkit-gradient(linear, center bottom, center top, from(#<?php echo $websitez_options['colors']['custom_color_dark'];?>), to(#<?php echo $websitez_options['colors']['custom_color_light'];?>));
	line-height: 19px;
}

#twitter_feed ul li a{
	color: #<?php echo $websitez_options['colors']['custom_header_logo'];?>;
	font-size: 12px;
}

/*
Sidebar
*/

.websitez-sidebar h3{
	margin: 0;
	padding: 5px 0px 0px;
	color: #333333;
}

.websitez-sidebar ul{
	margin: 0;
	padding: 0;
}

.websitez-sidebar ul li{
	-moz-border-radius: 10px;
	-webkit-border-radius: 10px;
	-khtml-border-radius: 10px;
	border-radius: 10px;
	margin: 4px 0px;
	background-color: #ffffff;
	padding: 5px 8px;
	list-style: none;
	-webkit-box-shadow:#333333 1px 1px 3px;
	font-size: 1.0em;
	border: 1px solid #999999;
}

/*
Comments
*/

input[type=submit] {
	color: #333;
}
#respond {
	margin: 10px 0;
	overflow: hidden;
	position: relative;
}
#respond p {
	margin: 0;
}
#respond .comment-notes {
	margin-bottom: 1em;
}
.form-allowed-tags {
	line-height: 1em;
}
.children #respond {
	margin: 0 48px 0 0;
}
h3#reply-title {
	margin: 18px 0 10px;
}
#comments-list #respond {
	margin: 0 0 18px 0;
}
#comments-list ul #respond {
	margin: 0;
}
#cancel-comment-reply-link {
	font-size: 12px;
	font-weight: normal;
	line-height: 18px;
}
#respond .required {
	color: #ff4b33;
	font-weight: bold;
}
#respond label {
	color: #888;
	font-size: 1.1em;
}
#respond input {
	margin: 0 0 9px;
	width: 98%;
	font-size: 1.1em;
}
#respond textarea {
	width: 98%;
	font-size: 1.1em;
}
#respond .form-allowed-tags {
	color: #888;
	font-size: 12px;
	line-height: 18px;
}
#respond .form-allowed-tags code {
	font-size: 11px;
}
#respond .form-submit {
	margin: 12px 0;
}
#respond .form-submit input {
	font-size: 14px;
	width: auto;
}

.websitez-comments{
	margin-bottom: 10px;
	padding: 10px;
	background-color: #f1f1f1;
	-moz-border-radius: 10px;
	-webkit-border-radius: 10px;
	-khtml-border-radius: 10px;
	border-radius: 10px;
}

.websitez-comments-p{
	padding: 0px 0px 10px;
}

.websitez-comments-author{
	float: left;
	position: relative;
	text-align: center;
	margin-right: 15px;
}

.websitez-comments-author-link{
	text-align: center;
	padding: 5px 0px 10px;
}

.websitez-comments-text{
	padding-top: 0px;
	margin-top: 0px;
}

.websitez-comments-reply{
	padding-top: 10px;
	text-align: right;
}

.websitez-comments-awaiting-moderation{
	font-weight: bold;
	color: #ff0000;
	padding-bottom: 10px;
}