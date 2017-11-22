<!DOCTYPE html>
<html lang="en">
  <head>
    <?php echo $__env->make('admin/widget/head', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
  </head>
  <body class="main-container animated fadeInLeft" style="height: 100%">
    <!--topnav start-->
        <?php echo $__env->make('admin/widget/top', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <!--topnav end-->
  <section class="container-fluid">
    <!--[if lt IE 8]>
        <div class="alert alert-danger">您正在使用 <strong>过时的</strong> 浏览器. 是时候 <a href="http://browsehappy.com/">更换一个更好的浏览器</a> 来提升用户体验.</div>
    <![endif]-->
      <!--sidebar start-->
      <aside class="col-xs-2	col-sm-2	col-md-2	col-lg-2">
        <?php echo $__env->make('admin/widget/aside', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
      </aside>
      <!--sidebar end-->
      <!--main content start-->
      <section class="col-xs-10	col-sm-10	col-md-10	col-lg-10">
        <div class="col-xs-3	col-sm-3	col-md-3	col-lg-3">
          <?php echo $__env->startComponent('admin/widget/breadcrumb'); ?>
            <?php if(isset($crumbs['first'])): ?>
              <?php $__env->slot('crumbsFirstText'); ?>  <?php echo e($crumbs['first']['title']); ?> <?php $__env->endSlot(); ?>
              <?php $__env->slot('crumbsFirstActi'); ?>  <?php echo e($crumbs['first']['action']); ?> <?php $__env->endSlot(); ?>
              <?php $__env->slot('crumbsSecendText'); ?>  <?php echo e(isset($crumbs['secend']['title']) ? $crumbs['secend']['title'] : ''); ?> <?php $__env->endSlot(); ?>
              <?php $__env->slot('crumbsSecendActi'); ?>  <?php echo e(isset($crumbs['secend']['action']) ? $crumbs['secend']['action'] : ''); ?> <?php $__env->endSlot(); ?>
            <?php endif; ?>
          <?php echo $__env->renderComponent(); ?>
        </div>
        <div class="col-xs-9	col-sm-9	col-md-9	col-lg-9 text-right">
          <?php if(isset($button) && count($button)==count($button, 1)): ?>
            <?php echo $__env->startComponent('admin/widget/right_button'); ?>
                <?php $__env->slot('text'); ?>  <?php echo e($button['text']); ?> <?php $__env->endSlot(); ?>
                <?php $__env->slot('link'); ?>  <?php echo e(isset($button['link']) ? $button['link'] : '#'); ?> <?php $__env->endSlot(); ?>
                <?php $__env->slot('icon'); ?> <?php echo e(isset($button['icon']) ? $button['icon'] : 'plus'); ?> <?php $__env->endSlot(); ?>
                <?php $__env->slot('remote'); ?>  <?php echo e(isset($button['remote']) ? $button['remote'] : ''); ?> <?php $__env->endSlot(); ?>
                <?php $__env->slot('size'); ?> <?php echo e(isset($button['size']) ? $button['size'] : 'lg'); ?> <?php $__env->endSlot(); ?>
                <?php $__env->slot('modal'); ?> <?php echo e(isset($button['modal']) ? $button['modal'] : ''); ?> <?php $__env->endSlot(); ?>
                <?php $__env->slot('theme'); ?> <?php echo e(isset($button['theme']) ? $button['theme'] : 'primary'); ?> <?php $__env->endSlot(); ?>
                <?php $__env->slot('customer'); ?> <?php echo e(isset($button['customer']) ? $button['customer'] : ''); ?> <?php $__env->endSlot(); ?>
            <?php echo $__env->renderComponent(); ?>
          <?php elseif(isset($button)): ?>
            <?php $__currentLoopData = $button; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$val): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                <?php echo $__env->startComponent('admin/widget/right_button'); ?>
                    <?php $__env->slot('text'); ?>  <?php echo e($val['text']); ?> <?php $__env->endSlot(); ?>
                    <?php $__env->slot('link'); ?>  <?php echo e(isset($val['link']) ? $val['link'] : '#'); ?> <?php $__env->endSlot(); ?>
                    <?php $__env->slot('icon'); ?> <?php echo e(isset($val['icon']) ? $val['icon'] : 'plus'); ?> <?php $__env->endSlot(); ?>
                    <?php $__env->slot('remote'); ?>  <?php echo e(isset($val['remote']) ? $val['remote'] : ''); ?> <?php $__env->endSlot(); ?>
                    <?php $__env->slot('size'); ?> <?php echo e(isset($val['size']) ? $val['size'] : 'lg'); ?> <?php $__env->endSlot(); ?>
                    <?php $__env->slot('modal'); ?> <?php echo e(isset($val['modal']) ? $val['modal'] : ''); ?> <?php $__env->endSlot(); ?>
                    <?php $__env->slot('theme'); ?> <?php echo e(isset($val['theme']) ? $val['theme'] : 'primary'); ?> <?php $__env->endSlot(); ?>
                    <?php $__env->slot('customer'); ?> <?php echo e(isset($val['customer']) ? $val['customer'] : ''); ?> <?php $__env->endSlot(); ?>
                <?php echo $__env->renderComponent(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
          <?php endif; ?>
        </div>
        <div class="panel col-xs-12	col-sm-12	col-md-12	col-lg-12" id="print_area">
          <div class="panel-body">
            <section class="wrapper">
                <!--信息提示-->
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
            </section>
          </div>
        </div>
      </section>
      <!--main content end-->
  </section>
    <footer>
      <?php echo $__env->make('admin/widget/footer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </footer>
  </body>
</html>
