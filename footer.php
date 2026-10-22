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
	<script>
		// 导航栏折叠（原生实现，替代 Bootstrap collapse）
		document.addEventListener('click', function (e) {
			var toggler = e.target.closest('[data-toggle="collapse"]');
			if (!toggler) return;
			var target = document.querySelector(toggler.getAttribute('data-target'));
			if (!target) return;
			var expanded = toggler.getAttribute('aria-expanded') === 'true';
			toggler.setAttribute('aria-expanded', expanded ? 'false' : 'true');
			target.classList.toggle('show', !expanded);
		});
		// Headroom：滚动时隐藏/显示导航栏
		if (window.Headroom && document.querySelector('#navbar-main.headroom')) {
			new Headroom(document.querySelector('#navbar-main'), {
				offset: 300,
				tolerance: { up: 30, down: 30 }
			}).init();
		}
	</script>
	<!-- scrollup -->
	<script>
		(function () {
			var scrollup = document.getElementById('scrollup');
			var scrollbtn = document.getElementById('scrollbtn');
			var adminbtn = document.getElementById('adminbtn');
			if (!scrollup || !scrollbtn) return;

			function positionScrollBtn() {
				var bottom = 25;
				if (adminbtn) {
					var cs = getComputedStyle(adminbtn);
					var b = parseInt(cs.bottom, 10) + parseInt(cs.height, 10) + 25;
					if (!isNaN(b)) bottom = b;
				}
				scrollbtn.style.bottom = bottom + 'px';
			}

			positionScrollBtn();

			var resizeTimer;
			window.addEventListener('resize', function () {
				if (!adminbtn) return;
				clearTimeout(resizeTimer);
				resizeTimer = setTimeout(positionScrollBtn, 250);
			});

			var scrollLock = 0;

			function toggleVisible() {
				if (window.pageYOffset > 500) scrollup.style.display = 'block';
				else scrollup.style.display = 'none';
			}

			if (window.pageYOffset > 500) scrollup.style.display = 'block';
			window.addEventListener('scroll', function () {
				if (!scrollLock) toggleVisible();
			}, { passive: true });

			scrollup.addEventListener('click', function (e) {
				e.preventDefault();
				scrollLock = 1;
				scrollup.style.display = 'none';
				window.scrollTo({ top: 0, behavior: 'smooth' });
				setTimeout(function () { scrollLock = 0; }, 500);
			});
		})();
	</script>
	<!-- Pjax -->
	<script>
		function initViewer() {
			if (!window.Viewer) return;
			document.querySelectorAll('.content').forEach(function (el) {
				if (el.__viewer) return;
				el.__viewer = new Viewer(el, { url: 'src' });
			});
		}

		function destroyViewer() {
			document.querySelectorAll('.content').forEach(function (el) {
				if (el.__viewer) {
					el.__viewer.destroy();
					el.__viewer = null;
				}
			});
		}

		function init(){
			// 图片懒加载：正文图片延迟加载，减轻首屏压力
			document.querySelectorAll('.content img').forEach(function(img){
				if (!img.hasAttribute('loading')) img.setAttribute('loading', 'lazy');
				if (!img.hasAttribute('decoding')) img.setAttribute('decoding', 'async');
			});
			<?php if($this->options->prismjs and $this->options->prismLine): ?>
			var pres = document.querySelectorAll('pre');
			var lineNumberClassName = 'line-numbers';
			pres.forEach(function (item, index) {
				item.className = item.className == '' ? lineNumberClassName : item.className + ' ' + lineNumberClassName;
			});
			Prism.highlightAll(false,null);
			<?php endif; ?>

			<?php if($this->options->katex): ?>
			try{
				renderMathInElement(document.body,{
					delimiters: [
						{left: "$$", right: "$$", display: true},
						{left: "$", right: "$", display: false}
					]
				})
			}catch{}
			<?php endif; ?>
			parseBbcode()
			parseBblink()
			<?php if($this->options->Pjax): ?>
			<?php $this->options->pjaxcomp() ?>
			<?php endif; ?>
			setTimeout(initViewer, 300);
		}
	</script>
	<?php if($this->options->Pjax): ?>
	<script src="<?php themeAsset("assets/js/progress.js"); ?>"></script>
	<script>
		(function () {
			var siteUrl = '<?php Helper::options()->siteUrl(); ?>';
			var rewrite = <?php echo $this->options->rewrite ? 'true' : 'false'; ?>;
			var pgid = 0;
			var cover = document.querySelector('.black-cover');

			function showCover(show) {
				if (cover) cover.style.display = show ? 'block' : 'none';
			}

			function load(url, push) {
				pgid = start_progress();
				// 支持原生视图过渡时用它做交叉淡入，否则回退到黑色遮罩
				var useVT = !!(document.startViewTransition && !window.matchMedia('(prefers-reduced-motion: reduce)').matches);
				if (!useVT) showCover(true);
				window.scrollTo(0, 0);
				destroyViewer();
				fetch(url)
					.then(function (res) { return res.text(); })
					.then(function (html) {
						var doc = new DOMParser().parseFromString(html, 'text/html');
						var next = doc.querySelector('#pjax-container');
						var cur = document.querySelector('#pjax-container');
						function apply() {
							if (next && cur) cur.innerHTML = next.innerHTML;
							if (push) history.pushState({ url: url }, '', url);
							init();
							if (typeof translateBody === "function" && currentEncoding != targetEncoding) translateBody();
							var t = doc.querySelector('title');
							if (t) document.title = t.textContent;
						}
						function finish() {
							if (!useVT) showCover(false);
							stop_progress(pgid);
						}
						if (useVT) {
							try {
								document.startViewTransition(apply);
							} catch (e) {
								apply();
							}
						} else {
							apply();
						}
						finish();
					})
					.catch(function () {
						showCover(false);
						stop_progress(pgid);
					});
			}

			document.addEventListener('click', function (e) {
				var a = e.target.closest('a[href]');
				if (!a) return;
				if (a.target === '_blank' || a.hasAttribute('no-pjax')) return;
				if (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;
				var href = a.getAttribute('href');
				if (!href || href.indexOf(siteUrl) !== 0) return;
				if (href.indexOf(siteUrl + '/admin') === 0) return;
				e.preventDefault();
				load(href, true);
			});

			var search = document.getElementById('search');
			if (search) {
				search.addEventListener('submit', function (e) {
					e.preventDefault();
					var input = search.querySelector('input[name="s"]');
					var kw = input ? input.value : '';
					var url = rewrite ? (siteUrl + 'search/' + encodeURIComponent(kw) + '/') : (siteUrl + 'index.php/search/' + encodeURIComponent(kw) + '/');
					load(url, true);
				});
			}

			window.addEventListener('popstate', function (e) {
				load((e.state && e.state.url) || location.href, false);
			});
		})();
	</script>
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
	<script>
		function hideNotification() {
			var dialog = document.getElementById('modal-notification');
			if (dialog) dialog.close();
		}
		function alert(main, detail) {
			var dialog = document.getElementById('modal-notification');
			var mainEl = document.getElementById('msgMain');
			var detailEl = document.getElementById('msgDetail');
			if (mainEl) mainEl.innerHTML = main;
			if (detailEl) detailEl.innerHTML = detail || '';
			if (dialog && !dialog.open) dialog.showModal();
		}
		init()
	</script>
	
	<!-- 简繁转换（自托管） -->
	<script src="<?php themeAsset("assets/js/tw-cn.js"); ?>"></script>
	<script>
		translateInitilization();
	</script>
	<!-- Typecho footer -->
	<?php $this->footer(); ?>
	</body>
</html>