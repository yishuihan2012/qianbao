<?php $__env->startSection('title','控制面板~'); ?>
<?php $__env->startSection('wrapper'); ?>
<section>
<hr/>
<div class="list">
  <header>
    <h3><i class="icon-list-ul"></i>控制面板 <small>详情</small></h3>
  </header>
  <div class="items items-hover">
    <div class="z-card">
      当前用户总数:
      <span><?php echo e($data['count']); ?></span>
    </div>
    <div class="z-card">
      今日用户增量:
      <span><?php echo e($data['Todaycount']); ?></span>
    </div>
  </div>
  <div class="items items-hover">
    <div class="z-card">
      当前套现总量:
      <span><?php echo e($data['CashOrdercount']); ?></span>
    </div>
    <div class="z-card">
      当前还款总量:
      <span><?php echo e($data['Todaycount']); ?></span>
    </div>
    
  </div>
  <div class="items items-hover">
    <?php $__currentLoopData = $membergrouplist; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
    <div class="z-card">
      <?php echo e($value['group_name']); ?>总量:
      <span><?php echo e($value['membergroupcount']); ?></span>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
  </div>
</div>
</div>
</section>
<script type="text/javascript">
$(document).ready(function(){
    $('.menu .nav .active').removeClass('active');
    $('.menu .nav li.dashboard').addClass('active');
});
</script>
<?php $__env->stopSection(); ?>
<style type="text/css">
  .z-card{
    width: 200px;
    height: 80px;
    border-radius: 10px;
    background-color: #3280fc;
    text-align: center;
    line-height: 70px;
    font-size: 1.6rem;
    color: white;
    /*float: left;*/
    margin: 15px;
    display: inline-block;
  }
  .z-card span{
    font-size: 2rem;
    font-weight: 800;
  }
</style>
<?php echo $__env->make('admin/layout/layout_main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>