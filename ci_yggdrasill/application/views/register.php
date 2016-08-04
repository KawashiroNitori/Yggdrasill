<!DOCTYPE html>
<html lang="zh_cn">
  <head>
    <meta charset="utf-8">
    <title>Belldandy - 注册</title>
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
              <input type="text" class="form-control login-field" value="" placeholder="用户名" id="reg-name" />
              <label class="login-field-icon fui-user" for="reg-name"></label>
            </div>

            <div class="form-group">
              <input type="password" class="form-control login-field" value="" placeholder="密码" id="reg-pass"/>
              <label class="login-field-icon fui-eye" for="reg-pass" id="show_password"></label>
            </div>
            
            <div class="form-group">
              <input type="text" class="form-control login-field" data-toggle="tooltip" data-placement="top" title="向机器人私聊 ! getcode 来获取注册码" value="" placeholder="注册码" id="reg-code" />
              <label class="login-field-icon fui-new" for="reg-code"></label>
            </div>

            <button class="btn btn-primary btn-lg btn-block" id="btn-submit">
              <p class="login-btn-text" style="margin:0 0 0 0;">注册</p>
              <div class="spinner" style="display:none;"><div class="double-bounce1"></div><div class="double-bounce2"></div></div>
            </button>
            <div class="alert alert-danger login-alert" role="alert" style="margin:20px 0 20px 0;display:none;">
              <small>
                <strong class="alert-title"></strong>
                <p class="alert-text"></p>
              </small>
            </div>
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
        var Register=function(){
            $('.login-alert').hide();
            if ($('#reg-name').val().length<4)
            {
                $('.alert-title').text('用户名长度过短！');
                $('.alert-text').text('至少需要4字符。');
                $('.login-alert').show();
                return;
            }
            else if ($('#reg-pass').val().length<6)
            {
                $('.alert-title').text('密码长度过短！');
                $('.alert-text').text('至少需要6字符。');
                $('.login-alert').show();
                return;
            }
            $('.login-btn-text').hide();
            $('.spinner').show();
            $('#btn-submit').attr('disabled','disabled');
            $.ajax({
                url: '/auth/register',
                type: 'POST',
                dataType: 'JSON',
                data: {
                    username:$('#reg-name').val(),
                    password:$('#reg-pass').val(),
                    reg_code:$('#reg-code').val()
                },
                success: function(data){
                    if (data.errcode==0)
                    {
                        window.location.href='/';
                    }
                    else
                    {
                        $('.alert-title').text('注册失败！');
                        $('.alert-text').text(data.errmsg);
                        $('.login-alert').show();
                        $('.login-btn-text').show();
                        $('.spinner').hide();
                        $('#btn-submit').removeAttr('disabled');
                    }
                },
                error: function(jqXHR){
                    $('.alert-title').text('发生错误。');
                    $('.alert-text').text(jqXHR.status);
                    $('.login-alert').show();
                    $('.login-btn-text').show();
                    $('.spinner').hide();
                    $('#btn-submit').removeAttr('disabled');
                }
            });
            
        };
        
        
        $('#reg-code').tooltip();
        
        $('.login-form').keydown(function(event){
            if (event.keyCode==13)
                Register();
        });
        
        $('#btn-submit').click(function(){
            Register();
        });
        
        $('#show_password').click(function(){
        if ($('#reg-pass').attr('type')=="password")
            $('#reg-pass').attr('type','text');
        else
            $('#reg-pass').attr('type','password');
        });
        
    });
    
  </script>
</html>
