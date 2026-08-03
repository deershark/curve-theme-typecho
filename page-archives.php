<?php
/**
 * 文章归档
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) { exit; }
?>
<?php $this->need('header.php'); ?>
<main class="mian-layout"><div class="archives s-card"><div class="title"><h1 class="name">文章</h1><?php $stat = $this->widget('Widget_Stat'); ?><sup class="num"><?php echo (int) $stat->publishedPostsNum; ?></sup></div><div class="archives-list"><?php $this->widget('Widget_Contents_Post_Recent', 'pageSize=1000')->to($posts); $year = ''; if ($posts->have()): while ($posts->next()): $postYear = date('Y', (int) $posts->created); if ($postYear !== $year): if ($year !== ''): ?></div></div><?php endif; $year = $postYear; ?><div class="year-list"><span class="year"><?php echo $year; ?></span><div class="posts"><?php endif; ?><div class="posts-item s-card hover"><a class="title" href="<?php $posts->permalink(); ?>"><?php $posts->title(); ?></a><div class="tags"><?php $posts->tags(' ', true, ''); ?></div></div><?php endwhile; if ($year !== ''): ?></div></div><?php endif; else: ?><p>暂无文章</p><?php endif; ?></div></div>
<?php $this->need('footer.php'); ?>
