//fgnass.github.com/spin.js#v1.2.7
!function(e,t,n){function o(e,n){var r=t.createElement(e||"div"),i;for(i in n)r[i]=n[i];return r}function u(e){for(var t=1,n=arguments.length;t<n;t++)e.appendChild(arguments[t]);return e}function f(e,t,n,r){var o=["opacity",t,~~(e*100),n,r].join("-"),u=.01+n/r*100,f=Math.max(1-(1-e)/t*(100-u),e),l=s.substring(0,s.indexOf("Animation")).toLowerCase(),c=l&&"-"+l+"-"||"";return i[o]||(a.insertRule("@"+c+"keyframes "+o+"{"+"0%{opacity:"+f+"}"+u+"%{opacity:"+e+"}"+(u+.01)+"%{opacity:1}"+(u+t)%100+"%{opacity:"+e+"}"+"100%{opacity:"+f+"}"+"}",a.cssRules.length),i[o]=1),o}function l(e,t){var i=e.style,s,o;if(i[t]!==n)return t;t=t.charAt(0).toUpperCase()+t.slice(1);for(o=0;o<r.length;o++){s=r[o]+t;if(i[s]!==n)return s}}function c(e,t){for(var n in t)e.style[l(e,n)||n]=t[n];return e}function h(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var i in r)e[i]===n&&(e[i]=r[i])}return e}function p(e){var t={x:e.offsetLeft,y:e.offsetTop};while(e=e.offsetParent)t.x+=e.offsetLeft,t.y+=e.offsetTop;return t}var r=["webkit","Moz","ms","O"],i={},s,a=function(){var e=o("style",{type:"text/css"});return u(t.getElementsByTagName("head")[0],e),e.sheet||e.styleSheet}(),d={lines:12,length:7,width:5,radius:10,rotate:0,corners:1,color:"#000",speed:1,trail:100,opacity:.25,fps:20,zIndex:2e9,className:"spinner",top:"auto",left:"auto",position:"relative"},v=function m(e){if(!this.spin)return new m(e);this.opts=h(e||{},m.defaults,d)};v.defaults={},h(v.prototype,{spin:function(e){this.stop();var t=this,n=t.opts,r=t.el=c(o(0,{className:n.className}),{position:n.position,width:0,zIndex:n.zIndex}),i=n.radius+n.length+n.width,u,a;e&&(e.insertBefore(r,e.firstChild||null),a=p(e),u=p(r),c(r,{left:(n.left=="auto"?a.x-u.x+(e.offsetWidth>>1):parseInt(n.left,10)+i)+"px",top:(n.top=="auto"?a.y-u.y+(e.offsetHeight>>1):parseInt(n.top,10)+i)+"px"})),r.setAttribute("aria-role","progressbar"),t.lines(r,t.opts);if(!s){var f=0,l=n.fps,h=l/n.speed,d=(1-n.opacity)/(h*n.trail/100),v=h/n.lines;(function m(){f++;for(var e=n.lines;e;e--){var i=Math.max(1-(f+e*v)%h*d,n.opacity);t.opacity(r,n.lines-e,i,n)}t.timeout=t.el&&setTimeout(m,~~(1e3/l))})()}return t},stop:function(){var e=this.el;return e&&(clearTimeout(this.timeout),e.parentNode&&e.parentNode.removeChild(e),this.el=n),this},lines:function(e,t){function i(e,r){return c(o(),{position:"absolute",width:t.length+t.width+"px",height:t.width+"px",background:e,boxShadow:r,transformOrigin:"left",transform:"rotate("+~~(360/t.lines*n+t.rotate)+"deg) translate("+t.radius+"px"+",0)",borderRadius:(t.corners*t.width>>1)+"px"})}var n=0,r;for(;n<t.lines;n++)r=c(o(),{position:"absolute",top:1+~(t.width/2)+"px",transform:t.hwaccel?"translate3d(0,0,0)":"",opacity:t.opacity,animation:s&&f(t.opacity,t.trail,n,t.lines)+" "+1/t.speed+"s linear infinite"}),t.shadow&&u(r,c(i("#000","0 0 4px #000"),{top:"2px"})),u(e,u(r,i(t.color,"0 0 1px rgba(0,0,0,.1)")));return e},opacity:function(e,t,n){t<e.childNodes.length&&(e.childNodes[t].style.opacity=n)}}),function(){function e(e,t){return o("<"+e+' xmlns="urn:schemas-microsoft.com:vml" class="spin-vml">',t)}var t=c(o("group"),{behavior:"url(#default#VML)"});!l(t,"transform")&&t.adj?(a.addRule(".spin-vml","behavior:url(#default#VML)"),v.prototype.lines=function(t,n){function s(){return c(e("group",{coordsize:i+" "+i,coordorigin:-r+" "+ -r}),{width:i,height:i})}function l(t,i,o){u(a,u(c(s(),{rotation:360/n.lines*t+"deg",left:~~i}),u(c(e("roundrect",{arcsize:n.corners}),{width:r,height:n.width,left:n.radius,top:-n.width>>1,filter:o}),e("fill",{color:n.color,opacity:n.opacity}),e("stroke",{opacity:0}))))}var r=n.length+n.width,i=2*r,o=-(n.width+n.length)*2+"px",a=c(s(),{position:"absolute",top:o,left:o}),f;if(n.shadow)for(f=1;f<=n.lines;f++)l(f,-2,"progid:DXImageTransform.Microsoft.Blur(pixelradius=2,makeshadow=1,shadowopacity=.3)");for(f=1;f<=n.lines;f++)l(f);return u(t,a)},v.prototype.opacity=function(e,t,n,r){var i=e.firstChild;r=r.shadow&&r.lines||0,i&&t+r<i.childNodes.length&&(i=i.childNodes[t+r],i=i&&i.firstChild,i=i&&i.firstChild,i&&(i.opacity=n))}):s=l(t,"animation")}(),typeof define=="function"&&define.amd?define(function(){return v}):e.Spinner=v}(window,document);
if("undefined"!=typeof jQuery){(function(a){a.imgpreload=function(b,c){c=a.extend({},a.fn.imgpreload.defaults,c instanceof Function?{all:c}:c);if("string"==typeof b){b=new Array(b)}var d=new Array;a.each(b,function(e,f){var g=new Image;var h=f;var i=g;if("string"!=typeof f){h=a(f).attr("src");i=f}a(g).bind("load error",function(e){d.push(i);a.data(i,"loaded","error"==e.type?false:true);if(c.each instanceof Function){c.each.call(i)}if(d.length>=b.length&&c.all instanceof Function){c.all.call(d)}a(this).unbind("load error")});g.src=h})};a.fn.imgpreload=function(b){a.imgpreload(this,b);return this};a.fn.imgpreload.defaults={each:null,all:null}})(jQuery)}

