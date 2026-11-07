<?php
/**
 * 目录（时间轴归档）
 *
 * @package custom
 */
	if (!defined('__TYPECHO_ROOT_DIR__')) exit;
	$this->need('header.php');

	// 全部已发布文章，按年份分组（Post_Recent 已按发布时间倒序）
	\Widget\Contents\Post\Recent::allocWithAlias('kamiArchives', 'pageSize=100000')->to($arcPosts);
	$arcYears = array();
	$arcCats = array();
	$arcTotal = 0;
	while ($arcPosts->next()) {
		$cats = array();
		foreach ((array)$arcPosts->categories as $cat) {
			$cats[] = (int)$cat['mid'];
			if (!isset($arcCats[$cat['mid']])) {
				$arcCats[$cat['mid']] = array('name' => $cat['name'], 'count' => 0);
			}
		}
		if (!empty($arcPosts->categories)) {
			$arcCats[$arcPosts->categories[0]['mid']]['count']++;
		}
		$arcYears[date('Y', $arcPosts->created)][] = array(
			'title' => $arcPosts->title,
			'url' => $arcPosts->permalink,
			'created' => $arcPosts->created,
			'cats' => $cats,
			'cat' => empty($arcPosts->categories) ? '' : $arcPosts->categories[0]['name'],
		);
		$arcTotal++;
	}
	uasort($arcCats, function ($a, $b) { return $b['count'] - $a['count']; });
	$arcYearKeys = array_keys($arcYears);
?>

	<main id="main" tabindex="-1">
		<section class="section section-lg section-hero section-shaped">
			<?php printBackground(getRandomImage($this->options->randomImage)); ?>
			<div class="container shape-container d-flex align-items-center py-lg">
				<div class="col px-0 text-center">
					<div class="row align-items-center justify-content-center">
						<h1 class="text-white"><?php $this->title() ?></h1>
					</div>
				</div>
			</div>
		</section>
		<section class="section section-components bg-secondary content-card-container">
			<div class="container container-lg py-5 align-items-center content-card-container">
				<div class="card shadow content-card content-card-head">
					<section class="section">
						<div class="container">
							<div class="content">
								<?php if (trim(strip_tags($this->content)) !== ''): ?>
								<div class="arc-intro"><?php $this->content(); ?></div>
								<?php endif; ?>
								<div class="arc-wrap" id="arc-wrap">
									<div class="arc-head">
										<p class="arc-total"><b><?php echo $arcTotal; ?></b>篇<?php if ($arcYearKeys): ?> · <?php echo end($arcYearKeys); ?> — <?php echo reset($arcYearKeys); ?><?php endif; ?></p>
										<?php if (count($arcCats) > 1): ?>
										<div class="arc-filter" role="group" aria-label="按分类筛选">
											<button type="button" class="arc-chip active" data-cat="" aria-pressed="true">全部<small><?php echo $arcTotal; ?></small></button>
											<?php foreach ($arcCats as $mid => $cat): ?>
											<button type="button" class="arc-chip" data-cat="<?php echo (int)$mid; ?>" aria-pressed="false"><?php echo $cat['name']; ?><small><?php echo $cat['count']; ?></small></button>
											<?php endforeach; ?>
										</div>
										<?php endif; ?>
									</div>
									<?php foreach ($arcYears as $year => $items): ?>
									<section class="arc-year">
										<div class="arc-year-label"><strong><?php echo $year; ?></strong><span><b class="arc-year-count"><?php echo count($items); ?></b> 篇</span></div>
										<ol class="arc-list">
											<?php foreach ($items as $item): ?>
											<li class="arc-item" data-cats="<?php echo implode(' ', $item['cats']); ?>">
												<a class="arc-title" href="<?php echo $item['url']; ?>"><?php echo $item['title']; ?></a>
												<span class="arc-meta"><time datetime="<?php echo date('c', $item['created']); ?>"><?php echo date('n', $item['created']); ?> 月 <?php echo date('d', $item['created']); ?> 日</time><?php if ($item['cat'] !== ''): ?><i><?php echo $item['cat']; ?></i><?php endif; ?></span>
											</li>
											<?php endforeach; ?>
										</ol>
									</section>
									<?php endforeach; ?>
								</div>
							</div>
						</div>
					</section>
				</div>
				<?php if (!$this->hidden && $this->allow('comment')): ?>
				<div class="card shadow content-card">
					<?php $this->need('comments.php'); ?>
				</div>
				<?php endif; ?>
			</div>
		</section>
<?php $this->need('footer.php'); ?>
