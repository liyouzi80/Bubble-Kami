<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

function themeConfig($form) {
	themeConfigSeedBackup();
	$subtitle = new Typecho_Widget_Helper_Form_Element_Text('subtitle', NULL, '', _t('站点副标题'), _t('在这里填入站点副标题，以在网站标题后显示'));
	$form->addInput($subtitle);
	$logoUrl = new Typecho_Widget_Helper_Form_Element_Text('logoUrl', NULL, '', _t('站点图标（favicon）地址'), _t('浏览器标签页上显示的小图标，填入图片 URL；留空使用主题自带图标'));
	$form->addInput($logoUrl);
	$navLogo = new Typecho_Widget_Helper_Form_Element_Text('navLogo', NULL, '', _t('导航栏 LOGO 图片'), _t('填入图片 URL 后，左上角用图片代替站点名称；留空则显示文字。导航栏是深色背景，建议用浅色、透明底的 PNG / SVG，高度 80px 以上（显示高度桌面 40px、手机 32px）'));
	$form->addInput($navLogo);
	$titleFont = new Typecho_Widget_Helper_Form_Element_Text('titleFont', NULL, '', _t('标题字体子集文件'), _t('可选。填入用主题 tools/build-title-font.py 为本站生成的 woff2 地址后，标题优先用这个只含本站标题用字的小字体（通常几十 KB），子集里没有的字自动回退到原来的按需字体切片。留空则保持原样'));
	$form->addInput($titleFont);
	$avatarUrl = new Typecho_Widget_Helper_Form_Element_Text('avatarUrl', NULL, '', _t('站点头像地址'), _t('在这里填入一个图片 URL 地址，以在网站首页上加上一个头像'));
	$form->addInput($avatarUrl);
	$indexImage = new Typecho_Widget_Helper_Form_Element_Text('indexImage', NULL, '', _t('首页背景图像地址'), _t('在这里填入一个图片 URL 地址, 以设定网站首页背景图片，留空则使用默认紫色渐变背景'));
	$form->addInput($indexImage);
	$randomImage = new Typecho_Widget_Helper_Form_Element_Textarea('randomImage', NULL, '', _t('随机背景图像地址'), _t('在这里填入一个或多个图片 URL 地址，每行一个，<strong>请勿包含多余字符</strong>，以设定网站文章页、独立页面以及其他页面的头图，设定后将随机显示，留空则使用默认紫色渐变背景'));
	$form->addInput($randomImage);
	$footerText = new Typecho_Widget_Helper_Form_Element_Text('footerText', NULL, 'Powered by <a href="https://typecho.org/" class="footer-link">Typecho</a> · Theme by <a href="https://github.com/liyouzi80/Bubble-Kami" class="footer-link">Bubble Kami</a>', _t('页脚左下角文字'), _t('在这里填入页脚左下角的说明文字，如 Copyright 和 备案信息，可添加 HTML 标签'));
	$form->addInput($footerText);
	$footerWidget = new Typecho_Widget_Helper_Form_Element_Radio('footerWidget', array('0' => _t('不显示'), '1' => _t('显示')), '1', _t('页脚小工具'), _t('选择是否在页面底部显示“最新评论”、“最新文章”等栏目'));
	$form->addInput($footerWidget);
	$siteCreateDate = new Typecho_Widget_Helper_Form_Element_Text('siteCreateDate', NULL, '', _t('站点创建时间'), _t('在这里填入站点创建时间（如 2012-11-17 00:00:00），用于首页统计的运行时长；关闭「首页点赞与统计」时改在页脚显示「距今已有 X 年 X 天」'));
	$form->addInput($siteCreateDate);
	$heroExtras = new Typecho_Widget_Helper_Form_Element_Radio('heroExtras', array('0' => _t('不显示'), '1' => _t('显示')), '1', _t('首页点赞与统计'), _t('在首页大图的题句下方显示点赞按钮，以及文章数、字数、阅读数、评论数与运行时长（统计每小时更新一次；运行时长按「站点创建时间」计算，未填写时从第一篇文章算起）'));
	$form->addInput($heroExtras);
	$stickyCids = new Typecho_Widget_Helper_Form_Element_Text('stickyCids', NULL, '', _t('首页置顶文章 CID'), _t('输入文章 CID，多个以英文逗号分隔；按输入顺序显示在首页最前面。留空则不置顶。'));
	$form->addInput($stickyCids);
	$viewsEnabled = new Typecho_Widget_Helper_Form_Element_Radio('viewsEnabled', array('0' => _t('关闭'), '1' => _t('打开')), '1', _t('文章阅读数'), _t('内置阅读数统计，无需 Views 插件；已有 views 数据会保留。'));
	$form->addInput($viewsEnabled);
	$customCss = new Typecho_Widget_Helper_Form_Element_Textarea('customCss', NULL, '', _t('自定义 css'), _t('在这里填入所需要的 css，以实现自定义页面样式，如调整字体大小等'));
	$form->addInput($customCss);
	$Pjax = new Typecho_Widget_Helper_Form_Element_Radio('Pjax', array('0' => _t('关闭'), '1' => _t('打开')), '1', _t('开启全站 pjax 模式'), _t('选择是否启用全站 pjax 模式提升用户访问体验。注意：启用该项可能带来页面加载问题，请仔细阅读主题说明文档。'));
	$form->addInput($Pjax);
	$pjaxcomp = new Typecho_Widget_Helper_Form_Element_Textarea('pjaxcomp', NULL, '', _t('pjax 回调代码'), _t('在这里填入 pjax 渲染完毕后需执行的 JS 代码，具体使用方法请仔细阅读主题说明文档'));
	$form->addInput($pjaxcomp);
	$katex = new Typecho_Widget_Helper_Form_Element_Radio('katex', array('0' => _t('关闭'), '1' => _t('打开')), '0', _t('开启 katex 数学公式渲染'), _t('选择是否启用 katex 数学公式渲染'));
	$form->addInput($katex);
	$prismjs = new Typecho_Widget_Helper_Form_Element_Radio('prismjs', array('0' => _t('关闭'), '1' => _t('打开')), '0', _t('开启 prism.js 代码高亮'), _t('选择是否启用 prism.js 代码高亮'));
	$form->addInput($prismjs);
	$prismLine = new Typecho_Widget_Helper_Form_Element_Radio('prismLine', array('0' => _t('关闭'), '1' => _t('打开')), '0', _t('开启 prism.js 行号显示'), _t('选择是否显示 prism.js 代码高亮左侧行号'));
	$form->addInput($prismLine);
	$prismTheme = new Typecho_Widget_Helper_Form_Element_Select('prismTheme',
		array('prism' => _t('default'),
			'prism-coy' => _t('coy'),
			'prism-dark' => _t('dark'),
			'prism-funky' => _t('funky'),
			'prism-okaidia' => _t('okaidia'),
			'prism-solarizedlight' => _t('solarizedlight'),
			'prism-tomorrow' => _t('tomorrow'),
			'prism-twilight' => _t('twilight')
		),
	'prism', _t('prism.js 高亮主题'), _t('选择 prism.js 代码高亮的主题配色'));
	$form->addInput($prismTheme);
	$toc = new Typecho_Widget_Helper_Form_Element_Radio('toc',
		array('0' => _t('关闭'),
			'1' => _t('打开'),
		),
		'1', _t('开启 TOC 文章目录功能'), _t('选择是否开启 TOC 文章目录功能'));
	$form->addInput($toc);
	$toc_enable = new Typecho_Widget_Helper_Form_Element_Radio('toc_enable',
		array('0' => _t('关闭'),
			'1' => _t('展开'),
		),
		'0', _t('默认 TOC 目录展开状态'), _t('选择打开文章时 TOC 目录的展开状态'));
	$form->addInput($toc_enable);
}

