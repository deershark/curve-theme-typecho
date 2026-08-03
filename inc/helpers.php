<?php

if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

function curve_esc($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function curve_option($options, $name, $default = '')
{
    $value = isset($options->{$name}) ? trim((string) $options->{$name}) : '';
    return $value === '' ? $default : $value;
}

function curve_accent_color($options)
{
    $value = isset($options->accentColor) ? trim((string) $options->accentColor) : '';
    if ($value === '' || strtolower($value) === '#425aef') {
        return '';
    }
    if (!preg_match('/^#(?:[0-9a-f]{3}|[0-9a-f]{6})$/i', $value)) {
        return '';
    }
    if (strlen($value) === 4) {
        $value = '#' . $value[1] . $value[1] . $value[2] . $value[2] . $value[3] . $value[3];
    }
    return strtolower($value);
}

function curve_site_path($options, $path)
{
    $base = isset($options->siteUrl) ? rtrim((string) $options->siteUrl, '/') : '';
    return $base . '/' . ltrim($path, '/');
}

function curve_page_template_urls()
{
    static $urls;
    if ($urls !== null) {
        return $urls;
    }

    $urls = array();
    $pages = Typecho_Widget::widget('Widget_Contents_Page_List');
    while ($pages->next()) {
        $template = trim((string) $pages->template);
        if ($template === '') {
            continue;
        }
        ob_start();
        $pages->permalink();
        $permalink = trim(ob_get_clean());
        if ($permalink !== '') {
            $urls[$template] = $permalink;
        }
    }
    return $urls;
}

function curve_page_url($template)
{
    $urls = curve_page_template_urls();
    return isset($urls[$template]) ? $urls[$template] : '';
}

function curve_post_field($post, $name, $default = '')
{
    if (!isset($post->fields) || !isset($post->fields->{$name})) {
        return $default;
    }
    $value = trim((string) $post->fields->{$name});
    return $value === '' ? $default : $value;
}

function curve_lines($value)
{
    $rows = preg_split('/\r\n|\r|\n/', (string) $value);
    return array_values(array_filter(array_map('trim', $rows), 'strlen'));
}

function curve_link_rows($value)
{
    $items = array();
    foreach (curve_lines($value) as $row) {
        $parts = array_map('trim', explode('|', $row, 2));
        if (count($parts) === 2 && $parts[0] !== '' && filter_var($parts[1], FILTER_VALIDATE_URL)) {
            $items[] = array('name' => $parts[0], 'url' => $parts[1]);
        }
    }
    return $items;
}

/** 按后台配置的名称挑选侧栏社交链接；名称必须与社交链接配置完全一致。 */
function curve_pick_social_links($socialLinks, $names, $limit = 2)
{
    $socialLinks = array_values((array) $socialLinks);
    $requestedNames = curve_lines($names);
    $selected = array();

    foreach ($requestedNames as $requestedName) {
        foreach ($socialLinks as $socialLink) {
            if ($socialLink['name'] !== $requestedName) {
                continue;
            }
            $alreadySelected = false;
            foreach ($selected as $selectedLink) {
                if ($selectedLink['name'] === $socialLink['name']) {
                    $alreadySelected = true;
                    break;
                }
            }
            if (!$alreadySelected) {
                $selected[] = $socialLink;
            }
            break;
        }
        if (count($selected) >= $limit) {
            break;
        }
    }

    foreach ($socialLinks as $socialLink) {
        if (count($selected) >= $limit) {
            break;
        }
        $alreadySelected = false;
        foreach ($selected as $selectedLink) {
            if ($selectedLink['name'] === $socialLink['name']) {
                $alreadySelected = true;
                break;
            }
        }
        if (!$alreadySelected) {
            $selected[] = $socialLink;
        }
    }

    return empty($requestedNames) || empty($selected) ? array_slice($socialLinks, 0, $limit) : $selected;
}

/** 解析页脚站点地图：栏目|名称|链接|新窗口(1/0)。 */
function curve_footer_columns($value)
{
    $columns = array();
    foreach (curve_lines($value) as $row) {
        $parts = array_map('trim', explode('|', $row, 4));
        if (count($parts) < 3 || $parts[0] === '' || $parts[1] === '' || $parts[2] === '') {
            continue;
        }
        $url = $parts[2];
        if (!preg_match('/^(?:https?:\/\/|\/|#|mailto:|tel:)/i', $url)) {
            continue;
        }
        $title = $parts[0];
        if (!isset($columns[$title])) {
            $columns[$title] = array('title' => $title, 'links' => array());
        }
        $columns[$title]['links'][] = array(
            'name' => $parts[1],
            'url' => $url,
            'newTab' => isset($parts[3]) && in_array(strtolower($parts[3]), array('1', 'true', 'yes'), true),
        );
    }
    return array_values($columns);
}

function curve_social_icon($name)
{
    $name = strtolower((string) $name);
    if (strpos($name, 'github') !== false) return 'github';
    if (strpos($name, 'email') !== false || strpos($name, 'mail') !== false || strpos($name, '邮箱') !== false) return 'email';
    if (strpos($name, 'qq') !== false) return 'qq';
    if (strpos($name, 'telegram') !== false || strpos($name, 'tg') !== false) return 'telegram';
    if (strpos($name, 'twitter') !== false || strpos($name, '推特') !== false || strpos($name, '𝕏') !== false || preg_match('/^x(?:$|[\s_\-.()])/u', $name)) return 'twitter';
    if (strpos($name, 'bilibili') !== false) return 'bilibili';
    if (strpos($name, 'home') !== false || strpos($name, '主页') !== false || strpos($name, '首页') !== false) return 'home';
    return 'link';
}

/** 解析独立页中的友情链接 Markdown 块。 */
function curve_parse_friend_markdown($content)
{
    $groups = array();
    $current = null;
    $lines = preg_split('/\r\n|\r|\n/', (string) $content);
    foreach ($lines as $line) {
        $line = trim($line);
        if (preg_match('/^#{1,6}\s+(.+?)\s*$/u', $line, $match)) {
            $parts = array_map('trim', explode('|', $match[1], 2));
            if ($parts[0] === '') {
                continue;
            }
            $current = array(
                'typeName' => $parts[0],
                'typeDesc' => isset($parts[1]) && $parts[1] !== '' ? $parts[1] : '与优秀的人和站点同行',
                'typeList' => array(),
            );
            $groups[] = $current;
            continue;
        }
        if (preg_match('/^[-*+]\s+(.+?)\s*$/u', $line, $match)) {
            if ($current === null) {
                $current = array(
                    'typeName' => '',
                    'typeDesc' => '',
                    'typeList' => array(),
                );
                $groups[] = $current;
            }
            $parts = array_map('trim', explode('|', $match[1], 4));
            if (count($parts) < 2 || $parts[0] === '' || !filter_var($parts[1], FILTER_VALIDATE_URL)) {
                continue;
            }
            $groups[count($groups) - 1]['typeList'][] = array(
                'name' => $parts[0],
                'url' => $parts[1],
                'avatar' => isset($parts[2]) && filter_var($parts[2], FILTER_VALIDATE_URL) ? $parts[2] : '',
                'desc' => isset($parts[3]) && $parts[3] !== '' ? $parts[3] : parse_url($parts[1], PHP_URL_HOST),
            );
        }
    }
    return array_values(array_filter($groups, function ($group) {
        return !empty($group['typeList']);
    }));
}

/** 将友情链接分组输出为 Curve 卡片列表。 */
function curve_render_friend_groups($groups)
{
    if (empty($groups)) {
        return '<div class="no-data">暂无友链数据</div>';
    }
    $html = '<div class="link-list" id="friends">';
    foreach ($groups as $group) {
        $html .= '<div class="link-type-list">';
        if ($group['typeName'] !== '' || $group['typeDesc'] !== '') {
            $html .= '<div class="title"><h2 class="name"><span class="name-text">' . curve_esc($group['typeName']) . '</span><span class="name-count">（' . count($group['typeList']) . '）</span></h2><span class="tip">' . curve_esc($group['typeDesc']) . '</span></div>';
        }
        $html .= '<div class="all-link">';
        foreach ($group['typeList'] as $friend) {
            $html .= '<a class="link-card s-card" href="' . curve_esc($friend['url']) . '" target="_blank" rel="noopener"><div class="cover">';
            if ($friend['avatar'] !== '') {
                $html .= '<img src="' . curve_esc($friend['avatar']) . '" class="cover-img loaded" alt="' . curve_esc($friend['name']) . '">';
            }
            $html .= '</div><div class="data"><span class="name">' . curve_esc($friend['name']) . '</span><span class="desc">' . curve_esc($friend['desc']) . '</span></div></a>';
        }
        $html .= '</div></div>';
    }
    return $html . '</div>';
}

function curve_default_covers($options)
{
    return curve_lines(curve_option($options, 'defaultCovers'));
}

function curve_post_cover($post, $options)
{
    $cover = curve_post_field($post, 'cover');
    if ($cover !== '') {
        return $cover;
    }
    $covers = curve_default_covers($options);
    if (empty($covers)) {
        return '';
    }
    $index = isset($post->cid) ? abs((int) $post->cid) % count($covers) : 0;
    return $covers[$index];
}

function curve_excerpt($post, $length = 130)
{
    $description = curve_post_field($post, 'description');
    if ($description !== '') {
        return $description;
    }
    ob_start();
    $post->excerpt($length, '…');
    $excerpt = trim(strip_tags(ob_get_clean()));
    /* Typecho may return the original Markdown source here. Keep the
     * automatic summary readable instead of exposing heading/list markers. */
    $excerpt = preg_replace('/!\[[^\]]*\]\([^)]*\)/u', '', $excerpt);
    $excerpt = preg_replace('/\[([^\]]+)\]\([^)]*\)/u', '$1', $excerpt);
    $excerpt = preg_replace('/(^|\s)#{1,6}\s*/u', '$1', $excerpt);
    $excerpt = preg_replace('/^\s*(?:[-*+]|\d+\.)\s+/mu', '', $excerpt);
    $excerpt = preg_replace('/[`*_~]/u', '', $excerpt);
    return trim((string) preg_replace('/\s+/u', ' ', $excerpt));
}

/** 将 Typecho 中保存的 Markdown 正文转换为文章 HTML。 */
function curve_render_markdown($content)
{
    $content = (string) $content;
    if ($content === '') {
        return $content;
    }
    /* Typecho may wrap an unparsed Markdown field in <p> tags. Convert that
     * wrapper back to Markdown before parsing; preserve already-rendered HTML. */
    $plainContent = strip_tags($content);
    $looksLikeWrappedMarkdown = preg_match('/(?:^|\n)\s*(?:#{1,6}\s|```|[-*+]\s|\d+\.\s|>\s)/m', $plainContent)
        && preg_match('/^\s*<p\b/i', $content);
    if ($looksLikeWrappedMarkdown) {
        $content = preg_replace('/<br\s*\/?>/i', "\n", $content);
        $content = preg_replace('/<\/p>\s*<p[^>]*>/i', "\n\n", $content);
        $content = strip_tags($content);
    } elseif (preg_match('/<(?:p|h[1-6]|ul|ol|pre|blockquote|div|table|img|details|figure)\b/i', $content)) {
        return curve_normalize_markdown_html($content);
    }
    if (!preg_match('/^(?:#{1,6}\s|```|>\s|[-*+]\s|\d+\.\s|\s*\|[^\n]*\|\s*$)|(?:\*\*|__|`|!\[)/m', $content)) {
        return curve_normalize_markdown_html('<p>' . nl2br(curve_esc($content)) . '</p>');
    }

    $lines = preg_split('/\r\n|\r|\n/', $content);
    $html = '';
    $paragraph = array();
    $listType = null;
    $inCode = false;
    $codeLanguage = '';
    $codeLines = array();

    $flushParagraph = function () use (&$html, &$paragraph) {
        if (!empty($paragraph)) {
            $parts = array_map('trim', $paragraph);
            $html .= '<p>' . implode('<br>', array_map('curve_markdown_inline', $parts)) . '</p>';
            $paragraph = array();
        }
    };
    $closeList = function () use (&$html, &$listType) {
        if ($listType !== null) {
            $html .= '</' . $listType . '>';
            $listType = null;
        }
    };

    for ($lineIndex = 0, $lineCount = count($lines); $lineIndex < $lineCount; $lineIndex++) {
        $line = $lines[$lineIndex];
        if ($inCode) {
            if (preg_match('/^```\s*$/', trim($line))) {
                $html .= '<pre><code' . ($codeLanguage !== '' ? ' class="language-' . curve_esc($codeLanguage) . '"' : '') . '>' . curve_esc(implode("\n", $codeLines)) . '</code></pre>';
                $inCode = false;
                $codeLanguage = '';
                $codeLines = array();
            } else {
                $codeLines[] = $line;
            }
            continue;
        }
        if ($lineIndex + 1 < $lineCount && strpos($line, '|') !== false && curve_markdown_table_separator($lines[$lineIndex + 1])) {
            $flushParagraph();
            $closeList();
            $headers = curve_markdown_table_cells($line);
            $html .= '<div class="table-container"><table><thead><tr>';
            foreach ($headers as $header) $html .= '<th>' . curve_markdown_inline($header) . '</th>';
            $html .= '</tr></thead><tbody>';
            $lineIndex++;
            while ($lineIndex + 1 < $lineCount && trim($lines[$lineIndex + 1]) !== '' && strpos($lines[$lineIndex + 1], '|') !== false) {
                $lineIndex++;
                $html .= '<tr>';
                foreach (curve_markdown_table_cells($lines[$lineIndex]) as $cell) $html .= '<td>' . curve_markdown_inline($cell) . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody></table></div>';
            continue;
        }
        if (preg_match('/^```\s*([\w+-]*)\s*$/', trim($line), $match)) {
            $flushParagraph();
            $closeList();
            $inCode = true;
            $codeLanguage = $match[1];
            continue;
        }
        if (trim($line) === '') {
            $flushParagraph();
            $closeList();
            continue;
        }
        if (preg_match('/^(#{1,6})\s+(.+?)\s*#*$/', trim($line), $match)) {
            $flushParagraph();
            $closeList();
            $level = strlen($match[1]);
            $text = trim($match[2]);
            $id = 'heading-' . substr(sha1($text), 0, 8);
            $html .= '<h' . $level . ' id="' . curve_esc($id) . '">' . curve_markdown_inline($text) . '</h' . $level . '>';
            continue;
        }
        if (preg_match('/^\s*([-*_])(?:\s*\1){2,}\s*$/', $line)) {
            $flushParagraph();
            $closeList();
            $html .= '<hr>';
            continue;
        }
        if (preg_match('/^\s*>\s?(.*)$/', $line, $match)) {
            $flushParagraph();
            $closeList();
            $html .= '<blockquote>' . curve_markdown_inline(trim($match[1])) . '</blockquote>';
            continue;
        }
        if (preg_match('/^\s*[-*+]\s+(.+)$/', $line, $match) || preg_match('/^\s*\d+\.\s+(.+)$/', $line, $match)) {
            $type = preg_match('/^\s*\d+\./', $line) ? 'ol' : 'ul';
            $flushParagraph();
            if ($listType !== $type) {
                $closeList();
                $html .= '<' . $type . '>';
                $listType = $type;
            }
            $itemText = trim($match[1]);
            if (preg_match('/^\[([ xX])\]\s*(.*)$/', $itemText, $taskMatch)) {
                $checked = strtolower($taskMatch[1]) === 'x' ? ' checked' : '';
                $itemText = '<input type="checkbox" disabled' . $checked . '> ' . curve_markdown_inline($taskMatch[2]);
            } else {
                $itemText = curve_markdown_inline($itemText);
            }
            $html .= '<li>' . $itemText . '</li>';
            continue;
        }
        $closeList();
        $paragraph[] = $line;
    }
    if ($inCode) {
        $html .= '<pre><code' . ($codeLanguage !== '' ? ' class="language-' . curve_esc($codeLanguage) . '"' : '') . '>' . curve_esc(implode("\n", $codeLines)) . '</code></pre>';
    }
    $flushParagraph();
    $closeList();
    $html = preg_replace('/<\/ul>\s*<ul>/', '', $html);
    $html = preg_replace('/<\/ol>\s*<ol>/', '', $html);
    return curve_normalize_markdown_html($html);
}

/** 将 Typecho 直出的代码块、表格补成 Curve 原主题的容器结构。 */
function curve_highlight_code($code, $language = '')
{
    if (strpos($code, 'class="line"') !== false || strpos($code, "class='line'") !== false) {
        return $code;
    }
    $language = strtolower(trim((string) $language));
    $keywords = array(
        'php' => 'and|array|as|break|case|catch|class|const|continue|declare|default|die|do|echo|else|elseif|empty|extends|final|finally|fn|for|foreach|function|global|if|implements|include|instanceof|interface|isset|namespace|new|or|print|private|protected|public|require|return|static|switch|throw|trait|try|unset|use|var|while|xor|yield|true|false|null',
        'javascript' => 'as|async|await|break|case|catch|class|const|continue|debugger|default|delete|do|else|export|extends|false|finally|for|from|function|get|if|import|in|instanceof|let|new|null|of|return|set|static|super|switch|this|throw|true|try|typeof|undefined|var|void|while|with|yield',
        'typescript' => 'as|async|await|break|case|catch|class|const|continue|debugger|default|delete|do|else|enum|export|extends|false|finally|for|from|function|if|implements|import|in|instanceof|interface| keyof |let|namespace|new|null|number|of|private|protected|public|readonly|return|string|super|switch|this|throw|true|try|type|typeof|undefined|var|void|while|with|yield',
        'css' => 'and|from|important|media|not|or|supports|url',
        'json' => 'true|false|null',
        'bash' => 'case|do|done|elif|else|esac|fi|for|function|if|in|select|then|time|until|while',
    );
    if ($language === 'js') $language = 'javascript';
    if ($language === 'ts') $language = 'typescript';
    if ($language === 'sh' || $language === 'shell' || $language === 'zsh') $language = 'bash';
    if ($language === 'yml' || $language === 'yaml') $language = 'json';
    $keywordPattern = isset($keywords[$language]) ? $keywords[$language] : 'and|break|case|class|const|continue|default|else|false|for|function|if|let|new|null|return|switch|this|throw|true|try|var|while';
    $lines = preg_split('/\r\n|\r|\n/', html_entity_decode((string) $code, ENT_QUOTES, 'UTF-8'));
    $output = array();
    $tokenize = function ($token) use ($keywordPattern) {
        $type = '';
        $light = '';
        $dark = '';
        if ($token !== '' && $token[0] === '<' && substr($token, -1) === '>') {
            $type = 'tag'; $light = '#22863a'; $dark = '#7ee787';
        } elseif ($token !== '' && ($token[0] === '/' || $token[0] === '#')) {
            $type = 'comment'; $light = '#6a737d'; $dark = '#8b949e';
        } elseif ($token !== '' && ($token[0] === '"' || $token[0] === "'" || $token[0] === '`')) {
            $type = 'string'; $light = '#032f62'; $dark = '#a5d6ff';
        } elseif (preg_match('/^\d/', $token)) {
            $type = 'number'; $light = '#005cc5'; $dark = '#79c0ff';
        } elseif (preg_match('/^(?:' . $keywordPattern . ')$/i', $token)) {
            $type = 'keyword'; $light = '#d73a49'; $dark = '#ff7b72';
        } elseif (preg_match('/^(?:console|log|greet|print_r|var_dump|strlen|count|array_map|fetch|setInterval|require|JSON|parseInt|Math)$/', $token)) {
            $type = 'function'; $light = '#6f42c1'; $dark = '#d2a8ff';
        }
        if ($type === '') return curve_esc($token);
        return '<span class="token ' . $type . '" style="--shiki-light:' . $light . ';--shiki-dark:' . $dark . '">' . curve_esc($token) . '</span>';
    };
    foreach ($lines as $line) {
        $pattern = '/<[^>\n]+>|\/\/.*$|#.*$|\/\*.*?\*\/|"(?:\\\\.|[^"\\\\])*"|\'(?:\\\\.|[^\'\\\\])*\'|`(?:\\\\.|[^`\\\\])*`|\b\d+(?:\.\d+)?\b|\b[A-Za-z_$][\w$]*\b/';
        $highlighted = '';
        $offset = 0;
        if (preg_match_all($pattern, $line, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as $match) {
                $token = $match[0];
                $position = (int) $match[1];
                $highlighted .= curve_esc(substr($line, $offset, $position - $offset));
                $highlighted .= $tokenize($token);
                $offset = $position + strlen($token);
            }
        }
        $highlighted .= curve_esc(substr($line, $offset));
        $output[] = '<span class="line">' . $highlighted . '</span>';
    }
    return implode("\n", $output);
}

function curve_normalize_markdown_html($content)
{
    $content = preg_replace_callback('/<pre\b([^>]*)>\s*<code\b([^>]*)>(.*?)<\/code>\s*<\/pre>/is', function ($match) {
        $language = '';
        $attributes = (string) ($match[1] ?? '') . ' ' . (string) ($match[2] ?? '');
        if (preg_match('/(?:lang|language)-([a-z0-9_+-]+)/i', $attributes, $languageMatch)) {
            $language = trim($languageMatch[1]);
        }
        $code = curve_highlight_code($match[3], $language);
        $langLabel = $language !== '' ? '<span class="lang">' . curve_esc($language) . '</span>' : '';
        return '<div class="language-' . curve_esc($language) . '"><button type="button" title="Copy Code" class="copy" data-copy-code aria-label="复制代码"></button>' . $langLabel . '<pre><code>' . $code . '</code></pre></div>';
    }, $content);
    if (strpos($content, 'table-container') === false) {
        $content = preg_replace_callback('/<table\b[^>]*>.*?<\/table>/is', function ($match) {
            return '<div class="table-container">' . $match[0] . '</div>';
        }, $content);
    }
    /* Markdown-it task-list output is not guaranteed when Typecho's Markdown
     * plugin is enabled. Keep the same semantic checkbox when it arrives as
     * a plain `[ ]`/`[x]` list item. */
    $content = preg_replace_callback('/<li([^>]*)>\s*(?:<p>\s*)?\[([ xX])\]\s*(.*?)(?:<\/p>\s*)?<\/li>/is', function ($match) {
        $checked = strtolower($match[2]) === 'x' ? ' checked' : '';
        return '<li' . $match[1] . '><input class="task-list-item-checkbox" type="checkbox" disabled' . $checked . '> ' . $match[3] . '</li>';
    }, $content);
    $content = preg_replace('/<details\b(?![^>]*\bclass=)/i', '<details class="custom-block details"', $content);
    $headingIndex = 0;
    $content = preg_replace_callback('/<h([1-6])([^>]*)>(.*?)<\/h\1>/is', function ($match) use (&$headingIndex) {
        $headingIndex++;
        $attrs = $match[2];
        $inner = $match[3];
        if (stripos($inner, 'header-anchor') !== false) return $match[0];
        if (preg_match('/\bid=["\']([^"\']+)["\']/i', $attrs, $idMatch)) {
            $id = $idMatch[1];
        } else {
            $id = 'heading-' . substr(sha1(trim(strip_tags($inner)) . '-' . $headingIndex), 0, 8);
            $attrs .= ' id="' . curve_esc($id) . '"';
        }
        return '<h' . $match[1] . $attrs . '><a class="header-anchor" href="#' . curve_esc($id) . '" aria-label="Permalink"></a>' . $inner . '</h' . $match[1] . '>';
    }, $content);
    return preg_match('/class=["\']markdown-body["\']/', $content)
        ? $content
        : '<div class="markdown-body">' . $content . '</div>';
}

function curve_markdown_inline($text)
{
    $text = htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
    $text = preg_replace('/!\[([^\]]*)\]\((https?:\/\/[^\s)]+)(?:\s+["\']([^"\']*)["\'])?\)/', '<img src="$2" alt="$1" title="$3">', $text);
    $text = preg_replace('/\[([^\]]+)\]\((https?:\/\/[^\s)]+)\)/', '<a href="$2" target="_blank" rel="noopener">$1</a>', $text);
    $text = preg_replace('/`([^`]+)`/', '<code>$1</code>', $text);
    $text = preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', $text);
    $text = preg_replace('/__([^_]+)__/', '<strong>$1</strong>', $text);
    $text = preg_replace('/(?<!\*)\*([^*]+)\*(?!\*)/', '<em>$1</em>', $text);
    $text = preg_replace('/(?<!_)_([^_]+)_(?!_)/', '<em>$1</em>', $text);
    $text = preg_replace('/~~([^~]+)~~/', '<del>$1</del>', $text);
    return $text;
}

function curve_markdown_table_cells($line)
{
    $line = trim($line);
    $line = trim($line, '|');
    return array_map('trim', explode('|', $line));
}

function curve_markdown_table_separator($line)
{
    $cells = curve_markdown_table_cells($line);
    return count($cells) >= 2 && !array_filter($cells, function ($cell) {
        return !preg_match('/^:?-{3,}:?$/', trim($cell));
    });
}

function curve_reading_time($post)
{
    $text = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $post->content)));
    $count = function_exists('mb_strlen') ? mb_strlen($text, 'UTF-8') : strlen($text);
    return max(1, (int) ceil($count / 450));
}

