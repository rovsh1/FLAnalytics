app.form.element.Site = function (name, params) {
	app.form.element.Element.call(this, name, $.extend({
		cls: 'field-site',
		label: 'Сайт'
	}, params));

	const subdomains = [
		{id: 1, sub: 'www', name: 'Строй'},
		{id: 2, sub: 'auto', name: 'Авто'},
		{id: 3, sub: 'tech', name: 'Техника'},
		{id: 4, sub: 'home', name: 'Дом быта'}
	];

	this.createInputEl = function () {
		let html = '<select>';
		const sites = app.getSites();
		sites.forEach(s => {
			html += '<optgroup label="' + s.get('name') + '">';
			subdomains.forEach(sub => {
				html += '<option value="' + s.id + '-' + sub.id + '">' + sub.name + '</option>';
			});
			html += '</optgroup>';
		});
		html += '</select>';

		return $(html);
	};
};