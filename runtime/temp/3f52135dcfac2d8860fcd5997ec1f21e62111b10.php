<?php $__env->startSection('title','控制面板~'); ?>
<?php $__env->startSection('wrapper'); ?>
<section>
<hr/>
<div class="list">
  <header>
    <h3><i class="icon-list-ul"></i>控制面板 <small>详情</small></h3>
  </header>
  <div class="items items-hover">
      <div class="row">
           <div class="col-sm-6" id="memberData" style="width:100%; height: 400px;"></div>
           <script type="text/javascript">
                // 基于准备好的dom，初始化echarts实例
                var myChart = echarts.init(document.getElementById('memberData'));
                option = {
                     title : {
                           text: "会员统计数据,总会员数：<?php echo e($data['count']); ?>",
                           x:'center',
                           top: '15'
                     },
                     tooltip: {
                           trigger: 'item',
                           formatter: "{a} <br/>{b}: {c} ({d}%)"
                     },
                     series: [{
                           name:'会员信息',
                           type:'pie',
                           selectedMode: 'single',
                           radius: [0, '35%'],
                           label: {
                                normal: {
                                     position: 'inner'
                                }
                           },
                           labelLine: {
                                normal: {
                                     show: false
                                }
                           },
                           data:[
                                <?php $__currentLoopData = $membergrouplist; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                {value:<?php echo e($group['membergroupcount']); ?>, name:"<?php echo e($group['group_name']); ?>"},
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                           ]
                     },
                     {
                     name:'会员信息',
                     type:'pie',
                     radius: ['40%', '55%'],
                     label: {
                           normal: {
                                formatter: '{a|{a}}{abg|}\n{hr|}\n  {b|{b}：}{c}  {per|{d}%}  ',
                                backgroundColor: '#eee',
                                borderColor: '#aaa',
                                borderWidth: 1,
                                borderRadius: 4,
                           rich: {
                                a: {
                                     color: '#999',
                                     lineHeight: 22,
                                     align: 'center'
                                },
                                hr: {
                                     borderColor: '#aaa',
                                     width: '100%',
                                     borderWidth: 0.5,
                                     height: 0
                                },
                                b: {
                                     fontSize: 16,
                                     lineHeight: 33
                                },
                                per: {
                                     color: '#eee',
                                     backgroundColor: '#334455',
                                     padding: [2, 4],
                                     borderRadius: 2
                                }
                           }
                     }
                },
                data:[
                     {value:<?php echo e($data['cert']); ?>, name:'实名认证人数'},
                     {value:<?php echo e($data['Todaycount']); ?>, name:'今日注册会员'},
                ]
           }
      ]
};
                    // 使用刚指定的配置项和数据显示图表。
                    myChart.setOption(option);
                </script>
            <div class="col-sm-6">
              
            </div>
      </div>


    <div class="z-card">
      用户总数:
      <span><?php echo e($data['count']); ?></span>
    </div>
    <div class="z-card">
      今日用户增量:
      <span><?php echo e($data['Todaycount']); ?></span>
    </div>
  </div>
  <div class="items items-hover">
    <div class="z-card">
      交易总次数:
      <span><?php echo e($data['CashOrdercount']); ?></span>
    </div>
    <div class="z-card">
      还款计划总数:
      <span><?php echo e($data['GenerationOrdercount']); ?></span>
    </div>
    <div class="z-card">
      交易总金额:
      <span><?php echo e($data['CashOrderSum']); ?></span>
    </div>
    <div class="z-card">
      还款计划总金额:
      <span><?php echo e($data['GenerationOrderSum']); ?></span>
    </div>
    
  </div>
  <div class="items items-hover">
    <?php $__currentLoopData = $membergrouplist; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
    <div class="z-card">
      <?php echo e($value['group_name']); ?>总数:
      <span><?php echo e($value['membergroupcount']); ?></span>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
  </div>
</div>
 <blockquote> 
  通道使用详情(仅展示已开启的)
 </blockquote>
<div class="list">
    <?php $__currentLoopData = $passway; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
  <div class="items items-hover">
    <div class="z-card">
      <?php echo e($v['passageway_name']); ?>用量统计
      <span></span>
    </div>
    <div class="z-card">
      今日:
      <span><?php echo e($v['todaysum']); ?></span>
    </div>
    <div class="z-card">
      昨日:
      <span><?php echo e($v['yesterdaysum']); ?></span>
    </div>
    <div class="z-card">
      本周:
      <span><?php echo e($v['weeksum']); ?></span>
    </div>
    <div class="z-card">
      本月:
      <span><?php echo e($v['allsum']); ?></span>
    </div>
  </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
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
    height: 40px;
    border-radius: 4px;
    background-color: #3280fc;
    text-align: center;
    line-height: 40px;
    font-size: 1.4rem;
    color: white;
    /*float: left;*/
    margin: 2px;
    display: inline-block;
  }
  .z-card span{
    font-size: 1.8rem;
    font-weight: 800;
  }
</style>
<?php echo $__env->make('admin/layout/layout_main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>