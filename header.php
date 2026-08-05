<?php if (!defined('__TYPECHO_ROOT_DIR__')) { exit; } ?>
<!doctype html>
<html lang="zh-CN" class="light">
<head>
    <meta charset="<?php $this->options->charset(); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?php $this->archiveTitle('', '', ' - '); ?><?php $this->options->title(); ?></title>
    <link rel="stylesheet" href="<?php $this->options->themeUrl('assets/css/curve.css'); ?>?v=<?php echo (int) @filemtime(__DIR__ . '/assets/css/curve.css'); ?>">
    <?php $accentColor = curve_accent_color($this->options); if ($accentColor !== ''): ?><style>:root{--main-color:<?php echo $accentColor; ?>;--main-color-bg:<?php echo $accentColor; ?>0d;}html.dark{--main-color:<?php echo $accentColor; ?>;--main-color-bg:<?php echo $accentColor; ?>23;}</style><?php endif; ?>
    <link rel="stylesheet" href="https://cdn.jsdmirror.com/gh/Minngc/lxgw-wenkai-webfonts@a676ddbd89161bdae9e1ace31d27cef0d5d6bb3d/lxgw-wenkai/bold/bold.css">
    <link rel="stylesheet" href="https://cdn2.codesign.qq.com/icons/g5ZpEgx3z4VO6j2/latest/iconfont.css">
    <link rel="alternate" type="application/rss+xml" title="RSS" href="<?php $this->options->feedUrl(); ?>">
    <?php $this->header(); ?>
