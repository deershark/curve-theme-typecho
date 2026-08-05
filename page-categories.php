<?php
/**
 * 全部分类
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) { exit; }
?>
<?php $this->need('header.php'); ?>
<?php $categories = $this->widget('Widget_Metas_Category_List'); $categoryTotal = 0; ob_start(); while ($categories->next()): $categoryTotal++; ?><a href="<?php $categories->permalink(); ?>" class="type-item s-card"><i class="iconfont icon-folder"></i><span class="name"><?php $categories->name(); ?></span><span class="num"><?php $categories->count(); ?></span></a><?php endwhile; $categoryList = ob_get_clean(); ?>
<main class="mian-layout"><div class="cat-or-tag"><div class="title"><h1 class="title-name"><?php $this->title(); ?></h1><span class="title-num">分类总览 · <?php echo $categoryTotal; ?> 个分类</span></div><div class="type-lists"><?php echo $categoryList; ?></div></div>
<?php $this->need('footer.php'); ?>
