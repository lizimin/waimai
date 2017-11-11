// get slider
function getJagoSlider(autoPlay, speed, autoPlayStyle) {
	// auto play
	var autoPlay = autoPlay;			// auto Y/N
	var autoPlayStyle = autoPlayStyle;	// fade\left
	var autoPlaySpeed = speed;			// auto play speed

	// width height setting
	var jagoSliderWidth = $('.jago-slider').width();

	if (jagoSliderWidth > 1199) {
		var jagoSliderHeight = jagoSliderWidth * 0.45;
		var sliderLength = $('.jago-slider .slider-window .slider').length;

		// slider css
		$('.jago-slider').css({'height': jagoSliderHeight});
		$('.slider-window').css({'width': jagoSliderWidth*sliderLength, 'height': jagoSliderHeight});
		$('.slider-window .slider').css({'width': jagoSliderWidth});
	} else if(jagoSliderWidth > 767 && jagoSliderWidth < 1200) {
		var jagoSliderHeight = jagoSliderWidth * 0.5;
		var sliderLength = $('.jago-slider .slider-window .slider').length;

		// slider css
		$('.jago-slider').css({'height': jagoSliderHeight});
		$('.slider-window').css({'width': jagoSliderWidth*sliderLength, 'height': jagoSliderHeight});
		$('.slider-window .slider').css({'width': jagoSliderWidth});
	}else {
		var jagoSliderHeight = window.innerHeight * 0.88;
		var sliderLength = $('.jago-slider .slider-window .slider').length;

		// slider css
		$('.jago-slider').css({'height': jagoSliderHeight});
		$('.slider-window').css({'width': jagoSliderWidth*sliderLength, 'height': jagoSliderHeight});
		$('.slider-window .slider').css({'width': jagoSliderWidth});
	}

	// slider add image
	for(var i = 1; i <= sliderLength; i++) {
		var url = $('.slider.slider-'+i).data('img');
		if (jagoSliderWidth > 1199) {
			$('.slider.slider-'+i).css({'background': 'url('+url+') no-repeat', 'background-size': '100% auto', 'background-position': '0 0'});
		} else if(jagoSliderWidth > 767 && jagoSliderWidth < 1200) {
			// iPad
			$('.slider.slider-'+i).css({'background': 'url('+url+') no-repeat', 'background-size': '100% 100%', 'background-position': 'center'});
		}else {
			// 手机
			$('.slider.slider-'+i).css({'background': 'url('+url+') no-repeat', 'background-size': 'auto 100%', 'background-position': 'center'});
		}
	}

	// slider auto play
	if (autoPlay) {
		if (autoPlayStyle == 'left') {
			setInterval(function() {
				$('.slider-window').css({'transition': 'transform 1.5s ease-in-out'});
				var index = $('.slider-window .slider.active').index();
				
				$('.slider-window .slider').eq(index).removeClass('active');
				if ((index+1) == sliderLength) {
					index = 0;
				}else {
					index++;
				}
				var distance = (index) * jagoSliderWidth * (-1);
				$('.slider-window').css({'transform': 'translateX('+distance+'px)'});
				$('.slider-window .slider').eq(index).addClass('active');
			}, autoPlaySpeed);
		}

		if (autoPlayStyle == 'fade') {
			$('.slider-window .slider').css({'float': 'none', 'position': 'absolute', 'top': 0, 'left': 0, 'display': 'none'});
			$('.slider-window .slider.active').css({'display': 'block'});
			setInterval(function() {
				
				var index = $('.slider-window .slider.active').index();
				$('.slider-window .slider.active').fadeOut(1500);
				$('.slider-window .slider').eq(index).removeClass('active');
				if ((index+1) == sliderLength) {
					index = 0;
				}else {
					index++;
				}
				$('.slider-window .slider').eq(index).addClass('active');
				$('.slider-window .slider.active').fadeIn(1500);
			}, autoPlaySpeed);	
		}
	}
}

