  <script src="/js/jquery.min.js"></script>
  <script src="/js/flat-ui.min.js"></script>
  <script src="/js/angular.min.js"></script>
  <?php if (isset($js)): ?>
    <?php foreach ($js as $link): ?>
      <script src="<?=$link?>"></script>
    <?php endforeach; ?>
  <?php endif; ?>
  <script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
          (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-82044764-1', 'auto');
    ga('send', 'pageview');

  </script>
  </body>
</html>