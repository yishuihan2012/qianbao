 <link rel="stylesheet" href="/static/message/iziToast.min.css">
 <style>
 		.tips_success,.tips_warning,.tips_error{width:400px;}
 		.tips_success > .iziToast-body p{color:#329d38;}
 		.tips_success > .iziToast-body strong{color: #329d38; font-size:16px;}
 		.tips_warning > .iziToast-body p{color: #ed980f;}
 		.tips_warning > .iziToast-body strong{color: #ed980f; font-size:16px;}
 		.tips_error > .iziToast-body p{color: #e75033;}
 		.tips_error > .iziToast-body strong{color: #e75033; font-size:16px;}
 </style>
 <script>window.jQuery || document.write('<script src="/static/message/jquery-1.11.0.min.js"><\/script>')</script>
 <script src="/static/message/iziToast.min.js" type="text/javascript"></script>
 <script type="text/javascript">
 	iziToast.{{ $type }}({
	    title: '{{$type}}',
	    position: 'topCenter',
	    icon:"{{$type=='success' ? 'icon-ok-sign' : ($type=='warning' ? 'icon-frown' : 'icon-remove-sign')}}",
	    iconColor:"{{$type=='success' ? '#38b03f' : ($type=='warning' ? '#f1a325' : '#ea644a')}}",
	    transitionIn: 'bounceInLeft',
	    class: "tips_{{$type}}",
	    color: "{{$type=='success' ? '#ddf4df' : ($type=='warning' ? '#fff0d5' : '#ffe5e0')}}",
	    message: '{{ $slot }}',
	});
 </script>
