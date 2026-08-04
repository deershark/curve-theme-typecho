<?php
/**
 * 关于本站
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) { exit; }

$pageSource = isset($this->text) ? (string) $this->text : '';
$pageSource = preg_replace('/^<!--markdown-->/i', '', $pageSource);
$hasAboutBlock = false;
$aboutResult = array('valid' => false, 'data' => array());
if (preg_match('/<!--\s*curve-about\b(.*?)-->/is', $pageSource, $aboutMatch)) {
    $hasAboutBlock = true;
    $aboutResult = curve_about_parse_markdown($aboutMatch[1]);
}

$aboutHelp = <<<'MARKDOWN'
# 关于本站配置

请在这个页面的正文中粘贴下面的特殊 Markdown 块，并按需修改内容。未写入的区块不会显示；已经写入的区块需要完整配置。如果格式错误或缺少必填项，页面会继续显示本教程。

```md
<!-- curve-about
## 介绍
问候语 | 你好，很高兴认识你👋
名称 | 無名
简介 | 是一名前端开发工程师、独立开发者、博主

## 追求
- 源于
- 热爱而去开发
- 优秀的作品

## 技能
- JavaScript | #f1e05a | javascript | https://developer.mozilla.org/zh-CN/docs/Web/JavaScript
- HTML5 | #e34f26 | html5 | https://developer.mozilla.org/zh-CN/docs/Web/HTML
- CSS3 | #563d7c | css3 | https://developer.mozilla.org/zh-CN/docs/Web/CSS
- Vue | #41b883 | vue | https://cn.vuejs.org/
- React | #149eca | react | https://react.dev/

## 生涯
标题 | 無限進步
图片 | https://pic.efefee.cn/uploads/2024/02/22/65d71db18bcf9.png
- ZZRVTC · 计算机应用技术 | #357ef5
- FE · 前端开发工程师 | #eb372a

## 性格
名称 | 物流师
类型 | ISTJ-A / ISTJ-T
图片 | https://pic.efefee.cn/uploads/2024/02/22/65d6bc7ae72ae.png
链接 | https://www.16personalities.com/ch/istj-%E4%BA%BA%E6%A0%BC

## 座右铭
- 脚踏实地，
- 一丝不苟。

## 关注偏好
标题 | 数码科技
说明 | 手机、电脑及软硬件
图片 | https://pic.efefee.cn/uploads/2024/02/27/65dd812567723.webp
颜色 | #0c0e20

## 音乐偏好
标题 | 欧美、华语流行、纯音乐、ACG
说明 | 一起欣赏更多音乐
图片 | https://pic.efefee.cn/uploads/2024/02/27/65dd836099d16.webp
颜色 | #7b3c25

## 数据
开启 | 1
图片 | https://pic.efefee.cn/uploads/2024/04/15/661c8fbf226d3.webp

## 信息
所在地 | 中国，河南省
所在地图片 | https://pic.efefee.cn/uploads/2024/04/15/661cbccc56af5.webp
出生年份 | 2001
当前职业 | 前端开发工程师

## 心路历程
标题 | 为什么建站？
段落 | 创建这个站的时候，想要就是能够有一个自己能够积累知识、积累兴趣的地方。和他人分享，会让这些成为积累和沉淀。
段落 | 这里大多都是技术向的文章，可能不太会有很多人看，权当是做个自我记录吧。
段落 | 这些就是创造这个小站的本意，也是我分享生活的方式。
-->
```

格式说明：技能为“名称 | 颜色 | 图标名 | 链接”，生涯为“内容 | 颜色”；颜色使用十六进制格式，图片和技能链接使用 HTTP(S) 地址；“数据”中的“开启”填写 `0` 可隐藏数据卡片。
MARKDOWN
?>
<?php $this->need('header.php'); ?>
<main class="mian-layout"><div class="about">
<h1 class="title">关于本站</h1>
<?php if (!$hasAboutBlock): ?>
    <div class="about-article markdown-main-style s-card"><?php echo curve_render_markdown($pageSource); ?></div>
<?php elseif (!$aboutResult['valid']): ?>
    <?php if (!empty($aboutResult['errors'])): ?><div class="about-config-errors s-card"><strong>关于本站配置有误，请按提示修改：</strong><p>下面的行号按 curve-about 配置块内部计算。</p><ul><?php foreach ($aboutResult['errors'] as $error): ?><li><?php echo curve_esc($error); ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <div class="about-config-help s-card markdown-main-style"><?php echo curve_render_markdown($aboutHelp); ?></div>
<?php else: ?>
<?php
$about = $aboutResult['data'];
$aboutStat = $this->widget('Widget_Stat');
$aboutStatRows = array(
    array('name' => '文章总数', 'value' => (int) $aboutStat->publishedPostsNum),
    array('name' => '评论总数', 'value' => (int) $aboutStat->publishedCommentsNum),
);
$aboutDays = curve_since_days($this->options);
if ($aboutDays !== null) {
    $aboutStatRows[] = array('name' => '建站天数', 'value' => $aboutDays);
}
$hasIntro = isset($about['sections']['intro']);
$hasPursuit = isset($about['sections']['pursuit']);
$hasSkills = isset($about['sections']['skills']);
$hasCareer = isset($about['sections']['career']);
$hasPersonality = isset($about['sections']['personality']);
$hasMotto = isset($about['sections']['motto']);
$hasInterest = isset($about['sections']['interest']);
$hasMusic = isset($about['sections']['music']);
$showStats = isset($about['sections']['stats']) && $about['stats']['enabled'] !== '0';
$hasInfo = isset($about['sections']['info']);
$hasStory = isset($about['sections']['story']);
?>
<?php if ($hasIntro || $hasPursuit): ?><div class="about-content" style="<?php echo $hasIntro && $hasPursuit ? 'grid-template-columns:3fr 2fr' : 'display:flex'; ?>">
    <?php if ($hasIntro): ?><div class="about-item hello"><span class="text1"><?php echo curve_esc($about['intro']['greeting']); ?></span><span class="text2 title2">我是 <?php echo curve_esc($about['intro']['name']); ?></span><span class="text3"><?php echo curve_esc($about['intro']['description']); ?></span></div><?php endif; ?>
    <?php if ($hasPursuit): ?><div class="about-item pursuit"><span class="tip">追求</span><?php foreach ($about['pursuit'] as $pursuitLine): ?><span class="title2"><?php echo curve_esc($pursuitLine); ?></span><?php endforeach; ?></div><?php endif; ?>
</div><?php endif; ?>
<?php if ($hasSkills || $hasCareer): ?><div class="about-content" style="<?php echo $hasSkills && $hasCareer ? 'grid-template-columns:2fr 3fr' : 'display:flex'; ?>">
    <?php if ($hasSkills): ?><div class="about-item skills"><span class="tip">技能</span><span class="title2">开启创造力</span><div class="skills-list"><?php foreach ($about['skills'] as $skill): ?><a class="skills-item" style="--color:<?php echo curve_esc($skill['color']); ?>" href="<?php echo curve_esc($skill['url']); ?>" target="_blank" rel="noopener"><div class="skills-logo"><i class="iconfont icon-<?php echo curve_esc($skill['icon']); ?>"></i></div><span class="skills-name"><?php echo curve_esc($skill['name']); ?></span></a><?php endforeach; ?></div></div><?php endif; ?>
    <?php if ($hasCareer): ?><div class="about-item career"><span class="tip">生涯</span><span class="title2"><i><?php echo curve_esc($about['career']['title']); ?></i></span><div class="list"><?php foreach ($about['career']['items'] as $career): ?><span class="list-item" style="--color:<?php echo curve_esc($career['color']); ?>"><?php echo curve_esc($career['text']); ?></span><?php endforeach; ?></div><img class="career-img" src="<?php echo curve_esc($about['career']['image']); ?>" alt="career"></div><?php endif; ?>
</div><?php endif; ?>
<?php if ($hasPersonality || $hasMotto): ?><div class="about-content" style="<?php echo $hasPersonality && $hasMotto ? 'grid-template-columns:3fr 2fr' : 'display:flex'; ?>">
    <?php if ($hasPersonality): ?><div class="about-item character" style="--color:#4298b4"><span class="tip">性格</span><span class="title2"><?php echo curve_esc($about['personality']['name']); ?></span><span class="title2" style="color:var(--color)"><?php echo curve_esc($about['personality']['type']); ?></span><span class="more">在 <a href="https://www.16personalities.com/ch/" target="_blank" rel="noopener">16personalities</a> 了解更多关于 <a href="<?php echo curve_esc($about['personality']['url']); ?>" target="_blank" rel="noopener"><?php echo curve_esc($about['personality']['name']); ?></a></span><img src="<?php echo curve_esc($about['personality']['image']); ?>" alt="male" class="male"></div><?php endif; ?>
    <?php if ($hasMotto): ?><div class="about-item"><span class="tip">座右铭</span><span class="title1" style="margin-top:20px"><?php echo curve_esc($about['motto'][0]); ?></span><?php foreach (array_slice($about['motto'], 1) as $mottoLine): ?><span class="title2"><?php echo curve_esc($mottoLine); ?></span><?php endforeach; ?></div><?php endif; ?>
</div><?php endif; ?>
<?php if ($hasInterest || $hasMusic): ?><div class="about-content" style="<?php echo $hasInterest && $hasMusic ? 'grid-template-columns:1fr 1fr' : 'display:flex'; ?>">
    <?php if ($hasInterest): ?><div class="about-item like image" style="--color:<?php echo curve_esc($about['interest']['color']); ?>;background-image:url('<?php echo curve_esc($about['interest']['image']); ?>')"><div class="image-content"><span class="tip">关注偏好</span><span class="title2"><?php echo curve_esc($about['interest']['title']); ?></span><div class="image-desc"><span class="left"><?php echo curve_esc($about['interest']['description']); ?></span></div></div></div><?php endif; ?>
    <?php if ($hasMusic): ?><div class="about-item like image" style="--color:<?php echo curve_esc($about['music']['color']); ?>;background-image:url('<?php echo curve_esc($about['music']['image']); ?>')"><div class="image-content"><span class="tip">音乐偏好</span><span class="title2"><?php echo curve_esc($about['music']['title']); ?></span><div class="image-desc"><span class="left"><?php echo curve_esc($about['music']['description']); ?></span></div></div></div><?php endif; ?>
</div><?php endif; ?>
<?php if ($showStats || $hasInfo): ?><div class="about-content" style="<?php echo $showStats && $hasInfo ? 'grid-template-columns:2fr 3fr' : 'display:flex'; ?>">
    <?php if ($showStats): ?><div class="about-item static image" style="--color:#0f1114;background-image:url('<?php echo curve_esc($about['stats']['image']); ?>')"><div class="image-content"><span class="tip">数据</span><span class="title2">站点数据</span><div class="static-data"><?php foreach ($aboutStatRows as $statRow): ?><div class="static-item"><span class="static-name"><?php echo curve_esc($statRow['name']); ?></span><span class="static-num"><?php echo (int) $statRow['value']; ?></span></div><?php endforeach; ?></div><div class="image-desc opacity"><span class="left">统计信息来自本站</span></div></div></div><?php endif; ?>
    <?php if ($hasInfo): ?><div class="about-item child"><div class="about-item map image" style="background-image:url('<?php echo curve_esc($about['info']['locationImage']); ?>')"><span class="position">我现在住在 <strong><?php echo curve_esc($about['info']['location']); ?></strong></span></div><div class="about-item info"><div class="info-item"><span class="info-name">生于</span><span class="info-num" style="--color:#43a6c6"><?php echo curve_esc($about['info']['birthYear']); ?></span></div><div class="info-item"><span class="info-name">现在职业</span><span class="info-num" style="--color:#dfac46"><?php echo curve_esc($about['info']['occupation']); ?></span></div></div></div><?php endif; ?>
</div><?php endif; ?>
<?php if ($hasStory): ?><div class="about-content" style="display:flex"><div class="about-item"><span class="tip">心路历程</span><span class="title2"><?php echo curve_esc($about['story']['title']); ?></span><div class="text markdown-main-style"><?php echo curve_render_markdown(implode("\n\n", $about['story']['paragraphs'])); ?></div></div></div><?php endif; ?>
<?php endif; ?>
</div></main>
<?php $this->need('footer.php'); ?>
