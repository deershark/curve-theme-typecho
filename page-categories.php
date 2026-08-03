<?php
/**
 * 全部分类
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) { exit; }
?>
<?php $this->need('header.php'); ?>
<main class="mian-layout"><div class="cat-or-tag"><div class="title"><h1 class="title-name">全部分类</h1><span class="title-num">分类总览</span></div><div class="type-lists"><?php $categories = $this->widget('Widget_Metas_Category_List'); while ($categories->next()): ?><a href="<?php $categories->permalink(); ?>" class="type-item s-card"><i class="iconfont icon-folder"></i><span class="name"><?php $categories->name(); ?></span><span class="num"><?php $categories->count(); ?></span></a><?php endwhile; ?></div></div>
<?php $this->need('footer.php'); ?>
