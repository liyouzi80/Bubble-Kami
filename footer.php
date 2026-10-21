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
					<h5>最新文章</h5>
					<ul><?php $this->widget('Widget_Contents_Post_Recent', 'pageSize=6')->parse('<li><a href="{permalink}" class="footer-link">{title}</a></li>'); ?></ul>
				</div>
				<div class="col-md-4 widget">
					<h5>近期归档</h5>
					<ul><?php $this->widget('Widget_Contents_Post_Date', 'limit=6&type=year&month=Y-M')->parse('<li><a href="{permalink}" class="footer-link">{date}</a></li>'); ?></ul>
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
							<li class="nav-item"><a class="nav-link" href="<?php $this->options->adminUrl(); ?>">进入后台(<?php $this->user->screenName(); ?>)</a></li>
							<li class="nav-item"><a class="nav-link" href="<?php $this->options->logoutUrl(); ?>">退出</a></li>
						<?php else: ?>
							<li class="nav-item"><a class="nav-link" href="<?php $this->options->adminUrl('login.php'); ?>">登录</a></li>
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
	<!-- Core -->
	<script src="<?php $this->options->themeUrl("assets/js/jquery.min.js"); ?>"></script>
	<!-- Viewer.js Plugin -->
	<script src="<?php $this->options->themeUrl("assets/js/viewer.min.js"); ?>"></script>
	<script src="<?php $this->options->themeUrl("assets/js/jquery-viewer.min.js"); ?>"></script>
	<!-- Headroom（导航栏滚动隐藏） -->
	<script src="<?php $this->options->themeUrl("assets/js/headroom.min.js"); ?>"></script>
	<!-- Theme JS -->
	<script src="<?php $this->options->themeUrl("assets/js/bbrender.js"); ?>"></script>
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
		$(function(){
			var scrollBottom = parseInt($("#adminbtn").css("bottom")) + parseInt($("#adminbtn").css("height")) + 25;
			$("#scrollbtn").css("bottom", scrollBottom);
			var resizeTimer;
			$(window).resize(function(e) {
				if ($("#adminbtn").length > 0)
				{
					clearTimeout(resizeTimer);
					resizeTimer = setTimeout(function() {
						scrollBottom = parseInt($("#adminbtn").css("bottom")) + parseInt($("#adminbtn").css("height")) + 25;
						$("#scrollbtn").css("bottom", scrollBottom);
					}, 250);
				}
			});
			var scrollLock = 0;
			if ($(window).scrollTop() > 500) $("#scrollup").fadeIn(400);
			$(window).scroll(function() {
				if (!scrollLock)
				{
					if ($(window).scrollTop() > 500) $("#scrollup").fadeIn(400);
					else $("#scrollup").fadeOut(400);
				}
			});
			$("#scrollup").click(function() {
				scrollLock = 1;
				$("#scrollup").fadeOut(400);
				$("html,body").animate({scrollTop: "0px"}, 500, function() {
					scrollLock = 0;
				});
			});
		});
	</script>
	<!-- Pjax -->
	
	<script>
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
			try{
				window.onload()
			}catch{}
			<?php endif; ?>
			setTimeout(() => {
				$('.content').viewer({
					url: 'src'
				})
			},300)
		}
		function destroy(){
			// viewerjs
			var viewer = $('.content').data('viewer');
			if(viewer){
				viewer.destroy()
			}
		}
		window.addEventListener("popstate", function(e) {
			setTimeout(() => {
				$('.content').viewer({
					url: 'src'
				})
			},300)
		}, false);
	</script>
	<?php if($this->options->Pjax): ?>
	<script src="<?php $this->options->themeUrl("assets/js/jquery.pjax.js"); ?>"></script>
	<script src="<?php $this->options->themeUrl("assets/js/progress.js"); ?>"></script>
	<script>
		var pgid = 0
		$(document).pjax('a[href^="<?php Helper::options()->siteUrl()?>"]:not(a[target="_blank"], a[no-pjax], a[href^="<?php Helper::options()->siteUrl()?>/admin"])',
		{
    		container: '#pjax-container',
    		fragment: '#pjax-container',
    		timeout: 8000
		}).on('pjax:send', function() {
			pgid = start_progress()
			$(".black-cover").fadeIn(400)
			$('html,body').animate({ scrollTop: $('html').offset().top}, 500)

			destroy()
		}).on('pjax:complete', function() {
			$(".black-cover").fadeOut(400)
			stop_progress(pgid)
			init()
			if (typeof translateBody === "function" && currentEncoding != targetEncoding) translateBody();
			
		})
		$("#search").submit(function() {
			var att = $(this).serializeArray()
			for(var i in att){
				if(att[i].name=="s"){
					$.pjax({url: <?php if ($this->options->rewrite): ?>"<?php $this->options->siteUrl(); ?>search/"+att[i].value+"/"<?php else: ?>"<?php $this->options->siteUrl(); ?>index.php/search/"+att[i].value+"/"<?php endif; ?>, container: '#pjax-container',fragment: '#pjax-container',timeout:8000})
				}
			}
			return false
		})
	</script>
	<div class="black-cover" style="display: none;"></div>
	<?php endif; ?>
	<!-- KaTeX JS -->
	<?php if($this->options->katex): ?>
	<script src="<?php $this->options->themeUrl("assets/js/katex.min.js"); ?>"></script>
	<script src="<?php $this->options->themeUrl("assets/js/auto-render.min.js"); ?>"></script>
	<?php endif; ?>
	<!-- Prism JS -->
	<?php if($this->options->prismjs): ?>
	<script src="<?php $this->options->themeUrl("assets/js/prism-core.min.js"); ?>"></script>
	<script src="<?php $this->options->themeUrl("assets/js/prism-autoloader.min.js"); ?>"></script>
	<script src="<?php $this->options->themeUrl("assets/js/prism-toolbar.min.js"); ?>"></script>
	<script src="<?php $this->options->themeUrl("assets/js/prism-show-language.min.js"); ?>"></script>
	<script src="<?php $this->options->themeUrl("assets/js/prism-copy-to-clipboard.min.js"); ?>"></script>
	<?php if($this->options->prismLine): ?>
		<script src="<?php $this->options->themeUrl("assets/js/prism-line-numbers.min.js"); ?>"></script>
	<?php endif; ?>
	<?php endif; ?>
	<!-- Alert -->
	<div id="modal-notification" class="modal fade" style="z-index: 102;display: none;">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<h5 id="msgMain" class="modal-title"></h5>
					<button type="button" class="close" aria-label="Close" onclick="$('#modal-notification').hide('normal');">
						<span aria-hidden="true">×</span>
					</button>
				</div>
				<div id="msgDetail" class="modal-body"></div>
			</div>
		</div>
	</div>
	<script>
		function alert(main,detail){
			$("#msgMain").html(main)
			if(detail) $("#msgDetail").html(detail)
			else $("#msgDetail").html("")
			$("#modal-notification").show("normal");
		}
		init()
	</script>
	
	<!-- 简繁转换（自托管） -->
	<script src="<?php $this->options->themeUrl("assets/js/tw-cn.js"); ?>"></script>
	<script>
		translateInitilization();
	</script>
	<!-- Typecho footer -->
	<?php $this->footer(); ?>
	</body>
</html>