function curve_is_enabled($options, $name, $default = true)
{
    $value = curve_option($options, $name, $default ? '1' : '0');
    return $value === '1';
}

function curve_comment_form($archive, $comments, $options, $user, $commentUrl)
{
    $commentAction = Typecho_Widget::widget('Widget_Security')->getTokenUrl($commentUrl);
    $commentMailRequired = !empty($options->commentsRequireMail);
    $commentUrlRequired = !empty($options->commentsRequireUrl);
    $commentPlaceholder = curve_option($options, 'commentPlaceholder', '友善交流，请遵守当地法律法规。');
    ?>
    <div id="<?php $archive->respondId(); ?>" class="respond">
    <div class="cancel-comment-reply"><?php $comments->cancelReply(); ?></div>
    <form method="post" action="<?php echo curve_esc($commentAction); ?>" class="comment-form" id="comment-form">
        <div class="comment-fields">
            <?php if (!$user->hasLogin()): ?>
            <label>昵称<input type="text" name="author" value="<?php $archive->remember('author'); ?>" required></label>
            <label>邮箱<input type="email" name="mail" value="<?php $archive->remember('mail'); ?>"<?php echo $commentMailRequired ? ' required' : ''; ?>></label>
            <label>网址<input type="url" name="url" value="<?php $archive->remember('url'); ?>"<?php echo $commentUrlRequired ? ' required' : ''; ?>></label>
            <?php else: ?>
            <p>已登录为 <?php $user->screenName(); ?></p>
            <?php endif; ?>
        </div>
        <textarea name="text" rows="5" placeholder="<?php echo curve_esc($commentPlaceholder); ?>" required><?php $archive->remember('text'); ?></textarea>
        <button type="submit">提交评论</button>
    </form>
    </div>
    <?php
}

