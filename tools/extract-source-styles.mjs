import fs from "node:fs";
import path from "node:path";

const sourceRoot = process.argv[2] || "/home/yuco/projects/vitepress-theme-curve/.vitepress/theme";
const output = "assets/scss/components/_source.scss";
let styles = "/* Extracted from vitepress-theme-curve component styles. */\n";

function addFile(file) {
  const source = fs.readFileSync(file, "utf8");
  for (const match of source.matchAll(/<style[^>]*>([\s\S]*?)<\/style>/g)) {
    const style = match[1]
      .replace(/^\s*@use [^;]+;\s*/gm, "")
      .replace(/:deep\(([^()]*)\)/g, "$1");
    styles += `\n/* ${path.relative(sourceRoot, file)} */\n${style}\n`;
  }
}

function walk(directory) {
  for (const name of fs.readdirSync(directory)) {
    const file = path.join(directory, name);
    if (fs.statSync(file).isDirectory()) walk(file);
    else if (file.endsWith(".vue")) addFile(file);
  }
}

addFile(path.join(sourceRoot, "App.vue"));
walk(path.join(sourceRoot, "components"));
walk(path.join(sourceRoot, "views"));
fs.writeFileSync(output, styles);
