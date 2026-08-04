<?php
/**
 * 友情链接
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) { exit; }

/* Typecho's $this->content() has already converted Markdown to HTML and
 * escapes the custom comment block. Read the original page source instead. */
$pageSource = isset($this->text) ? (string) $this->text : '';
$pageSource = preg_replace('/^<!--markdown-->/i', '', $pageSource);
$friendBlocks = array();
$friendTotal = 0;
$pageSource = preg_replace_callback('/<!--\s*curve-friends\b(.*?)-->/is', function ($match) use (&$friendBlocks, &$friendTotal) {
    $slot = 'CURVEFRIENDSSLOT' . count($friendBlocks);
    $groups = curve_parse_friend_markdown($match[1]);
    foreach ($groups as $group) {
        $friendTotal += count($group['typeList']);
    }
    $friendBlocks[$slot] = curve_render_friend_groups($groups);
    return "\n\n" . $slot . "\n\n";
}, $pageSource);
$pageHtml = curve_render_markdown($pageSource);
foreach ($friendBlocks as $slot => $html) {
    $pageHtml = str_replace('<p>' . $slot . '</p>', $html, $pageHtml);
    $pageHtml = str_replace($slot, $html, $pageHtml);
}
?>
<?php $this->need('header.php'); ?>
<main class="mian-layout">
    <div class="link">
        <div class="cat-or-tag">
            <div class="title">
                <h1 class="title-name">友情链接</h1>
                <span class="title-num">友链总览 · <?php echo $friendTotal; ?> 个友链</span>
            </div>
        </div>
        <div class="markdown-main-style">
            <?php echo $pageHtml; ?>
        </div>
    </div>
    <?php $this->need('comments.php'); ?>
</main>
<?php $this->need('footer.php'); ?>