function curve_since_days($options)
{
    $since = curve_option($options, 'since');
    if ($since === '' || strtotime($since) === false) {
        return null;
    }
    return max(0, (int) floor((time() - strtotime($since)) / 86400));
}

function curve_page_title($archive)
{
    if ($archive->is('search')) {
        return '搜索：' . $archive->keywords;
    }
    if ($archive->is('category') || $archive->is('tag') || $archive->is('date')) {
        ob_start();
        $archive->archiveTitle('', '', '');
        return trim(strip_tags(ob_get_clean()));
    }
    return '文章归档';
}

/** 输出与 Curve 原主题一致的分页，并附加快速跳转输入框。 */
function curve_page_nav($archive)
{
    ob_start();
    $archive->pageNav(
        '<i class="iconfont icon-page-right"></i><span class="page-text">上页</span>',
        '<span class="page-text">下页</span><i class="iconfont icon-page-right"></i>',
        3,
        '…',
        'textTag='
    );
    $navigation = ob_get_clean();
    if ($navigation === '' || strpos($navigation, '</ol>') === false) {
        echo $navigation;
        return;
    }

    $totalPages = (int) $archive->getTotalPage();
    if ($totalPages > 1) {
        $jump = '<li class="fast-jump" data-fast-jump data-total-pages="' . $totalPages . '" title="快速跳转">'
            . '<input type="text" min="1" max="' . $totalPages . '" inputmode="numeric" aria-label="输入页码">'
            . '<i class="iconfont icon-arrow-right" data-fast-jump-submit></i></li>';
        $navigation = preg_replace('/<\/ol>\s*$/', $jump . '</ol>', $navigation, 1);
    }
    echo $navigation;
}

