$(document).ready(function(){
  $("table tr").click(function(){
          var input = $(this).find("input[type=checkbox]");//获取checkbox
          if(input.is(":checked")){
              input.prop("checked",false);
          }else{
              input.prop("checked",true);
          }
  });
  $('a.print').on('click',function(){
      $('#print_area').printArea();
  });
});
