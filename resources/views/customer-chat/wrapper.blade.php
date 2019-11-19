<!-- Customer chat plugin START -->
@if ($injectSdk === true)
<script type="text/javascript">
window.fbAsyncInit = function() {
    FB.init({
        appId            : '{{ $appId }}',
        autoLogAppEvents : {{ $sdk['autoLogAppEvents'] }},
        xfbml            : {{ $sdk['xfbml'] }},
        version          : '{{ $sdk['graph_version'] }}'
    });
}
</script>
<script type="text/javascript">
(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "https://connect.facebook.net/{{ $locale }}/sdk/xfbml.customerchat.js";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
</script>
@endif
<div class="fb-customerchat" page_id="{{$pageId}}" {!! implode(' ', $attributes) !!}></div>
<!-- Customer chat plugin END -->