/** 将评论时间转换为更适合评论区阅读的相对时间。 */
function curve_comment_relative_time($comments)
{
    $created = isset($comments->created) ? (int) $comments->created : 0;
    if ($created <= 0) {
        ob_start();
        $comments->date('U');
        $created = (int) trim(ob_get_clean());
    }

    if ($created <= 0) {
        return '具体时间';
    }

    $diff = time() - $created;
    if ($diff < 0) {
        $diff = 0;
    }
    if ($diff < 60) {
        return '刚刚';
    }
    if ($diff < 3600) {
        return floor($diff / 60) . ' 分钟前';
    }
    if ($diff < 86400) {
        return floor($diff / 3600) . ' 小时前';
    }
    if ($diff < 2592000) {
        return floor($diff / 86400) . ' 天前';
    }

    return date('Y-m-d H:i', $created);
}

/** 从评论 User-Agent 中提取操作系统信息。 */
function curve_comment_client_meta($agent)
{
    $agent = trim((string) $agent);
    if ($agent === '') {
        return '未知系统';
    }

    $platform = '';
    if (preg_match('/HarmonyOS|OpenHarmony/i', $agent)) {
        $platform = 'HarmonyOS';
    } elseif (preg_match('/Android/i', $agent)) {
        $platform = 'Android';
    } elseif (preg_match('/Windows NT/i', $agent)) {
        $platform = 'Windows';
    } elseif (preg_match('/CrOS/i', $agent)) {
        $platform = 'ChromeOS';
    } elseif (preg_match('/iPhone|iPad|iPod/i', $agent)) {
        $platform = 'IOS';
    } elseif (preg_match('/Mac OS X/i', $agent)) {
        $platform = 'MacOS';
    } elseif (preg_match('/Linux/i', $agent)) {
        $platform = 'Linux';
    }

    return $platform !== '' ? $platform : '未知系统';
}

