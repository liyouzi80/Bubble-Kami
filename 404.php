<?php
	if (!defined('__TYPECHO_ROOT_DIR__')) exit;
	$this->need('header.php');
?>

	<main id="main" tabindex="-1">
		<section class="section section-lg section-hero section-shaped">
			<?php printBackground(getRandomImage($this->options->randomImage)); ?>
			<div class="container shape-container d-flex align-items-center py-lg">
				<div class="col px-0 text-center">
					<div class="row align-items-center justify-content-center">
						<h1 class="text-white">页面没找到</h1>
					</div>
				</div>
			</div>
		</section>
		<section class="section section-components bg-secondary content-card-container">
			<div class="container container-lg py-5 align-items-center content-card-container">
				<div class="card shadow content-card list-card content-card-head not-found-card">
					<section class="section">
						<div class="container">
							<div class="content">
								<div class="not-found-code">404</div>
								<p>你要找的页面不存在，可能已被删除、改名，或者链接地址有误。</p>
								<form method="post" action="<?php $this->options->siteUrl(); ?>" class="not-found-search" role="search">
									<input type="text" name="s" placeholder="搜点什么……" autocomplete="off" aria-label="搜索关键词" required>
									<button type="submit">搜索</button>
								</form>
								<h3 class="not-found-title">最近的文章</h3>
								<ul class="not-found-list">
									<?php $this->widget('Widget_Contents_Post_Recent', 'pageSize=6')->parse('<li><a href="{permalink}">{title}</a></li>'); ?>
								</ul>
								<p><a class="continue-reading" href="<?php $this->options->siteUrl(); ?>">返回首页 <span aria-hidden="true">→</span></a></p>
							</div>
						</div>
					</section>
				</div>
			</div>
		</section>
<?php $this->need('footer.php'); ?>
