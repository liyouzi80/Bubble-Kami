<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="theme-color" content="#5e72e4">
	<meta property="og:type" content="website">
	<meta property="og:site_name" content="<?php $this->options->title(); ?>">
	<meta property="og:title" content="<?php if ($this->is('single')) { $this->title(); } else { $this->options->title(); } ?>">
	<meta property="og:url" content="<?php $this->request->getRequestUrl(); ?>">
	<meta property="og:image" content="<?php if ($this->options->avatarUrl == '') { $this->options->themeUrl('images/avatar.png'); } else { $this->options->avatarUrl(); } ?>">
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
	<link href="<?php
		if ($this->options->logoUrl == '') {
			$this->options->themeUrl("images/logo.png");
		} else {
			$this->options->logoUrl();
		}
	?>" rel="icon" type="image/png">

	<!-- FontAwesome -->
	<link type="text/css" href="<?php $this->options->themeUrl("assets/css/font-awesome.min.css"); ?>" rel="stylesheet">

	<!-- Main CSS -->
	<link type="text/css" href="<?php $this->options->themeUrl("assets/css/main.min.css"); ?>" rel="stylesheet">

	<!-- 中文字体栈：优先使用系统字体，避免加载体积庞大的在线 webfont -->
	<style>
		body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "PingFang SC", "Hiragino Sans GB", "Microsoft YaHei", "Helvetica Neue", Arial, "Noto Sans SC", sans-serif; }
	</style>

	<!-- KaTeX CSS -->
	<?php if ($this->options->katex): ?>
	<link type="text/css" href="<?php $this->options->themeUrl("assets/css/katex.min.css"); ?>" rel="stylesheet">
	<?php endif; ?>

	<!-- PrismJS CSS -->
	<?php if ($this->options->prismjs): ?>
	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/prismjs@1.20.0/themes/<?php $this->options->prismTheme(); ?>.css" />
	<link type="text/css" href="<?php $this->options->themeUrl("assets/css/prism-toolbar.css"); ?>" rel="stylesheet">
		<?php if ($this->options->prismLine): ?>
		<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/prismjs@1.20.0/plugins/line-numbers/prism-line-numbers.css" />
		<?php endif; ?>
	<?php endif; ?>

	<!-- Viewer CSS -->
	<link type="text/css" href="<?php $this->options->themeUrl("assets/css/viewer.min.css"); ?>" rel="stylesheet">
	
	<!-- Jquery -->
	<script src="<?php $this->options->themeUrl("assets/js/jquery.min.js"); ?>"></script>

	<!-- Custom CSS -->
	<?php if ($this->options->customCss): ?>
	<style type="text/css"><?php $this->options->customCss(); ?></style>
	<?php endif; ?>
	<!-- Viewer.js Plugin -->
    <script src="<?php $this->options->themeUrl("assets/js/viewer.min.js"); ?>"></script>
    <script src="<?php $this->options->themeUrl("assets/js/jquery-viewer.min.js"); ?>"></script>
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