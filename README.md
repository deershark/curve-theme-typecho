# Curve for Typecho

<div align="center">

一个简洁、现代、可配置的 Typecho 主题。

[![Check theme](https://github.com/deershark/curve-theme-typecho/actions/workflows/check.yml/badge.svg)](https://github.com/deershark/curve-theme-typecho/actions/workflows/check.yml)
[![Latest Release](https://img.shields.io/github/v/release/deershark/curve-theme-typecho?display_name=tag&sort=semver)](https://github.com/deershark/curve-theme-typecho/releases)
[![License](https://img.shields.io/github/license/deershark/curve-theme-typecho)](LICENSE)

</div>

> 本项目移植自 [imsyy/vitepress-theme-curve](https://github.com/imsyy/vitepress-theme-curve)，原作者为 [imsyy](https://github.com/imsyy)。感谢原作者创作并开源了优秀的 Curve 主题。

![Curve for Typecho 预览](screenshot.png)

Curve for Typecho 是将 [imsyy/vitepress-theme-curve](https://github.com/imsyy/vitepress-theme-curve) 移植到 Typecho 的主题版本。它保留了原主题的视觉风格、页面结构和交互体验，并使用 Typecho 原生 PHP、评论系统和文章数据驱动。

## 特性

- 响应式布局，适配桌面端和移动端。
- 首页、文章页、独立页、分类、标签、搜索、归档和 404 页面。
- Typecho 原生嵌套评论、相关文章、上下篇、目录、打赏和版权卡片。
- 可视化主题设置：站点信息、主题外观、导航菜单、社交链接、文章摘要、评论和页脚等。
- 文章置顶、封面、摘要、阅读量、参考资料和版权卡片等自定义字段。
- 友链、关于本站、文章归档、分类总览、标签总览和隐私协议页面模板。
- 支持提示块、Tabs、Timeline、Card、Button、LinkCard、数学公式等文章内容扩展。
- 原生 JavaScript 交互，无需构建前端项目即可安装使用。

## 安装

### 下载发布包

1. 前往 [Releases](https://github.com/deershark/curve-theme-typecho/releases) 下载最新的 `curve-theme-typecho-*.zip`。
2. 将压缩包解压到 Typecho 的 `usr/themes/` 目录。压缩包内已经包含 `curve` 主题目录，不需要再次重命名。
3. 在 Typecho 后台进入「控制台 → 外观」，启用 Curve。
4. 进入「外观 → 设置外观」，按需完成主题配置。

### 从源码安装

```sh
cd /path/to/typecho/usr/themes
git clone https://github.com/deershark/curve-theme-typecho.git curve
```

然后在 Typecho 后台启用 Curve 即可。

## 页面模板

新建独立页面时，可在“自定义模板”中选择以下模板：

| 页面 | 模板文件 |
| --- | --- |
| 文章归档 | `page-archives.php` |
| 关于本站 | `page-about.php` |
| 分类总览 | `page-categories.php` |
| 标签总览 | `page-tags.php` |
| 友情链接 | `page-links.php` |
| 隐私协议 | `page-privacy.php` |

主题会自动识别这些页面并使用实际链接。评论区和页脚的“服务”栏也会自动显示已创建的隐私协议页面；投诉反馈地址则需要在主题设置中手动填写。

## 文章字段

在文章编辑页的自定义字段中可以使用：

| 字段 | 用途 |
| --- | --- |
| `cover` | 文章封面图片 URL |
| `description` | 文章摘要；留空时自动截取正文 |
| `top` | 填写 `1` 后显示置顶标记，并让文章优先显示在首页 |
| `references` | 参考资料，一行一个 `标题\|https://链接` |
| `copyright` | 填写 `0` 隐藏版权卡片 |
| `views` | 文章阅读量，由主题自动累加 |

旧文章中的 `articleGPT` 字段仍会兼容读取，但新文章请使用 `description`。主题设置中的 FakeGPT 摘要可以控制摘要卡片及其点击文案。

## 友情链接

使用 `page-links.php` 模板的独立页，可以在正文中加入 `curve-friends` 配置块：

```md
<!-- curve-friends
## 推荐 | 推荐关注的站点
- 阮一峰 | https://www.ruanyifeng.com/blog/ | https://example.com/avatar.png | 阮老师，知名博主

## 小伙伴 | 一起交流和成长
- 我的博客 | https://example.com/ | https://example.com/avatar.png | 分享技术与生活
-->
```

列表格式为 `名称 | 链接 | 头像 | 简介`。分组标题可以省略，也可以使用 1 到 6 级 Markdown 标题。配置块之外的正文仍会按普通 Markdown 渲染。

页脚会随机展示 3 个友链，并提供刷新按钮更换列表；没有配置友情链接页时，会使用主题内置的默认推荐友链。

## 内容扩展

文章正文支持以下常用写法：

- Admonition：`::: info`、`::: tip`、`::: warning`、`::: danger`、`::: details`
- Tabs：`:::tabs` 配合 `== 标签`
- Timeline：`::: timeline`
- Radio：`::: radio`
- Card、Button：`::: card`、`::: button`
- LinkCard：`<LinkCard url="..." title="..." desc="..." />`
- 数学公式：`$...$` 和 `$$...$$`

后台编辑器会自动加载 Curve 快捷工具栏，可快速插入这些内容结构。

## 本地开发

仓库提供 Docker Compose 开发环境：

```sh
docker compose up -d
```

启动后访问 <http://localhost:8081>。主题源码会以只读方式挂载到容器中的 `usr/themes/curve`。

样式源修改后，使用 Sass CLI 重新编译：

```sh
node /path/to/sass/sass.js assets/scss/curve.scss assets/css/curve.css --style=expanded --no-source-map
```

本地基础检查：

```sh
find . -path './.docker' -prune -o -name '*.php' -print0 | xargs -0 -n1 php -l
node --check assets/js/curve.js
```

建议至少手动验证：首页、含封面和无封面文章、长标题、无标签文章、分页、搜索、未登录评论以及移动端菜单。

## GitHub Actions

- `Check theme`：Push 和 Pull Request 时自动检查 PHP 7.4/8.3 语法、JavaScript/MJS 语法及提交空白。
- `Publish theme`：在 Actions 中手动输入版本号，例如 `0.1.0`。流水线会更新 `index.php` 的 `@version`，提交并推送到当前分支，检查新提交后创建 `v0.1.0` Release，并上传主题压缩包和 SHA-256 校验文件。

发布流水线需要仓库允许 `GITHUB_TOKEN` 写入内容；如果目标分支启用了分支保护，请允许 GitHub Actions 推送，或从允许推送的分支执行发布。

## 上游项目与移植说明

本项目移植自 [imsyy/vitepress-theme-curve](https://github.com/imsyy/vitepress-theme-curve)，原作者为 [imsyy](https://github.com/imsyy)。原主题基于 VitePress、Vue 和 Pinia；本项目将其替换为 Typecho PHP 模板与原生 JavaScript。评论统一使用 Typecho 原生评论，不包含 Algolia、Artalk、Twikoo、Meting、音乐播放器或第三方统计服务。一言作为可选的外部能力，仅在启用对应设置时请求接口。

如果你正在寻找原版 VitePress 主题，请直接访问上游项目：[imsyy/vitepress-theme-curve](https://github.com/imsyy/vitepress-theme-curve)。

## 许可证

本项目基于 [MIT License](LICENSE) 发布。由于本项目源自上游 Curve 主题，请同时遵守上游项目的许可与署名要求。
