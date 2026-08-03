<?php
/**
 * Curve for Typecho
 *
 * @package Curve
 * @author Curve Typecho Contributors
 * @version 0.1.0
 * @link https://github.com/imsyy/vitepress-theme-curve
 */

if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

require_once __DIR__ . '/inc/helpers.php';

/** 判断是否为单个 Emoji（允许变体、肤色、国旗和 ZWJ 组合）。 */
function curve_is_single_emoji($value)
{
    $value = trim((string) $value);
    if ($value === '') return true;

    return preg_match('/^(?:\p{Regional_Indicator}{2}|(?:\p{Extended_Pictographic}(?:\x{FE0F}|\x{FE0E})?(?:\p{Emoji_Modifier})?(?:\x{200D}\p{Extended_Pictographic}(?:\x{FE0F}|\x{FE0E})?(?:\p{Emoji_Modifier})?)*)|(?:[0-9#*]\x{FE0F}?\x{20E3}))$/u', $value) === 1;
}

/** 侧栏社交链接只允许填写最多两个名称，每行一个，且不能包含分隔符。 */
function curve_validate_sidebar_social_links($value)
{
    $names = curve_lines($value);
    $seen = array();
    if (count($names) > 2) {
        return false;
    }
    foreach ($names as $name) {
        if (strpos($name, '|') !== false || strlen($name) > 100 || isset($seen[$name])) {
            return false;
        }
        $seen[$name] = true;
    }
    return true;
}

/** 主题后台设置。 */
function themeConfig($form)
{
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Text('siteAuthorName', null, 'Admin', _t('博主名称')));
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Text('siteAuthorLink', null, '', _t('博主链接')));
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Text('siteAuthorEmail', null, '', _t('博主邮箱')));
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Text('postSize', null, '8', _t('每页文章数'), _t('对应源主题 postSize 配置。')));
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Text(
        'logoUrl', null, '', _t('Logo 地址'), _t('留空时显示站点标题；可填写媒体库或外部图片地址。')
    ));
    $footerAvatarEmoji = new Typecho_Widget_Helper_Form_Element_Text(
        'footerAvatarEmoji', null, '', _t('页脚头像表情'), _t('显示在页脚头像右下角，例如：👋；留空则不显示。')
    );
    $footerAvatarEmoji->addRule('curve_is_single_emoji', _t('请输入一个有效的 Emoji，不要填写普通文字或多个 Emoji。'));
    $form->addInput($footerAvatarEmoji);
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Textarea(
        'footerAvatarMessage', null, '', _t('页脚头像悬浮文案'), _t('鼠标移到头像右下角表情时显示；需同时填写表情才会生效。')
    ));
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Textarea(
        'intro', null, '记录值得分享的技术、想法与生活。', _t('站点简介'), _t('显示在首页侧栏和页脚。')
    ));
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Text(
        'accentColor', null, '', _t('强调色'), _t('留空使用主题原色；填写三位或六位十六进制颜色，例如 #5b8def。')
    ));
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Select(
        'coverLayout', array('left' => _t('封面在左'), 'right' => _t('封面在右'), 'both' => _t('交替排列'), 'grid' => _t('双列卡片')), 'both', _t('文章列表封面布局')
    ));
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Textarea(
        'defaultCovers', null, '', _t('默认封面'), _t('一行一个图片地址；文章未设置 cover 时随机使用。')
    ));
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Text(
        'recordNumber', null, '', _t('备案号')
    ));
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Text(
        'since', null, '', _t('建站日期'), _t('格式：2020-07-28；用于页脚运行天数。')
    ));
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Textarea(
        'socialLinks', null, '', _t('社交链接'), _t('一行一个，格式：名称|链接。支持图标名称：GitHub、Email/邮箱、QQ、Telegram/TG、Twitter/X、Bilibili、Home/主页/首页；未匹配的名称使用通用链接图标。例如：GitHub|https://github.com/name')
    ));
    $sidebarSocialLinks = new Typecho_Widget_Helper_Form_Element_Textarea(
        'sidebarSocialLinks', null, '', _t('侧栏社交链接'), _t('填写“社交链接”中的名称，一行一个，最多两个；名称需完全一致。留空时显示社交链接中的前两个。')
    );
    $sidebarSocialLinks->addRule('curve_validate_sidebar_social_links', _t('请填写社交链接中的名称，每行一个，最多两个，且名称不能包含 |。'));
    $form->addInput($sidebarSocialLinks);
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Radio(
        'fakeGptEnable', array('1' => _t('开启'), '0' => _t('关闭')), '1', _t('FakeGPT 开关'), _t('控制文章中的 FakeGPT 摘要卡片是否显示。')
    ));
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Textarea(
        'fakeGptClickText', null, '你好，我是 FakeGPT：名字听起来很懂，实际上只负责把作者认真写好的摘要一个字一个字端上来。没有联网、没有偷看正文，也没有在后台煮咖啡；这段内容由作者亲自审核，放心食用。', _t('FakeGPT 点击文案'), _t('点击文章摘要卡片右上角的 FakeGPT 后显示的内容。')
    ));
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Textarea(
        'footerLinks', null, '', _t('页脚链接'), _t('一行一个，格式：名称|链接。')
    ));
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Textarea(
        'footerSitemap', null, '', _t('页脚分栏'), _t('一行一个，格式：栏目|名称|链接|新窗口(1/0)。相同栏目会自动合并。')
    ));
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Text('reportUrl', null, '', _t('投诉反馈地址'), _t('投诉反馈没有内置模板，仅在填写地址后显示。')));
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Text(
        'rewardWechat', null, '', _t('微信收款码地址')
    ));
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Text(
        'rewardAlipay', null, '', _t('支付宝收款码地址')
    ));
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Radio(
        'travellingsEnable', array('1' => _t('开启'), '0' => _t('关闭')), '1', _t('开往按钮'), _t('控制页面右上角的“开往-友链接力”按钮是否显示。')
    ));
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Radio(
        'showToc', array('1' => _t('开启'), '0' => _t('关闭')), '1', _t('文章目录')
    ));
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Radio(
        'showCopyright', array('1' => _t('开启'), '0' => _t('关闭')), '1', _t('文章版权卡片')
    ));
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Text(
        'countdownName', null, '', _t('倒计时名称'), _t('例如：春节；留空则不显示。')
    ));
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Text(
        'countdownDate', null, '', _t('倒计时日期'), _t('格式：2026-01-01。')
    ));
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Radio('commentEnable', array('1' => _t('开启'), '0' => _t('关闭')), '1', _t('评论开关')));
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Select('commentFormPosition', array('bottom' => _t('评论最后'), 'top' => _t('评论最前')), 'bottom', _t('评论框位置')));
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Text('commentPlaceholder', null, '友善交流，请遵守当地法律法规。', _t('评论框提示语')));
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Radio('relatedEnable', array('1' => _t('开启'), '0' => _t('关闭')), '1', _t('相关推荐')));
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Radio(
        'commentAuthorShowSensitive', array('1' => _t('显示'), '0' => _t('隐藏')), '0', _t('作者评论/回复显示 IP 和系统信息'), _t('关闭后，作者发布的评论和回复只显示时间，不公开 IP 归属地和系统信息。')
    ));
}

