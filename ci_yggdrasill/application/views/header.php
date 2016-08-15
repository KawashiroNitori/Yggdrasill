<!DOCTYPE html>
<html lang="zh-cmn-Hans">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?=$title?></title>
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/flat-ui.min.css" rel="stylesheet">
    <link href="/css/font.css" rel="stylesheet">
    <?php foreach ($css as $link): ?>
        <link href="<?=$link?>" rel="stylesheet">
    <?php endforeach; ?>
  </head>
  <body>