var cup_slider_start = function(json_config_url){
	var spin_opts = {
	  lines: 13, // The number of lines to draw
	  length: 8, // The length of each line
	  width: 4, // The line thickness
	  radius: 10, // The radius of the inner circle
	  corners: 1, // Corner roundness (0..1)
	  rotate: 0, // The rotation offset
	  color: '#FFF', // #rgb or #rrggbb
	  speed: 1.5, // Rounds per second
	  trail: 37, // Afterglow percentage
	  shadow: false, // Whether to render a shadow
	  hwaccel: false, // Whether to use hardware acceleration
	  className: 'spinner', // The CSS class to assign to the spinner
	  zIndex: 1, // 2e9  The z-index (defaults to 2000000000)
	  top: 'auto', // Top position relative to parent in px
	  left: 'auto' // Left position relative to parent in px
	};
	//var target = document.getElementById('foo');
	var indicator_block = jQuery('.cup-competition-slider .loading_indicator');
	var target = indicator_block[0];
	var spinner = new Spinner(spin_opts).spin(target);
	
	
	var indicator_left = (indicator_block.parent().width() - indicator_block.width()) / 2;
	var indicator_top = (indicator_block.parent().height() - indicator_block.height()) / 2;
	
	indicator_block.css({top: indicator_top+'px', left:indicator_left+'px'});
	indicator_block.css('zIndex', 1);
	
	
	
	
	var slider_data = "";
	var carousle_interval = 8;	//second
						
	var image_array = new Array();
						
	var loop_idx = -1;
	
	var carousel_frame = jQuery('.cup-competition-slider .slider_frame');
	var unit_width = carousel_frame.width();
	var image_holder = jQuery('.cup-competition-slider .slider_frame .image_holder');
	var image_frame = jQuery('.cup-competition-slider .slider_frame .image_frame');
	var transition_frame = image_frame.find('.transition_frame');
	
	var image_position_one = transition_frame.find('.position_one');
	var image_position_two = transition_frame.find('.position_two');
	transition_frame.width(unit_width * 2);
	image_position_one.width(unit_width);
	image_position_two.width(unit_width);
	var description_frame = jQuery('.cup-competition-slider .slider_frame .description_frame');
	var info_frame = jQuery('.cup-competition-slider .slider_frame .description_frame .info_frame');
	var content_area = info_frame.find('.content_area');
	var indicator_holder = jQuery('.cup-competition-slider .slider_frame .description_frame .indication_frame');
	
	
	var slide_timer;
	
	var showNextSlide = function(){
			loop_idx++;
			if(loop_idx >= indicator_holder.find('.indicator').length){
				loop_idx = 0;
			}
			
			showSlide(loop_idx);
		};
		
	var showSlide = function(selected_idx){
		//alert(selected_idx);
			indicator_holder.find('.indicator').removeClass('active');
			var tmp_indicator = indicator_holder.find('.indicator').eq(selected_idx);
			tmp_indicator.addClass('active');
			
			var image_src = tmp_indicator.find('img').attr('src');
			var tmp_title = jQuery('<h5></h5>');
				tmp_title.text(slider_data[selected_idx]['title']);
			var tmp_description = jQuery('<p></p>');
				tmp_description.text(slider_data[selected_idx]['description']);
			var tmp_link = slider_data[selected_idx]['link']
			
			description_frame.show();

			var image_to_display = image_holder.find('img').eq(selected_idx).clone();
			image_position_two.empty();
			image_position_two.append(image_to_display);
			
			image_position_one.find('img').fadeOut(500);
			image_position_two.find('img').hide().fadeIn(500);
			transition_frame.animate({left: '-'+unit_width+'px'}, 600, function(){
					image_position_one.empty().append(image_to_display);
				}).animate({left: '0%'}, 0);
			
			content_area.fadeOut(300, function(){
				content_area.empty();
				content_area.append(tmp_title);
				content_area.append(tmp_description);
				content_area.fadeIn(300);
			});
			
			//jQuery('.cup-main-carousel .carousel_frame .description_frame .btn_frame a').attr('href', tmp_link);
			slide_timer = setTimeout(showNextSlide, carousle_interval*1000);
		}
		
	jQuery.getJSON(json_config_url, function(json){
		carousle_interval = json.interval;
		slider_data = json.carousel_data;
		
		for(var i=0; i < slider_data.length; i++){
			image_array.push(slider_data[i]['image']);
		}
		
			
		$.imgpreload(image_array,{
			all: function()
			{
				for(var idx = 0; idx < slider_data.length; idx++){
					//var img = jQuery('<img/>').attr('src', carousle_data[idx]['image']).css('display','none');
					var img_layer = jQuery('<div></div').addClass('image_layer');
						img_layer.css('background', "url("+slider_data[idx]['image']+") no-repeat");
						img_layer.hide();
					var img = jQuery('<img/>').attr('src', slider_data[idx]['image']);
					image_holder.append(img);
					var indicator = jQuery('<div></div>').addClass('indicator');
					//indicator.append(img);
					
					indicator.click(function(){
						clearTimeout(slide_timer);
						loop_idx = jQuery('.cup-competition-slider .slider_frame .indicator').index(this);
						showSlide(loop_idx);
					});
					indicator_holder.append(indicator);
				}
				
				indicator_block.remove();
				
				showNextSlide();
			}
		});
		
	});
}