function DateInputPopup(el, domCreateFunc, onChangeFunc) {
	var popup;
	var fadeTimeout;
	var ondocumentclick = function(e) {
		if (!popup.is(e.target) && 0 === popup.find(e.target).length)
			return;
		fadeTimeout = false;
		//e.stopPropagation();
		$(document).one('mouseup', function(){ el.focus(); });
	};

	el
		.focus(function(){
			fadeTimeout = undefined;
			if (!popup) {
				popup = $('<div class="ui-calendar-popup" style="display:none"></div>')
					.appendTo(document.body);
				domCreateFunc.call(popup);
			} else if (!popup.is(':hidden'))
				return;

			let offset = el.offset();
			let left = offset.left + el.outerWidth() - popup.outerWidth();
			popup.css({
				left: left < 32 ? 32 : left,
				top: offset.top + el.outerHeight()
			});

			popup.show();
			$(document).bind('mousedown', ondocumentclick);
		})
		.blur(function(e){
			//return;
			if (!popup || false === fadeTimeout)
				return;
			//fadeTimeout = setTimeout(function(){
				popup.hide();
				$(document).unbind('mousedown', ondocumentclick);
			//}, 10);
		})
		// .keyup(onChangeFunc)
		.change(onChangeFunc);
}

function DateNavigation(dateObject, menu) {
	var $menu;
	var self = this;
	var ondocumentclick;
	var close = function() {
		$menu.removeClass('expanded');
		$(document).unbind('click', ondocumentclick);
	};
	this.refresh = function(currentDateObject) {
		if (!$menu)
			return;
		var currentString = currentDateObject ? currentDateObject.format('Y-m-d') : '';
		$menu.find('div.date').each(function(){
			if ($(this).data('value') === currentString)
				$(this).addClass('selected');
			else
				$(this).removeClass('selected');
		});
		ondocumentclick = close;
	};
	this.getEl = function() {
		var html = '';
		html += '<nav>';
		for (let i in menu) {
			html += '<div class="date active" data-value="';
			if (i !== '') {
				let d = dateObject.clone();
				d.fromString(i);
				html += d.format('Y-m-d');
			}
			html += '">' + menu[i] + '</div>';
		}
		html += '</nav>';
		html += '<div class="btn-expand"></div>';

		$menu = $('<div class="nav-menu"></div>');
		$menu.html(html);
		$menu.find('div.btn-expand').click(function(e){
			if ($menu.hasClass('expanded'))
				return;
			e.stopPropagation();
			$menu.addClass('expanded');
			$(document).bind('click', ondocumentclick);
		});
		$menu.find('div.date').click(function(){ self.trigger('select', $(this).data('value')); });

		return $menu;
	};
	
	constructors.triggers.call(this);
}

function DateNavigationDates() {
	DateNavigation.call(this, new Date(), {
		'': 'Не выбрано',
		today: 'Сегодня',
		yesterday: 'Вчера',
		tomorrow: 'Завтра',
		'first day': 'Начало месяца',
		'last day': 'Конец  месяца',
		'first day of this year': 'Начало года',
		'last day of this year': 'Конец года'
	});
}

function DateNavigationRange() {
	DateNavigation.call(this, new DateRange(), {
		//all: 'За все время',
		today: 'Сегодня',
		yesterday: 'Вчера',
		thismonth: 'Текущий месяц',
		thisweek: 'Текущая неделя',
		thisyear: 'Текущий год',
		last7days: 'Последние 7 дней',
		last14days: 'Последние 14 дней',
		last30days: 'Последние 30 дней',
		prevmonth: 'Прошлый месяц',
		prevweek: 'Прошлая неделя',
		prevyear: 'Прошлый год'
	});
}

$.fn.elementDate = function(options) {
	options = $.extend({}, options);
	
	var el = $(this);
	var calendar = new Calendar();
	var navMenu = new DateNavigationDates();
	
	if (el.is('input')) {
		
		let onInputChange = function() {
			if ('' === el.val()) {
				calendar.getSelection().deselect();
				navMenu.refresh();
			} else {
				let dateString = el.val();
				let selection = calendar.getSelection();
				selection.select(dateString);
				let date = selection.getDate();
				navMenu.refresh(date);
				let newString = date.format('date');
				if (dateString === newString)
					return;
				el.val(newString);
			}
		};
		
		new DateInputPopup(el, function(){
			if (false !== options.navmenu) {
				this.append(navMenu.getEl());
				navMenu.bind('select', function(dateString) {
					if ('' === dateString)
						calendar.getSelection().deselect();
					else
						calendar.getSelection().select(dateString);
					calendar.trigger('select');
				});
				navMenu.refresh(calendar.getSelection().getDate());
			}
			this.append(calendar.getEl());
		}, onInputChange);
		
		el.mask('00.00.0000');
	
		calendar.bind('select', function(){
			let date = this.getSelection().getDate();
			el.val(date ? date.format('date') : '');
			navMenu.refresh(date);
			el.focus();
		});
		
		onInputChange();
	} else {
		el
			.append(navMenu.getEl())
			.append(calendar.getEl());
	}
};

