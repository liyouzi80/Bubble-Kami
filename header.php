<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="theme-color" content="#1B365D">
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
	<?php if ($this->options->logoUrl == ''): ?>
	<link href="<?php themeAsset("images/logo.webp"); ?>" rel="icon" type="image/webp">
	<link href="<?php themeAsset("images/logo.png"); ?>" rel="icon" type="image/png">
	<?php else: ?>
	<link href="<?php $this->options->logoUrl(); ?>" rel="icon" type="image/png">
	<?php endif; ?>

	<!-- FontAwesome -->
	<link type="text/css" href="<?php themeAsset("assets/css/font-awesome.min.css"); ?>" rel="stylesheet">

	<!-- Main CSS -->
	<link type="text/css" href="<?php themeAsset("assets/css/main.min.css"); ?>" rel="stylesheet">

	<!-- Kami 风格（内置默认外观） -->
	<link type="text/css" href="<?php themeAsset("assets/css/kami.css"); ?>" rel="stylesheet">

	<!-- 自托管 Noto Serif SC（500，unicode-range 切片，仅标题用）。
	     样式表约 105KB，异步加载不阻塞首屏；字体未到时标题先用系统衬线字体（font-display: swap） -->
	<link rel="preload" href="<?php themeAsset("assets/css/noto-serif-sc.css"); ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
	<noscript><link type="text/css" href="<?php themeAsset("assets/css/noto-serif-sc.css"); ?>" rel="stylesheet"></noscript>

	<!-- KaTeX / Prism / Viewer 的样式与脚本一样按正文内容懒加载（见 theme.js） -->
	
	<!-- Custom CSS -->
	<?php if ($this->options->customCss): ?>
	<style type="text/css"><?php $this->options->customCss(); ?></style>
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
	<?php if (!$this->options->Pjax): ?>
	<!-- Speculation Rules：悬停预取同源页面，让整页导航近乎零延迟。
	     pjax 用 fetch 取页面，用不上浏览器的导航预取缓存，开启 pjax 时不输出，避免每次悬停多请求一遍 -->
	<script type="speculationrules">
	{
		"prefetch": [
			{
				"source": "document",
				"where": {
					"and": [
						{ "href_matches": "/*" },
						{ "not": { "selector_matches": "[no-pjax], [target='_blank']" } }
					]
				},
				"eagerness": "moderate"
			}
		]
	}
	</script>
	<?php endif; ?>
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
							$this->widget('Widget_Contents_Page_List')->to($pages);
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
	<?php if($this->options->Pjax) _e('<div id="pjax-container">'); ?>