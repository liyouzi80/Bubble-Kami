<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="theme-color" content="#5e72e4">
	<script>(function(){try{var t=localStorage.getItem('theme');if(t!=='dark'&&t!=='light'){t=(window.matchMedia&&window.matchMedia('(prefers-color-scheme: dark)').matches)?'dark':'light'}document.documentElement.setAttribute('data-theme',t)}catch(e){document.documentElement.setAttribute('data-theme','light')}})();</script>
	<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
	<meta property="og:type" content="website">
	<meta property="og:site_name" content="<?php $this->options->title(); ?>">
	<meta property="og:title" content="<?php if ($this->is('single')) { $this->title(); } else { $this->options->title(); } ?>">
	<meta property="og:url" content="<?php $this->request->getRequestUrl(); ?>">
	<meta property="og:image" content="<?php if ($this->options->avatarUrl == '') { themeAsset('images/avatar.png'); } else { $this->options->avatarUrl(); } ?>">
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

	<!-- 中文字体栈：优先使用系统字体，避免加载体积庞大的在线 webfont -->
	<style>
		body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "PingFang SC", "Hiragino Sans GB", "Microsoft YaHei", "Helvetica Neue", Arial, "Noto Sans SC", sans-serif; }
	</style>

	<!-- KaTeX CSS -->
	<?php if ($this->options->katex): ?>
	<link type="text/css" href="<?php themeAsset("assets/css/katex.min.css"); ?>" rel="stylesheet">
	<?php endif; ?>

	<!-- PrismJS CSS -->
	<?php if ($this->options->prismjs): ?>
	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/prismjs@1.20.0/themes/<?php $this->options->prismTheme(); ?>.css" />
	<link type="text/css" href="<?php themeAsset("assets/css/prism-toolbar.css"); ?>" rel="stylesheet">
		<?php if ($this->options->prismLine): ?>
		<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/prismjs@1.20.0/plugins/line-numbers/prism-line-numbers.css" />
		<?php endif; ?>
	<?php endif; ?>

	<!-- Viewer CSS -->
	<link type="text/css" href="<?php themeAsset("assets/css/viewer.min.css"); ?>" rel="stylesheet">
	
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
	);
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
	<!-- Typecho header -->
	<?php $this->header(); ?>
</head>
<body>
	<header class="header-global">
		<nav id="navbar-main" class="navbar navbar-main navbar-expand-lg navbar-transparent navbar-light headroom">
			<div class="container">
				<a class="navbar-brand" href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title() ?></a>
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-default" aria-controls="navbar-default" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="navbar-default">
					<div class="navbar-collapse-header">
						<div class="row">
							<div class="col-6 collapse-brand">
								<a href="<?php $this->options->siteUrl(); ?>"><h5><?php $this->options->title() ?></h5></a>
							</div>
							<div class="col-6 collapse-close">
								<button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbar-default" aria-controls="navbar-default" aria-expanded="false" aria-label="Toggle navigation">
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
						<li class="nav-item">
							<a class="nav-link" id="translateLink" href="javascript:translatePage();">简/繁</a>
						</li>
						<li class="nav-item">
							<button class="nav-link theme-toggle" id="theme-toggle" type="button" aria-label="切换深色模式">
								<svg class="theme-icon theme-icon-moon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
								<svg class="theme-icon theme-icon-sun" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
							</button>
						</li>

						<li class="nav-item">
						<?php
							$this->widget('Widget_Contents_Page_List')->to($pages);
							while($pages->next()):
						?>
							<li class="nav-item">
								<a class="nav-link" href="<?php $pages->permalink(); ?>" title="<?php $pages->title(); ?>"><?php $pages->title(); ?></a>
							</li>
						<?php endwhile; ?>
						<li class="navbar_search_container">
							<form method="post" action="" id="search">
								<div class="input-group input-group-alternative">
									<div class="input-group-prepend">
										<span class="input-group-text"><i class="fa fa-search"></i></span>
									</div>
									<input type="text" name="s" class="form-control" placeholder="搜点什么……" autocomplete="off">
								</div>
							</form>
						</li>
					</ul>
				</div>
			</div>
		</nav>
	</header>
	<?php if($this->options->Pjax) _e('<div id="pjax-container">'); ?>