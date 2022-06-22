(function(){
	
	var rangeFromString = function(string) {
		let from = new Date();
		let to = new Date();
		switch (string) {
			case 'all':
				return [null, null];
			case 'today':
				return [from, to];
			case 'yesterday':
				from.setDate(from.getDate() - 1);
				to.setDate(from.getDate());
				return [from, to];
			case 'last7days':
				from.setDate(from.getDate() - 7);
				return [from, to];
			case 'last14days':
				from.setDate(from.getDate() - 14);
				return [from, to];
			case 'last30days':
				from.setDate(from.getDate() - 30);
				return [from, to];
			case 'thisyear':
				from.setMonth(0);
				from.setDate(1);
				to.setMonth(11);
				to.setDate(31);
				return [from, to];
			case 'prevyear':
				from.setYear(from.getYear() - 1);
				to.setYear(from.getYear());
				from.setMonth(0);
				from.setDate(1);
				to.setMonth(11);
				to.setDate(31);
				return [from, to];
			case 'thismonth':
				from.setDate(1);
				to.setMonth(to.getMonth() + 1);
				to.setDate(0);
				return [from, to];
			case 'prevmonth':
				from.setMonth(from.getMonth() - 1);
				from.setDate(1);
				to.setDate(0);
				return [from, to];
			case 'nextmonth':
				from.setMonth(from.getMonth() + 1);
				from.setDate(1);
				to.setMonth(from.getMonth() + 1, 0);
				return [from, to];
			case 'thisweek':
				(function(){
					let d = from.getDay();
					if (d !== 1)
						from.setDate(from.getDate() - d + 1);
					to.setMonth(from.getMonth(), from.getDate() + 6);
				})();
				return [from, to];
			case 'prevweek':
				(function(){
					let d = from.getDay();
					from.setDate(from.getDate() - d + 1 - 7);
					to.setMonth(from.getMonth(), from.getDate() + 6);
				})();
				return [from, to];
			case 'nextweek':
				(function(){
					let d = from.getDay();
					from.setDate(from.getDate() - d + 1 + 7);
					to.setMonth(from.getMonth(), from.getDate() + 6);
				})();
				return [from, to];
		}
		return null;
	};

	DateRange = function(dateStart, dateEnd) {
		
		var validate = function() {
			if (!dateStart || !dateEnd)
				return;
			if (dateStart.getTime() > dateEnd.getTime())
				dateEnd = dateStart.clone();
		};
		
		dateStart = dateStart ? Date.factory(dateStart) : null;
		dateEnd = dateEnd ? Date.factory(dateEnd) : null;
		
		validate();
			
		this.clone = function() {
			return new DateRange(this.getStart(), this.getEnd());
		};
		this.fromString = function(string) {
			var dates = rangeFromString(string);
			if (dates) {
				dateStart = dates[0] ? dates[0] : null;
				dateEnd = dates[1] ? dates[1] : null;
			} else {
				dates = string.split(' - ');
				dateStart = dates[0] ? Date.factory(dates[0]) : null;
				dateEnd = dates[1] ? Date.factory(dates[1]) : null;
			}
			validate();
		};
		this.clear = function() {
			dateStart = null;
			dateEnd = null;
			return this;
		};
		this.setStart = function(date) {
			dateStart = date === null ? null : Date.factory(date);
			validate();
			return this;
		};
		this.setEnd = function(date) {
			dateEnd = date === null ? null : Date.factory(date);
			validate();
			return this;
		};
		this.getStart = function() { return dateStart; };
		this.getEnd = function() { return dateEnd; };
		this.getDates = function() {
			if (!dateStart || !dateEnd)
				return [];
			var date = dateStart.clone();
			var dateEndString = dateEnd.format('Y-m-d');
			var dates = [date.clone()];
			
			while (date.format('Y-m-d') !== dateEndString) {
				date.modify('+1 day');
				dates[dates.length] = date.clone();
			}
			
			return dates;
		};
		this.format = function(format) {
			if (!dateStart && !dateEnd)
				return '';
			return (dateStart ? dateStart.format(format) : '')
					+ ' - ' + (dateEnd ? dateEnd.format(format) : '');
		};
		this.toString = function() { return this.format(); };
	};
	
})();