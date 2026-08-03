<?php if (!defined('__TYPECHO_ROOT_DIR__')) { exit; } ?>
<?php $this->need('header.php'); ?>
<?php $isTaxonomyArchive = $this->is('category') || $this->is('tag'); ?>
<?php $isSearchArchive = $this->is('search'); ?>
<?php $isCompactArchive = $isSearchArchive || $this->is('author') || $this->is('date'); ?>
<main class="mian-layout">
    <div class="home archive-page<?php echo $this->is('category') ? ' category-archive' : ''; ?><?php echo $this->is('tag') ? ' tag-archive' : ''; ?>">
        <?php if ($isCompactArchive): ?><div class="category-summary compact-archive-summary s-card"><div class="summary-top"><span class="summary-label"><?php echo $isSearchArchive ? '搜索结果' : ($this->is('author') ? '作者文章' : '日期归档'); ?></span><h1 class="summary-name"><?php $this->archiveTitle('', '', ''); ?></h1></div><span class="summary-count"><?php echo (int) $this->getTotal(); ?> 篇文章</span></div><?php elseif (!$isTaxonomyArchive): ?><div class="banner-page s-card"><div class="top"><div class="title"><span class="title-small">文章列表</span><span class="title-big"><?php $this->archiveTitle('', '', ''); ?></span></div></div><div class="footer"><div class="footer-left">记录值得分享的内容</div></div></div><?php else: ?><div class="category-summary s-card"><div class="summary-top"><span class="summary-label"><?php echo $this->is('category') ? '文章分类' : '文章标签'; ?></span><h1 class="summary-name"><?php $this->archiveTitle('', '', ''); ?></h1><?php $archiveDescription = method_exists($this, 'getArchiveDescription') ? trim((string) $this->getArchiveDescription()) : ''; if ($archiveDescription !== ''): ?><p class="summary-description"><?php echo curve_esc($archiveDescription); ?></p><?php endif; ?></div><span class="summary-count"><?php echo (int) $this->getTotal(); ?> 篇文章</span></div><?php endif; ?>
        <div class="home-content">
            <div class="posts-content">
                <div class="post-lists"><?php if ($this->have()): while ($this->next()): $this->need('partials/post-card.php'); endwhile; else: ?><div class="post-item s-card"><div class="post-content"><span class="post-title">没有找到内容</span></div></div><?php endif; ?></div>
                <div class="pagination"><?php curve_page_nav($this); ?></div>
            </div>
            <?php $this->need('sidebar.php'); ?>
        </div>
    </div>
<?php $this->need('footer.php'); ?>
