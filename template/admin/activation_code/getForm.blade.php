<form class="row" style="padding:10px 20px;" action="{{ url('ActivationCode/add') }}" method="post">
  <div class="form-group">
    <label for="code_count">生成数量</label>
    <input type="number" class="form-control" id="code_count" placeholder="生成数量" name="code_count">
  </div>
  <!-- <div class="form-group">
    <label for="code_dead_line">截止日期</label>
    <input type="text" class="form-control" id="code_dead_line" placeholder="截止日期">
  </div> -->
  <div class="form-group">
    <label for="code_prefix">激活码前缀(四位数字，选填,默认1000)</label>
    <input type="number" class="form-control" id="code_prefix" placeholder="激活码前缀" name="code_prefix" min="1000" max="9999">
  </div>
  <div class="form-group">
    <label for="code_prefix">激活码级别（用户组组别）</label>
    <select data-placeholder="选择用户组..." class="chosen-select form-control" tabindex="2" name="code_group">
      @foreach($group as $key=>$val)
        <option value="{{ $val['group_id'] }}" {{$key==0 ? 'selected' : ''}}>{{ $val['group_name'] }}</option>
      @endforeach
    </select>
  </div>
  <div class="form-group">
    <label for="code_for">所属代理</label>
    <select data-placeholder="选择代理商..." class="chosen-select form-control" tabindex="2" name="code_for">
      @foreach($adminster as $key=>$val)
        <option value="{{ $val['adminster_id'] }}" {{$key==0 ? 'selected' : ''}}>{{ $val['adminster_login'] }}</option>
      @endforeach
    </select>
    </div>
  <button type="submit" class="btn btn-primary">确认生成</button>
</form>
<script>
$(document).ready(function(){
  $('select.chosen-select').chosen({
    no_results_text: '没有找到',    // 当检索时没有找到匹配项时显示的提示文本
    disable_search_threshold: 3, // 10 个以下的选择项则不显示检索框
    search_contains: true         // 从任意位置开始检索
  });
});
</script>
<style type="text/css">
/*解决一个奇葩BUG 在本地测试没事 在服务器显示自动生成的select容器宽度为0*/
  .chosen-container-single-nosearch{
    width: 848px;
  }
</style>