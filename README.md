# Curve for Typecho

这是 [vitepress-theme-curve](https://github.com/imsyy/vitepress-theme-curve) 的 Typecho 移植版。源主题的组件类名、SCSS、页面布局和交互语义保留在 `assets/scss/source`、`assets/scss/components/_source.scss` 以及 PHP 模板中；Vue/Pinia 仅被替换为 Typecho PHP 数据和原生 JavaScript。

## 安装

1. 将本目录重命名为 `curve`，上传至 Typecho 的 `usr/themes/curve`。
2. 在 Typecho 后台「控制台 → 外观」启用 Curve。
3. 打开「外观 → 设置外观」，填写 Logo、强调色、默认封面、站点简介、社交链接等设置。
4. 按需新建独立页面，并在“自定义模板”中选择：

   - `文章归档`：`page-archives.php`
   - `关于本站`：`page-about.php`
   - `分类总览`：`page-categories.php`
   - `标签总览`：`page-tags.php`
   - `友情链接`：`page-links.php`

   主题会根据模板自动识别这些页面并使用实际链接；投诉反馈地址没有内置模板，需要在主题设置中手动填写。

友情链接页面使用 `page-links.php` 模板，友链写在独立页正文的特殊 Markdown 块中：

```md
<!-- curve-friends
## 推荐 | 都是大佬，推荐关注
- 阮一峰 | https://www.ruanyifeng.com/blog/ | https://example.com/avatar.png | 阮老师，知名博主

## 小伙伴们 | 我们在一起，共同进步
- 我的博客 | https://example.com/ | https://example.com/avatar.png | 分享技术与生活
-->
```

格式为：`- 名称 | 链接 | 头像 | 简介`。特殊块外的正文仍按普通 Markdown 渲染，因此可以自由添加标题、说明和友链申请内容。

分组标题不是必填的。如果省略分组标题，所有列表项会直接放在一个没有标题的列表中：

```md
<!-- curve-friends
- 我的博客 | https://example.com/ | https://example.com/avatar.png | 分享技术与生活
-->
```

如果需要分组，标题不要求必须是两个井号，使用 `#` 到 `######` 均可，例如 `# 推荐 | 推荐站点`。

## 文章字段

在文章编辑页的自定义字段中可填写：

| 字段 | 用途 |
| --- | --- |
| `cover` | 文章封面图片 URL |
| `description` | 列表摘要；留空时自动截取正文 |
| `top` | 显示“置顶”标记，并让首页文章查询优先排序 |
| `articleGPT` | 正文前的内容摘要卡片（旧字段兼容） |
| `references` | 参考资料，一行一个 `标题|https://链接` |
| `copyright` | `0` 隐藏文章版权卡片，默认显示 |

主题设置中的 `FakeGPT 摘要` 可控制摘要卡片开关，`FakeGPT 点击文案` 用于配置点击卡片右上角 FakeGPT 后显示的文字。

首页会读取 `top=1` 的文章并优先排在首页分页之前；分类归档页仍按 Typecho 原生发布时间排序。

## 已实现

- 首页、文章页、独立页、分类/标签/搜索归档、404 和原生分页。
- Typecho 原生嵌套评论、相关文章、上下篇、文章目录、打赏与版权卡片。
- 源主题的 Banner、导航、移动菜单、设置面板、右键菜单、侧栏、封面卡片、归档、友链、About、Project、页脚和倒计时结构。
- 源主题的 SCSS 由 `assets/scss/curve.scss` 编译为 `assets/css/curve.css`，Typecho 只增加少量数据与评论适配样式。

## 与原 VitePress 主题的差异

原项目的 Vue/Pinia 被替换为 PHP/JavaScript；一言仍作为可选的外部能力使用。评论统一使用 Typecho 原生评论，不支持 Algolia、Artalk、Twikoo、Meting、音乐播放器或第三方统计服务。由于 Typecho 的文章内容不是 VitePress 构建结果，文章 Frontmatter 改为文章自定义字段，友链使用独立页 Markdown 特殊块。

VitePress 的 `::: info`、Tabs 等语法不是 Typecho 原生内容格式。普通 Markdown/HTML 的标题、代码块、表格、引用、图片和链接已得到样式支持。

## 开发与验证

样式源修改后，需要重新编译 CSS：

```sh
node tools/extract-source-styles.mjs /home/yuco/projects/vitepress-theme-curve/.vitepress/theme
node /path/to/sass/sass.js assets/scss/curve.scss assets/css/curve.css --style=expanded --no-source-map
find . -path './.docker' -prune -o -name '*.php' -print0 | xargs -0 -n1 php -l
node --check assets/js/curve.js
```

建议至少测试：首页、含封面/无封面文章、长标题、无标签文章、分页、搜索、未登录评论和移动端菜单。
