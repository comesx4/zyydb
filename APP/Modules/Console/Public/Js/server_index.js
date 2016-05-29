var timer = Object();
$(function(){
	 
	 Highcharts.setOptions({  
         global: {  
            useUTC: true, //false 设置开始时间为早上8点
                 },

    });  
    //声明报表对象   
    var chart = new Highcharts.Chart({   
    	credits:{//版权信息
        	enabled : true,
        	text	: '御智云平台',
        	href	: 'http://zyy.yjcom.com.cn'
        },
        chart:{  
 	 		renderTo:'container',
            zoomType: 'x'
        },
        title: {
            text: 'CPU使用率'
        },
        // subtitle: {
        //     text: document.ontouchstart === undefined ?
        //             'Click and drag in the plot area to zoom in' :
        //             'Pinch the chart to zoom in'
        // },
        xAxis: {
            type: 'datetime',
            minRange: 10000 // fourteen days
        },
        yAxis: {
            title: {
                text: '占用百分比'
            }
        },
        legend: {
            enabled: false
        },
        plotOptions: {
            area: {
                fillColor: {
                    // linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1},
                    stops: [
                        [0, Highcharts.getOptions().colors[0]],
                        [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                    ]
                },
                marker: {
                    radius: 2
                },
                lineWidth: 1,
                states: {
                    hover: {
                        lineWidth: 1
                    }
                },
                threshold: null
            }
        },

        series: [{
            type: 'line',
            name: '值',
           	pointInterval: pointInterval, //刚好间隔1分钟        
            pointStart: pointStart,
            data: datas
        }]
    });
	

	/*选择监控类型时*/
	$('.requestType').find('button').click(function(){
		
		requestType = $(this).attr('value');
		set_data($(this));		
	});

	/*选择监控时间时*/
	$('.timeType').find('button').click(function(){
		timeType = $(this).attr('value');
		if (timeType != 1) {
		
			window.clearInterval(timer);
		} else {

			realTimer();
		}
		set_data($(this));
	});

	/*
		@der 实时的请求
	*/
	if (timeType == 1) {
		realTimer();
	}
	/*
		@der 处理图表所需要的数据
		@param object $_this
	*/
	function set_data(_this){
		if (_this != undefined) {
			_this.siblings('button').removeClass('btn-primary').addClass('btn-default');		
			_this.removeClass('btn-default').addClass('btn-primary primary');
		}		
		
		/*异步请求数据*/
		$.ajax({
			type 	   : 'GET',
			url  	   : requestAction,
			dataType   : 'json',
			data 	   : {server_name:server_name,dimensions_value:dimensions_value , requestType:requestType , timeType:timeType},
			/*请求之前*/
			beforeSend : function(){
				$('#position').show();
				$("<div id='pachar'></div>").css({
					'height' 	 : $(document).height(),
					'width'  	 : $(document).width(),
					'background' : '#000',
					'opacity' 	 : 0.3,
 					'filter' 	 : 'Alpha(Opacity = 30)',
 					'position'   : 'absolute',
 					'top'		 : 0,
 					'left'	     : 0,
 					'z-index'    : 10
				}).appendTo($('body'));
			},
			/*请求成功之后*/
			success    : function(data){
				if (data.status == 0) {
					alert(data.message);
				}

				$('#position').hide();
				$('#pachar').remove();
				
				eval('datas = '+ data.data);
				var arr = get_info(requestType);
				pointStart = Date.UTC(data.year , data.month , data.day , data.hour , data.minute);
				/*更新图表的值*/
				chart.series['0'].update({          
		           	pointInterval:data.pointInterval, //间隔时间		        
		           	pointStart: pointStart,
		            data: datas 		//数据	           
		        });
		        $('.highcharts-title').html(arr['0']);
		        $('.highcharts-yaxis-title').html(arr['1']);

			}
			
		});
	}
	
	/*
		@der 根据类型返回标题和说明
		@param int $requestType 类型
		@return array
	*/
	function get_info(requestType){

		var arr = [];
		switch (requestType) {
			case '1':
				arr = ['CPU使用率' , '百分比'];				
				break;
			case '2':				
				arr = ['内网下行带宽' , 'M'];			
				break;
			case '3':
				arr = ['内网上行带宽' , 'M'];			
				break;
			case '4':
				arr = ['外网下行带宽' , 'M'];					
				break;
			case '5':
				arr = ['外网上行带宽' , 'M'];					
				break;
		}

		return arr;
	}

	function realTimer(){
		
		//定时器(1分钟请求一次)
		timer = setInterval(function(){
			/*异步请求数据*/
			$.ajax({
				type 	   : 'GET',
				url  	   : requestAction,
				dataType   : 'json',
				data 	   : {isRealTime:1,server_name:server_name,dimensions_value:dimensions_value , requestType:requestType , timeType:timeType},
				/*请求之前*/
				beforeSend : function(){
				
				},
				/*请求成功之后*/
				success    : function(data){
					
					eval('var chartData = '+ data.data);
					var arr = get_info(requestType);
					datas.shift();
					datas.push(chartData)
					/*更新图表的值*/
					chart.series['0'].update({          
			           	pointInterval:data.pointInterval, //间隔时间			           
			           	pointStart: parseInt(pointStart + 60000),
			            data: datas 		//数据	           
			        });
			      
				}
				
			});
		},60000);
	}


});