<?php

foreach ($content as $row)
{
    if ($row->type=='dice')
    {
        $row->chat_text='【投骰】 '.$row->chat_text.' = '.$row->dice_result;
        $row->type='chat';
    }
}

?>

<!DOCTYPE html>
<html >
  <head>
    <meta charset="UTF-8">
    <title><?=$group_name?> - Belldandy</title>
    <!-- Loading Flat UI -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/flat-ui.min.css" rel="stylesheet">
    <link href="/css/spinner.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
    <link href="/css/font.css" rel="stylesheet">
  </head>

  <body>
    <dl class="chat-thread" group-id="<?=$group_id?>" status="<?=$status?>">
        <?php foreach ($content as $row): ?>
        <?php if ($row->type=='chat'): ?>
            <dt uuid="<?=$row->uu_id?>"><?=htmlentities($row->name, ENT_QUOTES, 'UTF-8')?></dt>
            <dd><?=htmlentities($row->chat_text, ENT_QUOTES, 'UTF-8')?></dd>
        <?php endif; ?>
        <?php endforeach; ?>
    </dl>

    <?php if ($status=='ended' && !$is_mobile): ?>
  <?php $down_url="/episode/download/$group_id/$access_code"; ?>
  <style type="text/css">
    .footer {
    position:fixed;
    left:20px;
    bottom:20px;
    height:50px;
    width:calc(100% - 560px);
    z-index=-100;
    }
  </style>
  <div class="footer">
    <a class="btn btn-primary btn-lg btn-block" id="download-btn" style="width:100px" href="<?=$down_url?>">
      <span class="glyphicon glyphicon-cloud-download"></span>
      下载
    </a>
  </div>
  <?php endif; ?>
  
    <!-- jQuery (necessary for Flat UI's JavaScript plugins) -->
    <script src="/js/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="/js/flat-ui.min.js"></script>
    <script src="/js/view.js"></script>
  </body>
  <?php if ($status=='running'): ?>
  <?php endif; ?>
  
  
</html>