/** 将 IP 归属地格式化为“中国省份 / 中国港澳 / 其他国家”。 */
function curve_comment_format_location($data)
{
    if (!is_array($data)) {
        return '';
    }

    $code = strtoupper(trim(isset($data['country_code']) ? $data['country_code'] : ''));
    $country = trim(isset($data['country']) ? $data['country'] : '');
    $region = trim(isset($data['region']) ? $data['region'] : '');

    if ($code === 'HK' || strpos($country, '香港') !== false || strpos($region, '香港') !== false) {
        return '中国香港';
    }
    if ($code === 'MO' || strpos($country, '澳门') !== false || strpos($region, '澳门') !== false) {
        return '中国澳门';
    }

    if ($code === 'CN' || $country === '中国' || $country === 'China') {
        $provinceMap = array(
            'beijing' => '北京市', '北京' => '北京市',
            'shanghai' => '上海市', '上海' => '上海市',
            'tianjin' => '天津市', '天津' => '天津市',
            'chongqing' => '重庆市', '重庆' => '重庆市',
            'hebei' => '河北省', '河北' => '河北省',
            'shanxi' => '山西省', '山西' => '山西省',
            'liaoning' => '辽宁省', '辽宁' => '辽宁省',
            'jilin' => '吉林省', '吉林' => '吉林省',
            'heilongjiang' => '黑龙江省', '黑龙江' => '黑龙江省',
            'jiangsu' => '江苏省', '江苏' => '江苏省',
            'zhejiang' => '浙江省', '浙江' => '浙江省',
            'anhui' => '安徽省', '安徽' => '安徽省',
            'fujian' => '福建省', '福建' => '福建省',
            'jiangxi' => '江西省', '江西' => '江西省',
            'shandong' => '山东省', '山东' => '山东省',
            'henan' => '河南省', '河南' => '河南省',
            'hubei' => '湖北省', '湖北' => '湖北省',
            'hunan' => '湖南省', '湖南' => '湖南省',
            'guangdong' => '广东省', '广东' => '广东省',
            'hainan' => '海南省', '海南' => '海南省',
            'sichuan' => '四川省', '四川' => '四川省',
            'guizhou' => '贵州省', '贵州' => '贵州省',
            'yunnan' => '云南省', '云南' => '云南省',
            'shaanxi' => '陕西省', '陕西' => '陕西省',
            'gansu' => '甘肃省', '甘肃' => '甘肃省',
            'qinghai' => '青海省', '青海' => '青海省',
            'taiwan' => '台湾', '台湾' => '台湾',
            'inner mongolia' => '内蒙古自治区', '内蒙古' => '内蒙古自治区',
            'guangxi' => '广西壮族自治区', '广西' => '广西壮族自治区',
            'tibet' => '西藏自治区', 'xizang' => '西藏自治区', '西藏' => '西藏自治区',
            'ningxia' => '宁夏回族自治区', '宁夏' => '宁夏回族自治区',
            'xinjiang' => '新疆维吾尔自治区', '新疆' => '新疆维吾尔自治区',
        );
        $key = strtolower($region);
        return isset($provinceMap[$key]) ? $provinceMap[$key] : ($region !== '' ? $region : '中国');
    }

    return $country !== '' ? $country : '未知地区';
}

