<header>
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">导航切换</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/">Yggdrasill</a>
            </div>
            <div class="navbar-collapse collapse navbar-ex1-collapse" aria-expanded="false">
                <ul class="nav navbar-nav">
                    <li><a href="/tutorial">使用说明</a></li>
                    <?php if (isset($username) && $username != ''): ?>
                    <li><a href="/rooms">房间</a></li>
                    <?php endif; ?>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <?php if (isset($username) && $username != ''): ?>
                        <li><a href="/my"><span class="glyphicon glyphicon-user"
                                                aria-hidden="true"></span> <?= $username ?></a></li>
                        <li><a href="/auth/logout">退出</a></li>
                    <?php else: ?>
                        <li><a href="/login">登录</a></li>
                        <li><a href="/register">注册</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>