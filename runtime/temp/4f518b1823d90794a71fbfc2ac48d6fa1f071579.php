<nav class="menu" data-ride="menu">
    <a class="btn btn-primary" href="#"><i class="icon icon-edit"></i> 新增项目</a>
    <a class="btn" href="#"><i class="icon icon-cloud-upload"></i> 轮播图上传</a>
    <ul class="nav nav-primary">

        <li class="dashboard"><a href="#"><i class="0icon icon-dashboard"></i> 控制面板</a></li>
        <li class="nav-parent article-manager">
            <a href="#"><i class="0icon icon-list"></i> 文章管理</a>
            <ul class="nav">
                <li class="articles"><a href="<?php echo e(url('/index/article/index')); ?>"> 文章列表</a></li>
                <li class="articles_category"><a href="#"> 分类管理</a></li>
            </ul>
        </li>

        <li class="nav-parent member-manager">
            <a href="javascript:;"><i class="0icon icon-user"></i> 会员管理</a>
            <ul class="nav">
                <li class="member"><a href="#"> 会员管理</a></li>
                <li class="member_group"><a href="#"> 用户组管理</a></li>
            </ul>
        </li>

        <li class="adminster-manager nav-parent">
            <a href="#"><i class="0icon icon-user"></i> 管 理 员</a>
            <ul class="nav">
                <li class="adminster"><a href="#">管理员管理</a></li>
                <li class="auth_group"><a href="#">用户组管理</a></li>
            </ul>
        </li>

        <li class="nav-parent system-setting">
            <a href="javascript:;"><i class="icon icon-cog"></i> 系统管理</a>
            <ul class="nav">
                <li class="setting"><a href="#"><i class="icon icon-sliders"></i> 参数设置</a></li>


            </ul>
        </li>
    </ul>
</nav>