/** 查询并缓存 IP 归属地，缓存 7 天，避免每次刷新评论页都请求外部服务。 */
function curve_comment_location($ip)
{
    $ip = trim((string) $ip);
    if ($ip === '' || !filter_var($ip, FILTER_VALIDATE_IP) || !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
        return $ip !== '' && filter_var($ip, FILTER_VALIDATE_IP) ? '本地网络' : '未知地区';
    }

    static $runtimeCache = array();
    if (isset($runtimeCache[$ip])) {
        return $runtimeCache[$ip];
    }

    $cacheDir = defined('__TYPECHO_ROOT_DIR__') ? __TYPECHO_ROOT_DIR__ . '/usr/cache/curve-comment-location' : sys_get_temp_dir() . '/curve-comment-location';
    $cacheFile = $cacheDir . '/' . sha1($ip) . '.json';
    if (is_file($cacheFile) && filemtime($cacheFile) > time() - 604800) {
        $cached = json_decode((string) @file_get_contents($cacheFile), true);
        if (is_array($cached) && isset($cached['location'])) {
            return $runtimeCache[$ip] = (string) $cached['location'];
        }
    }

    $url = 'https://ipwho.is/' . rawurlencode($ip) . '?lang=zh-CN&fields=success,country,country_code,region,city';
    $body = false;
    if (function_exists('curl_init')) {
        $curl = curl_init($url);
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 1,
            CURLOPT_TIMEOUT => 2,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_USERAGENT => 'Curve-Typecho-Theme/1.0',
        ));
        $body = curl_exec($curl);
        curl_close($curl);
    } elseif (filter_var(ini_get('allow_url_fopen'), FILTER_VALIDATE_BOOLEAN)) {
        $context = stream_context_create(array(
            'http' => array('timeout' => 2, 'ignore_errors' => true),
            'ssl' => array('verify_peer' => true, 'verify_peer_name' => true),
        ));
        $body = @file_get_contents($url, false, $context);
    }

    $data = is_string($body) ? json_decode($body, true) : null;
    $location = is_array($data) && (!isset($data['success']) || $data['success']) ? curve_comment_format_location($data) : '未知地区';
    if (!is_dir($cacheDir)) {
        @mkdir($cacheDir, 0755, true);
    }
    @file_put_contents($cacheFile, json_encode(array('location' => $location), JSON_UNESCAPED_UNICODE), LOCK_EX);
    return $runtimeCache[$ip] = $location;
}

