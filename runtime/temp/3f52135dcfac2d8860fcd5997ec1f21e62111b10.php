<?php $__env->startSection('title','控制面板~'); ?>
<?php $__env->startSection('wrapper'); ?>
<script type="text/javascript">
$(document).ready(function(){
    $('.menu .nav .active').removeClass('active');
    $('.menu .nav li.dashboard').addClass('active');
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin/layout/layout_main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>