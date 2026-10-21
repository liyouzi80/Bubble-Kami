function changeprogress(progress) {
	progress -= 4;
	var bar = document.querySelector("#argprogress .progress-bar");
	if (bar) bar.style.width = progress + "%";
}

function start_progress() {
	var tick = 1;
	var el = document.getElementById("argprogress");
	if (el) el.style.display = "flex";
	var loadingtime = window.performance.timing["loadEventEnd"] - window.performance.timing["navigationStart"];
	var nowtime = 0;
	var clock = setInterval(function () {
		nowtime += tick;
		if (nowtime > loadingtime) {
		} else {
			var nowprogress = Math.round(nowtime / loadingtime * 100);
			changeprogress(nowprogress);
		}
	}, tick);
	return clock;
}

function stop_progress(clock) {
	var el = document.getElementById("argprogress");
	var bar = el ? el.querySelector("div") : null;
	if (bar) bar.style.transition = "all 600ms ease-in-out 0s";
	clearInterval(clock);
	changeprogress(104);
	setTimeout(function () {
		if (el) el.style.display = "none";
		setTimeout(function () {
			changeprogress(-4);
			if (bar) bar.style.transition = "all 200ms linear 0s";
		}, 150);
	}, 500);
}

(function () {
	var style = document.createElement("style");
	style.textContent = "#argprogress{position: fixed;top: 0;width: 100%;left: 0;border: none;z-index: 105;overflow: hidden;height: 5px;display: flex;background-color: #e9ecef;}";
	document.head.appendChild(style);
	var div = document.createElement("div");
	div.id = "argprogress";
	div.style.display = "none";
	div.innerHTML = '<div class="progress-bar bg-gradient-primary" style="width:0%;border: none;"></div>';
	document.body.appendChild(div);
	var bar = div.querySelector("div");
	if (bar) bar.style.transition = "all 200ms linear 0s";
})();
