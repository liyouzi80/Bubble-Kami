<?php
/**
 * 友链
 *
 * @package custom
 */
	if (!defined('__TYPECHO_ROOT_DIR__')) exit;
	$this->need('header.php');

	// 正文里「站名 | 地址 | 头像 | 描述」格式的行是友链，其余文字作为页面说明显示在上方
	list($linkItems, $linkIntro) = themeSplitListLines($this->text);
	$siteAvatar = $this->options->avatarUrl == '' ? themeAssetUrl('images/avatar.png') : (string)$this->options->avatarUrl;
	$siteInfo = array(
		'站名' => (string)$this->options->title,
		'地址' => rtrim((string)$this->options->siteUrl, '/'),
		'头像' => $siteAvatar,
		'描述' => (string)$this->options->description,
	);
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
								<?php if (trim($linkIntro) !== ''): ?>
								<div class="links-intro"><?php echo themeRenderText($this, $linkIntro); ?></div>
								<?php endif; ?>
								<?php if ($linkItems): ?>
								<div class="links-grid">
									<?php foreach ($linkItems as $item):
										$name = $item[0];
										$url = $item[1];
										$avatar = isset($item[2]) && preg_match('#^https?://#i', $item[2]) ? $item[2] : '';
										$desc = isset($item[3]) ? $item[3] : (isset($item[2]) && $avatar === '' ? $item[2] : '');
									?>
									<a class="link-card" href="<?php echo htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">
										<span class="link-avatar" style="background:<?php echo themeInitialColor($name); ?>"><?php echo htmlspecialchars(mb_substr($name, 0, 1, 'UTF-8'), ENT_QUOTES, 'UTF-8'); ?><?php if ($avatar !== ''): ?><img src="<?php echo htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8'); ?>" alt="" loading="lazy" referrerpolicy="no-referrer" onerror="this.remove()"><?php endif; ?></span>
										<span class="link-text">
											<span class="link-name"><?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></span>
											<?php if ($desc !== ''): ?><span class="link-desc"><?php echo htmlspecialchars($desc, ENT_QUOTES, 'UTF-8'); ?></span><?php endif; ?>
										</span>
									</a>
									<?php endforeach; ?>
								</div>
								<?php endif; ?>
								<div class="links-apply">
									<h3>申请友链</h3>
									<p>欢迎交换友链，请先添加本站，然后在下方留言附上你的站点信息。</p>
									<dl>
										<?php foreach ($siteInfo as $label => $value): ?>
										<dt><?php echo $label; ?></dt><dd><?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?></dd>
										<?php endforeach; ?>
									</dl>
									<button type="button" class="links-copy" data-copy="<?php echo htmlspecialchars(implode("\n", array_map(function ($k, $v) { return $k . '：' . $v; }, array_keys($siteInfo), $siteInfo)), ENT_QUOTES, 'UTF-8'); ?>">复制本站信息</button>
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
