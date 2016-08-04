<!DOCTYPE html>
<html lang="zh_cn">
  <head>
    <meta charset="utf-8">
    <title>Belldandy - 登录</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Loading Bootstrap -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">

    <!-- Loading Flat UI -->
    <link href="/css/flat-ui.min.css" rel="stylesheet">
    <link href="/css/spinner.css" rel="stylesheet">
    <link href="/css/font.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
    <!--[if lt IE 9]>
      <script src="/js/vendor/html5shiv.js"></script>
      <script src="/js/vendor/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="container">
      <div class="row">
        <div class="col-md-4 col-md-offset-4">
          <div class="login-form" style="margin-top:50%;margin-left:auto;margin-right:auto;height:auto;padding-bottom:24px">
            <div class="form-group">
              <input type="text" class="form-control login-field" value="" placeholder="用户名" id="login-name" />
              <label class="login-field-icon fui-user" for="login-name"></label>
            </div>

            <div class="form-group">
              <input type="password" class="form-control login-field" value="" placeholder="密码" id="login-pass" />
              <label class="login-field-icon fui-lock" for="login-pass"></label>
            </div>

            <button class="btn btn-primary btn-lg btn-block" id="btn-submit">
                <p class="login-btn-text" style="margin:0 0 0 0;">登录</p>
                <div class="spinner" style="display:none;"><div class="double-bounce1"></div><div class="double-bounce2"></div></div>
            </button>
            <div class="alert alert-danger login-alert" role="alert" style="margin:20px 0 20px 0;display:none;">
                <small>
                    <strong class="alert-title"></strong>
                    <p class="alert-text"></p>
                </small>
            </div>
            <a class="login-link" href="/register">注册</a>
          </div>
        </div>
      </div>
    </div>


    <!-- /.container -->


    <!-- jQuery (necessary for Flat UI's JavaScript plugins) -->
    <script src="/js/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="/js/flat-ui.min.js"></script>

  </body>
  
  <script>
    $(function(){
        var Login=function(){
            $('.login-alert').hide();
            $('.login-btn-text').hide();
            $('.spinner').show();
            $('#btn-submit').attr('disabled','disabled');
            $.ajax({
                url: '/auth/login',
                type: 'POST',
                dataType: 'JSON',
                data: {
                    username: $('#login-name').val(),
                    password: $('#login-pass').val()
                },
                success: function(data) {
                    if (data.errcode==0)
                    {
                        window.location.href='/';
                    }
                    else
                    {
                        $('.alert-title').text('登录失败！');
                        $('.alert-text').text(data.errmsg);
                        $('.login-alert').show();
                        $('.login-btn-text').show();
                        $('.spinner').hide();
                        $('#btn-submit').removeAttr('disabled');
                    }
                },
                error: function(jqXHR) {
                    $('.alert-title').text('发生错误。');
                    $('.alert-text').text(jqXHR.status);
                    $('.login-alert').show();
                    $('.login-btn-text').show();
                    $('.spinner').hide();
                    $('#btn-submit').removeAttr('disabled');
                }
            });
        };
        
        
        $('.login-form').keydown(function(event){
            if (event.keyCode==13)
                Login();
        });
        $('#btn-submit').click(function(){
            Login();
        });
    });
    
  </script>
</html>
