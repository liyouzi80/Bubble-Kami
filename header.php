<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="theme-color" content="#1B365D" media="(prefers-color-scheme: light)">
	<meta name="theme-color" content="#0f0f0e" media="(prefers-color-scheme: dark)">
	<?php $shareMeta = themeShareMeta($this); ?>
	<meta property="og:type" content="<?php echo $shareMeta['type']; ?>">
	<meta property="og:site_name" content="<?php echo themeAttr($this->options->title); ?>">
	<meta property="og:title" content="<?php echo themeAttr($shareMeta['title']); ?>">
	<meta property="og:url" content="<?php echo htmlspecialchars($this->request->getRequestUrl(), ENT_QUOTES, 'UTF-8'); ?>">
	<?php if ($shareMeta['description'] !== ''): ?>
	<meta property="og:description" content="<?php echo themeAttr($shareMeta['description']); ?>">
	<?php endif; ?>
	<meta property="og:image" content="<?php echo htmlspecialchars($shareMeta['image'], ENT_QUOTES, 'UTF-8'); ?>">
	<?php if ($shareMeta['type'] === 'article'): ?>
	<meta property="article:published_time" content="<?php echo date('c', $this->created); ?>">
	<meta property="article:modified_time" content="<?php echo date('c', $this->modified); ?>">
	<?php endif; ?>
	<meta name="twitter:card" content="<?php echo $shareMeta['hasPostImage'] ? 'summary_large_image' : 'summary'; ?>">
	<title><?php $this->archiveTitle(array(
			'category'  =>  _t('%s 下的文章'),
			'search'    =>  _t('包含关键字 %s 的文章'),
			'tag'       =>  _t('标签 %s 下的文章'),
			'author'    =>  _t('%s 的文章')
		), '', ' - '); ?><?php
		$this->options->title();
		if ($this->is('index') && $this->options->subtitle != '') echo " - {$this->options->subtitle}";
	?></title>

	<!-- Favicon -->
	<?php
		$iconUrl = trim((string)$this->options->logoUrl);
		$touchIcon = trim((string)$this->options->touchIcon);
		if ($iconUrl === '') {
			$touchIcon = $touchIcon !== '' ? $touchIcon : themeAssetUrl('images/apple-touch-icon.png');
		} elseif ($touchIcon === '' && in_array(themeIconType($iconUrl), array('image/png', 'image/jpeg'), true)) {
			$touchIcon = $iconUrl;
		}
	?>
	<?php if ($iconUrl === ''): ?>
	<link href="<?php themeAsset("images/logo.webp"); ?>" rel="icon" type="image/webp">
	<link href="<?php themeAsset("images/logo.png"); ?>" rel="icon" type="image/png">
	<?php else: $iconType = themeIconType($iconUrl); ?>
	<link href="<?php echo htmlspecialchars($iconUrl, ENT_QUOTES, 'UTF-8'); ?>" rel="icon"<?php if ($iconType !== ''): ?> type="<?php echo $iconType; ?>"<?php endif; ?>>
	<?php endif; ?>
	<?php if ($touchIcon !== ''): ?>
	<link href="<?php echo htmlspecialchars($touchIcon, ENT_QUOTES, 'UTF-8'); ?>" rel="apple-touch-icon">
	<?php endif; ?>

	<!-- FontAwesome -->
	<link type="text/css" href="<?php themeAsset("assets/css/font-awesome.min.css"); ?>" rel="stylesheet">

	<!-- 主题样式：基础层 + Kami 外观 + 深色模式，全部在这一个文件里 -->
	<link type="text/css" href="<?php themeAsset("assets/css/kami.css"); ?>" rel="stylesheet">

	<!-- 自托管 Noto Serif SC（500，unicode-range 切片，仅标题用）。
	     样式表约 105KB，异步加载不阻塞首屏；字体未到时标题先用系统衬线字体（font-display: swap） -->
	<link rel="preload" href="<?php themeAsset("assets/css/noto-serif-sc.css"); ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
	<noscript><link type="text/css" href="<?php themeAsset("assets/css/noto-serif-sc.css"); ?>" rel="stylesheet"></noscript>
	<?php if ($this->options->titleFont): $titleFontUrl = htmlspecialchars((string)$this->options->titleFont, ENT_QUOTES, 'UTF-8'); ?>
	<!-- 本站标题字体子集：只含本站标题用字，优先于上面的字体切片（见 tools/build-title-font.py） -->
	<link rel="preload" href="<?php echo $titleFontUrl; ?>" as="font" type="font/woff2" crossorigin>
	<style>@font-face{font-family:'Kami Title';font-style:normal;font-weight:500;font-display:swap;src:local('Noto Serif SC Medium'),local('NotoSerifSC-Medium'),local('Source Han Serif SC Medium'),local('SourceHanSerifSC-Medium'),local('Noto Serif CJK SC Medium'),local('NotoSerifCJKsc-Medium'),url('<?php echo $titleFontUrl; ?>') format('woff2');}</style>
	<?php endif; ?>

	<!-- Custom CSS -->
	<?php if ($this->options->customCss): ?>
	<style type="text/css"><?php $this->options->customCss(); ?></style>
	<?php endif; ?>
	<!-- 首页第一页：规范地址与网站级结构化数据（文章 / 独立页的规范地址由 Typecho 的 header() 输出） -->
	<?php if ($this->is('index') && $this->getCurrentPage() <= 1): ?>
	<link rel="canonical" href="<?php echo htmlspecialchars((string)$this->options->siteUrl, ENT_QUOTES, 'UTF-8'); ?>">
	<script type="application/ld+json"><?php echo json_encode(array(
		'@context' => 'https://schema.org',
		'@type' => 'WebSite',
		'name' => (string)$this->options->title,
		'url' => (string)$this->options->siteUrl,
		'description' => (string)$this->options->description,
		'inLanguage' => 'zh-CN',
		'potentialAction' => array(
			'@type' => 'SearchAction',
			'target' => \Typecho\Common::url('search/', $this->options->index) . '{search_term_string}/',
			'query-input' => 'required name=search_term_string',
		),
	), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?></script>
	<?php endif; ?>
	<!-- JSON-LD 结构化数据 -->
	<?php if ($this->is('single')): ?>
	<?php
	$ldAuthor = $this->author ? $this->author->screenName : '';
	$ldArticle = array(
		'@context' => 'https://schema.org',
		'@type' => 'Article',
		'headline' => $this->title,
		'url' => $this->permalink,
		'datePublished' => date('c', $this->created),
		'dateModified' => date('c', $this->modified),
		'author' => array('@type' => 'Person', 'name' => $ldAuthor),
		'publisher' => array('@type' => 'Organization', 'name' => $this->options->title),
		'mainEntityOfPage' => array('@type' => 'WebPage', '@id' => $this->permalink),
		'image' => $shareMeta['image'],
	);
	if ($shareMeta['description'] !== '') {
		$ldArticle['description'] = html_entity_decode(strip_tags($shareMeta['description']), ENT_QUOTES, 'UTF-8');
	}
	$ldBreadcrumb = array(
		'@context' => 'https://schema.org',
		'@type' => 'BreadcrumbList',
		'itemListElement' => array(
			array('@type' => 'ListItem', 'position' => 1, 'name' => '首页', 'item' => $this->options->index),
		),
	);
	$ldPos = 2;
	if ($this->is('post') && !empty($this->categories)) {
		$ldBreadcrumb['itemListElement'][] = array('@type' => 'ListItem', 'position' => $ldPos, 'name' => $this->categories[0]['name'], 'item' => $this->categories[0]['permalink']);
		$ldPos++;
	}
	$ldBreadcrumb['itemListElement'][] = array('@type' => 'ListItem', 'position' => $ldPos, 'name' => $this->title);
	?>
	<script type="application/ld+json"><?php echo json_encode($ldArticle, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?></script>
	<script type="application/ld+json"><?php echo json_encode($ldBreadcrumb, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?></script>
	<?php endif; ?>
	<!-- Speculation Rules：悬停预取同源页面，让整页导航近乎零延迟（Chrome / Edge）。
	     后台、登录、退出等链接（data-no-prefetch）与 /action/ 接口不预取，免得悬停就触发操作 -->
	<script type="speculationrules">
	{
		"prefetch": [
			{
				"source": "document",
				"where": {
					"and": [
						{ "href_matches": "/*" },
						{ "not": { "href_matches": "/action/*" } },
						{ "not": { "href_matches": "/index.php/action/*" } },
						{ "not": { "selector_matches": "[data-no-prefetch], [target='_blank']" } }
					]
				},
				"eagerness": "moderate"
			}
		]
	}
	</script>
	<!-- 方向感切页：点「下一篇」或后一页时新页面从右侧滑入，「上一篇」或前一页从左侧滑入；
	     浏览器后退 / 前进按原路反向。须在首次渲染前注册 pagereveal，所以放在 head 里。不支持的浏览器照常淡入 -->
	<script>
	(function () {
		var CLICK = 'kami-vt-click', MAP = 'kami-vt-map';
		function load(k) { try { return JSON.parse(sessionStorage.getItem(k) || 'null'); } catch (e) { return null; } }
		function save(k, v) { try { sessionStorage.setItem(k, JSON.stringify(v)); } catch (e) {} }
		addEventListener('click', function (e) {
			var a = e.target.closest && e.target.closest('a[href]'), d = '';
			if (!a) return;
			if (a.closest('.post-pager-next')) d = 'forward';
			else if (a.closest('.post-pager-prev')) d = 'backward';
			else if (a.closest('.page-nav')) {
				var cur = document.querySelector('.page-nav .active a');
				var c = cur ? parseInt(cur.textContent, 10) : NaN, n = parseInt(a.textContent, 10);
				if (!isNaN(n) && !isNaN(c) && n !== c) d = n > c ? 'forward' : 'backward';
				else if (a.querySelector('.fa-angle-right')) d = 'forward';
				else if (a.querySelector('.fa-angle-left')) d = 'backward';
			}
			save(CLICK, d ? { d: d, u: a.href, t: Date.now() } : null);
		}, true);
		addEventListener('pagereveal', function (e) {
			var nav = window.navigation, act = nav && nav.activation, map = load(MAP) || {}, d = '';
			if (act && act.navigationType === 'traverse' && act.from && act.entry) {
				// 后退：按当初来到上一页的方向反着走；前进：重走当初的方向
				if (act.entry.index < act.from.index) {
					d = { forward: 'backward', backward: 'forward' }[map[act.from.key]] || '';
				} else {
					d = map[act.entry.key] || '';
				}
			} else if (nav && nav.currentEntry) {
				var c = load(CLICK);
				save(CLICK, null);
				if (c && c.u === location.href && Date.now() - c.t < 10000) d = c.d;
				map[nav.currentEntry.key] = d;
				save(MAP, map);
			}
			if (d && e.viewTransition && e.viewTransition.types) e.viewTransition.types.add(d);
		});
	})();
	</script>
	<!-- Typecho header（social= 关掉 Typecho 自带的 og/twitter 标签，主题上方已输出更完整的一份，避免重复） -->
	<?php $this->header('social='); ?>
</head>
<body>
	<a class="skip-link" href="#main">跳到正文</a>
	<header class="header-global">
		<nav id="navbar-main" class="navbar navbar-main navbar-expand-lg navbar-transparent navbar-light headroom">
			<div class="container">
				<?php if ($this->options->navLogo): ?>
				<a class="navbar-brand navbar-brand-logo" href="<?php $this->options->siteUrl(); ?>"><img src="<?php echo htmlspecialchars((string)$this->options->navLogo, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo themeAttr($this->options->title); ?>"></a>
				<?php else: ?>
				<a class="navbar-brand" href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title() ?></a>
				<?php endif; ?>
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-default" aria-controls="navbar-default" aria-expanded="false" aria-label="展开导航菜单">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="navbar-default">
					<div class="navbar-collapse-header">
						<div class="row">
							<div class="col-6 collapse-brand">
								<a href="<?php $this->options->siteUrl(); ?>"><h5><?php $this->options->title() ?></h5></a>
							</div>
							<div class="col-6 collapse-close">
								<button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbar-default" aria-controls="navbar-default" aria-expanded="false" aria-label="收起导航菜单">
									<span></span>
									<span></span>
								</button>
							</div>
						</div>
					</div>
					<ul class="navbar-nav ml-lg-auto align-items-lg-center">
						<li class="nav-item">
							<a class="nav-link" href="<?php $this->options->siteUrl(); ?>">首页</a>
						</li>
						<?php
							\Widget\Contents\Page\Rows::alloc()->to($pages);
							while($pages->next()):
						?>
							<li class="nav-item">
								<a class="nav-link" href="<?php $pages->permalink(); ?>" title="<?php $pages->title(); ?>"><?php $pages->title(); ?></a>
							</li>
						<?php endwhile; ?>
					</ul>
				</div>
			</div>
		</nav>
	</header>
