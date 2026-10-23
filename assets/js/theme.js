/* Bubble Kami 主题脚本：导航折叠 / headroom / 回到顶部 / pjax / 图片查看器 / 评论 / 弹窗。
 * 运行参数由页面内联的 window.BB 提供（footer.php 注入站点开关，comments.php 注入评论相关配置）。 */
(function () {
	var BB = window.BB || {};

	/* ---------- 导航栏折叠（原生实现，替代 Bootstrap collapse） ---------- */
	document.addEventListener('click', function (e) {
		var toggler = e.target.closest('[data-toggle="collapse"]');
		if (!toggler) return;
		var target = document.querySelector(toggler.getAttribute('data-target'));
		if (!target) return;
		var expanded = toggler.getAttribute('aria-expanded') === 'true';
		toggler.setAttribute('aria-expanded', expanded ? 'false' : 'true');
		target.classList.toggle('show', !expanded);
	});

	/* ---------- 按需加载脚本（带缓存与顺序保证） ---------- */
	var scriptCache = {};
	function loadScript(src, cb) {
		if (!src) { if (cb) cb(); return; }
		if (scriptCache[src] === 'loaded') { if (cb) cb(); return; }
		if (scriptCache[src]) { scriptCache[src].push(cb); return; }
		scriptCache[src] = [];
		if (cb) scriptCache[src].push(cb);
		var s = document.createElement('script');
		s.src = src;
		s.async = false;
		s.onload = function () {
			var q = scriptCache[src];
			scriptCache[src] = 'loaded';
			(q || []).forEach(function (f) { if (f) f(); });
		};
		s.onerror = function () { scriptCache[src] = 'loaded'; };
		document.head.appendChild(s);
	}
	function loadScripts(urls, cb) {
		var i = 0;
		function next() {
			if (i >= urls.length) { if (cb) cb(); return; }
			loadScript(urls[i++], next);
		}
		next();
	}

	/* ---------- 导航栏兜底：离开 hero 或无 hero 页面强制深色，避免 pjax/headroom 状态错位 ---------- */
	function setupNavSolid() {
		var nav = document.querySelector('#navbar-main');
		if (!nav) return;
		var hero = document.querySelector('main .section-hero');
		if (!hero) { nav.classList.add('nav-solid'); return; }
		if (!window.IntersectionObserver) return;
		if (window.__navSolidObserver) window.__navSolidObserver.disconnect();
		window.__navSolidObserver = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				nav.classList.toggle('nav-solid', !entry.isIntersecting);
			});
		}, { rootMargin: '-64px 0px 0px 0px', threshold: 0 });
		window.__navSolidObserver.observe(hero);
	}
	setupNavSolid();

	/* ---------- Headroom：滚动时隐藏/显示导航栏 ---------- */
	if (window.Headroom && document.querySelector('#navbar-main.headroom')) {
		new Headroom(document.querySelector('#navbar-main'), {
			offset: 300,
			tolerance: { up: 30, down: 30 }
		}).init();
	}

	/* ---------- 回到顶部 ---------- */
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

	/* ---------- 图片查看器（有图才按需加载 Viewer.js） ---------- */
	function initViewer() {
		var targets = document.querySelectorAll('.content');
		var hasImage = false;
		targets.forEach(function (el) { if (el.querySelector('img')) hasImage = true; });
		if (!hasImage) return;
		function build() {
			if (!window.Viewer) return;
			targets.forEach(function (el) {
				if (el.__viewer) return;
				if (!el.querySelector('img')) return;
				el.__viewer = new Viewer(el, { url: 'src' });
			});
		}
		if (window.Viewer) { build(); }
		else if (BB.viewerJs) { loadScript(BB.viewerJs, build); }
	}

	function destroyViewer() {
		document.querySelectorAll('.content').forEach(function (el) {
			if (el.__viewer) {
				el.__viewer.destroy();
				el.__viewer = null;
			}
		});
	}

	/* ---------- 页面渲染后统一初始化 ---------- */
	function init() {
		setupNavSolid();

		// 图片懒加载：正文图片延迟加载（服务端已加 loading 属性，这里做兜底）
		document.querySelectorAll('.content img').forEach(function (img) {
			if (!img.hasAttribute('loading')) img.setAttribute('loading', 'lazy');
			if (!img.hasAttribute('decoding')) img.setAttribute('decoding', 'async');
		});

		// Prism 按需加载：正文含代码块才拉取，避免无代码的散文页白下脚本
		if (BB.prismjs && document.querySelector('pre, code[class*="language-"]')) {
			var prismUrls = [
				BB.prismJs.core,
				BB.prismJs.autoloader,
				BB.prismJs.toolbar,
				BB.prismJs.showLanguage,
				BB.prismJs.copy
			];
			if (BB.prismLine) prismUrls.push(BB.prismJs.lineNumbers);
			loadScripts(prismUrls, function () {
				if (!window.Prism) return;
				if (BB.prismLine) {
					var lineNumberClassName = 'line-numbers';
					document.querySelectorAll('pre').forEach(function (item) {
						item.className = item.className === '' ? lineNumberClassName : item.className + ' ' + lineNumberClassName;
					});
				}
				Prism.highlightAll(false, null);
			});
		}

		// KaTeX 按需加载：正文含 $ 数学定界符才拉取（katex.min.js 约 252KB，收益最大）
		if (BB.katex && document.body.textContent.indexOf('$') !== -1) {
			function renderKatex() {
				try {
					renderMathInElement(document.body, {
						delimiters: [
							{ left: "$$", right: "$$", display: true },
							{ left: "$", right: "$", display: false }
						]
					});
				} catch (e) {}
			}
			if (window.renderMathInElement) { renderKatex(); }
			else { loadScripts([BB.katexJs, BB.autoRenderJs], renderKatex); }
		}

		parseBbcode();
		parseBblink();

		if (BB.pjax && BB.pjaxcomp) {
			try { new Function(BB.pjaxcomp)(); } catch (e) {}
		}

		setTimeout(initViewer, 300);
	}

	/* ---------- pjax ---------- */
	if (BB.pjax) {
		(function () {
			var siteUrl = BB.siteUrl;
			var rewrite = BB.rewrite;
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
				window.scrollTo({ top: 0, behavior: 'instant' });
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
	}

	/* ---------- 弹窗提示（原生 <dialog>，替代 Bootstrap modal） ---------- */
	window.hideNotification = function () {
		var dialog = document.getElementById('modal-notification');
		if (dialog) dialog.close();
	};
	window.alert = function (main, detail) {
		var dialog = document.getElementById('modal-notification');
		var mainEl = document.getElementById('msgMain');
		var detailEl = document.getElementById('msgDetail');
		if (mainEl) mainEl.innerHTML = main;
		if (detailEl) detailEl.innerHTML = detail || '';
		if (dialog && !dialog.open) dialog.showModal();
	};

	/* ---------- 评论 ---------- */
	function focusToComment(username) {
		var roots = document.querySelectorAll('#comments > div');
		function getcomments(eles) {
			var comlist = [];
			var childrenlist = [];
			for (var p = 0; p < eles.length; p++) {
				var lis = eles[p].querySelectorAll(':scope > ol > li');
				for (var j = 0; j < lis.length; j++) {
					var h5 = lis[j].querySelectorAll(':scope > div[id^="comment"] > .comment-item > .comment-body > .comment-head > h5');
					for (var i = 0; i < h5.length; i++) {
						var name = h5[i].innerText.split("·")[0].trim();
						if (name == username) {
							comlist.push(parseInt(lis[j].id.split("-")[2]));
						}
					}
					var child = lis[j].querySelector(':scope > .comment-children');
					if (child) {
						childrenlist.push(child);
					}
				}
			}
			if (childrenlist.length > 0) {
				return comlist.concat(getcomments(childrenlist));
			}
			return comlist;
		}
		var commentIds = getcomments(roots);
		var commentId = Math.max.apply(null, commentIds);
		if (commentId != -Infinity) {
			var target = document.getElementById('comment-' + commentId);
			if (target) {
				window.scrollTo({ top: target.getBoundingClientRect().top + window.pageYOffset - 100, behavior: 'smooth' });
				setTimeout(function () {
					target.style.display = 'none';
					setTimeout(function () { target.style.display = ''; }, 90);
				}, 500);
			}
		}
	}

	function bindsubmit() {
		var form = document.getElementById("comment-form");
		if (!form) return;
		form.addEventListener("submit", function (e) {
			e.preventDefault();
			var pgid = start_progress();
			var btn = document.getElementById("add-comment-button");
			if (btn) btn.disabled = true;
			var data = new FormData(form);
			data.append("_", BB.comment.token);
			fetch(form.getAttribute("action"), { method: form.getAttribute("method") || "POST", body: data })
				.then(function (res) { return res.text(); })
				.then(function (html) {
					var newdocument = new DOMParser().parseFromString(html, "text/html");
					if (newdocument.title == "Error") {
						var container = newdocument.getElementsByClassName("container")[0];
						var error = container ? container.innerText.trim() : "";
						alert("评论提交错误", error);
					} else {
						var newComments = newdocument.getElementById("comments");
						var curComments = document.getElementById("comments");
						if (newComments && curComments) curComments.innerHTML = newComments.innerHTML;
						bindsubmit();
						var authorInput = document.getElementById("author");
						var authorName = authorInput && authorInput.value ? authorInput.value : (document.querySelector("a[href$='profile.php']") ? document.querySelector("a[href$='profile.php']").innerText : "");
						if (authorName) focusToComment(authorName);
					}
				})
				.catch(function () {
					alert("网络请求错误", "请重新尝试提交评论");
				})
				.finally(function () {
					if (btn) btn.disabled = false;
					stop_progress(pgid);
				});
		});
	}

	function reRunPluginScripts() {
		var html = BB.comment && BB.comment.pluginScripts;
		if (!html) return;
		var scripts = new DOMParser().parseFromString(html, "text/html").getElementsByTagName("script");
		for (var i = 0; i < scripts.length; i++) {
			try {
				// 用间接 eval 让插件脚本在全局作用域执行，等价于原内联脚本的顶层 eval
				(0, eval)(scripts[i].innerHTML);
			} catch (error) {
				alert(error);
			}
		}
	}

	/* TypechoComment：评论回复（需挂到全局，供回复链接的 onclick 调用） */
	if (BB.comment) {
		window.TypechoComment = (function () {
			var respondId = BB.comment.respondId;
			function dom(id) { return document.getElementById(id); }
			function create(tag, attr) {
				var el = document.createElement(tag);
				for (var key in attr) el.setAttribute(key, attr[key]);
				return el;
			}
			return {
				dom: dom,
				create: create,
				reply: function (cid, coid) {
					var comment = dom(cid), parent = comment.parentNode,
						response = dom(respondId), input = dom('comment-parent'),
						form = 'form' == response.tagName ? response : response.getElementsByTagName('form')[0],
						textarea = response.getElementsByTagName('textarea')[0];

					if (null == input) {
						input = create('input', {
							'type': 'hidden',
							'name': 'parent',
							'id': 'comment-parent'
						});
						form.appendChild(input);
					}

					input.setAttribute('value', coid);

					if (null == dom('comment-form-place-holder')) {
						var holder = create('div', { 'id': 'comment-form-place-holder' });
						response.parentNode.insertBefore(holder, response);
					}

					comment.appendChild(response);
					dom('cancel-comment-reply-link').style.display = '';

					if (null != textarea && 'text' == textarea.name) {
						textarea.focus();
					}

					return false;
				},
				cancelReply: function () {
					var response = dom(respondId),
						holder = dom('comment-form-place-holder'), input = dom('comment-parent');

					if (null != input) {
						input.parentNode.removeChild(input);
					}

					if (null == holder) {
						return true;
					}

					dom('cancel-comment-reply-link').style.display = 'none';
					holder.parentNode.insertBefore(response, holder);
					return false;
				}
			};
		})();

		bindsubmit();
		reRunPluginScripts();
	}

	init();
})();
