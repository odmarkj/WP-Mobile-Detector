var wz_mobile = {
	gnStartX: 0,
	gnStartY: 0,
	gnEndX: 0,
	gnEndY: 0,
	gnEndOffsetY: 0,
	validMove: false,
	currentWidth: 0,
	startX: null,
	startY: null,
	offsetY: 0,
	dx: null,
	direction: null,
	el: null,
	listener: null,
	tStart: function(event){
		wz_mobile.gnStartX = event.touches[0].pageX;
		wz_mobile.gnStartY = event.touches[0].pageY;
	},
	tMove: function(event){
		var bottom = document.body.scrollHeight - window.innerHeight;
		if(window.pageYOffset < wz_mobile.gnEndOffsetY && bottom != window.pageYOffset){
  			if(!jMobile.select('.ghost_bar').is(':visible')){
  				jMobile.select('.ghost_bar').fadeIn('fast');
				jMobile.select('#content').removeClass('full').addClass('part');
  			}
  		}else if(window.pageYOffset > wz_mobile.gnEndOffsetY){
  			if(jMobile.select('.ghost_bar').is(':visible')){
  				jMobile.select('.ghost_bar').fadeOut('fast');
				jMobile.select('#content').removeClass('part').addClass('full');
  			}
  		}
		
		wz_mobile.gnEndOffsetY = window.pageYOffset;
		wz_mobile.gnEndX = event.touches[0].pageX;
		wz_mobile.gnEndY = event.touches[0].pageY;
	},
	tEnd: function(event){

	},
	tScroll: function(){
		if(window.pageYOffset == 0){
			if(!jMobile.select('.ghost_bar').is(':visible')){
  				jMobile.select('.ghost_bar').fadeIn('fast');
				jMobile.select('#content').removeClass('full').addClass('part');
  			}
		}
	},
	open_left_menu: function(){
		if(jMobile.select('#header').hasClass('left_menu_open')){
			jMobile.select('#header').removeClass('left_menu_open').removeClass('shadow');
			jMobile.select('#content').removeClass('left_menu_open').removeClass('shadow');
			jMobile.select('#lbMenu').hide();
		}else{
			jMobile.select('#lbMenu').show();
			jMobile.select('#header').addClass('left_menu_open').addClass('shadow');
			jMobile.select('#content').addClass('left_menu_open').addClass('shadow');
		}
	},
	open_right_menu: function(){
		if(jMobile.select('#header').hasClass('right_menu_open')){
			jMobile.select('#header').removeClass('right_menu_open').removeClass('shadow');
			jMobile.select('#content').removeClass('right_menu_open').removeClass('shadow');
			jMobile.select('#rbMenu').hide();
			jMobile.select('#rbMenu-click').hide();
		}else{
			jMobile.select('#rbMenu').show();
			jMobile.select('#header').addClass('right_menu_open').addClass('shadow');
			jMobile.select('#content').addClass('right_menu_open').addClass('shadow');
			jMobile.select('#rbMenu-click').show();
			var width = (window.innerWidth > 0) ? window.innerWidth : screen.width;
			var targetWidth = (width > 270 ? (width - 270) : 0);
			jMobile.select('#rbMenu-click').attr('style','width: '+targetWidth+'px;');
		}
	},
	show_excerpt: function(elem){
		jMobile.select(elem).find('.excerpt').show();
		return;
		var link = jMobile.select(elem).attr('data-link');
		var click = jMobile.select(elem).attr('data-clicks');
		if(click == "0"){
			jMobile.select(elem).attr('data-clicks','1');
		}else if(click == "1"){
			// Catching end event
			jMobile.select(elem).attr('data-clicks','2').attr('onclick','window.location = \''+link+'\'');
			jMobile.select(elem).find('.excerpt').show();
		}else if(click == "2"){
			window.location = link;
			return false;
		}
	},
	slider: function(direction, elem){
		var index = 1;
		var i = 1;
		var elems = jMobile.select(elem).find('div');
		elems.each(function(){
			if(jMobile.select(this).is(":visible")){
				index = i;
			}
			i++;
		});
		if(direction == "left"){
			if(index == 1){
				index = elems.length;
			}else{
				index--;
			}
		}else if(direction == "right"){
			if(index == elems.length){
				index = 1;
			}else{
				index++;
			}
		}
		i = 1;
		elems.each(function(){
			if(i == index){
				jMobile.select(this).show();
			}else{
				jMobile.select(this).hide();
			}
			i++;
		});
	}
}

window.addEventListener('touchstart',function(event) {
  wz_mobile.tStart(event);
},false);

window.addEventListener('touchmove',function(event) {
  wz_mobile.tMove(event);
},false);

window.addEventListener('touchend',function(event) {
	wz_mobile.tEnd(event);
},false);

window.addEventListener('scroll', function(){
	wz_mobile.tScroll();
},false);

/* Fixes for iOS from jQuery Mobile */

//window.scrollTo( 0, 1 );

//var body = jMobile.select('body').html();
//var html = "<div id='wrapper' data-role='page' style='display: none;'>"+body+"</div>";
//jMobile.select('body').html(html);

