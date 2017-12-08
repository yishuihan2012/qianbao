 <!--dialog Title-->
 <div class="modal-header animated fadeInLeft">
	 <div class="row">
        	 <div class="col-sm-8"><h4>通道对会员组税率调整</h4></div>
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
 </div>

 <!--dialog Content-->
 <div class="modal-content animated fadeInLeft">
	 <form action="{{url('/index/Passageway/rate/id/'.$id)}}" method="post" class="form-horizontal" id="myform">
	 <h2></h2>
	 @foreach($group as $group)
	 <div class="row form-group">
		 <label for="bank_name" class="col-sm-4 text-right"><b>{{$group['group_name']}}:</b></label>
		 <div class="col-sm-4 input-group" id="bank_name">
		 	 <input type="number" class="form-control bank_name" name="group_{{$group['group_id']}}" placeholder="当前会员组此通道的税率百分比" 
		 	 @foreach($list as $lists) 
		 	 	@if($lists['item_group']==$group['group_id']) value="{{$lists['item_rate'] or '0.00'}}" @endif 
		 	 @endforeach >
		 	 <span class="input-group-addon">%</span>
		 </div>		
	 </div>
	 @endforeach
	 <h2></h2>
	 </form>
</div>

 <!--dialog Button-->
 <div class="modal-footer animated fadeInLeft">
	 <button type="button" class="btn btn-primary save">保存</button>
      <button type="button" class="btn" data-dismiss="modal">关闭</button>
 </div>

 <script>
 $(".save").click(function(){	
	 $("#myform").submit()
 })
</script>