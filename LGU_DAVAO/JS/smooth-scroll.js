//Smooth Anchor Scrolling
// Any anchor on that page with # tags will automatically trigger this smooth scroll rather than jumping instantly
    $(document).ready(function(){
      $('a.scroll').click(function() {
        if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'')
        && location.hostname == this.hostname) {
          var $target = $(this.hash);
          $target = $target.length && $target
          || $('[name=' + this.hash.slice(1) +']');
          if ($target.length) {
            var targetOffset = $target.offset().top;
            $('html,body')
            .animate({scrollTop: targetOffset}, 100);
           return false;
          }
        }
      });
    });