<?php
/**
 * 文章归档
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) { exit; }
?>
<?php $this->need('header.php'); ?>
<main class="mian-layout">
    <div class="home archive-page archives-page">
        <?php $stat = $this->widget('Widget_Stat'); ?>
        <div class="cat-or-tag archive-heading">
            <div class="title">
                <h1 class="title-name"><?php $this->title(); ?></h1>
                <span class="title-num">文章总览 · <?php echo (int) $stat->publishedPostsNum; ?> 篇文章</span>
            </div>
        </div>
        <div class="home-content">
            <div class="posts-content">
                <div class="archives s-card">
                    <div class="archives-list">
                        <?php $this->widget('Widget_Contents_Post_Recent', 'pageSize=1000')->to($posts); $year = ''; ?>
                        <?php if ($posts->have()): while ($posts->next()): ?>
                            <?php $postYear = date('Y', (int) $posts->created); ?>
                            <?php if ($postYear !== $year): ?>
                                <?php if ($year !== ''): ?></div></div><?php endif; ?>
                                <?php $year = $postYear; ?>
                                <div class="year-list">
                                    <span class="year"><?php echo curve_esc($year); ?></span>
                                    <div class="posts">
                            <?php endif; ?>
                            <div class="posts-item s-card hover">
                                <a class="title" href="<?php $posts->permalink(); ?>"><?php $posts->title(); ?></a>
                                <div class="tags">
                                    <?php foreach ((array) $posts->tags as $tag): ?>
                                        <?php $tagUrl = isset($tag['permalink']) ? (string) $tag['permalink'] : ''; $tagName = isset($tag['name']) ? (string) $tag['name'] : ''; if ($tagUrl !== '' && $tagName !== ''): ?>
                                            <a class="type-item" href="<?php echo curve_esc($tagUrl); ?>"><i class="iconfont icon-hashtag"></i><span class="name"><?php echo curve_esc($tagName); ?></span></a>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endwhile; if ($year !== ''): ?></div></div><?php endif; else: ?><p>暂无文章</p><?php endif; ?>
                    </div>
                </div>
            </div>
            <?php $this->need('sidebar.php'); ?>
        </div>
    </div>
<?php $this->need('footer.php'); ?>
