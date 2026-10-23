<?php
	if (!defined('__TYPECHO_ROOT_DIR__')) exit;
	$this->need('header.php');
?>

	<main id="main" tabindex="-1">

		<?php if($this->options->toc): ?>
		<div class="card shadow border-0 bg-secondary toc-container" id="toc-panel">
			<button class="carousel-control-prev toc-toggle" id="toc-toggle" type="button" aria-expanded="false" aria-controls="toc-panel" aria-label="展开/收起文章目录">
				<i class="fa fa-chevron-left" aria-hidden="true"></i>
			</button>
			<div class="card-img container container-lg py-5 toc">
				<strong>文章目录</strong>
				<div class="toc-list">
					<?php getCatalog(); ?>
				</div>
			</div>
		</div>
		<script>
			(function () {
				function initToc() {
					var toc = document.getElementById('toc-panel');
					var toggle = document.getElementById('toc-toggle');
					var icon = toggle.querySelector('i');
					var list = document.querySelector('.toc-list');
					if (!toc || !toggle || !list) return;
					var open = <?php echo ($this->options->toc_enable == '1') ? 'true' : 'false'; ?>;

					function setOpen(next) {
						open = next;
						toc.classList.toggle('toc-open', open);
						toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
						icon.classList.toggle('fa-chevron-left', !open);
						icon.classList.toggle('fa-chevron-right', open);
					}

					toggle.addEventListener('click', function () { setOpen(!open); });

					list.addEventListener('click', function (e) {
						var a = e.target.closest('a[href^="#cl-"]');
						if (!a) return;
						e.preventDefault();
						var target = document.querySelector('[name="' + a.getAttribute('href').slice(1) + '"]');
						if (target) {
							window.scrollTo({ top: target.getBoundingClientRect().top + window.pageYOffset - 120, behavior: 'smooth' });
						}
					});

					var headings = Array.prototype.slice.call(document.querySelectorAll('[name^="cl-"]')).reverse();
					var nowtoc = 'cl-1';

					function onScroll() {
						if (!headings.length) return;
						for (var i = 0; i < headings.length; i++) {
							var ele = headings[i];
							if (ele.getBoundingClientRect().top < 121) {
								if (nowtoc !== ele.getAttribute('name')) {
									var prev = list.querySelector('[name="dl-' + nowtoc.replace('cl-', '') + '"]');
									if (prev) prev.classList.remove('located');
									var next = list.querySelector('[name="dl-' + ele.getAttribute('name').replace('cl-', '') + '"]');
									if (next) {
										next.classList.add('located');
										list.scrollTop = list.scrollTop - 50 + next.getBoundingClientRect().top - list.getBoundingClientRect().top;
									}
									nowtoc = ele.getAttribute('name');
								}
								break;
							}
						}
					}

					if (window.__tocOnScroll) window.removeEventListener('scroll', window.__tocOnScroll);
					window.__tocOnScroll = onScroll;
					window.addEventListener('scroll', onScroll, { passive: true });

					setOpen(open);
				}

				if (document.readyState === 'loading') {
					document.addEventListener('DOMContentLoaded', initToc);
				} else {
					initToc();
				}
			})();
		</script>
		<?php endif; ?>
		<section class="section section-lg section-hero section-shaped">
			<?php printBackground(getRandomImage($this->options->randomImage)); ?>
			<div class="container shape-container d-flex align-items-center py-lg">
				<div class="col px-0 text-center">
					<div class="row align-items-center justify-content-center">
						<h1 class="text-white"><?php $this->title() ?></h1>
					</div>
					<div class="row align-items-center justify-content-center">
						<h5 class="text-white">于 <time datetime="<?php $this->date('c'); ?>"><?php $this->date(); ?></time> 由 <?php $this->author(); ?> 发布<?php $views = getPostViews($this); if ($views !== null): ?> · <i class="fa fa-eye" aria-hidden="true"></i> <?php echo number_format($views); ?> 次阅读<?php endif; ?></h5>
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
								<?php if (!$this->hidden) { ?>
								<hr/>
								<ul>
									<li>分类：<?php printCategory($this); ?></li>
									<li>标签：<?php printTag($this); ?></li>
								</ul>
								<?php } ?>
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
