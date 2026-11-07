<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
		<?php if ($this->user->hasLogin()) { ?>
			<?php if ($this->is('single')) { ?>
			<a id="adminbtn" class="btn btn-icon-only rounded-circle btn-primary admin-btn" href="<?php $this->options->adminUrl(); ?>write-<?php echo $this->is('post')?'post':'page'; ?>.php?cid=<?php echo $this->cid;?>" aria-label="编辑这篇文章" title="编辑这篇文章">
				<span class="btn-inner--icon"><i class="fa fa-pencil" aria-hidden="true"></i></span>
			</a>
			<?php } else { ?>
			<a id="adminbtn" class="btn btn-icon-only rounded-circle btn-primary admin-btn" href="<?php $this->options->adminUrl(); ?>" aria-label="进入后台" title="进入后台">
				<span class="btn-inner--icon"><i class="fa fa-cogs" aria-hidden="true"></i></span>
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
					<?php $comments_recent = \Widget\Comments\Recent::alloc('pageSize=5');
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
					<ul class="footer-list"><?php \Widget\Contents\Post\Recent::alloc('pageSize=6')->parse('<li><a href="{permalink}" class="footer-link">{title}</a></li>'); ?></ul>
				</div>
				<div class="col-md-4 widget">
					<h5><i class="fa fa-archive" aria-hidden="true"></i>近期归档</h5>
					<ul class="footer-list"><?php \Widget\Contents\Post\Date::alloc('limit=6&type=month&format=Y-m')->parse('<li><a href="{permalink}" class="footer-link">{date}</a></li>'); ?></ul>
				</div>
			</div>
			<hr/>
			<?php } ?>
			<div class="row">
				<div class="col-md-6">
					<div class="copyright">
					<?php echo (string)$this->options->footerText; /* 设置为空时为 null，Typecho 1.3 的 _e() 只接受字符串，会直接报错 */ ?><?php if (isset($this->options->heroExtras) && (string)$this->options->heroExtras === '0') getBuildTime(); /* 首页统计已显示运行时长，关闭时才在页脚显示 */ ?>
					</div>
				</div>
				<div class="col-md-6">
					<ul class="nav nav-footer justify-content-end">
						<li class="nav-item">
							<a class="nav-link" href="<?php $this->options->siteUrl(); ?>">首页</a>
						</li>
						<?php \Widget\Contents\Page\Rows::alloc()->to($pages);
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
	<!-- 右下角浮动工具：返回顶部（滚动后出现）/ 搜索 / 简繁切换 -->
	<div class="float-tools" id="float-tools">
	<!-- 返回顶部：外圈为阅读进度环，中间显示百分比，悬停/聚焦时换成箭头 -->
	<button id="scrollup" type="button" class="float-tool" aria-label="返回顶部" title="返回顶部">
		<svg class="scrollup-ring" viewBox="0 0 48 48" aria-hidden="true">
			<circle class="scrollup-track" cx="24" cy="24" r="22"></circle>
			<circle class="scrollup-bar" cx="24" cy="24" r="22" pathLength="100"></circle>
		</svg>
		<span class="scrollup-pct" aria-hidden="true">0</span>
		<i class="fa fa-arrow-up" aria-hidden="true"></i>
	</button>
	<button id="search-toggle" type="button" class="float-tool" aria-label="搜索" title="搜索" aria-haspopup="dialog" aria-controls="search-dialog">
		<i class="fa fa-search" aria-hidden="true"></i>
	</button>
	<button id="translateLink" type="button" class="float-tool" aria-label="简繁切换" title="简繁切换" onclick="translatePage()">繁</button>
	</div>
	<!-- Theme JS -->
	<script src="<?php themeAsset("assets/js/bbrender.js"); ?>"></script>
	<?php if($this->options->Pjax): ?>
	<script src="<?php themeAsset("assets/js/progress.js"); ?>"></script>
	<div class="black-cover" style="display: none;"></div>
	<?php endif; ?>
	<!-- KaTeX / Prism / PhotoSwipe 的 JS 已改为按正文内容懒加载（见 theme.js），此处不再无条件输出 -->
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
	<!-- 搜索弹窗 -->
	<dialog id="search-dialog" aria-label="搜索">
		<form method="post" action="<?php $this->options->siteUrl(); ?>" id="search" role="search">
			<i class="fa fa-search" aria-hidden="true"></i>
			<input type="text" name="s" placeholder="搜点什么……" autocomplete="off" enterkeyhint="search" aria-label="搜索关键词" required>
		</form>
	</dialog>
	<!-- 主题运行时配置：站点地址 / pjax / 语法高亮等开关，供 theme.js 读取 -->
	<script>
		window.BB = window.BB || {};
		// 注意：options 的 siteUrl() 是直接 echo 的方法，这里必须取属性值再 json_encode
		window.BB.siteUrl = <?php echo json_encode((string)$this->options->siteUrl); ?>;
		window.BB.adminUrl = <?php echo json_encode((string)$this->options->adminUrl); ?>;
		window.BB.searchUrl = <?php echo json_encode(\Typecho\Common::url('search/', $this->options->index)); ?>;
		window.BB.pjax = <?php echo $this->options->Pjax ? 'true' : 'false'; ?>;
		window.BB.prismjs = <?php echo $this->options->prismjs ? 'true' : 'false'; ?>;
		window.BB.prismLine = <?php echo $this->options->prismLine ? 'true' : 'false'; ?>;
		window.BB.katex = <?php echo $this->options->katex ? 'true' : 'false'; ?>;
		window.BB.pjaxcomp = <?php echo json_encode((string)$this->options->pjaxcomp); ?>;
		// 按需加载的资源地址（带版本号），供 theme.js 在检测到对应内容时动态注入
		window.BB.katexJs = "<?php themeAsset('assets/js/katex.min.js'); ?>";
		window.BB.autoRenderJs = "<?php themeAsset('assets/js/auto-render.min.js'); ?>";
		window.BB.pswpJs = "<?php themeAsset('assets/js/photoswipe.esm.min.js'); ?>";
		window.BB.pswpCss = "<?php themeAsset('assets/css/photoswipe.css'); ?>";
		window.BB.katexCss = "<?php themeAsset('assets/css/katex.min.css'); ?>";
		window.BB.prismCss = <?php
			$prismCss = array(themeAssetUrl('assets/css/' . $this->options->prismTheme . '.css'), themeAssetUrl('assets/css/prism-toolbar.css'));
			if ($this->options->prismLine) $prismCss[] = themeAssetUrl('assets/css/prism-line-numbers.css');
			echo json_encode($prismCss, JSON_UNESCAPED_SLASHES);
		?>;
		window.BB.twcnJs = "<?php themeAsset('assets/js/tw-cn.js'); ?>";
		window.BB.prismJs = {
			core: "<?php themeAsset('assets/js/prism-core.min.js'); ?>",
			autoloader: "<?php themeAsset('assets/js/prism-autoloader.min.js'); ?>",
			toolbar: "<?php themeAsset('assets/js/prism-toolbar.min.js'); ?>",
			showLanguage: "<?php themeAsset('assets/js/prism-show-language.min.js'); ?>",
			copy: "<?php themeAsset('assets/js/prism-copy-to-clipboard.min.js'); ?>",
			lineNumbers: "<?php themeAsset('assets/js/prism-line-numbers.min.js'); ?>",
			components: "<?php echo \Utils\Helper::options()->themeUrl('assets/js/prism-components/'); ?>"
		};
	</script>
	<!-- 主题脚本：导航折叠 / 导航栏滚动隐藏 / 回到顶部 / pjax / 图片查看器 / 评论 / 弹窗 -->
	<script src="<?php themeAsset("assets/js/theme.js"); ?>"></script>
	<!-- 简繁转换脚本（tw-cn.js）由 theme.js 按需加载：点击「繁」按钮或之前切换过繁体时才下载 -->
	<!-- Typecho footer -->
	<?php $this->footer(); ?>
	</body>
</html>