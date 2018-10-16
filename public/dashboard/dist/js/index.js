$(function(){
 $('.sidebar-menu li.treeview').click(function(){
        $(this).siblings().find(".btns").addClass('icon-jia').removeClass('icon-iconfontmove');

//  $('.treeview .pull-right').removeClass('fa-minus').addClass('fa-plus');
       $(this).find(".btns").toggleClass('icon-jia');
       $(this).find(".btns").toggleClass('icon-iconfontmove');
 })


});


