//用js控制幻灯片的手动播放
$(function () {
    $('.prev-slide').on('click',function () {
        $('#slideshow').carousel('prev');
    });

    $('.next-slide').on('click',function () {
        $('#slideshow').carousel('next');
    });
    //上下左右控制幻灯片
    $(document).on('keydown',function (e) {
        console.log(e.which);
        switch (e.which){
            case 37:
                $('#slideshow').carousel('prev');
                break;
            case 38:
                $('#slideshow').carousel('prev');
                break;
            case 39:
                $('#slideshow').carousel('next');
                break;
            case 40:
                $('#slideshow').carousel('next');
                break;
        }
    });

    var play = false;
    $('.play-and-stop').click(
        function () {
            if(!play){
                //如果幻灯片处于暂停状态
                //根据id寻找幻灯片控件，调用carousel方法，开始幻灯片的播放
                $('#slideshow').carousel('cycle');
                //将当前控件.play-and-stop,交给jquery，查找span子元素，移除播放图标类，添加暂停图标类
                $(this).children('span').removeClass('glyphicon-play').addClass('glyphicon-pause');
            } else {
                $('#slideshow').carousel('pause');
                $(this).children('span').removeClass('glyphicon-pause').addClass('glyphicon-play');
            }
            play = !play;
        }
    );


});













