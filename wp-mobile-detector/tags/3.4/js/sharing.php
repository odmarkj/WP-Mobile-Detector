<?php
header('Content-type: text/javascript');
$path = urldecode($_GET['path']);
$icons = urldecode($_GET['icons']);
$icons = explode(",", $icons);
foreach($icons as $k => $icon){
	if(strlen($icon) == 0){
		unset($icons[$k]);
	}
}
if(count($icons) > 0){
?>
(function(){
	var icons = <?php echo json_encode($icons); ?>;
	var bar = document.createElement('div');
	bar.setAttribute('class', 'wz_share_bar');
	bar.setAttribute('style', 'z-index: 200; position: fixed; bottom: 20px; left: 0px; width: 10px; height: 100px; background-color: #666666; color: #333333; opacity: .9; border-top-right-radius: 5px; border-bottom-right-radius: 5px;');
	bar.addEventListener('touchend', function(e){
		e.preventDefault();
		jMobile.select('.wz_share_bar').hide();
		jMobile.select('#wz_share').show();
	});
	bar.addEventListener('click', function(e){
		e.preventDefault();
		jMobile.select('.wz_share_bar').hide();
		jMobile.select('#wz_share').show();
	});
	var button = document.createElement('div');
	button.setAttribute('class','wz_share_bar');
	button.setAttribute('style','z-index: 201; position: fixed; bottom: 55px; left: 10px; width: 15px; height: 30px; background-color: #666666; color: #cccccc; opacity: .95; border-top-right-radius: 15px; border-bottom-right-radius: 15px;');
	button.addEventListener('click', function(e){
		e.preventDefault();
		jMobile.select('.wz_share_bar').hide();
		jMobile.select('#wz_share').show();
	});
	button.addEventListener('touchend', function(e){
		e.preventDefault();
		jMobile.select('.wz_share_bar').hide();
		jMobile.select('#wz_share').show();
	});
	var plus = document.createElement('div');
	plus.setAttribute('style','position: absolute; top: 2px; left: -4px; color: #cccccc; font-size: 22px; font-weight: bold;');
	plus.innerHTML = "+";
	bar.addEventListener('click', function(e){
		e.preventDefault();
		jMobile.select('.wz_share_bar').hide();
		jMobile.select('#wz_share').show();
	});
	bar.addEventListener('touchend', function(e){
		e.preventDefault();
		jMobile.select('.wz_share_bar').hide();
		jMobile.select('#wz_share').show();
	});
	button.appendChild(plus);
	var hbar = document.createElement('div');
	hbar.setAttribute('style', 'display: none; z-index: 202; position: fixed; bottom: 0px; left: 0px; right: 0px; height: 60px; color: #333333; text-align: center;');
	hbar.setAttribute('id', 'wz_share');
	for(var i in icons){
		var parts = icons[i].split(".");
		var a = document.createElement('a');
		a.href = "#";
		a.target = "_blank";
		a.setAttribute('onclick', 'jMobile.select(\'#wz_share\').hide(); jMobile.select(\'.wz_share_bar\').show(); return true;');
		if(parts[0] == "twitter"){
			a.href = "https://www.twitter.com/intent/tweet?url="+encodeURIComponent(document.location.href);
		}else if(parts[0] == "facebook"){
			a.href = "http://www.facebook.com/sharer.php?u="+encodeURIComponent(document.location.href)+"&t="+document.title;
		}else if(parts[0] == "delicious"){
			a.href = 'https://delicious.com/save?v=5&provider='+encodeURIComponent(document.domain)+'&noui&jump=close&url='+encodeURIComponent(location.href)+'&title='+encodeURIComponent(document.title);
		}else if(parts[0] == "google"){
			a.href = "https://plus.google.com/share?url="+encodeURIComponent(location.href);
		}else if(parts[0] == "linkedin"){
			a.href = "https://www.linkedin.com/shareArticle?mini=true&url="+encodeURIComponent(location.href)+"&title="+encodeURIComponent(document.title)+"&summary=&source=";
		}else if(parts[0] == "reddit"){
			a.href = "http://www.reddit.com/submit?url="+encodeURIComponent(location.href);+"&title="+encodeURIComponent(document.title);
		}else if(parts[0] == "stumbleupon"){
			a.href = "http://www.stumbleupon.com/submit?url="+encodeURIComponent(location.href);
		}else if(parts[0] == "email"){
			a.href = "mailto:?subject="+encodeURIComponent(document.title)+"&body="+encodeURIComponent(document.title)+encodeURIComponent('\n')+encodeURIComponent(location.href);
		}
		var icon = document.createElement('img');
		icon.setAttribute('data-id',parts[0]);
		icon.setAttribute('style','margin: 0px 5px;');
		icon.src = "<?php echo $path; ?>admin/images/32x32/"+icons[i];
		a.appendChild(icon);
		hbar.appendChild(a);
	}
	var close = document.createElement('a');
	close.setAttribute("href","#");
	close.setAttribute("onclick","jMobile.select('#wz_share').hide(); jMobile.select('.wz_share_bar').show(); return false;");
	var b_close = document.createElement('img');
	b_close.setAttribute("src","<?php echo $path; ?>admin/images/x.png");
	b_close.setAttribute('border','0');
	close.appendChild(b_close);
	hbar.appendChild(close);
	
	jMobile.select('body').get(0).appendChild(bar);
	jMobile.select('body').get(0).appendChild(button);
	jMobile.select('body').get(0).appendChild(hbar);
})();
<?php
}
?>