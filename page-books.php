<?php
/**
 * 书架（微信读书书单）
 *
 * @package custom
 */
	if (!defined('__TYPECHO_ROOT_DIR__')) exit;
	$this->need('header.php');

	// 正文里的微信读书书单链接决定显示哪个书单，其余文字作为页面说明显示在上方
	$shelfText = preg_replace('/^\s*<!--markdown-->/', '', (string)$this->text);
	$shelfUrl = preg_match('#https?://weread\.qq\.com/misc/booklist/[\w-]+#', $shelfText, $m) ? $m[0] : '';
	$shelfIntro = trim(preg_replace('#<?https?://weread\.qq\.com/misc/booklist/[\w-]+>?#', '', $shelfText));
	$shelf = $shelfUrl !== '' ? themeWereadBooklist($shelfUrl) : null;
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
								<?php if ($shelfIntro !== ''): ?>
								<div class="shelf-intro"><?php echo themeRenderText($this, $shelfIntro); ?></div>
								<?php endif; ?>
								<?php if ($shelf): ?>
								<div class="shelf-head">
									<div>
										<p class="shelf-name"><?php echo htmlspecialchars($shelf['name'], ENT_QUOTES, 'UTF-8'); ?></p>
										<span class="shelf-sub"><b><?php echo (int)$shelf['total']; ?></b>本<?php if ($shelf['description'] !== ''): ?> · <?php echo htmlspecialchars($shelf['description'], ENT_QUOTES, 'UTF-8'); ?><?php endif; ?> · 来自微信读书</span>
									</div>
									<a class="shelf-open" href="<?php echo htmlspecialchars($shelf['url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">在微信读书打开书单 →</a>
								</div>
								<div class="shelf-grid">
									<?php foreach ($shelf['books'] as $book): ?>
									<a class="book" href="<?php echo htmlspecialchars($book['url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener" title="<?php echo htmlspecialchars($book['title'] . ($book['author'] !== '' ? ' · ' . $book['author'] : ''), ENT_QUOTES, 'UTF-8'); ?>">
										<span class="book-cover"><?php if ($book['cover'] !== ''): ?><img src="<?php echo htmlspecialchars($book['cover'], ENT_QUOTES, 'UTF-8'); ?>" alt="" loading="lazy" decoding="async" referrerpolicy="no-referrer"><?php endif; ?></span>
										<span class="book-title"><?php echo htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8'); ?></span>
										<span class="book-meta"><?php echo htmlspecialchars($book['author'], ENT_QUOTES, 'UTF-8'); ?><?php if ($book['rating'] !== ''): ?><em><?php echo htmlspecialchars($book['rating'], ENT_QUOTES, 'UTF-8'); ?></em><?php endif; ?></span>
									</a>
									<?php endforeach; ?>
								</div>
								<?php elseif ($shelfUrl !== ''): ?>
								<p class="shelf-empty">暂时读不到书单内容，可以 <a href="<?php echo htmlspecialchars($shelfUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">在微信读书打开书单 →</a></p>
								<?php else: ?>
								<p class="shelf-empty">在页面正文里贴上微信读书书单的分享链接（weread.qq.com/misc/booklist/…），这里就会显示书单。</p>
								<?php endif; ?>
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
