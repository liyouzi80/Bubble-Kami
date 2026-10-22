<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
		<?php if ($this->user->hasLogin()) { ?>
			<?php if ($this->is('single')) { ?>
			<a href="<?php $this->options->adminUrl(); ?>write-<?php echo $this->is('post')?'post':'page'; ?>.php?cid=<?php echo $this->cid;?>">
				<button id="adminbtn" class="btn btn-icon-only rounded-circle btn-primary admin-btn">
					<span class="btn-inner--icon"><i class="fa fa-pencil" aria-hidden="true"></i></span>
				</button>
			</a>
			<?php } else { ?>
			<a href="<?php $this->options->adminUrl(); ?>">
				<button id="adminbtn" class="btn btn-icon-only rounded-circle btn-primary admin-btn">
					<span class="btn-inner--icon"><i class="fa fa-cogs" aria-hidden="true"></i></span>
				</button>
			</a>
			<?php } ?>
		<?php } ?>
	</main>

	<!-- Footer -->
	<footer class="footer">
		<div class="container">
			<?php if ($this->options->footerWidget) { ?>
			<div class="row">
				<div class="col-md-4 widget">
					<h5>最新评论</h5>
					<?php $comments_recent = $this->widget('Widget_Comments_Recent', 'pageSize=5');
						if ($comments_recent->have())
						{
							_e('<ul>');
							while($comments_recent->next())
							{
								_e('<li><a href="' . "$comments_recent->permalink" . '" class="footer-link">' . "$comments_recent->author" . ': ');
								$comments_recent->excerpt(35, '...');
								_e('</a></li>');
							}
							_e('</ul>');
						}
						else
						{
							_e('暂无评论');
						}
					?>
				</div>
				<div class="col-md-4 widget">
					<h5><i class="fa fa-file-text-o" aria-hidden="true"></i>最新文章</h5>
					<ul class="footer-list"><?php $this->widget('Widget_Contents_Post_Recent', 'pageSize=6')->parse('<li><a href="{permalink}" class="footer-link">{title}</a></li>'); ?></ul>
				</div>
				<div class="col-md-4 widget">
					<h5><i class="fa fa-archive" aria-hidden="true"></i>近期归档</h5>
					<ul class="footer-list"><?php $this->widget('Widget_Contents_Post_Date', 'limit=6&type=month&format=Y-m')->parse('<li><a href="{permalink}" class="footer-link">{date}</a></li>'); ?></ul>
				</div>
			</div>
			<hr/>
			<?php } ?>
			<div class="row">
				<div class="col-md-6">
					<div class="copyright">
					<?php _e($this->options->footerText); ?><?php getBuildTime(); ?>
					</div>
				</div>
				<div class="col-md-6">
					<ul class="nav nav-footer justify-content-end">
						<li class="nav-item">
							<a class="nav-link" href="<?php $this->options->siteUrl(); ?>">首页</a>
						</li>
						<?php $this->widget('Widget_Contents_Page_List')->to($pages);
							while($pages->next()): ?>
							<li class="nav-item">
								<a class="nav-link" href="<?php $pages->permalink(); ?>"><?php $pages->title(); ?></a>
							</li>
						<?php endwhile; ?>
						<?php if($this->user->hasLogin()): ?>
							<li class="nav-item"><a class="nav-link" no-pjax href="<?php $this->options->adminUrl(); ?>">进入后台(<?php $this->user->screenName(); ?>)</a></li>
							<li class="nav-item"><a class="nav-link" no-pjax href="<?php $this->options->logoutUrl(); ?>">退出</a></li>
						<?php else: ?>
							<li class="nav-item"><a class="nav-link" no-pjax href="<?php $this->options->adminUrl('login.php'); ?>">登录</a></li>
						<?php endif; ?>
					</ul>
				</div>
			</div>
		</div>
	</footer>
	<?php if($this->options->Pjax) _e('</div>'); ?>
	<a id="scrollup" href="#" style="display: none;">
		<button id="scrollbtn" class="btn btn-icon-only rounded-circle btn-secondary scrollup-btn">
			<span class="btn-inner--icon"><i class="fa fa-arrow-up" aria-hidden="true"></i></span>
		</button>
	</a>
	<!-- Viewer.js Plugin -->
	<script src="<?php themeAsset("assets/js/viewer.min.js"); ?>"></script>
	<!-- Headroom（导航栏滚动隐藏） -->
	<script src="<?php themeAsset("assets/js/headroom.min.js"); ?>"></script>
	<!-- Theme JS -->
	<script src="<?php themeAsset("assets/js/bbrender.js"); ?>"></script>
	<?php if($this->options->Pjax): ?>
	<script src="<?php themeAsset("assets/js/progress.js"); ?>"></script>
	<div class="black-cover" style="display: none;"></div>
	<?php endif; ?>
	<!-- KaTeX JS -->
	<?php if($this->options->katex): ?>
	<script src="<?php themeAsset("assets/js/katex.min.js"); ?>"></script>
	<script src="<?php themeAsset("assets/js/auto-render.min.js"); ?>"></script>
	<?php endif; ?>
	<!-- Prism JS -->
	<?php if($this->options->prismjs): ?>
	<script src="<?php themeAsset("assets/js/prism-core.min.js"); ?>"></script>
	<script src="<?php themeAsset("assets/js/prism-autoloader.min.js"); ?>"></script>
	<script src="<?php themeAsset("assets/js/prism-toolbar.min.js"); ?>"></script>
	<script src="<?php themeAsset("assets/js/prism-show-language.min.js"); ?>"></script>
	<script src="<?php themeAsset("assets/js/prism-copy-to-clipboard.min.js"); ?>"></script>
	<?php if($this->options->prismLine): ?>
		<script src="<?php themeAsset("assets/js/prism-line-numbers.min.js"); ?>"></script>
	<?php endif; ?>
	<?php endif; ?>
	<!-- Alert（原生 <dialog>，替代 Bootstrap modal） -->
	<dialog id="modal-notification">
		<div class="modal-header">
			<h5 id="msgMain" class="modal-title"></h5>
			<button type="button" class="close" aria-label="Close" onclick="hideNotification()">
				<span aria-hidden="true">×</span>
			</button>
		</div>
		<div id="msgDetail" class="modal-body"></div>
	</dialog>
	<!-- 主题运行时配置：站点地址 / pjax / 语法高亮等开关，供 theme.js 读取 -->
	<script>
		window.BB = window.BB || {};
		window.BB.siteUrl = <?php echo json_encode(Helper::options()->siteUrl()); ?>;
		window.BB.rewrite = <?php echo $this->options->rewrite ? 'true' : 'false'; ?>;
		window.BB.pjax = <?php echo $this->options->Pjax ? 'true' : 'false'; ?>;
		window.BB.prismjs = <?php echo $this->options->prismjs ? 'true' : 'false'; ?>;
		window.BB.prismLine = <?php echo $this->options->prismLine ? 'true' : 'false'; ?>;
		window.BB.katex = <?php echo $this->options->katex ? 'true' : 'false'; ?>;
		window.BB.pjaxcomp = <?php echo json_encode((string)$this->options->pjaxcomp); ?>;
	</script>
	<!-- 主题脚本：导航折叠 / headroom / 回到顶部 / pjax / 图片查看器 / 评论 / 弹窗 -->
	<script src="<?php themeAsset("assets/js/theme.js"); ?>"></script>
	
	<!-- 简繁转换（自托管） -->
	<script src="<?php themeAsset("assets/js/tw-cn.js"); ?>"></script>
	<script>
		translateInitilization();
	</script>
	<!-- Typecho footer -->
	<?php $this->footer(); ?>
	</body>
</html>