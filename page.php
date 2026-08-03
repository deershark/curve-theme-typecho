<?php if (!defined('__TYPECHO_ROOT_DIR__')) { exit; } ?>
<?php $this->need('header.php'); ?>
<main class="mian-layout">
    <div class="page">
        <div class="page-content"><div id="page-content" class="markdown-main-style s-card"><?php ob_start(); $this->content(); $pageContent = ob_get_clean(); echo curve_render_markdown($pageContent); ?></div><?php $this->need('comments.php'); ?></div>
        <?php $this->need('sidebar.php'); ?>
    </div>
<?php $this->need('footer.php'); ?>