$.fn.elementDaterange = function(options) {
	options = $.extend({}, options);
	var el = $(this);
	if (el.length === 0)
		return el;
	var calendarStart = new Calendar();
	var calendarEnd = new Calendar();
	var dateRange = new DateRange();
	var navMenu = new DateNavigationRange();
	var widget = {
		option: function(name, value) {
			if (undefined === value)
				return options[name];
			options[name] = value;
		},
		getRange: function() {
			return dateRange;
		},
		setMinDate: function(date) {
			options.minDate = date;
			calendarStart.setMinDate(date);
			calendarEnd.setMinDate(date);
			return this;
		},
		setMaxDate: function(date) {
			options.maxDate = date;
			calendarStart.setMaxDate(date);
			calendarEnd.setMaxDate(date);
			return this;
		},
		addBounds: function(minDate, maxDate, type) {
			calendarStart.addBounds(minDate, maxDate, type);
			calendarEnd.addBounds(minDate, maxDate, type);
			return this;
		}
	};
	
	if (options.minDate)
		widget.setMinDate(options.minDate);
	if (options.maxDate)
		widget.setMaxDate(options.maxDate);
	
	if (el.is('input')) {
		
		var onRangeChange = function() {
			let fn = function(selection, date) {
				selection.select(date);
				let sd = selection.getDate();
				if (sd === date)
					return date;
				return sd;
			};
			
			dateRange.setStart(fn(calendarStart.getSelection(), dateRange.getStart()));
			dateRange.setEnd(fn(calendarEnd.getSelection(), dateRange.getEnd()));
			
			calendarEnd.setMinDate(dateRange.getStart() || options.minDate);
			calendarEnd.setMaxDate(calendarEnd.getFirstBound(dateRange.getStart(), options.maxDate));
			calendarStart.setMinDate(calendarEnd.getLastBound(dateRange.getEnd(), options.minDate));
			calendarStart.setMaxDate(dateRange.getEnd() || options.maxDate);
			
			var newString = dateRange.format('date');
			
			if (el.val() !== newString)
				el.val(el._value = newString);
			
			navMenu.refresh(dateRange);
			//el.focus();
		};
		var setRangeString = function(rangeString) {
			widget.setMinDate(options.minDate);
			widget.setMaxDate(options.maxDate);
			
			dateRange.fromString(rangeString);
			calendarStart.getSelection().select(dateRange.getStart());
			calendarEnd.getSelection().select(dateRange.getEnd());
			onRangeChange();
		};
		let onInputChange = function(e, forced) {
			if (true !== forced && el._value === el.val())
				return;
			el._value = el.val();
			if ('' === el._value) {
				dateRange.clear();
				calendarStart.getSelection().deselect();
				calendarEnd.getSelection().deselect();
				navMenu.refresh(dateRange);
			} else {
				setRangeString(el._value);
			}
		};
		
		new DateInputPopup(el, function(){
			if (false !== options.navmenu) {
				this.append(navMenu.getEl());
				navMenu.bind('select', setRangeString);
				navMenu.refresh(dateRange);
			}
			this
				.append(calendarStart.getEl())
				.append(calendarEnd.getEl());
		}, onInputChange);
		
		el.mask('00.00.0000 - 00.00.0000');
		
		calendarStart.bind('select', function() {
			dateRange.setStart(this.getSelection().getDate());
			onRangeChange();
		});
		calendarEnd.bind('select', function() {
			dateRange.setEnd(this.getSelection().getDate());
			onRangeChange();
		});
		
		onInputChange();
	} else {
		el
			.append(navMenu.getEl())
			.append(calendarStart.getEl())
			.append(calendarEnd.getEl());
	}
	
	el[0]._widget = widget;
	
	return el;	
};