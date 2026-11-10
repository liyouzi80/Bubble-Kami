# Bubble Kami

Typecho 响应式主题：社区维护的 Bubble 优化分支，内置「Kami」纸质书质感外观。

> 适配 **Typecho 1.3 / PHP 8**（兼容 1.2）。原项目已停止维护，本分支移除了原版有安全隐患的在线更新器与失效的外部资源。目录名为 `Bubble-Kami`，可与原版 `Bubble` 并存。

![Bubble Kami 截图](./screenshot.jpg)

## 特性

+ **纸质书外观**：暖色纸面、墨蓝点缀、衬线标题，桌面与手机自适应；跟随系统自动切换深色模式
+ **阅读**：顶部阅读进度线、「继续阅读」提示、上一篇 / 下一篇与相关文章
+ **看图**：图片加载后淡入；点图从原位放大，可缩放、看原图、左右切换
+ **切页**：浏览器原生的跨页过渡，文章标题从列表平滑移到文章页，上一篇 / 下一篇与翻页像翻书一样分左右滑入；Chrome / Edge 悬停即预加载
+ **中文排版**：中英文自动留白、标点挤压，简繁一键切换
+ **页面模板**：时间轴目录、友链、关于、书架（同步微信读书书单）、404
+ **首页**：点赞与博客统计（文章、字数、阅读、评论、运行时长）、置顶文章
+ **轻量**：资源全部自托管，不依赖 jQuery 与 Bootstrap；每页只有一个样式文件和一个脚本，图片查看器、简繁转换用到时才加载

更新记录见 [CHANGELOG.md](./CHANGELOG.md)。

## 安装与更新

