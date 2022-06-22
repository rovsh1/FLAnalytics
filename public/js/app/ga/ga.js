app.GoogleAnalytics = new function () {
	this.init = function (token) {
		(function (w, d, s, g, js, fs) {
			g = w.gapi || (w.gapi = {});
			g.analytics = {q: [], ready: function (f) {this.q.push(f);}};
			js = d.createElement(s);
			fs = d.getElementsByTagName(s)[0];
			js.src = 'https://apis.google.com/js/platform.js';
			fs.parentNode.insertBefore(js, fs);
			js.onload = function () {g.load('analytics');};
		}(window, document, 'script'));

		gapi.analytics.ready(function () {
			gapi.analytics.auth.authorize({
				'serverAuth': {
					'access_token': token
				}
			});
		});

		return this;
	};
	this.ready = function (fn) {
		gapi.analytics.ready(function () { fn.call(app.GoogleAnalytics); });
		return this;
	};
};