// cus slider
function cusSlider(autoPlay, index) {
	$(window).resize(function() {
		$('.cus-slider-ctn .cus-slider .slider ul').css({'transform': 'translateX(0px)'});
		cusSliderGo();
	});
	cusSliderGo();
	function cusSliderGo() {
		var width = $('.cus-slider-ctn .cus-slider .slider').width();
		var count = $('.cus-slider-ctn .cus-slider .slider ul li').length;
		var handle = 0;

		// 手机端默认值
		if (width <= 767) {
			index = 1;
			autoPlay = false;
		}
		if (width > 767 && width < 1200) {
			index = 2;
			autoPlay = false;
		}

		// console.log(width);

		// console.log('width:'+width);
		// console.log('count:'+count);

		$('.cus-slider-ctn .cus-slider .slider ul').css({'width': width*count});
		$('.cus-slider-ctn .cus-slider .slider ul li').css({'width': width/index});

		$('.cus-slider-ctn .next').on('click', function() {
			handle++;
			if (handle > (count-index)) {
				handle = 0;
			}
			$('.cus-slider-ctn .cus-slider .slider ul').css({'transform': 'translateX('+ -1*width/index*handle +'px)'})
			return false;
		});
		$('.cus-slider-ctn .prev').on('click', function() {
			handle--;
			if (handle < 0) {
				handle = count-index;
			}
			$('.cus-slider-ctn .cus-slider .slider ul').css({'transform': 'translateX('+ -1*width/index*handle +'px)'})
			return false;
		});

		if (autoPlay) {
			var autoPlayInterval = setInterval(autoPlayFunction,2000);

			$('.cus-slider').hover(
				function() {
					clearInterval(autoPlayInterval);
				},
				function() {
					autoPlayInterval = setInterval(autoPlayFunction,2000);
				}
			);
		}

		function autoPlayFunction() {
			handle++;
			if (handle > (count-index)) {
				handle = 0;
			}
			$('.cus-slider-ctn .cus-slider .slider ul').css({'transform': 'translateX('+ -1*width/index*handle +'px)'})
		}
	}
}

// scroll move
function headerMove() {
	$(window).scroll(function() {
		// console.log('headerMove');
		var windowWidth = $(window).width();
		// console.log(windowWidth);
		if (windowWidth > 767) {
			// console.log(windowWidth);
			var scrollTop = $(window).scrollTop();
			if (scrollTop > 150) {
				$('.header').addClass('active');
			}else {
				$('.header').removeClass('active');
			}
		}
	});
}

function cusSwitch() {
	var width = $('.cus-switch').width();
	$('.cus-switch .ctn').css({'width': width*1.5});
	$('.cus-switch .ctn .entry').css({'width': width/2});

	$('.cus-switch .switch-btn').on('click', function() {
		var hasActive = $('.cus-switch .switch-btn').hasClass('active');
		if (hasActive) {
			$('.cus-switch .ctn').css({'transform': 'translateX(0px)'})
			$('.cus-switch .switch-btn').removeClass('active');
		}else {
			$('.cus-switch .ctn').css({'transform': 'translateX('+(-1*width/2)+'px)'})
			$('.cus-switch .switch-btn').addClass('active');
		}
	});
}

function cusAlert(status, info){
	var content = '<div class="alert alert-' + status + ' alert-dismissible" role="alert">' +
	'<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>' +
	'<strong>' + info + '</strong>' +
	'</div>';
	$('body').append(content);
	setTimeout(function() {
		$('.alert').fadeOut('1000');
	}, 1500);
}

function currentTimeAll() {
	var currentTime = '';
	var date = new Date();
	var year = date.getFullYear();
	var month = date.getMonth() + 1;
	var day = date.getDate();
	var hours = date.getHours();
	var minutes = date.getMinutes();
	var seconds = date.getSeconds();
	currentTime = year + '/' + month + '/' + day + ' ' + hours + ':' + minutes + ':' + seconds;
	return currentTime;
}

function currentTimeDate() {
	var currentTime = '';
	var date = new Date();
	var year = date.getFullYear();
	var month = date.getMonth() + 1;
	var day = date.getDate();
	currentTime = year + '/' + month + '/' + day;
	return currentTime;
}

function pagination() {
	$('.pagination .pagination-search input').change(function() {
		var value = $(this).val();
		if(isNaN(value)){
			$(this).val('');
		}
	});
}