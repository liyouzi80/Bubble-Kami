<?php
	if (!defined('__TYPECHO_ROOT_DIR__')) exit;
	$this->need('header.php');
?>

	<main id="main" tabindex="-1" data-post-id="<?php echo (int)$this->cid; ?>">
		<div class="read-progress" aria-hidden="true"></div>

		<section class="section section-lg section-hero section-shaped">
			<?php printBackground(getRandomImage($this->options->randomImage)); ?>
			<div class="container shape-container d-flex align-items-center py-lg">
				<div class="col px-0 text-center">
					<div class="row align-items-center justify-content-center">
						<h1 class="text-white" style="view-transition-name: post-<?php echo (int)$this->cid; ?>"><?php $this->title() ?></h1>
					</div>
					<div class="row align-items-center justify-content-center">
						<p class="text-white post-meta"><?php
							$metaItems = array();
							$metaItems[] = '<time datetime="' . $this->date->format('c') . '">' . $this->date->format('Y.m.d') . '</time>';

							$views = getPostViews($this);
							if ($views !== null) {
								$metaItems[] = '阅读 ' . number_format($views);
							}

							if (!$this->hidden && !empty($this->categories)) {
								$categoryLinks = array();
								foreach ($this->categories as $metaCategory) {
									$categoryLinks[] = '<a class="post-taxonomy" href="' . $metaCategory['permalink'] . '">' . $metaCategory['name'] . '</a>';
								}
								$metaItems[] = implode('、', $categoryLinks);
							}

							if (!$this->hidden && !empty($this->tags)) {
								$tagLinks = array();
								foreach ($this->tags as $metaTag) {
									$tagLinks[] = '<a class="post-taxonomy post-tag" href="' . $metaTag['permalink'] . '">#' . $metaTag['name'] . '</a>';
								}
								$metaItems[] = implode(' ', $tagLinks);
							}

							echo implode('<span class="sep" aria-hidden="true">·</span>', $metaItems);
						?></p>
					</div>
				</div>
			</div>
		</section>
		<section class="section section-components bg-secondary content-card-container">
			<div class="container container-lg py-5 align-items-center content-card-container">
				<div class="card shadow content-card content-card-head">
					<!-- Article content -->
					<section class="section">
						<div class="container">
							<div class="content">
								<?php $this->content(); ?>
							</div>
						</div>
					</section>
					<nav class="post-pager" aria-label="文章导航">
						<div class="post-pager-item post-pager-prev">
							<span class="post-pager-label">上一篇</span>
							<?php $this->thePrev('%s', '<span class="post-pager-none">已经是第一篇</span>', array('tagClass' => 'post-pager-link')); ?>
						</div>
						<div class="post-pager-item post-pager-next">
							<span class="post-pager-label">下一篇</span>
							<?php $this->theNext('%s', '<span class="post-pager-none">已经是最后一篇</span>', array('tagClass' => 'post-pager-link')); ?>
						</div>
					</nav>
				</div>
				<?php relatedPosts($this, 5); ?>
				<div class="card shadow content-card">
					<!-- Comment -->
					<?php if (!$this->hidden && $this->allow('comment')) $this->need('comments.php'); ?>
				</div>
			</div>
		</section>
<?php $this->need('footer.php'); ?>
