app.ga.Request = function (queryParams) {

	queryParams = $.extend({}, queryParams);
	/*let queryParams = {
		//'dimensions': 'ga:pagePath',
		//'sort': '-ga:pageviews',
		//'metrics': 'ga:pageviews,ga:uniquePageviews',
		//'filters': 'ga:pagePath=@<?=$routeName?>/view',
		//'start-date': dateRangeCompareChanel1["start-date"],
		//'end-date': dateRangeCompareChanel1["end-date"],
		//'max-results': 500
	};*/

	const query = function () {
		return new Promise(function (resolve, reject) {
			const data = new gapi.analytics.report.Data({query: queryParams});
			data
				.once('success', function (response) { resolve(response); })
				.once('error', function (response) {
					if (response.error && response.error.code && response.error.code == 403) {
						// query(params);
					}
					reject(response);
				})
				.execute();
		});
	}

	this.setParam = function (name, value) {
		if (null === value)
			delete queryParams[name];
		else
			queryParams[name] = value;
		return this;
	};
	this.setIds = function (ids) { return this.setParam('ids', ids); };
	this.setSegment = function (segment) { return this.setParam('segment', segment); };
	this.setMetrics = function (metrics) { return this.setParam('metrics', metrics); };
	this.setDimensions = function (dimensions) { return this.setParam('dimensions', dimensions); };
	this.setFilters = function (filters) { return this.setParam('filters', filters); };
	this.addFilter = function (filters) {
		if (!queryParams.filters)
			queryParams.filters = '';
		queryParams.filters += ';' + filters;
		return this;
	};
	this.setPeriod = function (period) {
		if (!period)
			period = [null, null];
		else if (typeof period === 'string') {
			const s = period.split(' - ');
			if (!s[1])
				return this.setPeriod(null);
			period = [Date.factory(s[0]).format('Y-m-d'), Date.factory(s[1]).format('Y-m-d')];
		}

		return this
			.setParam('start-date', period[0])
			.setParam('end-date', period[1]);
	};
	this.send = function (fn) {
		const promise = query();
		Promise.all([promise]).then(fn);
	};
};