 <!--dialog Title-->
 <div class="modal-header animated fadeInLeft">
     <div class="row">
             <div class="col-sm-8"><h4>代还计划详情</h4></div>
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
     <input type="hidden" name="id" value="">
     <div style="margin-bottom: 5px">
     <table class="table table-bordered table-hover table-striped" style="width:60%;float: left;margin-bottom: 0;margin-left: 30px">
         <tr>
             <td>ID</td>
             <td>{{$info['order_id']}}</td>
         </tr>
         <tr>
             <td>计划ID</td>
             <td>{{$info['order_no']}}</td>
         </tr>
         <tr>
             <td>订单编号</td>
             <td>{{$info['order_platform_no']}}</td>
         </tr>
         <tr>
             <td>第三方订单编号</td>
             <td>{{$info['back_tradeNo']}}</td>
         </tr>
         <tr>
             <td>会员名称</td>
             <td>{{$info['member_nick']}}</td>
         </tr>
         <tr>
             <td>会员手机号</td>
             <td>{{$info['member_mobile']}}</td>
         </tr>
          <tr>
             <td>交易通道</td>
             <td>{{$info['passageway_name']}}</td>
         </tr>
         <tr>
             <td>交易金额</td>
             <td>{{$info['order_money']}}</td>
         </tr>
         <tr>
             <td>手续费</td>
             <td>{{$info['order_pound']}}({{$info['order_money']}}*{{$info['user_rate']}}+{{$info['user_fix']}})</td>
         </tr>
         <tr>
             <td>成本手续费</td>
             <td>{{$info['order_platform_fee']}}({{$info['order_money']}}*{{$info['passageway_rate']}}+{{$info['passageway_fix']}})</td>
         </tr>
         <tr>
             <td>通道结算</td>
             <td>{{$info['order_pound']-$info['order_platform_fee']}}</td>
         </tr>
         <tr>
             <td>分润金额</td>
             <td>{{$info['fenrun']}}</td>
         </tr>
          <tr>
             <td>盈利金额</td>
             <td>{{$info['yingli']}}</td>
         </tr>
         <tr>
             <td>订单状态</td>
             <td>{{$info['status']}}</td>
         </tr>
         <tr>
             <td>信用卡号</td>
             <td>{{$info['order_card']}}</td>
         </tr>
         <tr>
             <td>重新执行次数</td>
             <td>{{$info['order_retry_count']}}</td>
         </tr>
         <tr>
             <td>执行结果</td>
             <td>{{$info['back_statusDesc']}}</td>
         </tr>
         <tr>
             <td>订单描述</td>
             <td>{{$info['order_desc']}}</td>
         </tr>
         <tr>
             <td>执行时间</td>
             <td>{{$info['order_time']}}</td>
         </tr>
          <tr>
             <td>创建时间</td>
             <td>{{$info['order_add_time']}}</td>
         </tr>
         
     </table>
 </div>

 <!--dialog Button-->
 <div class="modal-footer animated fadeInLeft">
    
 </div>
 <script>
     //移除背景多余遮罩
     $('.backdrop').click(function(){
        $('.modal-backdrop').remove();
     })
 </script>