<nav class="menu" data-ride="menu">
    <a class="btn btn-primary" href="#"><i class="icon icon-edit"></i> 新增项目</a>
    <a class="btn" href="#"><i class="icon icon-cloud-upload"></i> 轮播图上传</a>
    <ul class="nav nav-primary">

        <li class="dashboard"><a href="#"><i class="0icon icon-dashboard"></i> 控制面板</a></li>

        <li class="nav-parent article-manager">
            <a href="#"><i class="0icon icon-list"></i> 文章管理</a>
            <ul class="nav">
                <li class="articles"><a href="{{url('/index/article/index')}}"> 文章列表</a></li>
                <li class="articles_category"><a href="{{url('/index/article_category/index')}}"> 分类管理</a></li>
            </ul>
        </li>

        <li class="nav-parent bank-manager">
            <a href="javascript:;"><i class="icon icon-user"></i> 银行管理</a>
            <ul class="nav">
                <li class="bank_list"><a href="{{url('/index/bank/index')}}"><i class="icon icon-sliders"></i> 银行列表</a></li>
                <li class="bank_ident"><a href="{{url('/index/bank/ident')}}"><i class="icon icon-sliders"></i> 银行识别</a></li>
            </ul>
        </li>


        <li class="nav-parent member-manager">
            <a href="javascript:;"><i class="0icon icon-user"></i> 会员管理</a>
            <ul class="nav">
                <li class="member"><a href="{{url('/index/member/index')}}"> 会员管理</a></li>
                <li class="member_group"><a href="{{ url('/index/member_group/index') }}"> 用户组管理</a></li>
            </ul>
        </li>

        <li class="nav-parent passageway-manager">
            <a href="javascript:;"><i class="icon icon-user"></i> 通道管理</a>
            <ul class="nav">
                <li class="passageway"><a href="{{url('/index/passageway/index')}}"><i class="icon icon-sliders"></i> 通道列表</a></li>
            </ul>
        </li>

        <li class="nav-parent suggestion">
            <a href="javascript:;"><i class="icon icon-user"></i> 意见建议</a>
            <ul class="nav">
                <li class="suggestion_list"><a href="{{url('/index/suggestion/index')}}"><i class="icon icon-sliders"></i> 用户反馈</a></li>
            </ul>
        </li>

        <li class="nav-parent suggestion">
            <a href="javascript:;"><i class="icon icon-user"></i> 财务管理</a>
            <ul class="nav">
                <li class="suggestion_list"><a href="{{url('/index/suggestion/index')}}"><i class="icon icon-sliders"></i> 用户反馈</a></li>
            </ul>
        </li>


        <li class="adminster-manager nav-parent">
            <a href="{{ url('/index/adminster/index') }}"><i class="0icon icon-user"></i> 管 理 员</a>
            <ul class="nav">
                <li class="adminster"><a href="{{ url('/index/adminster/index') }}">管理员管理</a></li>
                <li class="auth_group"><a href="{{ url('/index/auth_group/index') }}">用户组管理</a></li>
            </ul>
        </li>

        <li class="nav-parent system-setting">
            <a href="javascript:;"><i class="icon icon-cog"></i> 系统管理</a>
            <ul class="nav">
                <li class="setting-basic"><a href="{{url('/index/system/basic')}}"><i class="icon icon-sliders"></i> 核心设置</a></li>
            </ul>
        </li>

    </ul>
</nav>
