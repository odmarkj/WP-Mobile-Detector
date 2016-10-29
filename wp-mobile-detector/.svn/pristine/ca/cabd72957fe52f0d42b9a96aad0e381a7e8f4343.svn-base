<?php
if (isset($_REQUEST['src'])) {
	$path = dirname(__FILE__) . "/cache/" . basename($_REQUEST['src']);
	if(file_exists($path)){
	    //Set the content-type header as appropriate
	    $imageInfo = getimagesize($path);
	    switch ($imageInfo[2]) {
	        case IMAGETYPE_JPEG:
	            header("Content-Type: image/jpg");
	            break;
	        case IMAGETYPE_GIF:
	            header("Content-Type: image/gif");
	            break;
	        case IMAGETYPE_PNG:
	            header("Content-Type: image/png");
	            break;
	       default:
	            break;
	    }
	
	    // Set the content-length header
	    header('Content-Length: ' . filesize($path));
	
	    // Write the image bytes to the client
	    readfile($path);
	    exit();
	}else{
		$acceptable_extensions = ['png','gif','jpg','jpeg','jif','jfif','svg'];
		$info = pathinfo($_REQUEST['src']);
		// Check file extension
		if(in_array($info['extension'],$acceptable_extensions)){
			file_put_contents($path, file_get_contents($_REQUEST['src']));
			if(file_exists(dirname(__FILE__)."/libs/image/PHP5/easyphpthumbnail.class.php")){
				if (defined('PHP_MAJOR_VERSION') && PHP_MAJOR_VERSION >= 5){
					include_once(dirname(__FILE__)."/libs/image/PHP5/easyphpthumbnail.class.php");
				}else{
					include_once(dirname(__FILE__)."/libs/image/PHP4/easyphpthumbnail.class.php");
				}
			
				try{
					$thumb = new easyphpthumbnail;
					$thumb -> Thumbsize = ($_REQUEST['w'] > 0 && $_REQUEST['w'] <= 320 ? $_REQUEST['w'] : 320);
					echo $thumb -> Createthumb($path);
					exit();
				} catch (Exception $e) {
				    // $e->getMessage()
				}
			}
		}
	}
}

// Always return something
header('Content-Type: image/gif');
echo base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw==');
?>