/** 返回仅当前评论提交者可见的审核状态文案。 */
function curve_comment_status_label($status)
{
    $labels = array(
        'waiting' => '待审核',
        'spam' => '未通过审核',
        'hidden' => '已隐藏',
    );
    $status = strtolower(trim((string) $status));
    return isset($labels[$status]) ? $labels[$status] : '';
}

/** 返回评论审核状态对应的图标。 */
function curve_comment_status_icon($status)
{
    $icons = array(
        'waiting' => 'icon-time',
        'spam' => 'icon-report',
        'hidden' => 'icon-safe',
    );
    $status = strtolower(trim((string) $status));
    return isset($icons[$status]) ? $icons[$status] : 'icon-time';
}

/** Typecho 评论列表回调。Typecho 会自动查找 threadedComments。 */
function threadedComments($comments, $options)
{
    $isAuthor = (int) $comments->authorId > 0 && (int) $comments->authorId === (int) $comments->ownerId;
    $themeOptions = Typecho_Widget::widget('Widget_Options');
    $showAuthorSensitive = curve_is_enabled($themeOptions, 'commentAuthorShowSensitive', false);
    $showSensitiveMeta = !$isAuthor || $showAuthorSensitive;
    $commentAgent = isset($comments->agent) ? $comments->agent : '';
    $commentIp = isset($comments->ip) ? $comments->ip : '';
    $commentStatus = isset($comments->status) ? $comments->status : 'approved';
    $commentStatusLabel = curve_comment_status_label($commentStatus);
    $commentStatusIcon = curve_comment_status_icon($commentStatus);
    $commentTime = isset($comments->created) ? (int) $comments->created : 0;
    if ($commentTime <= 0) {
        ob_start();
        $comments->date('U');
        $commentTime = (int) trim(ob_get_clean());
    }
    ?>
    <li id="li-<?php $comments->theId(); ?>" class="comment-item">
        <article id="<?php $comments->theId(); ?>" class="comment-item__body<?php echo $isAuthor ? ' is-author-comment' : ''; ?>">
            <div class="comment-item__avatar"><?php $comments->gravatar(72, ''); ?></div>
            <div class="comment-item__content">
                <header>
                    <span class="comment-item__author<?php echo $isAuthor ? ' is-author' : ''; ?>"><?php $comments->author(); ?><?php if ($isAuthor): ?><span class="comment-author-badge">作者</span><?php endif; ?></span>
                    <?php if ($commentStatusLabel !== ''): ?><span class="comment-author-badge comment-item__status is-pending"><i class="iconfont <?php echo curve_esc($commentStatusIcon); ?>" aria-hidden="true"></i><?php echo curve_esc($commentStatusLabel); ?></span><?php endif; ?>
                    <?php $comments->reply(isset($options->replyWord) ? $options->replyWord : '回复'); ?>
                </header>
                <div class="comment-item__meta">
                    <time datetime="<?php echo $commentTime > 0 ? date('c', $commentTime) : ''; ?>" title="<?php echo $commentTime > 0 ? date('Y-m-d H:i', $commentTime) : ''; ?>"><?php echo curve_esc(curve_comment_relative_time($comments)); ?></time>
                    <?php if ($showSensitiveMeta): ?>
                    <span>IP 归属地: <?php echo curve_esc(curve_comment_location($commentIp)); ?></span>
                    <span>系统: <?php echo curve_esc(curve_comment_client_meta($commentAgent)); ?></span>
                    <?php endif; ?>
                </div>
                <div class="comment-item__text"><?php $comments->content(); ?></div>
            </div>
        </article>
        <?php if ($comments->children): ?>
        <div class="children"><?php $comments->threadedComments(); ?></div>
        <?php endif; ?>
    </li>
    <?php
}