var swipe = false;
window.addEventListener("load",function() {
	setTimeout( function() {
		//window.scrollTo( 0, 1 );
		//jMobile.select('#loading').hide();
		//jMobile.select('#contain_all').show();
		//jMobile.select('#wrapper').show();
	}, 20 );
	jMobile.select('.slider').each(function(){
		detectswipe(jMobile.select(this).get(0), function(elem, direction){ wz_mobile.slider(direction,elem); });
	});
	jMobile.select('#header').get(0).addEventListener('touchstart', function(e){
		e.preventDefault();
		wz_mobile.open_right_menu();
	});
	jMobile.select('#header').get(0).addEventListener('click', function(e){
		e.preventDefault();
		wz_mobile.open_right_menu();
	});
	jMobile.select('.slider a.left').each(function(){ 
		this.addEventListener('touchstart', function(e){
			e.preventDefault();
			wz_mobile.slider('left', jMobile.select(this).parent().get(0)); return false;
		});
		this.addEventListener('click', function(e){
			e.preventDefault();
			wz_mobile.slider('left', jMobile.select(this).parent().get(0)); return false;
		});
	});
	jMobile.select('.slider a.right').each(function(){ 
		this.addEventListener('touchstart', function(e){
			e.preventDefault();
			wz_mobile.slider('right', jMobile.select(this).parent().get(0)); return false;
		});
		this.addEventListener('click', function(e){
			e.preventDefault();
			wz_mobile.slider('right', jMobile.select(this).parent().get(0)); return false;
		});
	});
	jMobile.select('.content').each(function(){
		jMobile.select(this).get(0).addEventListener('click', function(e){
			wz_mobile.show_excerpt(this);
		});
		jMobile.select(this).get(0).addEventListener('touchstart', function(e){
		});
		jMobile.select(this).get(0).addEventListener('touchmove', function(e){
			swipe = true;
		});
		jMobile.select(this).get(0).addEventListener('touchend', function(e){
			if(swipe != true){
				e.preventDefault();
				wz_mobile.show_excerpt(this);
			}
			swipe = false;
		});
	});
	jMobile.select('.content').each(function(){
		detectswipe(jMobile.select(this).get(0),function(ele,direction){
			if(direction == "left"){
				jMobile.select('#loading').show();
				jMobile.select('#contain_all').hide();
		        var link = jMobile.select(ele).attr("data-link");
		        window.location = link;
			}
		});
	});
	jMobile.select('#individual_post').each(function(){
		detectswipe(jMobile.select(this).get(0),function(ele,direction){
			if(direction == "right"){
				jMobile.select('#loading').show();
				jMobile.select('#contain_all').hide();
				history.go(-1);
			}
		});
	});
	jMobile.select('#rbMenu-click').each(function(){ 
		this.addEventListener('touchstart', function(e){
			e.preventDefault();
			wz_mobile.open_right_menu();
		});
	});
	jMobile.select('#rbMenu-click').each(function(){ 
		this.addEventListener('click', function(e){
			e.preventDefault();
			wz_mobile.open_right_menu();
		});
	});
});

function detectswipe(ele,func) {
  swipe_det = new Object();
  swipe_det.sX = 0;
  swipe_det.sY = 0;
  swipe_det.eX = 0;
  swipe_det.eY = 0;
  swipe_det.pX = 0;
  swipe_det.pY = 0;
  var min_x = 40;  //min x swipe for horizontal swipe
  var max_x = 20;  //max x difference for vertical swipe
  var min_y = 60;  //min y swipe for vertical swipe
  var max_y = 40;  //max y difference for horizontal swipe
  var direc = "";
  ele.addEventListener('touchstart',function(e){
    var t = e.touches[0];
    swipe_det.sX = t.screenX; 
    swipe_det.sY = t.screenY;
  },false);
  ele.addEventListener('touchmove',function(e){
    var t = e.touches[0];
    swipe_det.eX = t.screenX; 
    swipe_det.eY = t.screenY;
    
    if(abs(swipe_det.eY - swipe_det.pY) <= 10){
	    e.preventDefault();
    }
    
    swipe_det.pX = t.screenX; 
    swipe_det.pY = t.screenY;    
  },false);
  ele.addEventListener('touchend',function(e){
    //horizontal detection
    if ((((swipe_det.eX - min_x > swipe_det.sX) || (swipe_det.eX + min_x < swipe_det.sX)) && ((swipe_det.eY < swipe_det.sY + max_y) && (swipe_det.sY > swipe_det.eY - max_y)))) {
      if(swipe_det.eX > swipe_det.sX) direc = "right";
      else direc = "left";
    }
    //vertical detection
    if ((((swipe_det.eY - min_y > swipe_det.sY) || (swipe_det.eY + min_y < swipe_det.sY)) && ((swipe_det.eX < swipe_det.sX + max_x) && (swipe_det.sX > swipe_det.eX - max_x)))) {
      if(swipe_det.eY > swipe_det.sY) direc = "down";
      else direc = "up";
    }

    if (direc != "") {
      if(typeof func == 'function') func(ele,direc);
    }
    direc = "";
  },false);  
}