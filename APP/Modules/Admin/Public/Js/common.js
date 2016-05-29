
//公共的JS
$(function(){

	if (is_search) {
		$('.search').show();
	}

	/*
		@der 显示搜索
	*/
	$('.show-search').click(function(){
		var _this = $(this);
		if (_this.hasClass('show_true')) {
			$('.hide-search').click();
			_this.removeClass('show_true');
		} else {
			$('.search').slideDown('slow',function(){
				_this.addClass('show_true');
			});
		}
		
	});

	/*
		@der 隐藏搜索
	*/
	$('.hide-search').click(function(){
		$('.search').slideUp('slow');
		$('.show-search').removeClass('show_true');
	});

	/*全选和反选*/
	var isAll = 1;
	$('input[name=ids]').click(function(){            
        
		if (isAll) {
			$('.tid').prop('checked',true);
			isAll = 0;
		} else {
			$('.tid').prop('checked',false);
			isAll = 1;
		}
                 
	});

    /* 删除添加确认 */
    $('.del').click(function () {
        return confirm('操作后可能无法恢复数据，确定要进行本次操作吗！');
    });
	/*
		@der 日历
	*/
	$('#min_date').focus(function(){

		$('#laydate_box').show();
			laydate({
		    elem: '#min_date',
		    format: 'YYYY-MM-DD',
		    festival: true, 
		    choose: function(datas){ 
		        
		    }
		});
	});

	$('#max_date').focus(function(){

		$('#laydate_box').show();
			laydate({
		    elem: '#max_date',
		    format: 'YYYY-MM-DD',
		    festival: true, 
		    choose: function(datas){ 
		        
		    }
		});
	});

	$('a.btn-success').prepend('<span class="glyphicon glyphicon-plus"></span>&nbsp;');
	$('a.btn-info').prepend('<span class="glyphicon glyphicon-list"></span>&nbsp;');
	
      
});

	