/**
 * Typecho 会把主题设置存在 `theme:{目录名}` 这一行里；切换到其它主题时
 * `changeTheme()` 会删除该行，导致切回来时设置全部丢失。这里把设置额外
 * 镜像到固定的 `bubblekami:config`，切换主题时它不会被删除，切回时再据此恢复。
 *
 * 注意：该函数一旦定义，Typecho 就不再自行写入 `theme:{目录名}`，
 * 因此这里需要同时维护「备份键」和「当前主题键」两处。
 */
function themeConfigHandle($settings, $isInit = false) {
	$themeKey = 'theme:' . basename(dirname(__FILE__));
	$backupKey = 'bubblekami:config';
	$settings = is_array($settings) ? $settings : array();
	$json = json_encode($settings, JSON_UNESCAPED_UNICODE);

	if ($isInit) {
		// 启用/切回本主题：不要用默认值覆盖已保存的配置，改用备份恢复。
		$backup = themeOptionRead($backupKey);
		if ($backup !== null && $backup !== '') {
			$json = $backup;
		}
	} else {
		// 后台保存：同时更新备份与当前主题配置。
		themeOptionWrite($backupKey, $json);
	}

	themeOptionWrite($themeKey, $json);
}

/**
 * 读取 options 表中的一行（不存在或读取失败时返回 null）。
 */
