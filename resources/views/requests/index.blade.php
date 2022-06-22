<!-- index.blade.php -->
@extends('layouts.app')

@section('template_title')
	Showing Analytics
@endsection

@section('content')
	<div class="container">
		<h2>Аналитика по заявкам</h2>
		<div class="analytics-filters-wrap"></div>
		<div class="table-responsive hidden">
			<table class="table table-striped" id="table-total">
				<thead>
				<tr>
					<th>Кнопка заказать услугу</th>
					<th>В системе</th>
					<th>В GTM</th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td class="column-count-1"></td>
					<td class="column-count-2" width="200px" align="right"></td>
					<td class="column-count-3" width="200px" align="right"></td>
				</tr>
				</tbody>
			</table>

			<table class="table table-striped" id="table-categories">
				<thead>
				<tr>
					<th>ID</th>
					<th>Name</th>
					<th>В системе</th>
					<th>В GTM</th>
				</tr>
				</thead>
				<tbody>
				@foreach($siteData as $item)
					<tr data-id="{{$item->id}}">
						<td>{{$item->id}}</td>
						<td>{{$item->name}}</td>
						<td class="column-count column-site" width="200px">{{$item->requests_count}}</td>
						<td class="column-count column-requests" width="200px"></td>
					</tr>
				@endforeach
				</tbody>
			</table>
		</div>
	</div>

@stop


@section('template_scripts')
	<script src={{asset('js/app/index.php')}}></script>
	<script>
		app.init({
			sites: [{
				name: 'Ustabor',
				domain: 'ustabor.uz',
				gaId: 168341712
			}, {
				name: 'Fixinglist',
				domain: 'fixinglist.com',
				gaId: 168329090
			}]
		});
		app.GoogleAnalytics
			.init("{{$token}}")
			.ready(function () {
				const filters = new app.form.Filters($('div.analytics-filters-wrap'));
				const responseEl = $('div.table-responsive');
				const tableTotal = $('#table-total');
				const tableCategories = $('#table-categories');
				let requestTimer;

				const resetTimer = function () {
					if (!requestTimer)
						return;
					clearInterval(requestTimer);
					requestTimer = null;
				};
				const requestFactory = function () {
					const request = new app.ga.Request();
					request.setIds('{{$GAId}}');
					request.setPeriod(filters.getElement('date').getPeriod());
					return request;
				};
				const categoryRequestFactory = function (categoryId) {
					const request = requestFactory();
					request.setDimensions('ga:eventCategory, ga:eventAction, ga:eventLabel');
					request.setMetrics('ga:uniqueEvents');
					request.setFilters('ga:eventCategory==form_order-request-create;ga:eventLabel=@request-create-<?=$siteId?>-');
					request.addFilter('ga:eventAction=@request-create-' + (categoryId ? categoryId + '-' : ''))
					return request;
				};
				const updateButtonClicks = function () {
					const td = tableTotal.find('td.column-count-1').addClass('loading');
					const request = requestFactory();
					request.setDimensions('ga:eventCategory');
					request.setMetrics('ga:uniqueEvents');
					request.setFilters('ga:eventCategory==button-order-header');
					request.setSegment('<?=$segments?>');
					request.send(function (r) {
						//const count = r[0].totalResults;
						const count = +r[0].totalsForAllResults['ga:uniqueEvents'];
						td.html(count).removeClass('loading');
						if (count == 0)
							td.addClass('column-empty');
					});
				};
				const updateTotal = function () {
					const td = tableTotal.find('td.column-count-3').addClass('loading');
					const request = categoryRequestFactory(null);
					request.send(function (r) {
						const count = +r[0].totalsForAllResults['ga:uniqueEvents'];
						td.html(count).removeClass('loading');
						if (count == 0)
							td.addClass('column-empty');
					});
				};
				const updateTable = function () {
					let i = 0;
					const rows = tableCategories.find('tbody tr');
					rows.find('td.column-requests').html('').addClass('loading');
					requestTimer = setInterval(function () {
						const tr = rows.eq(i++);
						const categoryId = tr.data('id');
						const tdRequests = tr.find('td.column-requests');

						if (i === rows.length)
							resetTimer();

						const request = categoryRequestFactory(categoryId);
						request.send(function (r) {
							//console.log(r)
							const count = r[0].totalResults;
							tdRequests.html(count);
							if (count == 0)
								tdRequests.addClass('column-empty');
							tdRequests.removeClass('loading');
						});
					}, 200);
				};
				const updateStatistics = function () {
					let i = 0;
					const rows = tableCategories.find('tbody tr');
					const cells = rows.find('td.column-site').html('').addClass('loading');
					const totalCell = tableTotal.find('td.column-count-2').html('').addClass('loading');
					$.getJSON('/<?=$urlPrefix?>/requests/api', {
						site_id: <?=$siteId?>,
						created: filters.getElement('date').getPeriod()
					}, function(r) {
						let i = 0;
						let total = 0;
						r.forEach(c => {
							cells.eq(i++).html(c.requests_count);
							total += +c.requests_count;
						});
						cells.removeClass('loading');
						totalCell.html(total).removeClass('loading');
					});
				};

				filters
					//.addElement('site', 'site')
					.addElement('date', 'period')
					.addButton(function () {
						resetTimer();
						responseEl.show();
						updateButtonClicks();
						updateTotal();
						updateTable();
						updateStatistics();
					});
			});
	</script>
@endsection