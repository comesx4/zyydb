$(function () {
    $('.pay').find('li').click(function () {
        var index = $(this).index();
        $(this).siblings('li').removeClass('show');
        $(this).addClass('show');
        $('.pros').hide().eq(index).show();
    });

    //支付情况的的显示
    $('.recharge').click(function () {
        //创建灰色背景图层
        $('<div class = "ybc"></div>').appendTo('body').css({
            'width': $(document).width(),
            'height': $(document).height(),
            'position': 'absolute',
            'top': 0,
            'left': 0,
            'z-index': 100,
            'opacity': 0.3,
            'filter': 'Alpha(Opacity = 30)',
            'backgroundColor': '#000'
        });

        $('#quereng').show();

    });


    $('.shua').click(function () {
        //window.location='';
        location.reload();
    });

});