function themeOptionRead($name) {
	try {
		$db = Typecho_Db::get();
		$row = $db->fetchRow($db->select('value')->from('table.options')->where('name = ?', $name));
		if ($row && isset($row['value'])) {
			return $row['value'];
		}
	} catch (Throwable $e) {
		// 读取失败按不存在处理，不影响页面渲染。
	}
	return null;
}

/**
 * 写入 options 表中的一行（存在则更新，否则插入）。失败返回 false 但不影响页面渲染。
 */
function themeOptionWrite($name, $value) {
	try {
		$db = Typecho_Db::get();
		$exists = $db->fetchRow($db->select('name')->from('table.options')->where('name = ?', $name));
		if ($exists) {
			$db->query($db->update('table.options')->rows(array('value' => $value))->where('name = ?', $name));
		} else {
			$db->query($db->insert('table.options')->rows(array('name' => $name, 'value' => $value, 'user' => 0)));
		}
		return true;
	} catch (Throwable $e) {
		return false;
	}
}

/**
 * 首次启用新版后，把旧的 `theme:{目录名}` 配置镜像到备份键，之后切换主题即可保留配置。
 * 幂等：备份已存在时直接返回。
 */
function themeConfigSeedBackup() {
	$backupKey = 'bubblekami:config';
	// Typecho 每次请求都会把 user=0 的 options 整表读入内存，先查内存，已有备份就不再额外查库
	$options = Helper::options();
	if (isset($options->{$backupKey}) && $options->{$backupKey} !== '') {
		return;
	}
	$backup = themeOptionRead($backupKey);
	if ($backup !== null && $backup !== '') {
		return;
	}
	$current = themeOptionRead('theme:' . basename(dirname(__FILE__)));
	if ($current !== null && $current !== '') {
		themeOptionWrite($backupKey, $current);
	}
}

function printCategory($that, $icon = 0) {
	if (empty($that->categories)) return; ?>
	<span class="list-tag">
		<?php if ($icon) { ?><i class="fa fa-folder-o" aria-hidden="true"></i><?php } ?>
		<?php foreach( $that->categories as $categories): ?>
		<a href="<?php print($categories['permalink']) ?>" class="badge badge-info badge-pill"><?php print($categories['name']) ?></a>
		<?php endforeach;?>
	</span>
<?php }

function printTag($that, $icon = 0) {
	if (empty($that->tags)) return; ?>
	<span class="list-tag">
		<?php if ($icon) { ?><i class="fa fa-tags" aria-hidden="true"></i><?php } ?>
		<?php foreach( $that->tags as $tags): ?>
		<a href="<?php print($tags['permalink']) ?>" class="badge badge-success badge-pill"><?php print($tags['name']) ?></a>
		<?php endforeach;?>
	</span>
<?php }

function getThemeStickyCids($options = null) {
	if ($options === null) {
		$options = Helper::options();
	}
	$value = isset($options->stickyCids) ? (string)$options->stickyCids : '';
	$ids = preg_split('/[^0-9]+/', $value, -1, PREG_SPLIT_NO_EMPTY);
	return array_values(array_unique(array_filter(array_map('intval', $ids))));
}

function isThemeSticky($that) {
	$cid = isset($that->cid) ? (int)$that->cid : 0;
	return !empty($that->order) || ($cid > 0 && in_array($cid, getThemeStickyCids(), true));
}

function printSticky($that, $force = false) {
	if ($force || isThemeSticky($that)) {
		echo '<span class="sticky-label">置顶</span>';
	}
}

function themeViewsEnabled($options = null) {
	if ($options === null) {
		$options = Helper::options();
	}
	return !isset($options->viewsEnabled) || (string)$options->viewsEnabled !== '0';
}

/**
 * 确保 contents.views 字段存在。兼容旧 Views 插件创建的字段，失败时不影响页面渲染。
 */
