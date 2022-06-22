function Calendar(options) {
	
	options = $.extend({
		selectable: false,
		multiselect: false
	}, options);
	
	var self = this;
	var $el;
	var monthDate;
	
	var getDateValue = function(date) {
		return Date.factory(date).format('Y-m-d');
	};
	var ondateclick = function() {
		selection.toggle($(this).data('date'));
		self.trigger('select');
		monthsMenu.hide();
	};
	var monthsMenu = new function() {
		var $menu;
		this.show = function(){
			if ($menu) {
				var html = '<div class="months-menu">';
				html += '<table>';
				for (let r = 0; r < 3;r++) {
					html += '<tr>';
					for (let m, c = 0; c < 4;c++) {
						m = c + r * 4;
						html += '<td data-month="' + m + '" class="month active">' + Date.monthNamesShort[m] + '</td>';
					}
					html += '</tr>';
				}
				html += '</table>';
				html += '<div class="years">';
				for (let i = 0; i < 40; i++) {
					html += '<option></option>';
				}
				html += '</div>';
				html += '</div>';
				$menu = $(html).appendTo($el);
			} else {
				$menu.show();
			}
		};
		this.hide = function() {
			if (!$menu)
				return;
			$menu.hide();
		};
	};
	var setCurrentDate = function(date) {
		monthDate = date.clone();
		monthDate.setDate(1);
	};
	
	var bounds = new function() {
		var bound = {min: null, max: null, type: 'include'};
		var items = [];
		var has = function(bn, dateString) {
			var flag = bn.type === 'include';
			if (bn.min && dateString < getDateValue(bn.min))
				return !flag;
			else if (bn.max && dateString > getDateValue(bn.max))
				return !flag;
			return flag;
		};
		
		this.setMinDate = function(date) {
			bound.min = date ? Date.factory(date) : null;
		};
		this.setMaxDate = function(date) {
			bound.max = date ? Date.factory(date) : null;
		};
		this.findFirst = function(date, maxDate) {
			if (!date)
				return maxDate;
			
			var boundDate;
			var dateString = getDateValue(date);
			for (let s, i = 0; i < items.length; i++) {
				if (!items[i].min || dateString >= (s = getDateValue(items[i].min)))
					continue;
				
				if (boundDate && getDateValue(boundDate) < s)
					continue;
				
				boundDate = items[i].min;
			}
			
			if (!boundDate || (maxDate && getDateValue(boundDate) > getDateValue(maxDate)))
				return maxDate;
			
			return boundDate;
		};
		this.findLast = function(date, minDate) {
			if (!date)
				return minDate;
			
			var boundDate;
			var dateString = getDateValue(date);
			for (let s, i = 0; i < items.length; i++) {
				if (!items[i].max || dateString <= (s = getDateValue(items[i].max)))
					continue;
				
				if (boundDate && getDateValue(boundDate) > s)
					continue;
				
				boundDate = items[i].max;
			}
			
			if (!boundDate || (minDate && getDateValue(boundDate) < getDateValue(minDate)))
				return minDate;
			
			return boundDate;
		};
		this.getDate = function() { return bound.min || bound.max; };
		this.add = function(min, max, type) {
			/*if (min) {
				if (!minDate || getDateValue(minDate) > getDateValue(min))
					minDate = min;
			}
			if (max) {
				if (!maxDate || getDateValue(maxDate) < getDateValue(max))
					maxDate = max;
			}*/
			items[items.length] = {
				min: min ? Date.factory(min) : null,
				max: max? Date.factory(max) : null,
				type: type
			};
		};
		this.has = function(date) {
			var dateString = getDateValue(date);
			
			if (!has(bound, dateString))
				return false;
			
			for (let i = 0; i < items.length; i++) {
				if (has(items[i], dateString))
					continue;
				return false;
			}
			return true;
		};
	};
	
	var table = new function() {
		var cells;
		
		this.getCells = function() { return cells; };
		
		this.getCell = function(date) {
			var dateString = getDateValue(date);
			for (let i = 0; i < cells.length; i++) {
				if (cells.eq(i).data('date') === dateString)
					return cells.eq(i);
			}
			return null;
		};
		
		this.select = function(dates) {
			for (let i = 0; i < dates.length; i++) {
				let cell = this.getCell(dates[i]);
				if (!cell)
					continue;
				cell.addClass('selected');
			}
		};
		
		this.refresh = function(rerender) {
			if (!cells)
				return;
			if (rerender)
				this.render();
			else {
				this.deselect();
				this.select(selection.getDates());
			}
			self.trigger('update');
		};
		
		this.deselect = function() { cells.removeClass('selected'); };
		
		this.render = function() {
			var date = monthDate.clone();
			var currentMonth = monthDate.getMonth();
			var todayDateString = getDateValue(now());
			var html = '';
			date.setDate(date.getDay() > 1 ? (2 - date.getDay()) : 1);

			html += '<table>';
			html += '<thead><tr>';
			html += '<th class="active prev"></th><th colspan="5" class="active month">' + Date.monthNames[monthDate.getMonth()] + ' ' + monthDate.getYear() + '</th><th class="active next"></th>';
			html += '</tr><tr>';
			for (let day = 0;day < 7; day++) {
				html += '<th>' + Date.dayNamesShort[day] + '</th>';
			}
			html += '</tr>';
			html += '</thead>';

			html += '<tbody>';
			for (let week = 0;week < 6; week++) {
				html += '<tr>';

				for (let day = 0; day < 7;day++) {
					
					let dateString = getDateValue(date);
					let cls = 'day';

					if (bounds.has(dateString))
						cls += ' active';
					else
						cls += ' disabled';
					if (dateString === todayDateString)
						cls += ' today';
					else if (date.getMonth() !== currentMonth)
						cls += ' month-alt';

					html += '<td class="' + cls + '" data-date="' + dateString + '">' + date.getDate() + '</td>';

					date.setDate(date.getDate() + 1);
				}
				html += '</tr>';
			}
			html += '</tbody>';
			html += '</table>';
			
			$el.html(html);
			
			let $table = $el.find('table');
			
			cells = $table.find('tbody td.active').click(ondateclick);
			$table.find('thead th.month').click(function(){ monthsMenu.show(); });
			$table.find('thead th.prev').click(function(){
				monthDate.modify('-1 month');
				table.render();
			});
			$table.find('thead th.next').click(function(){
				monthDate.modify('+1 month');
				table.render();
			});
			
			this.refresh();
		};
	};
	
	var selection = new function() {
		var selectedDates = [];
		var setDate = function(date) {
			let currentMonth = monthDate.format('Y-m');
			setCurrentDate(date);
			table.refresh(monthDate.format('Y-m') !== currentMonth);
		};
		
		this.remove = function(date) {
			var dateString = getDateValue(date);
			for (let i = 0; i < selectedDates.length; i++) {
				if (getDateValue(selectedDates[i]) !== dateString)
					continue;
				selectedDates.splice(i, 1);
				break;
			}
		};
		this.has = function(date) {
			var dateString = getDateValue(date);
			for (let i = 0; i < selectedDates.length; i++) {
				if (getDateValue(selectedDates[i]) === dateString)
					return true;
			}
			return false;
		};
		this.select = function(dates) {
			if (null === dates)
				return this.deselect();
			if (dates instanceof Date || isString(dates))
				dates = [dates];
			else if (!isArray(dates))
				return console.error('dates format error', dates);
			
			var selected = [];
			for (let d, i = 0; i < dates.length; i++) {
				d = Date.factory(dates[i]);
				if (!d.isValid() || !bounds.has(getDateValue(d)))
					continue;
				selected[selected.length] = d;
			}
			
			if (selected.length === 0)
				return this.deselect();
			
			if (options.multiselect) {
				for (let i = 0; i < selected.length; i++) {
					selectedDates[selectedDates.length] = selected[i];
				}
			} else {
				selectedDates = [selected[0]];
			}
			setDate(selectedDates[0].clone());
		};
		this.toggle = function(date) {
			if (this.has(date))
				return this.deselect(date);
			else
				return this.select(date);
		};
		this.deselect = function(date) {
			selectedDates = [];
			setDate(now());
		};
		this.getDates = function() { return selectedDates; };
		this.getDate = function() { return selectedDates.length ? selectedDates[0] : null; };
	};
	
	this.getSelection = function() { return selection; };
	
	this.getEl = function() {
		if ($el)
			return $el;
		$el = $('<div class="ui-calendar"></div>');
		table.render();
		return $el;
	};

	this.destroy = function() {
		$el.html('');
	};
	
	this.setMonth = function(date) {
		setCurrentDate(Date.factory(date));
		table.refresh(true);
		return this;
	};
	
	this.addBounds = function(minDate, maxDate, type) {
		bounds.add(minDate, maxDate, type);
		table.refresh(true);
	};
	
	this.getFirstBound = function(date, maxDate) { return bounds.findFirst(date, maxDate); };
	
	this.getLastBound = function(date, minDate) { return bounds.findLast(date, minDate); };
	
	this.setMinDate = function(date) {
		bounds.setMinDate(date);
		if (!bounds.has(monthDate))
			setCurrentDate(bounds.getDate());
		table.refresh(true);
		return this;
	};
	
	this.setMaxDate = function(date) {
		bounds.setMaxDate(date);
		if (!bounds.has(monthDate))
			setCurrentDate(bounds.getDate());
		table.refresh(true);
		return this;
	};
	
	setCurrentDate(now());
	
	constructors.triggers.call(this);
	
}