<!DOCTYPE html>
<html lang="zh-cmn-Hans">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <title><?=$title?></title>
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/flat-ui.min.css" rel="stylesheet">
    <link href="/css/font.css" rel="stylesheet">
    <?php if (isset($css)): ?>
    <?php foreach ($css as $link): ?>
        <link href="<?=$link?>" rel="stylesheet">
    <?php endforeach; ?>
    <?php endif; ?>
    <script async src="/js/jquery.min.js"></script>
    <script async src="/js/flat-ui.min.js"></script>
    <script async src="/js/angular.min.js"></script>
    <?php if (isset($js)): ?>
      <?php foreach ($js as $link): ?>
        <script async src="<?=$link?>"></script>
      <?php endforeach; ?>
    <?php endif; ?>
  </head>
  <body>