function ensureThemeViewsColumn() {
	static $ready = null;
	if ($ready !== null) {
		return $ready;
	}
	try {
		$db = Typecho_Db::get();
		$db->fetchRow($db->select('views')->from('table.contents')->limit(1));
		$ready = true;
	} catch (Throwable $e) {
		try {
			$db = Typecho_Db::get();
			$prefix = $db->getPrefix();
			// 不加引号的标准写法，MySQL / SQLite / PostgreSQL 通用（表前缀只含字母数字下划线）
			$db->query('ALTER TABLE ' . $prefix . 'contents ADD COLUMN views INT NOT NULL DEFAULT 0');
			$ready = true;
		} catch (Throwable $migrationError) {
			$ready = false;
		}
	}
	return $ready;
}

function getPostViews($that) {
	if (!themeViewsEnabled() || !ensureThemeViewsColumn()) {
		return null;
	}
	$views = isset($that->views) ? (int)$that->views : 0;
	$cid = isset($that->cid) ? (int)$that->cid : 0;
	$fresh = false;
	if ($cid > 0 && method_exists($that, 'is') && $that->is('single')) {
		try {
			$row = Typecho_Db::get()->fetchRow(Typecho_Db::get()->select('views')->from('table.contents')->where('cid = ?', $cid));
			if ($row && isset($row['views'])) {
				$views = (int)$row['views'];
				$fresh = true;
			}
		} catch (Throwable $e) {
			// 回退到当前文章对象中已有的阅读数。
		}
	}
	if (!$fresh && $cid > 0 && !empty($GLOBALS['kamiViewsIncrementedCid']) && (int)$GLOBALS['kamiViewsIncrementedCid'] === $cid) {
		$views++;
	}
	return max(0, $views);
}

function printArticle($that, $flag, $forceSticky = false) { ?>
	<div class="card shadow content-card list-card <?php if ($flag): ?>content-card-head<?php endif; ?><?php if ($forceSticky || isThemeSticky($that)): ?> is-sticky<?php endif; ?>">
		<section class="section">
			<div class="container">
				<div class="content">
					<h1><a class="text-default post-vt-title" href="<?php $that->permalink() ?>" style="view-transition-name: post-<?php echo (int)$that->cid; ?>"><?php $that->title() ?></a><?php printSticky($that, $forceSticky); ?></h1>
					<div class="list-object">
						<span class="list-tag"><time datetime="<?php $that->date('c'); ?>"><?php $that->date('Y.m.d'); ?></time></span>
						<?php $views = getPostViews($that); if ($views !== null): ?>
						<span class="list-tag">阅读 <?php echo number_format($views); ?></span>
						<?php endif; ?>
						<?php if ((int)$that->commentsNum > 0): ?>
						<span class="list-tag"><?php $that->commentsNum('%d'); ?> 条评论</span>
						<?php endif; ?>
						<?php if (!empty($that->categories)): ?>
						<span class="list-tag list-cat">
							<?php
								$categoryLinks = array();
								foreach ($that->categories as $category) {
									$categoryLinks[] = '<a href="' . $category['permalink'] . '">' . $category['name'] . '</a>';
								}
								echo implode('、', $categoryLinks);
							?>
						</span>
						<?php endif; ?>
					</div>
					<?php echo lazyLoadImages($that->content(''), true); ?>
					<a class="continue-reading" href="<?php $that->permalink() ?>">继续阅读 <span aria-hidden="true">→</span></a>
				</div>
			</div>
		</section>
	</div>
<?php }

function printToggleButton($that) {
	if ($that->getTotal() > $that->parameter->pageSize) { ?>
		<section class="section" style="padding-bottom: 1rem; padding-top: 6rem">
			<div class="container">
				<nav class="page-nav"><?php $that->pageNav('<i class="fa fa-angle-left" aria-hidden="true"></i>', '<i class="fa fa-angle-right" aria-hidden="true"></i>', 1, '...', array('wrapTag' => 'ul', 'wrapClass' => 'pagination justify-content-center', 'textTag' => 'a', 'currentClass' => 'active', 'prevClass' => '', 'nextClass' => '')); ?></nav>
			</div>
		</section>
	<?php }
}

