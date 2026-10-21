# Bubble Kami

Typecho 清新风格响应式主题（社区维护的 Bubble 优化分支，内置「Kami」纸质书质感外观）。

化繁为简，如沐清风。

Demo: [https://tntofu.com/](https://tntofu.com/)

> 本仓库为社区维护分支（原项目已停止维护），已适配 **Typecho 1.3.0 / PHP 8.x**，并移除了原版中存在安全隐患的自动更新器、失效的在线字体与第三方脚本。使用中如发现问题，欢迎提交 issue 或 PR。
>
> **与原版 Bubble 共存**：安装时请将主题目录命名为 `Bubble Kami`（解压后重命名文件夹），即可与原版 `Bubble` 并存、互不覆盖。

# 特性

+ 清新界面：基于 argon design system 的卡片式布局，内置「Kami」暖色墨蓝、衬线标题的纸质书质感外观
+ 人性化设计：登录后显示后台管理按钮，布局自适应
+ 流畅体验：支持全站 pjax、ajax 评论无刷新
+ 性能优化：静态资源自托管，`main.min.css` 经 CSS 树摇压缩，FontAwesome 子集化为 21 个图标，脚本移至页面底部、正文图片懒加载
+ 简繁转换：自托管脚本，一键切换简体 / 繁体，适配 pjax
+ 代码高亮：自带 prism.js，主题丰富，解析迅速
+ 数学公式：自带 KaTeX 渲染，相比 MathJax 更轻量
+ 短代码：高亮代码框、友链卡片
+ 自定义壁纸：首页固定壁纸、其他页面随机壁纸
+ 自定义样式：支持用户添加 css，随意调整外观
+ 兼容性：适配 Typecho 1.3.0 / PHP 8.x

# 使用

## 安装

从 [Releases](https://github.com/liyouzi80/typecho-Bubble/releases) 下载最新 zip，解压后将目录重命名为 `Bubble Kami`，移动到 Typecho 主题目录 `usr/themes/Bubble Kami`，再到后台启用。若希望与原版 `Bubble` 并存，请务必使用不同的目录名。

> 建议使用 Typecho 1.3.0 及以上版本（PHP 8.0+）。本主题已通过 1.3.0 的兼容性适配，理论上也兼容 1.1/1.2。

## 更新

本分支已移除原版的「一键自动更新」功能（该功能会向主题目录写入无需登录即可访问的更新脚本，存在安全风险），请手动更新：从 Releases 下载新版 zip，删除旧主题目录后重新安装。

> 注意：由于 Typecho 的设计，更新后可能新增设置项；若不禁用主题再重新启用，新设置项会留空并可能导致异常。更新后请重启主题，或检查是否有留空的设置项（主要是选择型设置项）。

> **自动发布**：本仓库已配置 GitHub Actions，向 `main` 分支提交新的主题版本号（`index.php` 中的 `@version`）后，会自动创建对应版本的 Release 并附带主题 zip。

## 设置

主题设置页面：Typecho 后台 → 控制台 → 外观 → 设置外观。

### 外观

+ **站点副标题**：首页标题栏中站点标题后显示的文字。
+ **站点 LOGO**：浏览器标签标题左侧显示的图片 URL（请勿使用相对地址）。
+ **站点头像**：首页大标题上方显示的图片 URL（请勿使用相对地址）。
+ **首页背景图像**：首页背景图片 URL，留空则使用默认紫色渐变背景（请勿使用相对地址）。
+ **随机背景图像地址**：文章页 / 独立页等页面的头图，每行一个 URL，从中随机抽取；留空则使用默认紫色渐变背景（请勿使用相对地址）。
+ **页脚左下角文字**：页脚说明文字（如 Copyright、备案信息），可含 HTML。
+ **站点创建时间**：格式如 `2012-11-17 00:00:00`，用于页脚显示「本站已运行 X 年 X 天」，留空则不显示。
+ **页脚小工具**：是否在页面底部显示「最新评论 / 最新文章 / 近期归档」。
+ **自定义 css**：自定义 css 代码，生效无需清空缓存。

### 功能

+ **全站 pjax 模式**：是否启用全站 pjax。开启后可能影响部分第三方插件（详见「pjax 回调代码」）。
+ **pjax 回调代码**：pjax 渲染完毕后执行的 JS，用于修复第三方插件在无刷新场景下的失效问题。例如：

```js
myFunction();
```

+ **katex 数学公式渲染**：是否启用 KaTeX。
+ **prism.js 代码高亮**：是否启用 prism.js；可进一步选择是否显示行号、高亮主题配色。
+ **TOC 文章目录**：是否启用文章目录；可设置默认展开状态。

## 短代码

*若无法正常使用，请尝试关闭第三方插件（如第三方文章编辑器）。*

### 高亮代码框

标签：`bbcode`（`type` 取值 `success` / `danger` / `warning` / `secondary` / `info` / `default` / `primary`）。

```html
<bbcode type="success">这是绿色高亮代码框</bbcode>
<bbcode type="danger">这是红色高亮代码框</bbcode>
<bbcode type="warning">这是橙色高亮代码框</bbcode>
<bbcode type="secondary">这是灰色高亮代码框</bbcode>
<bbcode type="info">这是蓝绿色高亮代码框</bbcode>
<bbcode type="default">这是深蓝色高亮代码框</bbcode>
<bbcode type="primary">这是紫色高亮代码框</bbcode>
```

### 友链

标签：`bblist`（列表）、`bblink`（项）。

```html
<bblist>
<bblink link="友链 URL" icon="友链图标 URL" des="友链描述">友链名称</bblink>
</bblist>
```

示例：

```html
<bblist>
<bblink link="https://tntofu.com" icon="https://tntofu.com/usr/uploads/2020/03/4228973783.jpg" des="三硝基豆腐的博客">豆腐蒸锅</bblink>
<bblink link="https://blog.boxpaper.club" icon="https://tntofu.com/usr/uploads/2020/04/2709766458.png" des="/ No Result !">Rorical</bblink>
</bblist>
```

# 截图

![screenshot.png](./screenshot.png)

# License

Open sourced under the MIT license.

# 其他

+ 原版主题由 [三硝基豆腐](https://github.com/trinitrotofu)、[Rorical](https://github.com/Liupaperbox) 和 [Totorato](https://github.com/totorato) 三人共同完成
+ 基于 [Argon Design System](https://www.creative-tim.com/product/argon-design-system)
+ 评论模块部分代码参照 Typecho 官方主题 Default
+ 相关主题：[Rorical](https://github.com/Liupaperbox/Rorical) · [Bubble（Totorato 修改版）](https://github.com/totorato/Bubble)

# 更新历史

## 4.2.0

+ 移除「背景气泡」设置项及其渲染逻辑
+ 新增 GitHub Actions 自动发布：提交新的 `@version` 后自动创建 Release 并附带 zip
+ 更新 README 文档

## 4.1.0

+ 适配 Typecho 1.3.0 / PHP 8.x
+ 移除存在安全隐患的「一键自动更新」功能
+ 移除失效的 Google Fonts 镜像，改用系统中文字体栈，避免渲染阻塞
+ 简繁转换改为自托管脚本，默认简体，并适配 pjax
+ 修复文章列表对第三方 Views 浏览统计插件的硬依赖
+ 新增「站点创建时间」设置项
+ 新增 `lang`、Open Graph、`theme-color` 等元信息
+ 将 jQuery、viewer.js 等脚本移至页面底部，消除首屏渲染阻塞
+ `main.min.css` CSS 树摇（234KB → 186KB），移除未使用的组件
+ FontAwesome 子集化，仅保留 21 个图标（31KB+175KB → 约 8KB）
+ 移除 Bootstrap JS 与 Popper.js，导航栏折叠改用原生 JS（约 90KB）
+ 正文图片懒加载
+ 内置「Kami」纸质书质感外观作为默认样式
+ 主题更名为 Bubble Kami，可与原版并存

## 4.0.x

+ 4.0.1：修复副标题显示不正确的问题
+ 4.0.0：卡片式布局、TOC 目录、viewer.js 图片查看器、回到顶部、副标题设置（原版「自动更新」已在本分支移除）

## 3.x

+ 3.0.3：优化 pjax 进度条、图片圆角、搜索框美化等
+ 3.0.2：首页壁纸全屏、翻页自动滚动
+ 3.0.1：修复评论卡托条、左上角标题颜色问题
+ 3.0.0：新增 pjax、ajax 评论、KaTeX、prism.js、短代码；采用 jsDelivr CDN

## 2.x 及更早

原版的早期更新历史，详见原仓库提交记录。
