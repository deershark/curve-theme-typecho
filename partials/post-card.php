<?php
$cover = curve_post_cover($this, $this->options);
$layout = curve_option($this->options, 'coverLayout', 'both');
$isTop = curve_post_field($this, 'top') === '1';
?>
<div class="post-item s-card hover<?php echo $isTop ? ' is-top' : ''; ?><?php echo $cover !== '' ? ' cover cover-' . curve_esc($layout) : ''; ?>">
    <?php if ($cover !== ''): ?><a class="post-cover" href="<?php $this->permalink(); ?>"><img src="<?php echo curve_esc($cover); ?>" alt="<?php $this->titleAttribute(); ?>" loading="lazy"></a><?php endif; ?>
    <div class="post-content">
        <?php if (!empty($this->categories) || $isTop): ?><div class="post-category"><?php foreach ((array) $this->categories as $category): ?><a class="cat-name" href="<?php echo curve_esc($category['permalink']); ?>" data-category-filter><i class="iconfont icon-folder"></i><?php echo curve_esc($category['name']); ?></a><?php endforeach; ?><?php if ($isTop): ?><span class="top"><i class="iconfont icon-align-top"></i>置顶</span><?php endif; ?></div><?php endif; ?>
        <a class="post-title" data-post-link="<?php $this->permalink(); ?>" href="<?php $this->permalink(); ?>"><?php $this->title(); ?></a>
        <?php $excerpt = curve_excerpt($this); if ($excerpt !== ''): ?><span class="post-desc"><?php echo curve_esc($excerpt); ?></span><?php endif; ?>
        <div class="post-meta"><div class="post-tags"><?php foreach ((array) $this->tags as $tag): ?><span class="tags-name"><i class="iconfont icon-hashtag"></i><?php echo curve_esc($tag['name']); ?></span><?php endforeach; ?></div><span class="post-time"><?php $this->date('Y-m-d'); ?></span></div>
    </div>
</div>
