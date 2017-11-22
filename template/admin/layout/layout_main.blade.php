<!DOCTYPE html>
<html lang="en">
  <head>
    @include('admin/widget/head')
  </head>
  <body class="main-container animated fadeInLeft" style="height: 100%">
    <!--topnav start-->
        @include('admin/widget/top')
    <!--topnav end-->
  <section class="container-fluid">
    <!--[if lt IE 8]>
        <div class="alert alert-danger">您正在使用 <strong>过时的</strong> 浏览器. 是时候 <a href="http://browsehappy.com/">更换一个更好的浏览器</a> 来提升用户体验.</div>
    <![endif]-->
      <!--sidebar start-->
      <aside class="col-xs-2	col-sm-2	col-md-2	col-lg-2">
        @include('admin/widget/aside')
      </aside>
      <!--sidebar end-->
      <!--main content start-->
      <section class="col-xs-10	col-sm-10	col-md-10	col-lg-10">
        <div class="col-xs-3	col-sm-3	col-md-3	col-lg-3">
          @component('admin/widget/breadcrumb')
            @if(isset($crumbs['first']))
              @slot('crumbsFirstText')  {{$crumbs['first']['title']}} @endslot
              @slot('crumbsFirstActi')  {{$crumbs['first']['action']}} @endslot
              @slot('crumbsSecendText')  {{$crumbs['secend']['title'] or ''}} @endslot
              @slot('crumbsSecendActi')  {{$crumbs['secend']['action'] or ''}} @endslot
            @endif
          @endcomponent
        </div>
        <div class="col-xs-9	col-sm-9	col-md-9	col-lg-9 text-right">
          @if(isset($button) && count($button)==count($button, 1))
            @component('admin/widget/right_button')
                @slot('text')  {{ $button['text'] }} @endslot
                @slot('link')  {{ $button['link'] or '#' }} @endslot
                @slot('icon') {{ $button['icon'] or 'plus' }} @endslot
                @slot('remote')  {{ $button['remote'] or '' }} @endslot
                @slot('size') {{ $button['size'] or 'lg' }} @endslot
                @slot('modal') {{ $button['modal'] or '' }} @endslot
                @slot('theme') {{ $button['theme'] or 'primary' }} @endslot
                @slot('customer') {{ $button['customer'] or '' }} @endslot
            @endcomponent
          @elseif(isset($button))
            @foreach($button as $key=>$val)
                @component('admin/widget/right_button')
                    @slot('text')  {{ $val['text'] }} @endslot
                    @slot('link')  {{ $val['link'] or '#' }} @endslot
                    @slot('icon') {{ $val['icon'] or 'plus' }} @endslot
                    @slot('remote')  {{ $val['remote'] or '' }} @endslot
                    @slot('size') {{ $val['size'] or 'lg' }} @endslot
                    @slot('modal') {{ $val['modal'] or '' }} @endslot
                    @slot('theme') {{ $val['theme'] or 'primary' }} @endslot
                    @slot('customer') {{ $val['customer'] or '' }} @endslot
                @endcomponent
            @endforeach
          @endif
        </div>
        <div class="panel col-xs-12	col-sm-12	col-md-12	col-lg-12" id="print_area">
          <div class="panel-body">
            <section class="wrapper">
                <!--信息提示-->
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
            </section>
          </div>
        </div>
      </section>
      <!--main content end-->
  </section>
    <footer>
      @include('admin/widget/footer')
    </footer>
  </body>
</html>
