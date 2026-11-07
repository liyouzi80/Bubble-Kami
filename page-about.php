<?php
/**
 * 关于
 *
 * @package custom
 */
	if (!defined('__TYPECHO_ROOT_DIR__')) exit;
	$this->need('header.php');

	$aboutAvatar = $this->options->avatarUrl == '' ? themeAssetUrl('images/avatar.png') : (string)$this->options->avatarUrl;
	$aboutStats = themeSiteStats();
	$aboutRunning = themeRunningTime($aboutStats['since']);
	$aboutCreated = strtotime((string)$this->options->siteCreateDate) ?: $aboutStats['since'];
	$aboutEmail = isset($this->fields->email) ? trim((string)$this->fields->email) : '';
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
								<div class="about-head">
									<img src="<?php echo htmlspecialchars($aboutAvatar, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo themeAttr($this->options->title); ?>" width="96" height="96">
									<h2><?php $this->options->title(); ?></h2>
									<?php if ($this->options->description != ''): ?><p><?php $this->options->description(); ?></p><?php endif; ?>
								</div>
								<ul class="about-stats">
									<?php if ($aboutCreated): ?><li><b><?php echo date('Y', $aboutCreated); ?></b><span>建站</span></li><?php endif; ?>
									<?php if ($aboutRunning): ?><li><b><?php echo $aboutRunning[0]; ?></b><span>年</span></li><?php endif; ?>
									<li><b><?php echo themeShortNumber($aboutStats['posts']); ?></b><span>篇文章</span></li>
									<li><b><?php echo themeShortNumber($aboutStats['words']); ?></b><span>字</span></li>
									<li><b><?php echo themeShortNumber($aboutStats['comments']); ?></b><span>条评论</span></li>
								</ul>
								<div class="about-body"><?php $this->content(); ?></div>
								<div class="about-contact">
									<?php if ($aboutEmail !== ''): ?><a href="mailto:<?php echo htmlspecialchars($aboutEmail, ENT_QUOTES, 'UTF-8'); ?>">邮箱</a><?php endif; ?>
									<a href="<?php $this->options->feedUrl(); ?>" target="_blank" rel="noopener">RSS 订阅</a>
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
