<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

function themeConfig($form) {
	themeConfigSeedBackup();
	$subtitle = new \Typecho\Widget\Helper\Form\Element\Text('subtitle', NULL, '', _t('站点副标题'), _t('在这里填入站点副标题，以在网站标题后显示'));
	$form->addInput($subtitle);
	$logoUrl = new \Typecho\Widget\Helper\Form\Element\Text('logoUrl', NULL, '', _t('站点图标（favicon）地址'), _t('浏览器标签页上显示的小图标，填入图片 URL；留空使用主题自带图标'));
	$form->addInput($logoUrl);
	$touchIcon = new \Typecho\Widget\Helper\Form\Element\Text('touchIcon', NULL, '', _t('主屏幕图标地址'), _t('iPhone / iPad「添加到主屏幕」时的图标，填入 180×180 的 PNG 图片 URL（不支持 SVG，透明背景会被填成黑色）；留空时站点图标是 PNG / JPG 就用它，否则不设置'));
	$form->addInput($touchIcon);
	$navLogo = new \Typecho\Widget\Helper\Form\Element\Text('navLogo', NULL, '', _t('导航栏 LOGO 图片'), _t('填入图片 URL 后，左上角用图片代替站点名称；留空则显示文字。导航栏是深色背景，建议用浅色、透明底的 PNG / SVG，高度 80px 以上（显示高度桌面 40px、手机 32px）'));
	$form->addInput($navLogo);
	$titleFont = new \Typecho\Widget\Helper\Form\Element\Text('titleFont', NULL, '', _t('标题字体子集文件'), _t('可选。填入用主题 tools/build-title-font.py 为本站生成的 woff2 地址后，标题优先用这个只含本站标题用字的小字体（通常几十 KB），子集里没有的字自动回退到原来的按需字体切片。留空则保持原样'));
	$form->addInput($titleFont);
	$avatarUrl = new \Typecho\Widget\Helper\Form\Element\Text('avatarUrl', NULL, '', _t('站点头像地址'), _t('在这里填入一个图片 URL 地址，以在网站首页上加上一个头像'));
	$form->addInput($avatarUrl);
	$indexImage = new \Typecho\Widget\Helper\Form\Element\Text('indexImage', NULL, '', _t('首页背景图像地址'), _t('在这里填入一个图片 URL 地址, 以设定网站首页背景图片，留空则使用默认墨蓝渐变背景'));
	$form->addInput($indexImage);
	$randomImage = new \Typecho\Widget\Helper\Form\Element\Textarea('randomImage', NULL, '', _t('随机背景图像地址'), _t('在这里填入一个或多个图片 URL 地址，每行一个，<strong>请勿包含多余字符</strong>，以设定网站文章页、独立页面以及其他页面的头图，设定后将随机显示，留空则使用默认墨蓝渐变背景'));
	$form->addInput($randomImage);
	$footerText = new \Typecho\Widget\Helper\Form\Element\Text('footerText', NULL, 'Powered by <a href="https://typecho.org/" class="footer-link">Typecho</a> · Theme by <a href="https://github.com/liyouzi80/Bubble-Kami" class="footer-link">Bubble Kami</a>', _t('页脚左下角文字'), _t('在这里填入页脚左下角的说明文字，如 Copyright 和 备案信息，可添加 HTML 标签'));
	$form->addInput($footerText);
	$footerWidget = new \Typecho\Widget\Helper\Form\Element\Radio('footerWidget', array('0' => _t('不显示'), '1' => _t('显示')), '1', _t('页脚小工具'), _t('选择是否在页面底部显示“最新评论”、“最新文章”等栏目'));
	$form->addInput($footerWidget);
	$siteCreateDate = new \Typecho\Widget\Helper\Form\Element\Text('siteCreateDate', NULL, '', _t('站点创建时间'), _t('在这里填入站点创建时间（如 2012-11-17 00:00:00），用于首页统计的运行时长；关闭「首页点赞与统计」时改在页脚显示「距今已有 X 年 X 天」'));
	$form->addInput($siteCreateDate);
	$heroExtras = new \Typecho\Widget\Helper\Form\Element\Radio('heroExtras', array('0' => _t('不显示'), '1' => _t('显示')), '1', _t('首页点赞与统计'), _t('在首页大图的题句下方显示点赞按钮，以及文章数、字数、阅读数、评论数与运行时长（发文、改文、新评论后立即更新；运行时长按「站点创建时间」计算，未填写时从第一篇文章算起）'));
	$form->addInput($heroExtras);
	$stickyCids = new \Typecho\Widget\Helper\Form\Element\Text('stickyCids', NULL, '', _t('首页置顶文章 CID'), _t('输入文章 CID，多个以英文逗号分隔；按输入顺序显示在首页最前面。留空则不置顶。'));
	$form->addInput($stickyCids);
	$viewsEnabled = new \Typecho\Widget\Helper\Form\Element\Radio('viewsEnabled', array('0' => _t('关闭'), '1' => _t('打开')), '1', _t('文章阅读数'), _t('内置阅读数统计，无需 Views 插件；已有 views 数据会保留。'));
	$form->addInput($viewsEnabled);
	$customCss = new \Typecho\Widget\Helper\Form\Element\Textarea('customCss', NULL, '', _t('自定义 css'), _t('在这里填入所需要的 css，以实现自定义页面样式，如调整字体大小等'));
	$form->addInput($customCss);
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
		$db = \Typecho\Db::get();
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
		$db = \Typecho\Db::get();
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
	$options = \Utils\Helper::options();
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

function getThemeStickyCids($options = null) {
	if ($options === null) {
		$options = \Utils\Helper::options();
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
		$options = \Utils\Helper::options();
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
		$db = \Typecho\Db::get();
		$db->fetchRow($db->select('views')->from('table.contents')->limit(1));
		$ready = true;
	} catch (Throwable $e) {
		try {
			$db = \Typecho\Db::get();
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
			$row = \Typecho\Db::get()->fetchRow(\Typecho\Db::get()->select('views')->from('table.contents')->where('cid = ?', $cid));
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

	$related = \Widget\Contents\Related::alloc(array(
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
				<h3 class="related-posts-title">相关文章</h3>
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
	$arr = array_values(array_filter(array_map('trim', preg_split('/\R/u', (string)$str))));
	if (empty($arr)) return '';
	return $arr[mt_rand(0, count($arr) - 1)];
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
		$siteHost = parse_url((string)\Utils\Helper::options()->siteUrl, PHP_URL_HOST);
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
 * 按图片地址的扩展名给出 MIME 类型，用于 <link rel="icon" type="…">；认不出时返回空串（不写 type，交给浏览器判断）
 */
function themeIconType($url) {
	$ext = strtolower(pathinfo((string)parse_url((string)$url, PHP_URL_PATH), PATHINFO_EXTENSION));
	$types = array('svg' => 'image/svg+xml', 'png' => 'image/png', 'ico' => 'image/x-icon', 'webp' => 'image/webp', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'gif' => 'image/gif', 'avif' => 'image/avif');
	return isset($types[$ext]) ? $types[$ext] : '';
}

/**
 * 主题静态资源地址，带文件修改时间做版本号：文件更新后浏览器缓存自动失效。
 */
function themeAsset($path) {
	echo themeAssetUrl($path);
}

function themeAssetUrl($path) {
	static $cache = array();
	if (!isset($cache[$path])) {
		$file = __DIR__ . '/' . ltrim($path, '/');
		ob_start();
		\Utils\Helper::options()->themeUrl($path); // Typecho 1.2+ 的 themeUrl() 直接输出
		$cache[$path] = ob_get_clean() . '?v=' . (is_file($file) ? filemtime($file) : '1');
	}
	return $cache[$path];
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
	$options = \Utils\Helper::options();
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
		$meta['description'] = \Typecho\Common::subStr($excerpt, 0, 120, '…');
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

/**
 * 搜索引擎爬虫、监控与命令行工具等非真人访问：不计阅读数。没有 User-Agent 的也算。
 */
function themeIsBot() {
	$ua = isset($_SERVER['HTTP_USER_AGENT']) ? (string)$_SERVER['HTTP_USER_AGENT'] : '';
	if (trim($ua) === '') return true;
	return (bool)preg_match('/bot\b|bot[\/;-]|spider|crawl|slurp|scrap|fetch|preview|headless|lighthouse|pagespeed|pingdom|uptime|monitor|checker|validator|archiver|feed|rss|curl|wget|python|java\/|go-http|okhttp|axios|node|php\/|libwww|httpclient|postman|insomnia|semrush|ahrefs|mj12|dotbot|petalbot|bytespider|yandex|baiduspider|sogou|360spider|yisou|facebookexternalhit|embedly|vkshare|w3c_|google-|bingpreview|applebot|duckduck/i', $ua);
}

/**
 * 同一位访客 30 分钟内重复打开同一篇文章只计一次阅读：第一次计数时种一个 30 分钟有效的 cookie。
 */
function themeViewCounted($cid) {
	$name = 'bk_v' . (int)$cid;
	if (isset($_COOKIE[$name])) return true;
	if (!headers_sent()) {
		setcookie($name, '1', array('expires' => time() + 1800, 'path' => '/', 'samesite' => 'Lax', 'httponly' => true, 'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'));
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

	if (empty($GLOBALS['kamiLoadingSticky']) && !isThemePrefetchRequest() && themeViewsEnabled() && ensureThemeViewsColumn() && $archive->type === 'post'
		&& (!isset($archive->status) || $archive->status === 'publish')
		&& !(class_exists('Views_Plugin') && method_exists('Views_Plugin', 'theViews'))
	) {
		$cid = (int)$archive->cid;
		if ($cid > 0 && !themeIsBot() && !themeViewCounted($cid)) {
			try {
				$db = \Typecho\Db::get();
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
 * 从某个时间戳到现在：按日历先算整年，再算距上个周年日过了多少秒。返回 [年, 秒]，时间在未来时返回 null
 */
function themeElapsed($from) {
	$start = new DateTime('@' . (int)$from);
	$now = new DateTime('@' . time());
	if ($now < $start) {
		return null;
	}
	$years = (int)$start->diff($now)->y;
	$anniversary = (clone $start)->modify('+' . $years . ' years');
	return array($years, $now->getTimestamp() - $anniversary->getTimestamp());
}

/** 页脚的运行时长「距今已有 X 年 X 天 X 小时 X 分」（关闭首页统计时显示；未填站点创建时间则不显示） */
function getBuildTime() {
	$created = strtotime((string)\Utils\Helper::options()->siteCreateDate);
	$e = $created ? themeElapsed($created) : null;
	if (!$e) {
		return;
	}
	list($years, $rest) = $e;
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
		$db = \Typecho\Db::get();
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

/**
 * 文章数、字数、阅读数、评论数与建站时间。
 * 篇数、阅读数、评论数是几条汇总查询，每次实时计算；字数要读全部正文，结果缓存在 options 表，
 * 并记下「已发布文章数 + 最后修改时间」作为指纹：发布、修改或删除文章后指纹变化，下次访问即重新计算。
 */
function themeSiteStats() {
	$stats = array('posts' => 0, 'words' => 0, 'views' => 0, 'comments' => 0, 'since' => 0);
	try {
		$db = \Typecho\Db::get();
		$row = $db->fetchObject($db->select(array('COUNT(cid)' => 'num', 'MAX(modified)' => 'modified', 'MIN(created)' => 'since'))
			->from('table.contents')->where('type = ?', 'post')->where('status = ?', 'publish'));
		$stats['posts'] = (int)$row->num;
		$stats['since'] = (int)$row->since;
		$fingerprint = $stats['posts'] . ':' . (int)$row->modified;

		$name = 'bubblekami:stats';
		$cache = json_decode((string)themeOptionRead($name), true);
		if (is_array($cache) && isset($cache['fp'], $cache['words']) && $cache['fp'] === $fingerprint) {
			$stats['words'] = (int)$cache['words'];
		} else {
			$rows = $db->fetchAll($db->select('text', 'password')->from('table.contents')
				->where('type = ?', 'post')->where('status = ?', 'publish'));
			foreach ($rows as $post) {
				if (empty($post['password'])) {
					$stats['words'] += themeCountWords($post['text']);
				}
			}
			themeOptionWrite($name, json_encode(array('fp' => $fingerprint, 'words' => $stats['words'])));
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
	$created = strtotime((string)\Utils\Helper::options()->siteCreateDate);
	$from = $created ?: (int)$since;
	$e = $from ? themeElapsed($from) : null;
	return $e ? array($e[0], intdiv($e[1], 86400)) : null;
}

/* ---------- 友链 / 关于 / 书架页面 ---------- */

/** 名字首字做头像时的底色，按名字固定分配 */
function themeInitialColor($name) {
	$palette = array('#1B365D', '#7a5c3e', '#a33a2a', '#4f6b5a', '#5b5b7a', '#3d5a6c', '#6b4a3a', '#2f4a5c');
	return $palette[abs(crc32((string)$name)) % count($palette)];
}

/** 页面正文转 HTML（Markdown 或普通文本，与 Typecho 处理正文的方式一致） */
function themeRenderText($archive, $text) {
	$text = trim((string)$text);
	if ($text === '') {
		return '';
	}
	// Contents::markdown() / autoP() 在 Typecho 1.2+ 是 protected，模板里调不到，直接用 Typecho 自带的转换类
	if ($archive->isMarkdown) {
		return \Utils\Markdown::convert($text);
	}
	return (new \Utils\AutoP())->parse($text);
}

/**
 * 从页面正文里拆出「名称 | 链接 | …」格式的行（链接须以 http 开头），其余文字原样保留。
 * 返回 [条目数组, 剩余正文]；条目是按 | 切开后去掉首尾空白的字段数组。
 */
function themeSplitListLines($text) {
	$text = preg_replace('/^\s*<!--markdown-->/', '', (string)$text);
	$items = array();
	$rest = array();
	foreach (preg_split('/\R/u', $text) as $line) {
		$fields = array_map('trim', preg_split('/\s*[|｜]\s*/u', trim($line)));
		if (count($fields) >= 2 && preg_match('#^https?://#i', $fields[1])) {
			$items[] = $fields;
		} else {
			$rest[] = $line;
		}
	}
	return array($items, implode("\n", $rest));
}

/** 简单的 HTTP GET：优先 cURL，否则用 file_get_contents。返回 [状态码, 内容]，失败时状态码为 0 */
function themeHttpGet($url, $timeout = 8) {
	$ua = 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148';
	if (function_exists('curl_init')) {
		$ch = curl_init($url);
		curl_setopt_array($ch, array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_MAXREDIRS => 3,
			CURLOPT_CONNECTTIMEOUT => 5,
			CURLOPT_TIMEOUT => $timeout,
			CURLOPT_USERAGENT => $ua,
		));
		$body = curl_exec($ch);
		$code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		return array($body === false ? 0 : $code, (string)$body);
	}
	$ctx = stream_context_create(array('http' => array('header' => "User-Agent: $ua\r\n", 'timeout' => $timeout, 'ignore_errors' => true)));
	$body = @file_get_contents($url, false, $ctx);
	$code = 0;
	if (isset($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $m)) {
		$code = (int)$m[1];
	}
	return array($body === false ? 0 : $code, (string)$body);
}

/**
 * 解析 Nuxt 页面里 window.__NUXT__=(function(a,b,…){return {…}}(值,…)) 形式的 JS 数据字面量。
 * 只支持对象、数组、字符串、数字、true / false / null / void 0 与参数名引用。
 */
class ThemeJsLiteral {
	private $s;
	private $i;
	private $n;
	private $vars;

	public function __construct($src, $start = 0, $vars = array()) {
		$this->s = $src;
		$this->i = $start;
		$this->n = strlen($src);
		$this->vars = $vars;
	}

	public function position() {
		return $this->i;
	}

	private function ws() {
		while ($this->i < $this->n && strpos(" \t\r\n", $this->s[$this->i]) !== false) {
			$this->i++;
		}
	}

	public function value() {
		$this->ws();
		if ($this->i >= $this->n) {
			throw new RuntimeException('unexpected end');
		}
		$c = $this->s[$this->i];
		if ($c === '{') return $this->obj();
		if ($c === '[') return $this->arr();
		if ($c === '"' || $c === "'") return $this->str();
		if (preg_match('/\G-?(?:\d+\.?\d*|\.\d+)(?:[eE][+-]?\d+)?/', $this->s, $m, 0, $this->i)) {
			$this->i += strlen($m[0]);
			return $m[0] + 0;
		}
		if (substr_compare($this->s, 'void 0', $this->i, 6) === 0) {
			$this->i += 6;
			return null;
		}
		if (preg_match('/\G[A-Za-z_$][\w$]*/', $this->s, $m, 0, $this->i)) {
			$this->i += strlen($m[0]);
			if ($m[0] === 'true') return true;
			if ($m[0] === 'false') return false;
			if ($m[0] === 'null' || $m[0] === 'undefined') return null;
			return array_key_exists($m[0], $this->vars) ? $this->vars[$m[0]] : null;
		}
		throw new RuntimeException('unexpected token at ' . $this->i);
	}

	private function obj() {
		$this->i++;
		$out = array();
		while (true) {
			$this->ws();
			if ($this->s[$this->i] === '}') {
				$this->i++;
				return $out;
			}
			$c = $this->s[$this->i];
			if ($c === '"' || $c === "'") {
				$key = $this->str();
			} elseif (preg_match('/\G[\w$]+/', $this->s, $m, 0, $this->i)) {
				$key = $m[0];
				$this->i += strlen($key);
			} else {
				throw new RuntimeException('bad key at ' . $this->i);
			}
			$this->ws();
			if ($this->s[$this->i] !== ':') {
				throw new RuntimeException('expect : at ' . $this->i);
			}
			$this->i++;
			$out[$key] = $this->value();
			$this->ws();
			if ($this->s[$this->i] === ',') {
				$this->i++;
			}
		}
	}

	private function arr() {
		$this->i++;
		$out = array();
		while (true) {
			$this->ws();
			if ($this->s[$this->i] === ']') {
				$this->i++;
				return $out;
			}
			$out[] = $this->value();
			$this->ws();
			if ($this->s[$this->i] === ',') {
				$this->i++;
			}
		}
	}

	private function str() {
		$quote = $this->s[$this->i++];
		$out = '';
		while ($this->i < $this->n) {
			$c = $this->s[$this->i++];
			if ($c === $quote) {
				return $out;
			}
			if ($c !== '\\') {
				$out .= $c;
				continue;
			}
			$e = $this->s[$this->i++];
			if ($e === 'u') {
				$out .= mb_convert_encoding(pack('n', hexdec(substr($this->s, $this->i, 4))), 'UTF-8', 'UTF-16BE');
				$this->i += 4;
			} elseif ($e === 'n') {
				$out .= "\n";
			} elseif ($e === 't') {
				$out .= "\t";
			} elseif ($e === 'r') {
				$out .= "\r";
			} else {
				$out .= $e;
			}
		}
		throw new RuntimeException('unterminated string');
	}
}

/** 取出 Nuxt 页面状态（失败返回 null） */
function themeParseNuxt($html) {
	if (!preg_match('/window\.__NUXT__\s*=\s*\(function\(([^)]*)\)\s*\{\s*return\s*/', $html, $m, PREG_OFFSET_CAPTURE)) {
		return null;
	}
	try {
		$params = array_map('trim', explode(',', $m[1][0]));
		$start = $m[0][1] + strlen($m[0][0]);
		// 先不带变量走一遍返回值对象，拿到它结束的位置，后面紧跟的就是调用参数
		$probe = new ThemeJsLiteral($html, $start);
		$probe->value();
		$after = $probe->position();
		if (!preg_match('/\G\s*;?\s*\}\s*\(/', $html, $mm, 0, $after)) {
			return null;
		}
		$argsStart = $after + strlen($mm[0]);
		$end = strpos($html, '</script>', $argsStart);
		$src = rtrim(substr($html, $argsStart, $end === false ? null : $end - $argsStart));
		$src = preg_replace('/\)\s*\)\s*;?\s*$/', '', $src);
		$args = (new ThemeJsLiteral('[' . $src . ']'))->value();
		$vars = array();
		foreach ($params as $k => $name) {
			$vars[$name] = isset($args[$k]) ? $args[$k] : null;
		}
		return (new ThemeJsLiteral($html, $start, $vars))->value();
	} catch (Throwable $e) {
		return null;
	}
}

/** 微信读书书籍编号 → 网页版书籍详情页地址 */
function themeWereadBookUrl($bookId) {
	$id = (string)$bookId;
	$hash = md5($id);
	if (ctype_digit($id)) {
		$type = '3';
		$parts = array();
		foreach (str_split($id, 9) as $chunk) {
			$parts[] = dechex((int)$chunk);
		}
	} else {
		$type = '4';
		$hex = '';
		foreach (preg_split('//u', $id, -1, PREG_SPLIT_NO_EMPTY) as $ch) {
			$hex .= dechex(mb_ord($ch, 'UTF-8'));
		}
		$parts = array($hex);
	}
	$s = substr($hash, 0, 3) . $type . '2' . substr($hash, -2);
	foreach ($parts as $i => $p) {
		$s .= str_pad(dechex(strlen($p)), 2, '0', STR_PAD_LEFT) . $p;
		if ($i < count($parts) - 1) {
			$s .= 'g';
		}
	}
	if (strlen($s) < 20) {
		$s .= substr($hash, 0, 20 - strlen($s));
	}
	return 'https://weread.qq.com/web/bookDetail/' . $s . substr(md5($s), 0, 3);
}

/**
 * 读取微信读书公开书单（不需要登录）。结果缓存在 options 表：每天刷新一次；
 * 抓取失败时继续用上次成功的数据，并在 1 小时后再试。返回 null 表示从未成功过。
 */
function themeWereadBooklist($url) {
	if (!preg_match('#weread\.qq\.com/misc/booklist/([\w-]+)#', (string)$url, $m)) {
		return null;
	}
	$listId = $m[1];
	$name = 'bubblekami:weread:' . md5($listId);
	$cache = json_decode((string)themeOptionRead($name), true);
	$now = time();
	if (is_array($cache) && isset($cache['checked']) && $now - (int)$cache['checked'] < (empty($cache['books']) ? 3600 : 86400)) {
		return empty($cache['books']) ? null : $cache;
	}
	$fresh = null;
	list($code, $html) = themeHttpGet('https://weread.qq.com/misc/booklist/' . $listId);
	if ($code === 200 && ($state = themeParseNuxt($html)) && isset($state['data'][0]['bookIds'])) {
		$data = $state['data'][0];
		$books = array();
		foreach ((array)$data['bookIds'] as $id) {
			if (empty($data['bookEntities'][$id]['title'])) {
				continue;
			}
			$b = $data['bookEntities'][$id];
			$books[] = array(
				'title' => (string)$b['title'],
				'author' => (string)(isset($b['author']) ? $b['author'] : ''),
				'cover' => (string)(isset($b['cover']) ? $b['cover'] : ''),
				'rating' => (string)(isset($b['ratingString']) ? $b['ratingString'] : ''),
				'url' => themeWereadBookUrl($id),
			);
		}
		if ($books) {
			$list = isset($data['booklist']) ? $data['booklist'] : array();
			$fresh = array(
				'checked' => $now,
				'url' => 'https://weread.qq.com/misc/booklist/' . $listId,
				'name' => (string)(isset($list['name']) ? $list['name'] : ''),
				'description' => (string)(isset($list['description']) ? $list['description'] : ''),
				'total' => (int)(isset($list['totalCount']) ? $list['totalCount'] : count($books)),
				'books' => $books,
			);
		}
	}
	if (!$fresh) {
		// 抓取或解析失败：保留上次的书目，只更新检查时间（失败的缓存 1 小时后重试）
		$fresh = is_array($cache) && !empty($cache['books']) ? $cache : array('books' => array());
		$fresh['checked'] = empty($fresh['books']) ? $now : $now - 86400 + 3600;
	}
	themeOptionWrite($name, json_encode($fresh, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	return empty($fresh['books']) ? null : $fresh;
}

