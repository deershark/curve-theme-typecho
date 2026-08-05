<?php
/**
 * 全部标签
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) { exit; }
?>
<?php $this->need('header.php'); ?>
<?php $tags = $this->widget('Widget_Metas_Tag_Cloud', 'sort=count&desc=1&limit=1000'); $tagTotal = 0; ob_start(); while ($tags->next()): $tagTotal++; ?><a href="<?php $tags->permalink(); ?>" class="type-item s-card"><i class="iconfont icon-hashtag"></i><span class="name"><?php $tags->name(); ?></span><span class="num"><?php $tags->count(); ?></span></a><?php endwhile; $tagList = ob_get_clean(); ?>
<main class="mian-layout"><div class="cat-or-tag"><div class="title"><h1 class="title-name"><?php $this->title(); ?></h1><span class="title-num">标签总览 · <?php echo $tagTotal; ?> 个标签</span></div><div class="type-lists"><?php echo $tagList; ?></div></div>
<?php $this->need('footer.php'); ?>
