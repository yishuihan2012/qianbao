<!--dialog Title-->
<style>
  .huise{color:#666;}
  .text-ellipsis{cursor: pointer;}
</style>
<div class="modal-header animated fadeInLeft">
    <div class="row">
        <div class="col-sm-8"><h4>{{$wallet->member->member_nick}}钱包日志</h4></div>
        <div class="col-sm-4">
            <div class="text-right">
                <span class="label label-dot"></span>
                <span class="label label-dot label-primary"></span>
                <span class="label label-dot label-success"></span>
                <span class="label label-dot label-info"></span>
                <span class="label label-dot label-warning"></span>
                <span class="label label-dot label-danger"></span>
            </div>
        </div>
    </div>
    <div class="help-block">(本页面为<strong><code>{{$wallet->member->member_nick}}</code></strong>的钱包日志记录)</div>
</div>
<!--dialog Content-->
<div class="modal-content animated fadeInLeft" style="max-height: 400px; overflow-x: hidden; overflow-y: auto;">
<h2></h2>

@foreach($WalletLog as $log)
<div class="row">
    <div class="col-sm-2 text-center"><strong>{{$log->log_add_time}}</strong></div>
    <div class="col-sm-2 text-center">
        @if($log->log_relation_type=='1')
        分润收益~
        @elseif($log->log_relation_type=='5')
        邀请红包~
        @elseif($log->log_relation_type=='2')
        分佣收益~
        @elseif($log->log_relation_type=='4')
        其他收益~        
        @endif
    </div>
    <div class="col-sm-2 huise"><i class="icon icon-{{$log->log_wallet_type=='1' ? 'plus' : 'minus' }}"></i>{{format_money($log->log_wallet_amount)}}</div>
    <div class="col-sm-4 text-ellipsis" title="{{$log->log_desc}}">{{$log->log_desc}}</div>
    <div class="col-sm-1 text-center"><i class="icon icon-check text-success"></i></div>
</div>
<hr/>
@endforeach
<h2></h2>
</div>

<!--dialog Button-->
<div class="modal-footer animated fadeInLeft">
    <button type="button" class="btn" data-dismiss="modal">关闭</button>
</div>