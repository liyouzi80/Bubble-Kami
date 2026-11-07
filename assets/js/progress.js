/* 页面跳转进度条：顶部 2px 渐变细线，与文章阅读进度线同款（样式见 kami.css 的 #argprogress）
 * 开始后先快后慢地涨到 90% 附近等待，加载完成时补满再淡出；只改 transform，不触发重排。 */
(function () {
	var el = document.createElement("div");
	el.id = "argprogress";
	el.setAttribute("aria-hidden", "true");
	el.innerHTML = '<div class="argprogress-bar"></div>';
	document.body.appendChild(el);
	var bar = el.firstChild;
	var root = document.documentElement;
	var value = 0, trickle = null, hideTimer = null;

	function set(p) {
		value = p;
		bar.style.transform = "scaleX(" + p + ")";
	}

	window.start_progress = function () {
		clearInterval(trickle);
		clearTimeout(hideTimer);
		// 从 0 重新开始时不要有回退动画
		bar.style.transition = "none";
		set(0);
		void bar.offsetWidth;
		bar.style.transition = "";
		el.classList.add("is-active");
		root.classList.add("is-page-loading");
		set(0.12);
		trickle = setInterval(function () {
			set(value + (0.9 - value) * 0.1);
		}, 150);
		return trickle;
	};

	window.stop_progress = function (id) {
		clearInterval(id);
		clearInterval(trickle);
		set(1);
		hideTimer = setTimeout(function () {
			el.classList.remove("is-active");
			root.classList.remove("is-page-loading");
		}, 300);
	};
})();