/** 文章自定义字段。 */
function themeFields($layout)
{
    $layout->addItem(new Typecho_Widget_Helper_Form_Element_Text('cover', null, '', _t('封面图地址'), _t('文章列表与文章顶部使用。')));
    $layout->addItem(new Typecho_Widget_Helper_Form_Element_Textarea('description', null, '', _t('文章摘要'), _t('填写后用于文章列表，并在正文顶部以 FakeGPT 流式摘要展示；留空时列表由正文自动截取。')));
    $layout->addItem(new Typecho_Widget_Helper_Form_Element_Radio('top', array('0' => _t('否'), '1' => _t('是')), '0', _t('置顶标记'), _t('仅显示置顶样式；置顶排序可配合 Typecho 置顶插件。')));
    $layout->addItem(new Typecho_Widget_Helper_Form_Element_Textarea('articleGPT', null, '', _t('FakeGPT 摘要（旧字段兼容）'), _t('已有内容会继续显示；新文章请填写“文章摘要”。')));
    $layout->addItem(new Typecho_Widget_Helper_Form_Element_Textarea('references', null, '', _t('参考资料'), _t('一行一个，格式：标题|链接。')));
    $layout->addItem(new Typecho_Widget_Helper_Form_Element_Radio('copyright', array('1' => _t('显示'), '0' => _t('隐藏')), '1', _t('版权卡片')));
}

/** 首页按 top 自定义字段排序，并保留 Typecho 原生分页查询流程。 */
function curve_archive_query_top_first($archive, $select)
{
    if ($archive->is('index')) {
        $select->cleanAttribute('order');
        $select->join(
            'table.fields',
            "table.contents.cid = table.fields.cid AND table.fields.name = 'top' AND table.fields.str_value = '1'",
            Typecho_Db::LEFT_JOIN
        );
        $select->order('table.fields.cid', Typecho_Db::SORT_DESC);
        $select->order('table.contents.created', Typecho_Db::SORT_DESC);
    }
    Typecho_Db::get()->fetchAll($select, array($archive, 'push'));
}

function themeInit($archive)
{
    $options = Typecho_Widget::widget('Widget_Options');
    $pageSize = (int) curve_option($options, 'postSize', 8);
    $archive->parameter->pageSize = $pageSize > 0 ? $pageSize : 8;

    static $registered = false;
    if (!$registered) {
        Typecho_Plugin::factory('Widget_Archive')->query = 'curve_archive_query_top_first';
        $registered = true;
    }
}