function relatedPosts($that, $limit = 5) {
	$mids = array();
	if (!empty($that->tags)) {
		foreach ($that->tags as $tag) {
			$mids[] = $tag['mid'];
		}
	}
	if (empty($mids) && !empty($that->categories)) {
		foreach ($that->categories as $cat) {
			$mids[] = $cat['mid'];
		}
	}
	if (empty($mids)) {
		return;
	}
	$mids = array_unique($mids);

	$tagParams = array();
	foreach ($mids as $mid) {
		$tagParams[] = array('mid' => $mid);
	}

	$related = $that->widget('Widget_Contents_Related', array(
		'cid' => $that->cid,
		'type' => 'post',
		'tags' => $tagParams,
		'limit' => $limit,
	));

	if (!$related->have()) {
		return;
	}
	?>
	<div class="card shadow content-card related-posts-card">
		<section class="section">
			<div class="container">
				<h3 class="related-posts-title"><i class="fa fa-link" aria-hidden="true"></i> 相关文章</h3>
				<ul class="related-posts-list">
					<?php while ($related->next()): ?>
					<li class="related-posts-item">
						<a class="related-posts-link" href="<?php $related->permalink(); ?>"><?php $related->title(); ?></a>
						<span class="related-posts-date"><?php $related->date('Y.m.d'); ?></span>
					</li>
					<?php endwhile; ?>
				</ul>
			</div>
		</section>
	</div>
	<?php
}

function printBackground($url) {
	_e('<div ');
	if ($url == '') _e('class="shape shape-style-1 shape-primary"');
	else _e('class="shape shape-style-1 shape-image" style="background-image: url(\'' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '\')"');
	_e('></div>');
}

function getRandomImage($str)
{
	// 后台 textarea 提交的是 \r\n，按任意换行切分并去掉空行，避免抽到空地址或带 \r 的 URL
	$arr = array_values(array_filter(array_map('trim', preg_split('/\R/', (string)$str))));
	if (empty($arr)) return '';
	return $arr[mt_rand(0, count($arr) - 1)];
}

function createCatalog($obj) {
	global $catalog;
	global $catalog_count;
	$catalog = array();
	$catalog_count = 0;
	$obj = preg_replace_callback('/<h([1-6])(.*?)>(.*?)<\/h\1>/i', function($obj) {
		global $catalog;
		global $catalog_count;
		$catalog_count ++;
		$catalog[] = array('text' => trim(strip_tags($obj[3])), 'depth' => $obj[1], 'count' => $catalog_count);
		return '<h'.$obj[1].$obj[2].'><a id="cl-'.$catalog_count.'" class="toc-anchor"></a>'.$obj[3].'</h'.$obj[1].'>';
	}, $obj);
	return $obj;
}

function getCatalog() {
	global $catalog;
	$index = '';
	if ($catalog) {
		$index = '<ul>'."\n";
		$prev_depth = '';
		$to_depth = 0;
		foreach($catalog as $catalog_item) {
			$catalog_depth = $catalog_item['depth'];
			if ($prev_depth) {
				if ($catalog_depth == $prev_depth) {
					$index .= '</li>'."\n";
				} elseif ($catalog_depth > $prev_depth) {
					$to_depth++;
					$index .= '<ul>'."\n";
				} else {
					$to_depth2 = ($to_depth > ($prev_depth - $catalog_depth)) ? ($prev_depth - $catalog_depth) : $to_depth;
					if ($to_depth2) {
						for ($i=0; $i<$to_depth2; $i++) {
							$index .= '</li>'."\n".'</ul>'."\n";
							$to_depth--;
						}
					}
					$index .= '</li>';
				}
			}
			$index .= '<li><a id="dl-' . $catalog_item['count'] . '" href="#cl-' . $catalog_item['count'] . '">'.$catalog_item['text'].'</a>';
			$prev_depth = $catalog_item['depth'];
		}
		for ($i=0; $i<=$to_depth; $i++) {
			$index .= '</li>'."\n".'</ul>'."\n";
		}
	}
	echo $index;
}

/**
 * 本站上传目录里的图片没写宽高时，读取文件补上 width / height。
 * 浏览器据此提前按比例留出位置，图片加载完不会把下面的文字顶下去（CLS）；CSS 里 height:auto 保证不变形。
 */
function themeImageSize($attrs) {
	if (preg_match('/\swidth\s*=/i', $attrs) || preg_match('/\sheight\s*=/i', $attrs)) {
		return '';
	}
	if (!preg_match('/\ssrc\s*=\s*["\']([^"\']+)["\']/i', $attrs, $m)) {
		return '';
	}
	$src = html_entity_decode($m[1], ENT_QUOTES, 'UTF-8');
	$parts = parse_url($src);
	if (!$parts || empty($parts['path'])) {
		return '';
	}
	// 只处理本站地址（含 www 与否、http 与 https）或相对地址
	if (!empty($parts['host'])) {
		$siteHost = parse_url((string)Helper::options()->siteUrl, PHP_URL_HOST);
		if (!$siteHost || preg_replace('/^www\./i', '', strtolower($parts['host'])) !== preg_replace('/^www\./i', '', strtolower($siteHost))) {
			return '';
		}
	}
	$path = rawurldecode($parts['path']);
	if (strpos($path, '/usr/uploads/') !== 0 || strpos($path, '..') !== false) {
		return '';
	}
	$file = __TYPECHO_ROOT_DIR__ . $path;
	if (!is_file($file)) {
		return '';
	}
	$size = @getimagesize($file);
	if (!$size || empty($size[0]) || empty($size[1])) {
		return '';
	}
	return ' width="' . (int)$size[0] . '" height="' . (int)$size[1] . '"';
}

