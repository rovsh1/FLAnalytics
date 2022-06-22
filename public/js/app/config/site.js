app.config.Site = function (data) {
	this.id = data.gaId;
	this.get = function (name) { return data[name]; };
};