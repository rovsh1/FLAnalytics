app.form.element.Period = function(name, params) {
	app.form.element.Element.call(this, name, $.extend({
		cls: 'field-daterange',
		label: 'Период',
		default: (function() {
			const sd = now();
			sd.modify('-7 days');
			return sd.format('date') + ' - ' + now().format('date');
		})()
	}, params));

	this.init = function (el, inputEl) {
		inputEl.elementDaterange({});
	};
	this.getPeriod = function () {
		const v = this.getValue();
		if (!v)
			return null;
		const s = v.split(' - ');
		if (!s[1])
			return null;
		return [Date.factory(s[0]).format('Y-m-d'), Date.factory(s[1]).format('Y-m-d')];
	};
};