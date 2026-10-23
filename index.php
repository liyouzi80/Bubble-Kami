<?php
/**
 * Typecho 极简风格响应式主题
 *
 * @package Bubble Kami
 * @author TriNitroTofu, Rorical, Boshi (maintained by liyouzi80)
 * @version 4.2.5
 * @link https://github.com/liyouzi80/typecho-Bubble
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
						<div class="col-lg-6 text-center">
							<div class="index-avatar-container">
								<img src="<?php
									if ($this->options->avatarUrl == '') {
										themeAsset("images/avatar.png");
									} else {
										$this->options->avatarUrl();
									}
								?>" class="index-avatar">
							</div>
							<h1 class="text-white"><?php $this->options->title() ?></h1>
							
							<p class="lead text-white"><?php $this->options->description() ?></p>
						</div>
					</div>
				</div>
			</div>
		</section>
		<section class="section section-components bg-secondary content-card-container">
			<div class="container container-lg py-5 align-items-center content-card-container">
				<!-- Article list -->
				<?php
					$stickyIds = ($this->is('index') && (int)$this->_currentPage === 1) ? getThemeStickyCids($this->options) : array();
					$first_flag = true;
					foreach ($stickyIds as $stickyCid):
						$stickyArchive = null;
						$GLOBALS['kamiLoadingSticky'] = true;
						try {
							$this->widget('Widget_Archive@kamiSticky' . $stickyCid, 'pageSize=1&type=post', 'cid=' . $stickyCid)->to($stickyArchive);
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