从 [Releases](https://github.com/liyouzi80/Bubble-Kami/releases) 下载 zip，解压得到 `Bubble-Kami` 目录，放进 `usr/themes/`，到后台启用。

更新时删除旧目录、放入新目录即可。设置会保留，切换到别的主题再切回来也会自动恢复；新版本增加的设置项可能为空，到「设置外观」确认一遍。

## 设置

后台 → 外观 → 设置外观。图片地址请填完整 URL。

| 设置 | 说明 |
|---|---|
| 站点副标题 | 显示在站点标题后 |
| 站点图标（favicon） | 标签页小图标，PNG / SVG / ICO 均可；留空用主题自带 |
| 主屏幕图标 | iPhone「添加到主屏幕」的图标，180×180 PNG；留空时若站点图标是 PNG / JPG 就用它 |
| 导航栏 LOGO | 用图片代替左上角站名。导航栏是深色，建议浅色透明底、高 80px 以上 |
| 标题字体子集 | 可选，见「维护 → 生成标题字体子集」 |
| 站点头像 | 首页大标题上方的头像，关于页、友链页也会用到 |
| 首页背景 / 随机背景 | 首页大图；其它页面的头图每行一个 URL 随机抽取。留空用墨蓝渐变 |
| 页脚文字 | 页脚左下角，可含 HTML（版权、备案等） |
| 站点创建时间 | 如 `2012-11-17 00:00:00`，用于运行时长；留空则从第一篇文章算起 |
| 首页点赞与统计 | 首页题句下方的点赞按钮与统计；关闭后运行时长改在页脚显示 |
| 页脚小工具 | 页面底部的最新评论 / 最新文章 / 近期归档 |
| 首页置顶文章 | 文章 CID，多个用英文逗号分隔，按顺序置顶 |
| 文章阅读数 | 显示并累加阅读数，兼容 Views 插件的数据 |
| 自定义 CSS | 追加的样式，写在主题样式之后 |

## 页面模板

新建独立页面，在右侧「自定义模板」选择：

+ **目录（时间轴归档）**：按年份列出全部文章，可按分类筛选；正文内容显示在时间轴上方。
+ **友链**：正文每行一个 `站名 | 地址 | 头像 | 描述`（头像、描述可省略），其余文字作为说明；页面下方附本站信息供复制。
+ **关于**：正文即自我介绍，上方自动显示头像、站名与统计；自定义字段 `email` 可显示邮箱。
+ **书架**：正文贴上公开的微信读书书单链接（`https://weread.qq.com/misc/booklist/…`），显示封面、作者与评分，每天自动同步。

## 写文章小贴士

+ 首页显示到正文里的 `<!--more-->` 为止；没有 `<!--more-->` 时显示全文。
+ 上传到本站的图片会自动补上宽高，加载时版面不跳动。
+ 正文图片点开看大图；图片外面包了链接时，链接指向图片文件才看大图，指向网页则照常跳转。
+ 主题不带 Bootstrap：文章里手写 `alert`、`badge`、`table-striped` 这类 class 不会有样式，需要的话写进「自定义 CSS」。

## 常见问题

+ **阅读数比访问量少？** 搜索引擎爬虫、监控与命令行请求不计数，同一位访客 30 分钟内重复打开同一篇只算一次。
+ **深色模式怎么切换？** 跟随系统的「深色外观」自动切换，没有单独的开关；打印始终是浅色。
+ **改了样式没生效？** 主题资源地址带文件修改时间做版本号，重新上传后强制刷新一次即可。

## 维护

面向仓库维护者，使用主题无需关心。

**目录**

```
index.php / archive.php / post.php / page.php / 404.php   页面模板
page-archives.php / page-links.php / page-about.php / page-books.php   自定义页面模板
header.php / footer.php / comments.php   公共部分
functions.php   设置项、阅读数、点赞、统计、书架抓取等
assets/css/kami.css   全部样式（含基础层与深色模式）
assets/js/theme.js   全部交互；PhotoSwipe、tw-cn.js 由它按需加载
tools/   维护脚本，不打包进发布的 zip
```

**自动发布**：推送到 `main` 后，GitHub Actions 按 `index.php` 的 `@version` 打包。新版本号创建 Release；版本号不变则覆盖同版本的 zip，并把标签移到最新提交。只改文档、`tools/`、`.github/` 时不触发，可在 Actions 页面手动运行。

**样式约定**：kami.css 第零章「基础层」是从 Bootstrap 4 / Argon 精简来的重置、栅格、导航栏与表单（只保留本站用到的声明，规则顺序不要随意调整），其后按页面区域分章。新样式写进对应章节；颜色用 `:root` 里的变量，深色模式只在最后一章重新定义变量；尽量不用 `!important`，也不要再引入 CSS 框架。

**生成标题字体子集**：标题默认按需加载思源宋体切片，用字分散时一页要下载十几片。为本站生成只含标题用字的子集（通常几十 KB）：

```bash
pip install fonttools brotli
python3 tools/build-title-font.py https://你的域名/ -o title-font.woff2
```

上传生成的 woff2，填入「标题字体子集」。子集里没有的字会自动回退到原切片；发了新文章后重新生成即可。加 `--with-headings` 会一并收录正文小标题。

**为旧图片补登记附件**：从 WordPress 等迁移来的图片没有附件记录，后台「文件」里看不到。备份数据库后，把 `tools/register-attachments.php` 放到 Typecho 根目录，运行 `php register-attachments.php` 预览，加 `--apply` 写入（`--only-referenced` 只登记被文章引用的文件）。没有命令行时，改脚本里的 `RUN_KEY` 后用浏览器访问 `/register-attachments.php?key=…`，加 `&apply=1` 写入。用完删除脚本。

## License 与致谢

MIT License。

+ 原版主题：[三硝基豆腐](https://github.com/trinitrotofu)、[Rorical](https://github.com/Liupaperbox)、[Totorato](https://github.com/totorato)；评论模块参照 Typecho 官方主题
+ 基础样式精简自 [Bootstrap 4](https://getbootstrap.com)（MIT）与 [Argon Design System](https://www.creative-tim.com/product/argon-design-system)（MIT）
+ 自带资源：[PhotoSwipe](https://photoswipe.com)（MIT）、[Font Awesome 4.7](https://fontawesome.com/v4/) 精简版（SIL OFL 1.1 / MIT）、[Noto Serif SC](https://fonts.google.com/noto/specimen/Noto+Serif+SC)（SIL OFL 1.1）
