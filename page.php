<?php if (!defined('__TYPECHO_ROOT_DIR__')) { exit; } ?>
<?php $this->need('header.php'); ?>
<main class="mian-layout is-post">
    <div class="post">
        <div class="post-meta">
            <h1 class="title"><?php $this->title(); ?></h1>
            <div class="other-meta"><span class="meta date"><i class="iconfont icon-date"></i><?php $this->date('Y-m-d'); ?></span><span class="update meta"><i class="iconfont icon-time"></i><?php echo date('Y-m-d', (int) $this->modified); ?></span><a class="chat meta hover" href="#main-comment"><i class="iconfont icon-chat"></i><span><?php $this->commentsNum('0', '1', '%d'); ?></span></a></div>
        </div>
        <div class="post-content">
            <article class="post-article s-card">
                <?php ob_start(); $this->content(); $pageContent = ob_get_clean(); $curveRenderedContent = curve_render_markdown($pageContent); if (strpos($curveRenderedContent, 'math-inline') !== false || strpos($curveRenderedContent, 'math-block') !== false): ?><script>window.MathJax={tex:{inlineMath:[['\\(','\\)'],['$','$']],displayMath:[['\\[','\\]'],['$$','$$']]},options:{skipHtmlTags:['script','noscript','style','textarea','pre','code']}};</script><script defer src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script><?php endif; ?><div id="page-content" class="markdown-main-style"><?php echo $curveRenderedContent; ?></div>
                <?php $this->need('comments.php'); ?>
            </article>
            <?php $this->need('sidebar.php'); ?>
        </div>
    </div>
</main>
<?php $this->need('footer.php'); ?>
