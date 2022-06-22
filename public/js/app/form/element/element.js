app.form.element.Element = function (name, params) {
	let el, inputEl;
	const inputId = 'filter-' + name;

	this.name = name;

	this.getEl = function () {
		this.getEl = function () { return el; };

		let html = '<div class="form-field ' + params.cls + '">';
		if (params.label)
			html += '<label for="' + inputId + '">' + params.label + '</label>';
		html += '</div>';

		el = $(html);
		inputEl = this.createInputEl()
			.attr('id', inputId)
			.attr('name', name);
		el.append(inputEl);
		if (params.default)
			this.setValue(params.default);

		this.init(el, inputEl);

		return el;
	};
	this.init = function () {};
	this.createInputEl = function () { return $('<input />'); };
	this.getValue = function () { return inputEl.val(); };
	this.setValue = function (value) { inputEl.val(value); };
};