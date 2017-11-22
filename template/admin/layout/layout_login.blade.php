<!DOCTYPE html>
<html lang="en">
  <head>
    @include('admin/widget/head')
  </head>
  <body class="login-container">
  <section class="container-fluid">
    <!--[if lt IE 8]>
        <div class="alert alert-danger">您正在使用 <strong>过时的</strong> 浏览器. 是时候 <a href="http://browsehappy.com/">更换一个更好的浏览器</a> 来提升用户体验.</div>
    <![endif]-->
      <!--main content start-->
        <section class="wrapper">
            <!--信息提示-->
            <div class="panel">
              <div class="panel-body">
                @if (isset($jump_msg))
                  @component('admin/widget/message')
                  @slot('type')
                      {{ $jump_msg['type'] }}
                  @endslot
                    {{ $jump_msg['msg'] }}
                  @endcomponent
                @endif
                <!--信息提示-->
                @yield('wrapper')
             </div>
           </div>
        </section>
      </section>
      <!--main content end-->
    <footer>
      @include('admin/widget/footer')
    </footer>
  </body>
</html>
