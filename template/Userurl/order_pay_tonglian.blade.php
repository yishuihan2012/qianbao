<!doctype html>
<html>
<head>
    <!-- 荣邦申请快捷支付 ishtml=2 时调用本页面 填入验证码调用确认接口 -->
    <meta charset="UTF-8">
    <title>订单支付</title>
    <meta name="viewport"
          content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no"/>
    <link href="/static/css/mui.min.css" rel="stylesheet"/>
    <link href="/static/css/iconfont.css" rel="stylesheet"/>
    <link href="/static/css/base.css" rel="stylesheet"/>
    <link href="/static/css/page.css" rel="stylesheet"/>
    <link href="/static/css/themes.css" rel="stylesheet"/>
</head>
<body>
<header class="wrap bg-blue dis-flex-be white-color">
    <span></span>
    <span><strong>订单支付</strong></span>
    <img src="/static/images/order_pay.png" class="media-pic">
</header>
<div class="mui-content order-payment">
    <ul class="mui-table-view bg-color">
        <li class="mui-table-view-cell bg-w">
            持卡人：<span class="invalid-color">{{msubstr($creditcard['card_name'],0,1)}}*</span>
        </li>
        <li class="mui-table-view-cell bg-w bor-bot">
            金额：<span class="normal-color">{{$price}}</span>
        </li>
        <li class="mui-table-view-cell bg-w bor-bot">
            支付卡：<span class="normal-color">{{substrs($creditcard['card_bankno'],4)}}</span>
        </li>
        <li class="mui-table-view-cell bg-w bor-bot">
            手机号：<span class="normal-color">{{substrs($creditcard['card_phone'],3)}}</span>
        </li>
        <li class="mui-table-view-cell bg-w">
            验证码：
            <input type="text" placeholder="请输入验证码" name="smsCode" value="" class="my-code authcode"/>
            <input type="button" class="code-btn mui-pull-right" value="发送验证码" id="sendCode">
        </li>
    </ul>
</div>
<input type="hidden" id="trxid" value="">
<input type="hidden" id="thpinfo" value="">
<div id="loading" class="loading-box hid-load">
    <img src='/static/images/loading.gif'/>
</div>
<a class="my-confirm" id="regBtn">确认付款</a>
</div>
<script src="/static/js/mui.min.js"></script>
<script src="/static/js/mui.min.js"></script>
<script src="/static/js/jquery-2.1.4.min.js"></script>
<script src="/static/js/common.js"></script>
<script type="text/javascript">
    mui.init()
</script>
<script>
    $(function () {
        // 发送验证码
        var InterValObj; //timer变量，控制时间
        var count = 60; //间隔函数，1秒执行
        var curCount;//当前剩余秒数
        var sendFlag = true;
        var trxid = '';
        var thpinfo = '';
        $("#sendCode").click(function () {
            curCount = count;
            //设置button效果，开始计时
            $("#sendCode").attr("disabled", "true");
            $("#sendCode").val("" + curCount + "秒");
            InterValObj = window.setInterval(SetRemainTime, 1000); //启动计时器，1秒执行一次
            if (sendFlag) {
                var url = '/api/Userurl/applypay';
                var data = {
                    'memberId': "{{$memberId}}",
                    'price': "{{$price}}",
                    'tradeNo': "{{$tradeNo}}",
                    'agreeId': "{{$agreeid}}",
                    'passagewayId': "{{$passagewayId}}"
                };
                $.post(url, data, function (res) {
                    trxid = res.trxid;
                    thpinfo = res.thpinfo;
                    console.log(res.trxid);
                    $("#trxid").val(trxid);
                    $("#thpinfo").val(thpinfo);
                    if(res.trxstatus==1999){
                        mui.toast('发送验证码成功。');
                    }else{
                        mui.toast(res.errmsg);
                    } 
                })
            } else {
                console.log($('#trxid').val());
                var url = '/api/Userurl/paysms';
                var data = {
                    'memberId': "{{$memberId}}",
                    'trxid': trxid,
                    'agreeId': "{{$agreeid}}",
                    'passagewayId': "{{$passagewayId}}",
                    'thpinfo': thpinfo
                };
                console.log(data);
                $.post(url, data, function (res) {
                    mui.toast(res.retmsg);
                })
            }

            //timer处理函数
            function SetRemainTime() {
                if (curCount == 0) {
                    window.clearInterval(InterValObj);//停止计时器
                    $("#sendCode").removeAttr("disabled");//启用按钮
                    $("#sendCode").val("重新发送");
                    sendFlag = false;
                }
                else {
                    curCount--;
                    $("#sendCode").val("" + curCount + "秒");
                }
            }
        })
    })

</script>
<script type="text/javascript">
    $(function () {
        var u = navigator.userAgent;
        var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; //android终端
        var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
        var isClick = false;
        $("#regBtn").click(function () {
            var thpinfo = $('#thpinfo').val();
            var trxid = $('#trxid').val();
            var smsCode = $('.authcode').val();
            if (smsCode) {
                if (isClick) return;
                isClick = true;
                $("#regBtn").html('请稍后......');
                var url = '/api/Userurl/confirmpay';
                var data = {
                    'memberId': "{{$memberId}}",
                    'trxid': trxid,
                    'agreeId': "{{$agreeid}}",
                    'passagewayId': "{{$passagewayId}}",
                    'thpinfo': thpinfo,
                    'smscode': smsCode,
                };
                $.post(url, data, function (res) {
                    if (res.trxstatus == '0000') {
                        mui.toast("交易成功");
                        setTimeout(function () {
                            if (!isAndroid) {
                                window.webkit.messageHandlers.returnIndex.postMessage(1);
                            } else {
                                android.returnIndex();
                            }
                        }, 2000);
                    } else {
                        mui.toast("交易失败：" + res.errmsg);
                    }
                })
            } else {
                mui.toast('请输入验证码！');
            }
        })
    })
</script>
</body>
</html>