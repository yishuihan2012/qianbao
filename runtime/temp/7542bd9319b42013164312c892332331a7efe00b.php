<nav class="navbar navbar-default" role="navigation">
    <div class="container-fluid">
        <!-- 导航头部 -->
        <div class="navbar-header">
            <!-- 移动设备上的导航切换按钮 -->
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse-example">
              <span class="sr-only">切换导航</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <!-- 品牌名称或logo -->
            <a class="navbar-brand" href="#"><?php echo e($title); ?></a>
        </div>

        <!-- 导航项目 -->
        <div class="collapse navbar-collapse">
            <!-- <ul class="nav navbar-nav">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">管理 <b class="caret"></b></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="#">任务</a></li>
                        <li><a href="#">任务</a></li>
                        <li><a href="#">任务</a></li>
                    </ul>
                </li>
            </ul> -->
            <!-- 右侧的导航项目 -->
            <ul class="nav navbar-nav navbar-right">
                <li><a href="#">红包审批</a></li>
                <li><a href="#">提现审批</a></li>
                <li><a href="<?php echo e(url('/index/index/help')); ?>">使用说明</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo e($name); ?> <b class="caret"></b></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="#">个人信息</a></li>
                        <li class="divider"></li>
                        <li><a href="<?php echo e(url('/index/login/logout')); ?>">登出</a></li>
                    </ul>
                </li>
            </ul>
        </div><!-- END .navbar-collapse -->
    </div>
</nav>