function lazyLoadImages($content, $allLazy = false) {
	if (empty($content) || strpos($content, '<img') === false) {
		return $content;
	}
	$first = true;
	return preg_replace_callback('/<img\b([^>]*)>/i', function ($m) use (&$first, $allLazy) {
		$attrs = $m[1];
		$selfclose = '';
		if (substr($attrs, -1) === '/') {
			$attrs = rtrim(substr($attrs, 0, -1));
			$selfclose = ' /';
		}
		$add = themeImageSize($attrs);
		if (!$allLazy && $first) {
			// 单篇正文首张图通常是首屏 LCP：优先加载；其余懒加载
			$first = false;
			if (stripos($attrs, 'fetchpriority=') === false) {
				$add .= ' fetchpriority="high"';
			}
			if (stripos($attrs, 'decoding=') === false) {
				$add .= ' decoding="async"';
			}
		} else {
			// 列表页（首页全文流）所有图片一律懒加载，避免十几篇正文的图片抢占首屏带宽
			if (stripos($attrs, 'loading=') === false) {
				$add .= ' loading="lazy"';
			}
			if (stripos($attrs, 'decoding=') === false) {
				$add .= ' decoding="async"';
			}
		}
		return '<img' . $attrs . $add . $selfclose . '>';
	}, $content);
}

/**
 * 输出带版本号的主题静态资源 URL，用于浏览器缓存更新。
 * 基于文件修改时间自动生成版本号，文件更新后旧缓存自动失效。
 */
function themeAsset($path) {
	echo themeAssetUrl($path);
}

function themeAssetUrl($path) {
	static $cache = array();
	$file = __DIR__ . '/' . ltrim($path, '/');
	if (!array_key_exists($path, $cache)) {
		$cache[$path] = is_file($file) ? filemtime($file) : '1';
	}
	// Typecho 1.2+ 的 themeUrl() 直接 echo，旧版本可能是返回值，两种都兼容
	ob_start();
	$returned = Helper::options()->themeUrl($path);
	$echoed = ob_get_clean();
	return ($echoed !== '' ? $echoed : (string)$returned) . '?v=' . $cache[$path];
}

/**
 * 输出到 HTML 属性的纯文本：去标签、还原已有实体后统一转义，避免重复转义。
 */
function themeAttr($text) {
	$text = html_entity_decode(strip_tags((string)$text), ENT_QUOTES, 'UTF-8');
	return htmlspecialchars(trim(preg_replace('/\s+/u', ' ', $text)), ENT_QUOTES, 'UTF-8');
}

/**
 * 分享卡片（Open Graph / Twitter）与 JSON-LD 共用的页面信息。
 * 文章页取摘要与正文第一张图；加密文章与列表页回退到站点描述与头像。
 */
function themeShareMeta($archive) {
	$options = Helper::options();
	$avatar = $options->avatarUrl == '' ? themeAssetUrl('images/avatar.png') : (string)$options->avatarUrl;
	$meta = array(
		'type' => 'website',
		'title' => (string)$options->title,
		'description' => (string)$options->description,
		'image' => $avatar,
		'hasPostImage' => false,
	);
	if (!$archive->is('single')) {
		return $meta;
	}
	$meta['title'] = (string)$archive->title;
	if ($archive->is('post')) {
		$meta['type'] = 'article';
	}
	if ($archive->hidden) {
		return $meta;
	}
	$excerpt = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags((string)$archive->excerpt), ENT_QUOTES, 'UTF-8')));
	if ($excerpt !== '') {
		$meta['description'] = Typecho_Common::subStr($excerpt, 0, 120, '…');
	}
	if (preg_match('/<img\b[^>]*?\ssrc=["\']([^"\']+)["\']/i', (string)$archive->content, $m)) {
		$src = html_entity_decode($m[1], ENT_QUOTES, 'UTF-8');
		if (strpos($src, '//') === 0) {
			$src = (parse_url((string)$options->siteUrl, PHP_URL_SCHEME) ?: 'https') . ':' . $src;
		} elseif (strpos($src, '/') === 0) {
			$parts = parse_url((string)$options->siteUrl);
			$src = $parts['scheme'] . '://' . $parts['host'] . (isset($parts['port']) ? ':' . $parts['port'] : '') . $src;
		}
		if (preg_match('#^https?://#i', $src)) {
			$meta['image'] = $src;
			$meta['hasPostImage'] = true;
		}
	}
	return $meta;
}