</head>
<body>
<?php $logo = curve_option($this->options, 'logoUrl'); if ($logo !== '') { $loadingLogo = $logo; } else { ob_start(); $this->options->themeUrl('assets/images/logo.webp'); $loadingLogo = ob_get_clean(); } ?>
<?php
$archiveUrl = curve_page_url('page-archives.php');
$aboutUrl = curve_page_url('page-about.php');
$linksUrl = curve_page_url('page-links.php');
$categoriesUrl = curve_page_url('page-categories.php');
$tagsUrl = curve_page_url('page-tags.php');
$moreMenu = curve_top_left_menu_rows(curve_option($this->options, 'topLeftMenu'));
if (empty($moreMenu)) {
    $moreMenu = array(array('group' => '博客', 'name' => '主站', 'url' => $this->options->siteUrl, 'icon' => 'home', 'image' => true));
    if ($archiveUrl !== '') {
        $moreMenu[] = array('group' => '博客', 'name' => '文章归档', 'url' => $archiveUrl, 'icon' => 'article');
    }
}
$moreGroups = array();
foreach ($moreMenu as $moreItem) {
    if (!isset($moreGroups[$moreItem['group']])) {
        $moreGroups[$moreItem['group']] = array();
    }
    $moreGroups[$moreItem['group']][] = $moreItem;
}
?>
<div id="app" class="is-loading" aria-busy="true">
    <div class="background patterns light" data-background="patterns" aria-hidden="true">
        <img id="background-cover" class="cover" data-background-cover alt="background" hidden>
    </div>
    <div class="loading fade-enter-from" data-loading>
        <img src="<?php echo curve_esc($loadingLogo); ?>" class="logo" alt="loading-logo">
        <span class="tip">一直显示？点击任意区域即可关闭</span>
    </div>
    <header class="main-header">
        <nav class="main-nav top" data-main-nav>
            <div class="nav-all">
                <div class="left-nav">
                    <div class="more-menu nav-btn" title="更多内容" tabindex="0">
                        <i class="iconfont icon-menu"></i>
                        <div class="more-card s-card">
                            <?php foreach ($moreGroups as $groupName => $groupItems): ?><div class="more-item">
                                <span class="more-name"><?php echo curve_esc($groupName); ?></span>
                                <div class="more-list">
                                    <?php foreach ($groupItems as $moreItem): ?><a href="<?php echo curve_esc($moreItem['url']); ?>" class="more-link"><?php if (!empty($moreItem['image'])): ?><img class="link-icon" src="<?php echo curve_esc($logo ?: $this->options->siteUrl); ?>" alt="<?php echo curve_esc($moreItem['name']); ?>"><?php elseif (!empty($moreItem['iconUrl'])): ?><img class="link-icon" src="<?php echo curve_esc($moreItem['iconUrl']); ?>" alt="<?php echo curve_esc($moreItem['name']); ?>"><?php else: ?><i class="iconfont icon-<?php echo curve_esc($moreItem['icon']); ?> link-icon"></i><?php endif; ?><span class="link-name"><?php echo curve_esc($moreItem['name']); ?></span></a><?php endforeach; ?>
                                </div>
                            </div><?php endforeach; ?>
                        </div>
                    </div>
                    <a class="site-name" href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title(); ?></a>
                </div>
                <div class="nav-center">
                    <div class="site-menu">
                        <div class="menu-item"><span class="link-btn">文库</span><div class="link-child">
                            <?php if ($archiveUrl !== ''): ?><a class="link-child-btn" href="<?php echo curve_esc($archiveUrl); ?>"><i class="iconfont icon-article"></i>文章归档</a><?php endif; ?>
                            <?php if ($categoriesUrl !== ''): ?><a class="link-child-btn" href="<?php echo curve_esc($categoriesUrl); ?>"><i class="iconfont icon-folder"></i>全部分类</a><?php endif; ?>
                            <?php if ($tagsUrl !== ''): ?><a class="link-child-btn" href="<?php echo curve_esc($tagsUrl); ?>"><i class="iconfont icon-hashtag"></i>全部标签</a><?php endif; ?>
                        </div></div>
                        <div class="menu-item"><span class="link-btn">友链</span><div class="link-child">
                            <?php if ($linksUrl !== ''): ?><a class="link-child-btn" href="<?php echo curve_esc($linksUrl); ?>"><i class="iconfont icon-people"></i>友情链接</a><?php endif; ?>
                        </div></div>
                        <div class="menu-item"><span class="link-btn">我的</span><div class="link-child">
                            <?php if ($aboutUrl !== ''): ?><a class="link-child-btn" href="<?php echo curve_esc($aboutUrl); ?>"><i class="iconfont icon-contacts"></i>关于本站</a><?php endif; ?>
                        </div></div>
                    </div>
                    <span class="site-title" data-scroll-top><?php $this->is('index') ? $this->options->description() : $this->archiveTitle('', '', ''); ?></span>
                </div>
                <div class="right-nav">
                    <?php if (curve_is_enabled($this->options, 'travellingsEnable', true)): ?><a class="menu-btn nav-btn travellings" title="开往-友链接力" href="https://www.travellings.cn/go.html" target="_blank" rel="noopener"><i class="iconfont icon-subway"></i></a><?php endif; ?>
                    <button class="menu-btn nav-btn" title="随机前往一篇文章" data-random-post><i class="iconfont icon-shuffle"></i></button>
                    <button class="menu-btn nav-btn" title="全站搜索" data-search-open><i class="iconfont icon-search"></i></button>
                    <button id="open-control" class="menu-btn nav-btn pc" title="打开中控台" data-control-open><i class="iconfont icon-dashboard"></i></button>
                    <?php if ($this->user->hasLogin()): ?><a class="menu-btn nav-btn" title="进入后台" aria-label="进入后台" href="<?php $this->options->adminUrl(); ?>"><i class="iconfont icon-tools"></i></a><?php endif; ?>
                    <div class="to-top menu-btn hidden" title="返回顶部" data-scroll-top>
                        <div class="to-top-btn"><span class="num" data-scroll-percent>0</span><i class="iconfont icon-up"></i></div>
                    </div>
                    <button class="menu-btn nav-btn mobile" title="打开菜单" data-mobile-open><i class="iconfont icon-toc"></i></button>
                </div>
            </div>
        </nav>
        <div class="mobile-menu" hidden data-mobile-menu>
            <div class="menu-mask" data-mobile-close></div>
            <div class="menu-content s-card">
                <button class="close-control" data-mobile-close><i class="iconfont icon-close"></i></button>
                <div class="menu-list">
                    <div class="menu-item"><span class="link-title">文库</span><div class="link-child"><?php if ($archiveUrl !== ''): ?><a class="link-child-btn" href="<?php echo curve_esc($archiveUrl); ?>"><i class="iconfont icon-article"></i><span class="name">文章归档</span></a><?php endif; ?><?php if ($categoriesUrl !== ''): ?><a class="link-child-btn" href="<?php echo curve_esc($categoriesUrl); ?>"><i class="iconfont icon-folder"></i><span class="name">全部分类</span></a><?php endif; ?><?php if ($tagsUrl !== ''): ?><a class="link-child-btn" href="<?php echo curve_esc($tagsUrl); ?>"><i class="iconfont icon-hashtag"></i><span class="name">全部标签</span></a><?php endif; ?></div></div>
                    <div class="menu-item"><span class="link-title">友链</span><div class="link-child"><?php if ($linksUrl !== ''): ?><a class="link-child-btn" href="<?php echo curve_esc($linksUrl); ?>"><span class="name">友情链接</span></a><?php endif; ?></div></div>
                    <div class="menu-item"><span class="link-title">我的</span><div class="link-child"><?php if ($aboutUrl !== ''): ?><a class="link-child-btn" href="<?php echo curve_esc($aboutUrl); ?>"><span class="name">关于本站</span></a><?php endif; ?></div></div>
                </div>
            </div>
        </div>
        <div class="search modal" hidden data-search-modal>
            <div class="modal-mask" data-search-close></div>
            <div class="modal-main s-card" style="max-width:800px"><div class="title"><div class="title-left"><i class="iconfont icon-search"></i><span>全局搜索</span></div><button class="close" data-search-close><i class="iconfont icon-close"></i></button></div><div class="modal-content" style="--height:80vh"><form class="search-form" method="get" action="<?php $this->options->siteUrl(); ?>"><input name="s" type="search" placeholder="想要搜点什么" autofocus value="<?php echo $this->is('search') ? curve_esc($this->keywords) : ''; ?>"><button type="submit">搜索</button></form></div></div>
        </div>
    </header>
    <div class="control" hidden data-control>
        <div class="close-control" data-control-close><i class="iconfont icon-close"></i></div><div class="control-mask" data-control-close></div><div class="control-content"><div class="menu"><button class="menu-item open" data-theme-toggle title="当前：跟随系统，点击切换"><i class="iconfont icon-auto" data-theme-icon></i></button><button class="menu-item open" data-right-toggle title="右键菜单开关"><i class="iconfont icon-list"></i></button><button class="menu-item" data-blur-toggle title="背景模糊开关" aria-pressed="false"><i class="iconfont icon-blur"></i></button></div></div>
    </div>
    <div class="message" hidden data-message><div class="message-content"><span class="text"></span><button class="close" data-message-close><i class="iconfont icon-close"></i></button></div></div>
