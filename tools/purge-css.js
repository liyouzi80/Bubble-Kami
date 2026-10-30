/**
 * 按主题模板与脚本实际用到的 class 精简 assets/css/main.min.css（Argon 全量样式）。
 *
 * 用法（仓库根目录）：
 *   npm i --no-save purgecss@6
 *   git show 1d223d3:assets/css/main.min.css > /tmp/argon-full.css   # 精简前的全量样式
 *   node tools/purge-css.js /tmp/argon-full.css
 *
 * 新增模板 class 时直接重跑即可；仅在 JS 里拼接、或由 Typecho/插件输出的 class 需加入下方 safelist。
 */
const { PurgeCSS } = require('purgecss');
const fs = require('fs');
const path = require('path');

const root = path.resolve(__dirname, '..');
const input = process.argv[2];
if (!input) {
	console.error('用法：node tools/purge-css.js <全量 CSS 路径>');
	process.exit(1);
}

// 文章正文由 Markdown/编辑器生成，模板里看不到这些标签，元素选择器一律保留
const tags = ('html body main header footer nav section article aside div span p a h1 h2 h3 h4 h5 h6 ul ol li dl dt dd '
	+ 'table caption thead tbody tfoot tr th td colgroup col blockquote pre code kbd samp var hr br img picture figure '
	+ 'figcaption video audio iframe embed object svg canvas strong b em i u s del ins sub sup small mark abbr address '
	+ 'cite q dfn details summary input label button select option optgroup textarea fieldset legend output progress '
	+ 'meter template dialog hidden').split(' ');

(async () => {
	const [result] = await new PurgeCSS().purge({
		content: [
			path.join(root, '*.php'),
			path.join(root, 'assets/js/theme.js'),
			path.join(root, 'assets/js/bbrender.js'),
			path.join(root, 'assets/js/progress.js'),
		],
		css: [input],
		safelist: {
			standard: tags.concat([
				// 脚本运行时切换的状态 class
				'show', 'active', 'disabled', 'collapsing', 'nav-solid', 'toc-open', 'located', 'is-sticky', 'sr-only',
				// Typecho 输出的分页 / 评论列表 class
				'current', 'prev', 'next', 'avatar',
				// select.form-control[size]
				'size',
			]),
			// headroom 运行时 class；alert/badge 供 bbcode 短代码与正文手写使用；comment-* 由 Typecho 评论列表输出
			deep: [/^headroom/, /^alert/, /^badge/, /^comment/],
			greedy: [/^headroom/],
		},
	});
	const out = path.join(root, 'assets/css/main.min.css');
	fs.writeFileSync(out, result.css);
	console.log('写入 ' + out + '：' + fs.statSync(input).size + ' → ' + Buffer.byteLength(result.css) + ' 字节');
})();
