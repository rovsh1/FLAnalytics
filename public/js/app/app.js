const app = new function () {
	this.ui = {};
	this.form = {};
	this.ga = {};

	let sites = [];

	this.init = function (config) {
		for (let i = 0; i < config.sites.length; i++) {
			const site = new app.config.Site(config.sites[i]);
			sites.push(site);
		}
	};
	this.getSites = function () { return sites; };
	this.getSite = function (id) {
		for (let i = 0; i < sites.length; i++) {
			if (sites[i].id === id)
				return sites[i];
		}
		return false;
	};

};

app.config = {};