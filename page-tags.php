<?php
/**
 * 全部标签
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) { exit; }
?>
<?php $this->need('header.php'); ?>
<main class="mian-layout"><div class="cat-or-tag"><div class="title"><h1 class="title-name">全部标签</h1><span class="title-num">标签总览</span></div><div class="type-lists"><?php $tags = $this->widget('Widget_Metas_Tag_Cloud', 'sort=count&desc=1&limit=100'); while ($tags->next()): ?><a href="<?php $tags->permalink(); ?>" class="type-item s-card"><i class="iconfont icon-hashtag"></i><span class="name"><?php $tags->name(); ?></span><span class="num"><?php $tags->count(); ?></span></a><?php endwhile; ?></div></div>
<?php $this->need('footer.php'); ?>
