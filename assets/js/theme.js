/* Bubble Kami 主题脚本：导航折叠 / 导航栏滚动隐藏 / 回到顶部 / 搜索 / 简繁切换 / 点赞 / 图片查看器 / 阅读进度。
 * 运行参数由 footer.php 内联的 window.BB 提供。 */
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

	/* ---------- 按需加载脚本（带缓存） ---------- */
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
	var cssCache = {};
	function loadCss(href) {
		if (!href || cssCache[href]) return;
		cssCache[href] = true;
		var l = document.createElement('link');
		l.rel = 'stylesheet';
		l.href = href;
		document.head.appendChild(l);
	}

	function scrollToY(y) {
		window.scrollTo({ top: y, behavior: 'smooth' });
	}

	/* ---------- 导航栏兜底：离开 hero 或无 hero 页面强制深色，避免滚动隐藏的状态错位 ---------- */
	(function () {
		var nav = document.querySelector('#navbar-main');
		if (!nav) return;
		var hero = document.querySelector('main .section-hero');
		if (!hero) { nav.classList.add('nav-solid'); return; }
		new IntersectionObserver(function (entries) {
			nav.classList.toggle('nav-solid', !entries[entries.length - 1].isIntersecting);
		}, { rootMargin: '-64px 0px 0px 0px', threshold: 0 }).observe(hero);
	})();

	/* ---------- 导航栏滚动隐藏（代替 Headroom.js，类名与原库一致，样式不用改） ----------
	 * 离顶部 300px 内：headroom--top 且始终显示；之外：headroom--not-top，
	 * 一帧内往下滚超过 30px 收起（headroom--unpinned），往上滚超过 30px 或回到顶部区域时放出（headroom--pinned）。 */
	(function () {
		var nav = document.querySelector('#navbar-main.headroom');
		if (!nav) return;
		var OFFSET = 300, TOLERANCE = 30;
		var lastY = window.pageYOffset, ticking = false;
		function set(name, on) { nav.classList.toggle(name, on); }
		function update() {
			ticking = false;
			var y = window.pageYOffset;
			var delta = y - lastY;
			lastY = y;
			// 橡皮筋回弹（滚出顶部或底部）时不动
			if (y < 0 || y + window.innerHeight > document.documentElement.scrollHeight) return;
			var atTop = y <= OFFSET;
			set('headroom--top', atTop);
			set('headroom--not-top', !atTop);
			if (delta > TOLERANCE && !atTop) {
				set('headroom--unpinned', true);
				set('headroom--pinned', false);
			} else if ((delta < -TOLERANCE || atTop) && nav.classList.contains('headroom--unpinned')) {
				// 与原库一致：只有收起过才加 pinned
				set('headroom--pinned', true);
				set('headroom--unpinned', false);
			}
		}
		window.addEventListener('scroll', function () {
			if (ticking) return;
			ticking = true;
			requestAnimationFrame(update);
		}, { passive: true });
		update();
	})();

	/* ---------- 回到顶部（带阅读进度） ---------- */
	(function () {
		var scrollup = document.getElementById('scrollup');
		var tools = document.getElementById('float-tools');
		var adminbtn = document.getElementById('adminbtn');
		if (!scrollup || !tools) return;
		var bar = scrollup.querySelector('.scrollup-bar');
		var pct = scrollup.querySelector('.scrollup-pct');

		function positionScrollBtn() {
			// 有后台按钮时整列移到它上方，间距与列内按钮间距（CSS gap）一致
			tools.style.bottom = '';
			if (!adminbtn) return;
			var cs = getComputedStyle(adminbtn);
			var gap = parseFloat(getComputedStyle(tools).rowGap) || 15;
			var b = parseFloat(cs.bottom) + parseFloat(cs.height) + gap;
			if (!isNaN(b)) tools.style.bottom = b + 'px';
		}

		positionScrollBtn();

		var ticking = false;
		function update() {
			ticking = false;
			var y = window.pageYOffset;
			var max = document.documentElement.scrollHeight - window.innerHeight;
			var p = max > 0 ? Math.min(100, Math.max(0, Math.round(y / max * 100))) : 0;
			scrollup.classList.toggle('is-visible', y > 500);
			if (bar) bar.style.strokeDashoffset = 100 - p;
			if (pct) pct.textContent = p;
		}
		function requestUpdate() {
			if (ticking) return;
			ticking = true;
			requestAnimationFrame(update);
		}

		var resizeTimer;
		window.addEventListener('resize', function () {
			requestUpdate();
			if (!adminbtn) return;
			clearTimeout(resizeTimer);
			resizeTimer = setTimeout(positionScrollBtn, 250);
		});
		window.addEventListener('scroll', requestUpdate, { passive: true });
		update();

		scrollup.addEventListener('click', function () {
			scrollToY(0);
		});
	})();

	/* ---------- 搜索弹窗 ---------- */
	(function () {
		var toggle = document.getElementById('search-toggle');
		var dialog = document.getElementById('search-dialog');
		if (!toggle || !dialog || !dialog.showModal) return;
		var input = dialog.querySelector('input[name="s"]');
		toggle.addEventListener('click', function () {
			dialog.showModal();
			if (input) { input.focus(); input.select(); }
		});
		// 点击遮罩处关闭（Esc 由原生 <dialog> 处理）
		dialog.addEventListener('click', function (e) {
			if (e.target === dialog) dialog.close();
		});
	})();

	/* ---------- 简繁切换（tw-cn.js 按需加载） ---------- */
	(function () {
		// 与 tw-cn.js 中的 Cookie 名保持一致；值为 1 表示访客上次切到了繁体
		var cookieName = 'targetEncoding' + location.origin.replace(/\./g, '');
		var wantsTraditional = document.cookie.split(';').some(function (c) {
			return c.trim() === cookieName + '=1';
		});
		function loadTwcn(cb) {
			loadScript(BB.twcnJs, function () {
				if (typeof translateInitilization === 'function') translateInitilization();
				if (cb) cb();
			});
		}
		if (wantsTraditional) {
			loadTwcn();
		} else {
			// 占位：首次点击时加载 tw-cn.js，加载后它会定义真正的 translatePage，再调用一次完成切换
			var stub = window.translatePage = function () {
				loadTwcn(function () {
					if (window.translatePage !== stub) window.translatePage();
				});
			};
		}
	})();

	/* ---------- 首页点赞 ----------
	 * 不去重，可以一直点。数字先在页面上加，停手约 0.8 秒后把这段时间点的次数一次性发给服务器
	 * （每次最多 50 下，服务端每秒每个 IP 只受理一次，多出来的留到下一次发）。 */
	(function () {
		var HEART = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s-7.2-4.4-9.6-8.9C.7 8.9 2.4 5 6.1 4.3c2.3-.4 4.3.8 5.9 2.9 1.6-2.1 3.6-3.3 5.9-2.9 3.7.7 5.4 4.6 3.7 7.8C19.2 16.6 12 21 12 21z"/></svg>';
		var COLORS = ['#ef5b4c', '#ff8a7a', '#f6b26b', '#ff6f91', '#f28c6b'];
		var btn = document.getElementById('hero-like');
		if (!btn) return;
		var base = parseInt(btn.getAttribute('data-count'), 10) || 0;
		var pending = 0, timer = null, sending = false;
		var streak = 0, lastClick = 0;
		var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

		function render() {
			btn.querySelector('.hero-like-count').textContent = (base + pending).toLocaleString('en-US');
		}

		function burst() {
			for (var i = 0; i < 3; i++) {
				var s = document.createElement('span');
				s.className = 'like-float';
				s.innerHTML = HEART;
				s.style.color = COLORS[Math.floor(Math.random() * COLORS.length)];
				s.style.setProperty('--dx', Math.round(Math.random() * 60 - 30) + 'px');
				s.style.setProperty('--dy', -Math.round(90 + Math.random() * 90) + 'px');
				s.style.setProperty('--r', Math.round(Math.random() * 60 - 30) + 'deg');
				s.style.setProperty('--s', (0.8 + Math.random() * 0.6).toFixed(2));
				s.style.animationDelay = (i * 70) + 'ms';
				s.addEventListener('animationend', function (e) { if (e.target === this) this.remove(); });
				btn.appendChild(s);
			}
		}

		// 彩蛋：连点到 10 下、50 下时放一大簇爱心，并飘出连击数
		function bigBurst(level) {
			var count = level >= 50 ? 24 : 14;
			var reach = level >= 50 ? 300 : 200;
			for (var i = 0; i < count; i++) {
				var s = document.createElement('span');
				var angle = (-90 + (Math.random() * 2 - 1) * (level >= 50 ? 110 : 75)) * Math.PI / 180;
				var dist = reach * (0.55 + Math.random() * 0.45);
				s.className = 'like-float like-big';
				s.innerHTML = HEART;
				s.style.color = COLORS[i % COLORS.length];
				s.style.setProperty('--dx', Math.round(Math.cos(angle) * dist) + 'px');
				s.style.setProperty('--dy', Math.round(Math.sin(angle) * dist) + 'px');
				s.style.setProperty('--r', Math.round(Math.random() * 90 - 45) + 'deg');
				s.style.setProperty('--s', (1.1 + Math.random() * (level >= 50 ? 1.2 : 0.8)).toFixed(2));
				s.style.animationDelay = Math.round(Math.random() * 160) + 'ms';
				s.addEventListener('animationend', function (e) { if (e.target === this) this.remove(); });
				btn.appendChild(s);
			}
			var tag = document.createElement('span');
			tag.className = 'like-combo';
			tag.textContent = '×' + level;
			tag.addEventListener('animationend', function (e) { if (e.target === this) this.remove(); });
			btn.appendChild(tag);
		}

		function schedule(delay) {
			clearTimeout(timer);
			timer = setTimeout(flush, delay);
		}

		function flush() {
			if (!pending) return;
			if (sending) { schedule(800); return; }
			var n = Math.min(pending, 50);
			var body = new URLSearchParams();
			body.append('bk_action', 'like');
			body.append('n', n);
			sending = true;
			fetch(BB.siteUrl, { method: 'POST', body: body, credentials: 'same-origin' })
				.then(function (res) { return res.json(); })
				.then(function (data) {
					if (!data) return;
					if (data.ok) pending -= n;
					if (typeof data.count === 'number') base = data.count;
				})
				.catch(function () {})
				.finally(function () {
					sending = false;
					render();
					if (pending > 0) schedule(1200);
				});
		}

		btn.addEventListener('click', function () {
			pending++;
			btn.classList.add('liked');
			btn.classList.remove('pop');
			void btn.offsetWidth;
			btn.classList.add('pop');
			render();
			var now = Date.now();
			streak = now - lastClick > 1200 ? 1 : streak + 1;
			lastClick = now;
			if (reduceMotion.matches) {
				// 减少动态效果时不飘爱心
			} else if (streak === 10 || streak === 50) {
				bigBurst(streak);
			} else {
				burst();
			}
			schedule(800);
		});

		// 离开页面前把没发出的点击补发出去
		window.addEventListener('pagehide', function () {
			if (!pending || !navigator.sendBeacon) return;
			var body = new URLSearchParams();
			body.append('bk_action', 'like');
			body.append('n', Math.min(pending, 50));
			navigator.sendBeacon(BB.siteUrl, body);
			pending = 0;
		});
	})();

	/* ---------- 目录页分类筛选 ----------
	 * 切换时列表先淡出（约 150ms），换好内容后逐条淡入；减少动态效果时直接切换。 */
	document.addEventListener('click', function (e) {
		var chip = e.target.closest('.arc-chip');
		if (!chip) return;
		var wrap = chip.closest('.arc-wrap');
		var cat = chip.getAttribute('data-cat');
		wrap.querySelectorAll('.arc-chip').forEach(function (c) {
			c.classList.toggle('active', c === chip);
			c.setAttribute('aria-pressed', c === chip ? 'true' : 'false');
		});
		var animate = !window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		function apply() {
			var index = 0;
			wrap.querySelectorAll('.arc-year').forEach(function (year) {
				var shown = 0;
				year.querySelectorAll('.arc-item').forEach(function (item) {
					var hit = !cat || (' ' + item.getAttribute('data-cats') + ' ').indexOf(' ' + cat + ' ') !== -1;
					item.hidden = !hit;
					item.classList.remove('arc-enter');
					if (hit) {
						shown++;
						// 只给前 20 条错开节奏，后面的同时出现，免得长列表等太久
						if (animate) {
							item.style.setProperty('--i', Math.min(index++, 20));
							item.classList.add('arc-enter');
						}
					}
				});
				year.hidden = shown === 0;
				var count = year.querySelector('.arc-year-count');
				if (count) count.textContent = shown;
			});
			wrap.classList.remove('arc-fading');
		}
		clearTimeout(wrap._arcTimer);
		if (!animate) { apply(); return; }
		wrap.classList.add('arc-fading');
		wrap._arcTimer = setTimeout(apply, 150);
	});

	document.addEventListener('animationend', function (e) {
		if (e.animationName === 'arc-enter') e.target.classList.remove('arc-enter');
	});

	/* ---------- 友链页：复制本站信息 ---------- */
	document.addEventListener('click', function (e) {
		var btn = e.target.closest('.links-copy');
		if (!btn) return;
		var text = btn.getAttribute('data-copy') || '';
		var done = function () {
			var old = btn.textContent;
			btn.textContent = '已复制';
			setTimeout(function () { btn.textContent = old; }, 1500);
		};
		if (navigator.clipboard && window.isSecureContext) {
			navigator.clipboard.writeText(text).then(done);
		} else {
			var ta = document.createElement('textarea');
			ta.value = text;
			ta.style.position = 'fixed';
			ta.style.opacity = '0';
			document.body.appendChild(ta);
			ta.select();
			try { document.execCommand('copy'); done(); } catch (err) {}
			ta.remove();
		}
	});

	/* ---------- 图片查看器（PhotoSwipe 5，第一次点图时才加载） ----------
	 * 点正文图片从原位放大打开；同一篇里的图片可左右滑动切换，双指 / 滚轮缩放，下拉或点空白关闭。 */
	var pswp = null, pswpModule = null;
	// 书架封面、友链头像、关于页头像是链接或装饰，不弹出大图
	var PSWP_SKIP = '.book, .link-card, .about-head, .link-avatar';

	function isZoomable(img) {
		if (!img.closest('.content') || img.closest(PSWP_SKIP)) return false;
		// 包在链接里的图片：链接指向图片文件才当作看大图，指向页面的照常跳转
		var a = img.closest('a[href]');
		return !a || /\.(jpe?g|png|gif|webp|avif|bmp|svg)(\?|#|$)/i.test(a.getAttribute('href'));
	}

	function loadPhotoSwipe() {
		if (pswpModule) return Promise.resolve(pswpModule);
		loadCss(BB.pswpCss);
		// 用 Function 包一层，老浏览器不认识 import() 时也不会让整个 theme.js 解析失败
		return new Function('u', 'return import(u)')(BB.pswpJs).then(function (m) {
			pswpModule = m.default;
			return pswpModule;
		});
	}

	function openPhotoSwipe(img) {
		// 加载失败的图不放进相册（被点的那张除外）
		var imgs = Array.prototype.filter.call(img.closest('.content').querySelectorAll('img'), function (el) {
			return el === img || (isZoomable(el) && !(el.complete && el.naturalWidth === 0));
		});
		var items = imgs.map(function (el) {
			var src = el.currentSrc || el.src;
			return {
				src: src,
				msrc: src,
				width: el.naturalWidth || parseInt(el.getAttribute('width'), 10) || 0,
				height: el.naturalHeight || parseInt(el.getAttribute('height'), 10) || 0,
				alt: el.alt || '',
				element: el
			};
		});
		loadPhotoSwipe().then(function (PhotoSwipe) {
			if (pswp) pswp.destroy();
			pswp = new PhotoSwipe({
				dataSource: items,
				index: Math.max(0, imgs.indexOf(img)),
				bgOpacity: 0.92,
				wheelToZoom: true,
				// 打开时整图适配屏幕；点缩放按钮（或点图片）直接看原图 1:1，本来就能完整显示的小图放大到 2 倍
				secondaryZoomLevel: function (zoom) { return zoom.fit < 1 ? 1 : 2; },
				closeTitle: '关闭',
				zoomTitle: '缩放',
				arrowPrevTitle: '上一张',
				arrowNextTitle: '下一张',
				errorMsg: '图片加载失败'
			});
			// 尺寸未知的图（还没加载完）在大图加载后补上真实尺寸
			pswp.on('contentLoadImage', function (e) {
				var c = e.content;
				if (c.data.width && c.data.height) return;
				c.element && c.element.addEventListener('load', function () {
					c.data.width = this.naturalWidth;
					c.data.height = this.naturalHeight;
					c.width = this.naturalWidth;
					c.height = this.naturalHeight;
					if (c.slide) c.slide.updateContentSize(true);
				}, { once: true });
			});
			pswp.on('destroy', function () { pswp = null; });
			pswp.init();
		}).catch(function () {
			// 加载失败时退回直接打开原图
			window.open(img.currentSrc || img.src, '_blank', 'noopener');
		});
	}

	document.addEventListener('click', function (e) {
		var img = e.target.closest && e.target.closest('.content img');
		if (!img || !isZoomable(img) || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey) return;
		e.preventDefault();
		openPhotoSwipe(img);
	});

	// 鼠标移到图片上就提前拉取脚本，点下去时基本不用等
	document.addEventListener('pointerover', function (e) {
		if (pswpModule || e.pointerType === 'touch') return;
		var img = e.target.closest && e.target.closest('.content img');
		if (img && isZoomable(img)) loadPhotoSwipe().catch(function () {});
	});

	/* ---------- 记住阅读位置 ----------
	 * 文章页（main[data-post-id]）滚动时记下阅读比例，存在本机 localStorage，最多保留 50 篇；
	 * 再次打开时在底部提示「继续阅读」。读到 95% 以后视为读完，清除记录。 */
	var setupReadingResume = (function () {
		var STORE = 'bubblekami:reading';
		var MAX = 50;
		var key = null;
		var timer = null;
		var toast = null;

		function read() {
			try { return JSON.parse(localStorage.getItem(STORE)) || {}; } catch (e) { return {}; }
		}
		function write(data) {
			try { localStorage.setItem(STORE, JSON.stringify(data)); } catch (e) {}
		}
		function ratio() {
			var max = document.documentElement.scrollHeight - window.innerHeight;
			return max > 0 ? window.pageYOffset / max : 0;
		}
		function record(k, r) {
			var data = read();
			if (r >= 0.95) {
				delete data[k];
			} else if (r >= 0.05) {
				// 不到 5% 不更新：刚打开还没开始读时不覆盖原来的进度
				data[k] = { r: Math.round(r * 1000) / 1000, t: Date.now() };
				var keys = Object.keys(data);
				if (keys.length > MAX) {
					keys.sort(function (a, b) { return data[a].t - data[b].t; })
						.slice(0, keys.length - MAX)
						.forEach(function (old) { delete data[old]; });
				}
			} else {
				return;
			}
			write(data);
		}
		window.addEventListener('scroll', function () {
			if (!key) return;
			var k = key, r = ratio();
			clearTimeout(timer);
			timer = setTimeout(function () { record(k, r); }, 600);
		}, { passive: true });

		function hide() {
			if (toast && toast.parentNode) toast.parentNode.removeChild(toast);
			toast = null;
		}

		return function () {
			var main = document.querySelector('main[data-post-id]');
			key = main ? 'post-' + main.getAttribute('data-post-id') : null;
			if (!key || location.hash) return;
			var saved = read()[key];
			if (!saved || window.pageYOffset > 200) return;

			var el = document.createElement('div');
			el.className = 'reading-resume';
			el.setAttribute('role', 'status');
			el.innerHTML = '<button type="button" class="reading-resume-go">继续阅读 · 上次读到 ' + Math.round(saved.r * 100) + '%</button>'
				+ '<button type="button" class="reading-resume-close" aria-label="关闭">×</button>';
			el.querySelector('.reading-resume-go').addEventListener('click', function () {
				var max = document.documentElement.scrollHeight - window.innerHeight;
				scrollToY(saved.r * max);
				hide();
			});
			el.querySelector('.reading-resume-close').addEventListener('click', hide);
			document.body.appendChild(el);
			toast = el;
			setTimeout(function () { if (toast === el) hide(); }, 10000);
		};
	})();

	setupReadingResume();

	/* ---------- 正文图片 ----------
	 * 懒加载兜底（服务端已给大部分图片加了 loading 属性；首屏优先加载的图不动）；
	 * 还没加载完的图片先透明，加载好再淡入，已在缓存里的直接显示，不闪。 */
	(function () {
		var fadeImages = !window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		document.querySelectorAll('.content img').forEach(function (img) {
			if (!img.hasAttribute('loading') && !img.hasAttribute('fetchpriority')) img.setAttribute('loading', 'lazy');
			if (!img.hasAttribute('decoding')) img.setAttribute('decoding', 'async');
			if (!fadeImages || img.complete) return;
			var show = function () { img.classList.add('img-in'); };
			img.classList.add('img-fade');
			img.addEventListener('load', show, { once: true });
			img.addEventListener('error', show, { once: true });
		});
	})();
})();
