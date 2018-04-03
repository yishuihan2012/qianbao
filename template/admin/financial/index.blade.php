 @extends('admin/layout/layout_main')
 @section('title','财务管理-对账中心~')
 @section('wrapper')
 <style>
	 h4 > a,.pull-right > a{color:#145ccd;}
      .clearTime{ position: absolute; right: 5px; top: 5px; z-index: 99; border: 1px solid; color: red; font-size: .6rem; padding: 0 5px;}
 </style>
 <link rel="stylesheet" href="/static/lib/tabs/zui.tabs.min.css">

 <div class="panel">
      <div class="panel-body">
      <form action="{{url('index/Financial/index')}}" method="post">
          <div class="input-group" style="width: 180px;float: left;margin-right: 10px;">
            <span class="input-group-addon">会员</span>
            <input type="text" class="form-control" name="member_nick" value="{{$r['member_nick'] or ''}}" placeholder="用户名/手机号">
          </div>
           <div class="col-sm-2">
                <div class="input-group">
                     <span class="input-group-btn"><button class="btn btn-default" type="button">金额</button></span>
                     <input type="text" class="form-control" name="min_money" value="{{$r['min_money'] or ''}}">
                     <span class="input-group-btn fix-border"><button class="btn btn-default" type="button">~</button></span>
                     <input type="text" class="form-control" name="max_money" value="{{$r['max_money'] or ''}}">
                </div>
           </div>

    <div class="input-group" style="width: 360px;float: left;margin-right: 10px;">
      <span class="input-group-addon">注册时间</span>
      <input type="date" name="beginTime" style="width: 140px" class="form-control" value="{{$r['beginTime'] or ''}}" />
      <input type="date" name="endTime" style="width: 140px" class="form-control" value="{{$r['endTime'] or ''}}" /></div>

           <div class="col-sm-1">
                <button class="btn btn-primary" type="submit">搜索</button>
           </div>
      </form>
    </div>
 </div>

 <blockquote> 对账中心: 会员升级收益 <strong class="text-danger">{{$data['level']}}</strong> 元, 快捷支付收益 <strong class="text-danger">{{$data['quickPay']}}</strong> 元, 代还收益 <strong class="text-danger">{{$data['autoPay']}}</strong> 元, 共提现成功 <strong class="text-danger"> {{$data['withdraw']}} </strong>元。
 </blockquote>
 <section>
 <hr/>
 <div id="tabs" class="tabs" style="height: 550px;">
  <nav class="tabs-navbar"></nav>
  <nav class="tabs-container"></nav>
 </div>
 </section>
 <script src="/static/lib/tabs/zui.tabs.js"></script> 
 <script type="text/javascript">
      // 定义标签页
      var tabs = [{
                title: '会员升级收益',
                url: "{{url('index/financial/level')}}",
                type: 'iframe',
                forbidClose: true
           }, {
                title: '快捷支付收益',
                url: "{{url('index/financial/income')}}",
                type: 'iframe',
                forbidClose: true
           }, {
                title: '自动代还收益',
                url: "{{url('index/financial/substitute')}}",
                type: 'iframe',
                forbidClose: true
           }, {
                title: '提现统计',
                url: "{{url('index/financial/withdraw')}}",
                type: 'iframe',
                forbidClose: true
           }];
      // 初始化标签页管理器
      $('#tabs').tabs({tabs: tabs});
 </script>
 <script type="text/javascript">
 $(document).ready(function(){
     	 $('.menu .nav .active').removeClass('active');
    	 $('.menu .nav li.financial_center').addClass('active');
    	 $('.menu .nav li.financial-manager').addClass('show'); 
 });
 </script>
 @endsection