/**
 * 浏览器预取/预渲染（Speculation Rules、<link rel=prefetch>）发出的请求，用户未必真的打开页面，不计阅读数。
 */
function isThemePrefetchRequest() {
	foreach (array('HTTP_SEC_PURPOSE', 'HTTP_PURPOSE', 'HTTP_X_PURPOSE', 'HTTP_X_MOZ') as $key) {
		if (!empty($_SERVER[$key]) && stripos($_SERVER[$key], 'prefetch') !== false) {
			return true;
		}
	}
	return false;
}

function themeInit($archive) {
	themeConfigSeedBackup();
	if ($archive->is('index')) {
		themeHandleLike($archive);
	}
	if (!$archive->is('single')) {
		return;
	}

	$archive->content = createCatalog($archive->content);
	if (empty($GLOBALS['kamiLoadingSticky']) && !isThemePrefetchRequest() && themeViewsEnabled() && ensureThemeViewsColumn() && $archive->type === 'post'
		&& (!isset($archive->status) || $archive->status === 'publish')
		&& !(class_exists('Views_Plugin') && method_exists('Views_Plugin', 'theViews'))
	) {
		$cid = (int)$archive->cid;
		if ($cid > 0) {
			try {
				$db = Typecho_Db::get();
				$db->query($db->update('table.contents')->expression('views', 'views + 1')->where('cid = ?', $cid));
				$GLOBALS['kamiViewsIncrementedCid'] = $cid;
			} catch (Throwable $e) {
				// 阅读数统计失败时继续正常渲染文章。
			}
		}
	}

	$archive->content = lazyLoadImages($archive->content);
}

/**
 * 计算站点运行时长，格式 年 天 时 分
 * 站点创建时间可在主题设置中配置，留空则不显示
 */
function getBuildTime()
{
	$created = strtotime((string)Helper::options()->siteCreateDate);
	if (!$created) {
		return;
	}
	// 按日历计算（与首页统计的运行时长一致）：先算整年，再算距上个周年日的天、小时、分钟
	$start = new DateTime('@' . $created);
	$now = new DateTime('@' . time());
	if ($now < $start) {
		return;
	}
	$years = (int)$start->diff($now)->y;
	$anniversary = (clone $start)->modify('+' . $years . ' years');
	$rest = $now->getTimestamp() - $anniversary->getTimestamp();
	echo '<span class="btime"> 距今已有 ' . $years . ' 年 ' . intdiv($rest, 86400) . ' 天 '
		. intdiv($rest % 86400, 3600) . ' 小时 ' . intdiv($rest % 3600, 60) . ' 分</span>';
}

/* ---------- 首页点赞 ----------
 * 不去重：访客可以一直点。前端把连续点击合并成一次请求（每次最多计 50 下），
 * 服务端每个 IP 每秒最多受理一次，防止脚本刷爆数据库。总数存在 options 表的 bubblekami:likes。 */
function themeLikeCount() {
	return max(0, (int)themeOptionRead('bubblekami:likes'));
}

function themeLikeAdd($n) {
	$name = 'bubblekami:likes';
	if (themeOptionRead($name) === null) {
		themeOptionWrite($name, '0');
	}
	try {
		// MySQL / SQLite 可直接在文本列上做原子加法
		$db = Typecho_Db::get();
		$db->query($db->update('table.options')->expression('value', 'value + ' . (int)$n)->where('name = ?', $name));
	} catch (Throwable $e) {
		// PostgreSQL 等不支持文本与数字相加：退回读出再写入
		themeOptionWrite($name, (string)(themeLikeCount() + (int)$n));
	}
}

