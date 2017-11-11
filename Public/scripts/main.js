(function($) {
	$(function() {
		// resize

		$(window).resize(function() {
			var width = $(window).width();
			if (width > 720) {
				getJagoSlider(false, 3000, 'fade');
			}
		});

		// scroll header change
		headerMove();

		// 密码位数判断
		$('input[name="password"]').on('blur', function() {
			var str = $(this).val();
			if (str.length < 6) {
				cusAlert('warning', '密码至少要有6位(数字、字母、符号等)!');
				$(this).val('');
			}
		});
		
		// btn edit
		$('.btn-edit.cur').on('click', function() {
			// 按钮操作
			$(this).removeClass('cur');
			$(this).parent().find('.btn-save').addClass('cur');

			// disabled input open
			$('input[disabled="true"]').each(function() {
				$(this).removeAttr('disabled');
			});
			$('textarea[disabled="true"]').each(function() {
				$(this).removeAttr('disabled');
			});

			return false;
		});

		// jagoFancybox-cancel
		$('.jagoFancybox-cancel').on('click', function() {
			$('.jagoFancybox').removeClass('active');
			$('.jagoFancybox .box').removeClass('active');
			return false;
		});

		// 每页动画
		cusAnimateTop();

		function cusAnimateTop() {
			var width = $(window).width();
			var thatTop = 0;

			// 触发高度增加
			if (width >= 768 ) {
				thatTop = 200;
			}else {
				$('.cus-animate').each(function() {
					var that = $(this);
					that.removeClass('cus-animate');
				});
			}

			// 看见后
			$(window).scroll(function() {
				$('.cus-animate').each(function() {
					var that = $(this);
					var offsetTop = $(this).offset().top;
					var windowHeight = $(window).outerHeight();
					var height = $(this).innerHeight();
					var scrollTop = $(window).scrollTop();

					if (scrollTop + windowHeight >= offsetTop + thatTop) {
						that.removeClass('cus-animate');
					}
				});
			});
		}
	});
})(jQuery);