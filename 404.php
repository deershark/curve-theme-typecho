<?php if (!defined('__TYPECHO_ROOT_DIR__')) { exit; } ?>
<?php $this->need('header.php'); ?>
<main class="mian-layout"><div class="not-found"><div class="not-found-content"><h1 class="title">404</h1><span class="title-tip">Page not found</span><a class="to-home" href="<?php $this->options->siteUrl(); ?>">回到主页</a></div></div>
<?php $this->need('footer.php'); ?>
