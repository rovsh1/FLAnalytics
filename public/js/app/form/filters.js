app.form.Filters = function (container) {

	const self = this;
	let elements = [];
	//let urlParams = new URLSearchParams(location.search);

	const pushState = function () {
		const uri = location.pathname + '?' + urlParams.toString();
		window.history.pushState('', '', uri);
	};
	const elementFactory = function (name, type, params) {
		switch (type) {
			case 'site':
				return new app.form.element.Site(name, params);
			case 'period':
				return new app.form.element.Period(name, params);
		}
	};

	this.getEl = function () { return container; };
	this.get = function (name) { return this.getElement(name).getValue(); };
	this.addElement = function (name, type, params) {
		const element = elementFactory(name, type, params);
		elements.push(element);
		this.getEl().append(element.getEl());
		return this;
	};
	this.addButton = function (fn) {
		const btn = $('<button type="button" id="btn-filter">Обновить</button>').appendTo(this.getEl());
		btn.click(fn);//.click();

		return this;
	};
	this.getElement = function (name) {
		for (let i = 0; i < elements.length; i++) {
			if (elements[i].name === name)
				return elements[i];
		}
	};

	//window.onpopstate = function(event) { self.trigger('change'); };

	//constructors.triggers.call(this);
};

app.form.element = {};