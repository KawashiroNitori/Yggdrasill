<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Belldandy</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Loading Bootstrap -->
        <link href="/css/bootstrap.min.css" rel="stylesheet">

        <!-- Loading Flat UI -->
        <link href="/css/flat-ui.min.css" rel="stylesheet">
        <link href="/css/spinner.css" rel="stylesheet">
        <link href="/css/font.css" rel="stylesheet">
        <link href="/css/main-page.css" rel="stylesheet">

        <!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
        <!--[if lt IE 9]>
          <script src="/js/vendor/html5shiv.js"></script>
          <script src="/js/vendor/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
      <header class="navbar navbar-static-top bs-docs-nav">
        <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
          <div class="container">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-01">
                <span class="sr-only">Toggle navigation</span>
              </button>
              <a class="navbar-brand" href="/">Belldandy</a>
            </div>
            <div class="collapse navbar-collapse" id="navbar-collapse-01">
              <form class="navbar-form navbar-right">
                <div class="dropdown" style="padding-top:4px;">
                  <a class="dropdown-toggle" data-toggle="dropdown" role="button" href="#"><span class="glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp;&nbsp;<?=$username?><span class="caret"></span></a>
                  <ul class="dropdown-menu">
                    <li><a href="/auth/logout">退出</a></li>
                  </ul>
                </div>
              </form>
            </div><!-- /.navbar-collapse -->
          </div>
        </nav><!-- /navbar -->
      </header>
      
      <div class="container">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title">群组列表&nbsp;&nbsp;&nbsp;&nbsp;<small>Group List</small></h3>
          </div>
          <div class="panel-body">
            <?php foreach($groupList as $group): ?>
                <div class="col-md-3">
                <a class="thumbnail group-btn" group-id="<?=$group->group_id?>" status="<?=$group->status?>" style="cursor:pointer;">
                    <p style="margin-left:5px;font-size:18px"><?=$group->name?></p>
                  <?php if ($group->status=='running'): ?>
                    <p class="group-status text-right small" style="margin:0 5px 0 0"><span class="green-light"></span>进行中</p>
                  <?php elseif ($group->status=='paused'): ?>
                    <p class="group-status text-right small" style="margin:0 5px 0 0"><span class="yellow-light"></span>已暂停</p>
                  <?php elseif ($group->status=='ended'): ?>
                    <p class="group-status text-right small" style="margin:0 5px 0 0"><span class="red-light"></span>已结束</p>
                  <?php endif; ?>
                </a>
                </div>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title">章节列表&nbsp;&nbsp;&nbsp;&nbsp;<small>Episode List</small></h3>
          </div>
          <div class="panel-body">
            <p class="no-select-tip">点按群组列表中的群组来显示章节。</p>
            <div class="alert alert-success now-running" style="display:none"></div>
            <div class="alert alert-warning now-paused" style="display:none"></div>
            <div class="episode-list table-responsive" style="display:none">
              <table class="table table-hover episode-table">
                <thead>
                  <tr>
                    <th class="text-right" style="width:5%">#</th>
                    <th style="width:65%">标题</th>
                    <th class="text-right" style="width:10%">开始时间</th>
                    <th style="width:10%">用时</th>
                    <th style="width:10%">下载</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
            <div class="loading-mask">
              <div class="spinner-big"><div class="double-bounce1" style="background-color:#666"></div><div class="double-bounce2" style="background-color:#666"></div></div>
              <p class="text-center">正在加载...</p>
            </div>
          </div>
        </div>
      </div>
    
      <!-- jQuery (necessary for Flat UI's JavaScript plugins) -->
      <script src="/js/jquery.min.js"></script>
      <!-- Include all compiled plugins (below), or include individual files as needed -->
      <script src="/js/flat-ui.min.js"></script>
      <script src="/js/moment.min.js"></script>
      <script src="/js/main-page.js"></script>
    </body>
    
</html>
