@extends('admin/layout/layout_main')
@section('title','通道列表管理~')
@section('wrapper')
<style>
	 h4 > a,.pull-right > a{color:#145ccd;}
 </style>
 <blockquote> 通道利润统计</blockquote>
 <section>
 <hr/>
<form action="" method="post">
 <div class="input-group" style="width: 200px;float: left; margin-right: 10px;">
    <input type="text" class="form-control date-picker" id="dateTimeRange" placeholder="升级创建时间" />
    <input type="hidden" name="beginTime" id="beginTime" value="" />
    <input type="hidden" name="endTime" id="endTime" value="" />
    <z class='clearTime'>X</z>
</div>
  <button class="btn btn-primary" type="submit">搜索</button>
</form>

 <table class="table datatable">
      <thead>
           <tr>
                 <!-- 以下两列左侧固定 -->
                 <th>#</th>
                 <th>通道名</th>
                 <th>真实通道</th>
                 <!-- 以下三列中间可滚动 -->
                 <th class="flex-col">交易金额</th> 
                 <th class="flex-col">总笔数</th>
                 <th class="flex-col">利润</th>
                 <!-- 以下列右侧固定 -->
           </tr>
      </thead>
      <tbody>

           <tr>
                 <td>{{$passageway->passageway_id}}</td>
                 <td>{{$passageway->passageway_name}}</td>
                 <td>{{$passageway->passageway_true_name}}</td>
                 <td>{{$ordersum}}</td>
                 <td>{{$num}}</td>
                 <td>{{$lirun}}</td>
           </tr>

      </tbody>
 </table>
 </section>
 <script>
  $(document).ready(function(){
       $('.menu .nav .active').removeClass('active');
       $('.menu .nav li.order').addClass('active');
       $('.menu .nav li.order-manager').addClass('show');
 })
 
$('#dateTimeRange').daterangepicker({
        applyClass : 'btn-sm btn-success',
        cancelClass : 'btn-sm btn-default',
        locale: {
            applyLabel: '确认',
            cancelLabel: '取消',
            fromLabel : '起始时间',
            toLabel : '结束时间',
            customRangeLabel : '自定义',
            firstDay : 1
        },
        ranges : {
            //'最近1小时': [moment().subtract('hours',1), moment()],
            '今日': [moment().startOf('day'), moment()],
            '昨日': [moment().subtract('days', 1).startOf('day'), moment().subtract('days', 1).endOf('day')],
            '最近7日': [moment().subtract('days', 6), moment()],
            '最近30日': [moment().subtract('days', 29), moment()],
            '本月': [moment().startOf("month"),moment().endOf("month")],
            '上个月': [moment().subtract(1,"month").startOf("month"),moment().subtract(1,"month").endOf("month")]
        },
        opens : 'left',    // 日期选择框的弹出位置
        separator : ' 至 ',
        showWeekNumbers : true,     // 是否显示第几周

 
        //timePicker: true,
        //timePickerIncrement : 10, // 时间的增量，单位为分钟
        //timePicker12Hour : false, // 是否使用12小时制来显示时间
 
         
        //maxDate : moment(),           // 最大时间
        format: 'YYYY-MM-DD'
 
    }, function(start, end, label) { // 格式化日期显示框
        $('#beginTime').val(start.format('YYYY-MM-DD'));
        $('#endTime').val(end.format('YYYY-MM-DD'));
    });
begin_end_time_clear();
$('.clearTime').click(begin_end_time_clear);
  //清除时间
    function begin_end_time_clear() {
        $('#dateTimeRange').val('');
        $('#beginTime').val('');
        $('#endTime').val('');
    }
</script>
<style type="text/css">
   .clearTime{
    position: absolute;
    right: 5px;
    top: 5px;
    z-index: 99;
    border: 1px solid;
    color: red;
    font-size: .6rem;
    padding: 0 5px;
   }
 </style>

@endsection
