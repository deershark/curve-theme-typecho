(function (window, document) {
  "use strict";

  var CURSOR = "__CURVE_EDITOR_CURSOR__";

  var groups = [
    {
      label: "提示块",
      items: [
        { label: "Info", title: "插入 Info 提示块", template: "::: info\n" + CURSOR + "\n:::" },
        { label: "Tip", title: "插入 Tip 提示块", template: "::: tip\n" + CURSOR + "\n:::" },
        { label: "Warning", title: "插入 Warning 提示块", template: "::: warning\n" + CURSOR + "\n:::" },
        { label: "Danger", title: "插入 Danger 提示块", template: "::: danger STOP\n" + CURSOR + "\n:::" }
      ]
    },
    {
      label: "容器",
      items: [
        { label: "Tabs", title: "插入 Tabs 选项卡", template: ":::tabs\n\n== PHP\n\n```php\n" + CURSOR + "\n```\n\n== JavaScript\n\n```js\nconsole.log(\"Hello Curve\");\n```\n\n:::" },
        { label: "时间线", title: "插入时间线", template: "::: timeline 2024-03-07\n" + CURSOR + "\n:::" },
        { label: "Details", title: "插入 Details 折叠块", template: "::: details 点我展开\n" + CURSOR + "\n:::" },
        { label: "Card", title: "插入 Card 卡片", template: ":::card\n" + CURSOR + "\n:::" }
      ]
    },
    {
      label: "组件",
      items: [
        { label: "Button", title: "插入 Button 按钮", template: "::: button primary\n[" + CURSOR + "](https://example.com)\n:::" },
        { label: "Radio ✓", title: "插入已选中的 Radio", template: "::: radio checked\n" + CURSOR + "\n:::" },
        { label: "Radio", title: "插入未选中的 Radio", template: "::: radio\n" + CURSOR + "\n:::" },
        { label: "LinkCard", title: "插入 LinkCard 链接卡片", template: "<LinkCard url=\"https://example.com\" title=\"标题\" desc=\"描述\" />" }
      ]
    },
    {
      label: "公式 / Admonition",
      items: [
        { label: "行内公式", title: "插入行内数学公式", inline: true, template: "$" + CURSOR + "$" },
        { label: "公式块", title: "插入块级数学公式", template: "$$\n" + CURSOR + "\n$$" },
        { label: "ad-note", title: "插入 ad-note 提示代码块", template: "```ad-note\n" + CURSOR + "\n```" },
        { label: "ad-warning", title: "插入 ad-warning 警告代码块", template: "```ad-warning\n" + CURSOR + "\n```" }
      ]
    }
  ];

  function closest(element, selector) {
    while (element && element !== document) {
      if (element.matches && element.matches(selector)) return element;
      element = element.parentNode;
    }
    return null;
  }

  function insertSnippet(textarea, item) {
    var value = textarea.value;
    var start = typeof textarea.selectionStart === "number" ? textarea.selectionStart : value.length;
    var end = typeof textarea.selectionEnd === "number" ? textarea.selectionEnd : start;
    var selected = value.slice(start, end);
    var hasSelection = selected.length > 0;
    var placeholder = item.inline ? "x^2 + y^2 = z^2" : "这里填写内容";
    var body = hasSelection ? selected : placeholder;
    var hasCursor = item.template.indexOf(CURSOR) !== -1;
    var snippet = item.template.replace(CURSOR, body);
    var before = value.slice(0, start);
    var after = value.slice(end);

    if (!item.inline) {
      if (before && !/\n$/.test(before)) before += "\n";
      if (after && !/^\n/.test(after)) after = "\n" + after;
    }

    var insertionStart = before.length;
    textarea.value = before + snippet + after;
    textarea.dispatchEvent(new Event("input", { bubbles: true }));
    textarea.focus();

    if (!hasSelection && hasCursor) {
      var bodyStart = insertionStart + snippet.indexOf(body);
      textarea.setSelectionRange(bodyStart, bodyStart + body.length);
    } else {
      var caret = insertionStart + snippet.length;
      textarea.setSelectionRange(caret, caret);
    }
  }

  function createButton(textarea, item) {
    var button = document.createElement("button");
    button.type = "button";
    button.className = "curve-editor-toolbar__button";
    button.textContent = item.label;
    button.title = item.title;
    button.addEventListener("mousedown", function (event) {
      event.preventDefault();
    });
    button.addEventListener("click", function () {
      insertSnippet(textarea, item);
    });
    return button;
  }

  function init() {
    var textarea = document.getElementById("text");
    if (!textarea || textarea.dataset.curveEditorReady === "1") return;
    textarea.dataset.curveEditorReady = "1";

    var toolbar = document.createElement("div");
    toolbar.className = "curve-editor-toolbar";
    toolbar.setAttribute("role", "toolbar");
    toolbar.setAttribute("aria-label", "Curve Markdown 快捷插入");

    var label = document.createElement("span");
    label.className = "curve-editor-toolbar__label";
    label.textContent = "Curve 标签";
    toolbar.appendChild(label);

    groups.forEach(function (group) {
      var groupElement = document.createElement("span");
      groupElement.className = "curve-editor-toolbar__group";
      groupElement.setAttribute("aria-label", group.label);
      group.items.forEach(function (item) {
        groupElement.appendChild(createButton(textarea, item));
      });
      toolbar.appendChild(groupElement);
    });

    var hint = document.createElement("span");
    hint.className = "curve-editor-toolbar__hint";
    hint.textContent = "选中文本后点击按钮，可直接将文本放入对应组件。";
    toolbar.appendChild(hint);

    var parent = textarea.parentNode;
    if (parent) parent.insertBefore(toolbar, textarea);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})(window, document);
