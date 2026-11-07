<?php
/**
 * Typecho 极简风格响应式主题
 *
 * @package Bubble Kami
 * @author TriNitroTofu, Rorical, Boshi (maintained by liyouzi80)
 * @version 4.2.8
 * @link https://github.com/liyouzi80/Bubble-Kami
 */

	if (!defined('__TYPECHO_ROOT_DIR__')) exit;
	$this->need('header.php');
?>

	<main id="main" tabindex="-1">
		<section class="section section-lg section-hero section-shaped" style="height: 100vh;">
			<?php printBackground($this->options->indexImage); ?>
			<div class="container shape-container d-flex align-items-center py-lg">
				<div class="col px-0">
					<div class="row align-items-center justify-content-center">
						<div class="col-lg-6 text-center hero-intro">
							<div class="index-avatar-container">
								<img src="<?php
									if ($this->options->avatarUrl == '') {
										themeAsset("images/avatar.png");
									} else {
										$this->options->avatarUrl();
									}
								?>" class="index-avatar" alt="<?php $this->options->title() ?>" width="100" height="100" fetchpriority="high">
							</div>
							<h1 class="text-white"><?php $this->options->title() ?></h1>
							
							<p class="lead text-white"><?php $this->options->description() ?></p>
							<?php if (!isset($this->options->heroExtras) || (string)$this->options->heroExtras !== '0'):
								$likes = themeLikeCount();
								$stats = themeSiteStats();
								$running = themeRunningTime($stats['since']);
							?>
							<button type="button" class="hero-like" id="hero-like" data-count="<?php echo $likes; ?>" aria-label="点赞" title="点赞">
								<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s-7.2-4.4-9.6-8.9C.7 8.9 2.4 5 6.1 4.3c2.3-.4 4.3.8 5.9 2.9 1.6-2.1 3.6-3.3 5.9-2.9 3.7.7 5.4 4.6 3.7 7.8C19.2 16.6 12 21 12 21z"/></svg>
								<span class="hero-like-count"><?php echo number_format($likes); ?></span>
							</button>
							<ul class="hero-stats">
								<li><b><?php echo themeShortNumber($stats['posts']); ?></b>篇文章</li>
								<li><b><?php echo themeShortNumber($stats['words']); ?></b>字</li>
								<?php if (themeViewsEnabled()): ?><li><b><?php echo themeShortNumber($stats['views']); ?></b>次阅读</li><?php endif; ?>
								<li><b><?php echo themeShortNumber($stats['comments']); ?></b>条评论</li>
								<?php if ($running): ?><li>运行 <b><?php echo $running[0]; ?></b>年 <b><?php echo $running[1]; ?></b>天</li><?php endif; ?>
							</ul>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
			<a class="hero-scroll" href="#index-posts" aria-label="向下浏览文章" title="向下浏览文章"><span class="hero-scroll-chevron" aria-hidden="true"></span></a>
		</section>
		<section id="index-posts" class="section section-components bg-secondary content-card-container">
			<div class="container container-lg py-5 align-items-center content-card-container">
				<!-- Article list -->
				<?php
					$stickyIds = ($this->is('index') && (int)$this->_currentPage === 1) ? getThemeStickyCids($this->options) : array();
					$first_flag = true;
					foreach ($stickyIds as $stickyCid):
						$stickyArchive = null;
						$GLOBALS['kamiLoadingSticky'] = true;
						try {
							\Widget\Archive::allocWithAlias('kamiSticky' . $stickyCid, 'pageSize=1&type=post', 'cid=' . $stickyCid)->to($stickyArchive);
							if ($stickyArchive && $stickyArchive->have()) {
								while ($stickyArchive->next()) {
									if ($stickyArchive->status !== 'publish') continue;
									printArticle($stickyArchive, $first_flag, true);
									$first_flag = false;
								}
							}
						} catch (Throwable $e) {
							// 忽略已删除或不可公开访问的置顶文章，首页其他内容照常显示。
						} finally {
							unset($GLOBALS['kamiLoadingSticky']);
						}
					endforeach;
				?>
				<?php while($this->next()): ?>
					<?php if (in_array((int)$this->cid, $stickyIds, true)) continue; ?>
					<?php printArticle($this, $first_flag); $first_flag = false; ?>
				<?php endwhile; ?>
				<!-- Toggle page -->
				<?php printToggleButton($this); ?>
			</div>
		</section>
		<?php if($this->_currentPage>1) echo("<script>var el=document.querySelector('.card.shadow.content-card.list-card.content-card-head');if(el)window.scrollTo({top:el.getBoundingClientRect().top+window.pageYOffset,behavior:'smooth'})</script>") ?>
<?php $this->need('footer.php'); ?>
