@extends('admin/layout/layout_main')
@section('title','控制面板~')
@section('wrapper')
<script type="text/javascript">
$(document).ready(function(){
    $('.menu .nav .active').removeClass('active');
    $('.menu .nav li.dashboard').addClass('active');
});
</script>
@endsection
