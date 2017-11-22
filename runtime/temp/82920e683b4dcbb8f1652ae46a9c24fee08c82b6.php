 <!DOCTYPE html>
 <html lang="en">
 <head>
      <?php echo $__env->make('admin/widget/head', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
 </head>
 <body class="login-container">
 <section class="container-fluid">
      <!--[if lt IE 8]>
      <div class="alert alert-danger">
           您正在使用 <strong>过时的</strong> 浏览器. 是时候 <a href="http://browsehappy.com/">更换一个更好的浏览器</a> 来提升用户体验.</div>
      <![endif]-->
      <!--main content start-->
       <section class="wrapper">
           <!--信息提示-->
            <div class="panel">
                 <div class="panel-body">
                 <?php if(isset($jump_msg)): ?>
                      <?php echo $__env->startComponent('admin/widget/message'); ?>
                      <?php $__env->slot('type'); ?>
                           <?php echo e($jump_msg['type']); ?>

                      <?php $__env->endSlot(); ?>
                           <?php echo e($jump_msg['msg']); ?>

                      <?php echo $__env->renderComponent(); ?>
                 <?php endif; ?>
                 <!--信息提示-->
                 <?php echo $__env->yieldContent('wrapper'); ?>
                 </div>
           </div>
      </section>
 </section>
 <!--main content end-->
 <footer>
      <?php echo $__env->make('admin/widget/footer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
 </footer>
 </body>
</html>
