<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>生成计划</title>
    <meta name="viewport"
          content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no"/>
    <link href="/static/css/mui.min.css" rel="stylesheet"/>
    <link href="/static/css/mui.poppicker.css" rel="stylesheet"/>
    <link href="/static/css/mui.picker.css" rel="stylesheet"/>
    <link href="/static/css/iconfont.css" rel="stylesheet"/>
    <link href="/static/css/base.css" rel="stylesheet"/>
    <link href="/static/css/page.css" rel="stylesheet"/>
    <link href="/static/css/themes.css" rel="stylesheet"/>
</head>
<body>
<div class="mui-content generate-plan">
    <!--还款详情头部-->
    <div class="wrap bg-w">
        <div class="dis-flex-be bor-bot">
            <div class="dis-flex">
                <p class="invalid-color f16">还款总金额(含手续费{{$order_pound}})</p>
                <p class="f24 space-up3 space-bot"><strong>{{$generation['generation_total']+$order_pound}}</strong></p>
            </div>
            <div class="dis-flex fc">
                <p class="invalid-color f16">消费笔数</p>
                <p class="f24 space-up3 space-bot"><strong>{{$generation['generation_count']}}</strong></p>
            </div>
        </div>
        <div>
            <p class="space-up2 f16">
                <span class="invalid-color">还款日期为:</span>
                <span class="blue-color-th">{{date('m月d日',strtotime($generation['generation_start']))}}
                    -{{date('m月d日',strtotime($generation['generation_end']))}}</span>
            </p>
            <p class="invalid-color space-up3 f16">订单号：{{$generation['generation_no']}}</p>
            <p class="invalid-color space-up3 f16">银行卡：{{$generation['card_bankname']}}
                ({{substr($generation['generation_card'], -4)}})</p>
            <p class="invalid-color space-up3 f16" onclick="javascript:window.location.reload();"><a>重新生成</a></p>
        </div>
    </div>
    <div class="space-up2" style="background-color:#f5f5f5;">
        <!--还款详情列表-->
        @if($city_list)
            <div class="dis-flex-be space-up2 select-city-con">
                <span>消费地区</span>
                <input class="select-city" type="text" placeholder="请选择" value="" id="site"/>
                <input type="hidden" name="city_code" value="">
                <input type="hidden" name="city_name" value="">
            </div>
        @else
            <input class="select-city" type="hidden" placeholder="请选择" value="" id="site"/>
            <input type="hidden" name="city_code" value="">
            <input type="hidden" name="city_name" value="">
        @endif
        <input type="hidden" name="location_city" value="{{$location}}">
        <ul>
            @foreach($order as $key=>$list)
                <li class="space-up2">
                    <!-- 还款详情列表头 -->
                    <div class="dis-flex-be wrap bor-bot bg-blue-tit">
                        <p class="white-color">
                            <span class="iconfont icon-jihua f16"></span>
                            <span class="f14">还款￥{{$list['get']}} / 消费:￥{{$list['pay']}}</span>
                        </p>
                        <p class="white-color f-tex-l f14"><span>{{$key}}</span></p>
                    </div>
                    @foreach($list as $v)
                        @if(isset($v['order_member']))
                            <div class="wrap2 bg-w">
                                <!-- 还款 -->

                                @if($v['order_type']==1)
                                    <div class="dis-flex-be wrap-bt bor-bot">
                                        <p class="f15">
                                            <span>消费</span>
                                            <span><strong>(￥{{$v['order_money']}})</strong></span>
                                            <span class="bor-frame space-lr2"
                                                  style="border:0px;">{{isset($v['order_product_name'])?mb_substr($v['order_product_name'],0, 4):'无法选择'}}</span>
                                        </p>
                                        <p class="f16 yellow-color">
                                            <span class="normal-color space-lr2">{{date('H:i',strtotime($v['order_time']))}}</span>
                                        </p>
                                    </div>
                                @elseif($v['order_type']==2)
                                <!-- 还款 -->
                                    <div class="dis-flex-be wrap-bt bor-bot">
                                        <p class="f15">
                                            <span>还款</span>
                                            <span><strong>(￥{{$v['order_real_get']}})</strong></span>
                                            <!-- <span class="bor-frame space-lr2">超市百货</span> -->
                                        </p>
                                        <p class="f16 yellow-color">
                                            <span class="normal-color space-lr2">{{date('H:i',strtotime($v['order_time']))}}</span>
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </li>
            @endforeach
        </ul>
    </div>
</div>
<nav class="mui-bar mui-bar-tab my-footer">
    <a class="my-btn-blue2 f18">下一步</a>
</nav>
<script src="/static/js/mui.min.js"></script>
<script src="/static/js/mui.picker.js"></script>
<script src="/static/js/mui.poppicker.js"></script>
<script src="/static/js/jquery-2.1.4.min.js"></script>
<script src="/static/js/city.data.js" type="text/javascript" charset="utf-8"></script>
<script src="/static/js/city.data-3.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
    mui.init();
    mui.ready(function () {
        function entityToString(entity) {
            var div = document.createElement('div');
            div.innerHTML = entity;
            var res = div.innerText || div.textContent;
            // console.log(entity,'->',res);
            return res;
        }
        //定位市
        var location_city = $("input[name='location_city']").val();
        console.log(location_city);
        //选择省市区
        var city_picker = new mui.PopPicker({
            layer: 2
        });
        var city_json = "{{$city_list}}".replace(/&quot;/g, "\"");
        var city_list = entityToString(city_json);
        var city_list = eval('(' + city_list + ')');
        city_picker.setData(city_list);
        $("#site").on("tap", function () {
            city_picker.show(function (items) {
                if ((items[0] || {}).text == undefined) {
                    (items[0] || {}).text = "";
                } else if ((items[1] || {}).text == undefined) {
                    (items[1] || {}).text = "";
                }
                //该ID为接收城市ID字段
                $("#site").val((items[0] || {}).text + "-" + (items[1] || {}).text);
                $("input[name='city_code']").val((items[1] || {}).value);
                $("input[name='city_name']").val((items[1] || {}).text);
            });

        });
        $(".my-btn-blue2").click(function () {
            var city_json = "{{$city_list}}";
            if (city_json) {
                var myCity = $("#site").val();
                if (!myCity) {
                    mui.toast('请选择消费地区');
                }
            }
            var city_code = $("input[name='city_code']").val();
            var city_name = $("input[name='city_name']").val();
            console.log(city_code);
            console.log(city_name);
            window.top.location.href = '/api/userurl/repayment_plan_confirm/uid/{{$uid}}/token/{{$token}}/id/{{$generation["generation_id"]}}/city_code/' + city_code + '/city_name/' + city_name;
        });
    });

</script>
</body>
</html>
