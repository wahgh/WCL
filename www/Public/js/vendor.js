$(function(){
    var odiv=$('.leftcent .menu_main ');
    var jishu=$('.leftcent  .menu_sub');


    odiv.mouseover(function (){
        var index=$(this).index();
        jishu.eq(index).css('display','block');

    }).mouseout(function(){
        jishu.css('display','none');

    })

});