function themeHandleLike($archive) {
	$request = $archive->request;
	if (!$request->isPost() || $request->get('bk_action') !== 'like') {
		return;
	}
	$n = max(1, min(50, (int)$request->get('n')));
	$file = rtrim(sys_get_temp_dir(), '/\\') . '/bubblekami-like-' . md5($request->getIp() . '|' . __DIR__);
	$now = microtime(true);
	$accepted = !is_file($file) || $now - (float)@file_get_contents($file) >= 1;
	if ($accepted) {
		@file_put_contents($file, (string)$now, LOCK_EX);
		themeLikeAdd($n);
	}
	header('Content-Type: application/json; charset=utf-8');
	header('Cache-Control: no-store');
	echo json_encode(array('ok' => $accepted, 'count' => themeLikeCount()));
	exit;
}

/* ---------- 首页统计 ---------- */

/** 正文字数：中文按字、英文与数字按词计，不计代码块、图片与链接地址 */
function themeCountWords($text) {
	$t = preg_replace('/^\s*<!--markdown-->/', '', (string)$text);
	$t = preg_replace('/```.*?```/s', ' ', $t);
	$t = preg_replace('/<(script|style|pre|code)\b.*?<\/\1>/is', ' ', $t);
	$t = preg_replace('/!\[[^\]]*\]\([^)]*\)/', ' ', $t);
	$t = preg_replace('/\[([^\]]*)\]\([^)]*\)/', '$1', $t);
	$t = preg_replace('#https?://\S+#i', ' ', $t);
	$t = html_entity_decode(strip_tags($t), ENT_QUOTES, 'UTF-8');
	$han = preg_match_all('/[\x{3400}-\x{9fff}\x{f900}-\x{faff}]/u', $t);
	$latin = preg_match_all("/[A-Za-z0-9]+(?:['’-][A-Za-z0-9]+)*/u", $t);
	return (int)$han + (int)$latin;
}

/** 文章数、字数、阅读数、评论数与建站时间，缓存在 options 表，每小时重新计算一次 */
function themeSiteStats() {
	$name = 'bubblekami:stats';
	$cache = json_decode((string)themeOptionRead($name), true);
	if (is_array($cache) && isset($cache['t']) && time() - (int)$cache['t'] < 3600) {
		return $cache;
	}
	$stats = array('t' => time(), 'posts' => 0, 'words' => 0, 'views' => 0, 'comments' => 0, 'since' => 0);
	try {
		$db = Typecho_Db::get();
		$rows = $db->fetchAll($db->select('text', 'password', 'created')->from('table.contents')
			->where('type = ?', 'post')->where('status = ?', 'publish'));
		$stats['posts'] = count($rows);
		foreach ($rows as $row) {
			if (empty($row['password'])) {
				$stats['words'] += themeCountWords($row['text']);
			}
			if (!$stats['since'] || (int)$row['created'] < $stats['since']) {
				$stats['since'] = (int)$row['created'];
			}
		}
		if (ensureThemeViewsColumn()) {
			$row = $db->fetchObject($db->select(array('SUM(views)' => 'num'))->from('table.contents')
				->where('type = ?', 'post')->where('status = ?', 'publish'));
			$stats['views'] = (int)$row->num;
		}
		$row = $db->fetchObject($db->select(array('COUNT(coid)' => 'num'))->from('table.comments')
			->where('status = ?', 'approved')->where('type = ?', 'comment'));
		$stats['comments'] = (int)$row->num;
	} catch (Throwable $e) {
		// 统计失败不影响首页渲染
	}
	themeOptionWrite($name, json_encode($stats));
	return $stats;
}

/** 大数字缩写：1,234 / 5.99 万 / 22.3 万 */
function themeShortNumber($n) {
	$n = (int)$n;
	if ($n < 10000) {
		return number_format($n);
	}
	$v = $n / 10000;
	return rtrim(rtrim(number_format($v, $v >= 10 ? 1 : 2, '.', ''), '0'), '.') . ' 万';
}

/** 运行时长 [年, 天]：优先用「站点创建时间」，未填写时从第一篇文章算起 */
function themeRunningTime($since = 0) {
	$created = strtotime((string)Helper::options()->siteCreateDate);
	$from = $created ?: (int)$since;
	if (!$from) {
		return null;
	}
	$start = new DateTime('@' . $from);
	$now = new DateTime('@' . time());
	$years = (int)$start->diff($now)->y;
	$anniversary = (clone $start)->modify('+' . $years . ' years');
	return array($years, (int)floor(($now->getTimestamp() - $anniversary->getTimestamp()) / 86400));
}

