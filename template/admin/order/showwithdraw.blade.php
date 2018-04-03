

  <!--dialog Title-->
 <div class="modal-header animated fadeInLeft">
     <div class="row">
             <div class="col-sm-8"><h4>提现详情</h4></div>
             <div class="col-sm-4">
                 <div class="text-right">
                     <span class="label label-dot label-primary"></span>
                     <span class="label label-dot label-success"></span>
                     <span class="label label-dot label-info"></span>
                     <span class="label label-dot label-warning"></span>
                     <span class="label label-dot label-danger"></span>
                 </div>
             </div>
         </div>
         
 </div>

 <!--dialog Content-->
 <div class="modal-content animated fadeInLeft">
     <form action=" " method="post" class="form-group" id="myform">
     <input type="hidden" name="id" value="">
    
     <div style="margin-bottom: 5px">
     <table class="table table-bordered table-hover table-striped" style="width:60%;float: left;margin-bottom: 0;margin-left: 30px">
         <tr>
             <td>订单号</td>
             <td>{{$info['withdraw_no']}}</td>
         </tr>
         <tr>
             <td>会员名称</td>
             <td>{{$info['withdraw_name']}}</td>
         </tr>
         <tr>
             <td>手机号</td>
             <td>{{$info['member_mobile']}}</td>
         </tr>
         <tr>
             <td>收款方式</td>
             <td>{{$info['withdraw_method']}}</td>
         </tr>
          <tr>
             <td>收款账号</td>
             <td>{{$info['withdraw_account']}}</td>
         </tr>
         <tr>
             <td>操作金额</td>
             <td>{{$info['withdraw_amount']}}</td>
         </tr>
         <tr>
             <td>手续费</td>
             <td>{{$info['withdraw_charge']}}</td>
         </tr>
         <tr>
             <td>订单创建时间</td>
             <td>{{$info['withdraw_add_time']}}%</td>
         </tr>
          <tr>
             <td>订单更新时间</td>
             <td>{{$info['withdraw_update_time']}}</td>
         </tr>
         <tr>
             <td>备注信息</td>
             <td>{{$info['withdraw_bak']}}</td>
         </tr>
         <tr>
             <td>其他</td>
             <td>{{$info['withdraw_information']}}</td>
         </tr>
         
     </table>
     
     
     

     </form>
 </div>

 <!--dialog Button-->
 <div class="modal-footer">
    
 </div>
 <script>
     //移除背景多余遮罩
     $('.backdrop').click(function(){
        $('.modal-backdrop').remove();
     })
 </script>