<form class="row" style="padding:10px 20px;" action="{{ url('ActivationCode/export') }}" method="post">
  <div class="form-group">
    <label for="code_count">起始id</label>
    <input type="number" class="form-control" id="code_count" placeholder="不填则从1开始" name="begin">
  </div>
  <div class="form-group">
    <label for="code_count">结尾id</label>
    <input type="number" class="form-control" id="code_count" placeholder="不填则导出到最后一个" name="end">
  </div>
  <button type="submit" class="btn btn-primary">导出</button>
</form>
<script>
$(document).ready(function(){
});
</script>
