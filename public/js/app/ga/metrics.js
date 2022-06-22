app.ga.Metrics = function (params) {

	app.ga.Request.call(this, {});

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

	this.get = function () {
		this.send();
				var s = array.shift();
				var q = query(params, {
					'ids': view_id,
					'metrics': goal,
					'segment': 'sessions::condition::ga:pagePath=@' + s[0],
					'start-date': dateRangeCompareChanel1["start-date"],
					'end-date': dateRangeCompareChanel1["end-date"]
				});
				Promise.all([q]).then(function (results) {
					s[1].html(results[0].totalsForAllResults[goal]);
				});
				if (array.length)
					getContactClicks();
	};
	this.setParam = function (name, value) {
		queryParams[name] = value;
		return this;
	};
	this.setIds = function (ids) { return this.setParam('ids', ids); };
	this.setSegment = function (segment) { return this.setParam('segment', segment); };
	this.setMetrics = function (metrics) { return this.setParam('metrics', metrics); };
	this.setFilters = function (filters) { return this.setParam('filters', filters); };
	this.setPeriod = function (period) {
		return this
			.setParam('start-date', period[0])
			.setParam('end-date', period[1]);
	};
	this.send = function (fn) {
		const promise = query();
	};
};