<?php
$current_route = request()->route()->getPrefix();

if($filter_query){

    if(strpos($query,'www.') !==false){

        $filter = 'ga:pagePath=~^'.$query.'..*\,ga:pagePath=~^'.str_replace('www.','',$query).'..*\;ga:pagePath!~^'.$prefix[0].'..*'.'\;ga:pagePath!~^'.$prefix[1].'..*';
    }else{
        $filter = 'ga:pagePath=~^'.$query.'..*\;ga:pagePath!~^'.$prefix[0].'..*'.'\;ga:pagePath!~^'.$prefix[1].'..*';
    }

}else{
    $filter = 'ga:pagePath=~^'.$query.'..*';
}
?>

@extends('layouts.app')

@section('template_title')
    Showing Analytics
@endsection

@section('template_linked_css')
    <!-- Include the CSS that styles the charts. -->
    <link rel="stylesheet" href={{asset('css/chartjs-visualizations.css')}}>
@endsection

@section('content')
    <div>
        <div class="row">
            <div class="col-lg-4">
                <div id="active-users-container"></div>
            </div>
        </div>

        <h2><?=$h1_title ?? 'Blog filter'?></h2>
        <div class="row">
            @include('analytics._date-range')

        </div>
        <div class="table-responsive">
            <table class="table table-bordered dataTable  " id="filter-table">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Path</th>
                    <th scope="col">Pageviews</th>
                    <th scope="col">Unique Pageviews</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>

    </div>



@endsection

@section('template_scripts')

    <!-- Step 2: Load the library. -->
    <script>
        (function(w,d,s,g,js,fs){
            g=w.gapi||(w.gapi={});g.analytics={q:[],ready:function(f){this.q.push(f);}};
            js=d.createElement(s);fs=d.getElementsByTagName(s)[0];
            js.src='https://apis.google.com/js/platform.js';
            fs.parentNode.insertBefore(js,fs);js.onload=function(){g.load('analytics');};
        }(window,document,'script'));
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>


    <!-- Include the ViewSelector2 component script. -->
    <script src={{asset('js/view-selector2.js')}}></script>

    <!-- Include the ActiveUsers component script. -->
    <script src="{{asset('js/active-users.js')}}"></script>
    <!-- Include the DateRangeSelector component script. -->
    <script src={{asset('js/date-range-selector.js')}}></script>
    <script>

        $(document).on('click','.url a', function (e) {
            e.preventDefault();
            var query = $(this).attr('href');
            window.location.href = '<?=$current_route?>/filter?query='+query;
        });


        gapi.analytics.ready(function() {




            /**
             * Authorize the user immediately if the user has already granted access.
             * If no access has been created, render an authorize button inside the
             * element with the ID "embed-api-auth-container".
             */
            gapi.analytics.auth.authorize({
                'serverAuth': {
                    'access_token': '{{ $token }}'
                }
            });
            var view_id = 'ga:<?=$id?>';

            /**
             * Create a new ActiveUsers instance to be rendered inside of an
             * element with the id "active-users-container" and poll for changes every
             * five seconds.
             */
            var activeUsers = new gapi.analytics.ext.ActiveUsers({
                container: 'active-users-container',
                pollingInterval: 5
            });


            /**
             * Add CSS animation to visually show the when users come and go.
             */
            activeUsers.once('success', function() {
                var element = this.container.firstChild;
                var timeout;

                this.on('change', function(data) {
                    var element = this.container.firstChild;
                    var animationClass = data.delta > 0 ? 'is-increasing' : 'is-decreasing';
                    element.className += (' ' + animationClass);

                    clearTimeout(timeout);
                    timeout = setTimeout(function() {
                        element.className =
                            element.className.replace(/ is-(increasing|decreasing)/g, '');
                    }, 3000);
                });
            });
            activeUsers.set({ids: view_id}).execute();

            var start_date1 = (getParameterByName('start-date1')) ? getParameterByName('start-date1') : '7daysAgo';
            var end_date1 = (getParameterByName('end-date1')) ? getParameterByName('end-date1') : 'yesterday';
            var start_date2 = (getParameterByName('start-date2')) ? getParameterByName('start-date2') : '14daysAgo';
            var end_date2= (getParameterByName('end-date2')) ? getParameterByName('end-date2') : '8daysAgo';
            if(getParameterByName('compare') == 'true'){
                $('#compare').trigger('click');
                $('#compare').trigger('change');
                $('#compare').prop('checked', true);
                $('#date-range-selector-chanel-2-container-compare').parent().show();
                $('.date1').show();
                $('.date2').show();
            }

            var  dateRangeCompareChanel1 = {
                'start-date': start_date1,
                'end-date': end_date1
            };
            var  dateRangeCompareChanel2 = {
                'start-date': start_date2,
                'end-date': end_date2
            };
            var dateRangeSelectorCompareChanel1 = new gapi.analytics.ext.DateRangeSelector({
                container: 'date-range-selector-chanel-1-container-compare'
            })
                .set(dateRangeCompareChanel1)
                .execute();


            var dateRangeSelectorCompareChanel2 = new gapi.analytics.ext.DateRangeSelector({
                container: 'date-range-selector-chanel-2-container-compare'
            })
                .set(dateRangeCompareChanel2)
                .execute();

            dateRangeSelectorCompareChanel1.on('change', function(data) {
                var date_start1 = new Date(data["start-date"]); dateRangeCompareChanel1 = data;
                dateRangeSelectorCompareChanel1.setValues(); dateRangeSelectorCompareChanel1.setMinMax();

                if($('#compare')[0].checked !== true){
                    urlHistory('start-date1',data["start-date"]);
                    urlHistory('end-date1',data["end-date"]);
                    var date_start = new Date(data["start-date"]);
                    var date_end = new Date(data["end-date"]);
                    var one_day=1000*60*60*24;
                    var date_diff = Math.ceil((date_end.getTime()-date_start.getTime())/(one_day));
                    var date_end2 = date_start.setDate(date_start.getDate()-1);
                    var date_start2 = date_start.setDate(date_start.getDate()-date_diff);

                    dateRangeCompareChanel2["start-date"] =  formatDate(date_start2);
                    dateRangeCompareChanel2["end-date"] = formatDate(date_end2) ;

                    dateRangeSelectorCompareChanel2.set(dateRangeCompareChanel2).execute();
                }

                renderUrldata();

            });



            dateRangeSelectorCompareChanel2.on('change', function(data) {

                dateRangeCompareChanel2 = data;
                dateRangeSelectorCompareChanel2.setValues(); dateRangeSelectorCompareChanel2.setMinMax();
                urlHistory('start-date2',data["start-date"]);
                urlHistory('end-date2',data["end-date"]);
                renderTable();
            });
            /**
             * Draw the a chart.js line chart with data from the specified view that
             * overlays session data for the current week over session data for the
             * previous week.
             */

            $(document).on('change','#compare', function () {
                if($(this)[0].checked == true){
                    urlHistory('compare', true);
                    $('#date-range-selector-chanel-2-container-compare').parent().show();
                    $('.date1').show();
                    $('.date2').show();
                    renderUrldata();
                }else{

                    urlHistory('compare', false);
                    $('#date-range-selector-chanel-2-container-compare').parent().hide();
                    $('.date1').hide();
                    $('.date2').hide();
                    renderUrldata();
                }

            });

            renderUrldata();

            function renderUrldata() {

                // Adjust `now` to experiment with different days, for testing only...
                var now = moment(); // .subtract(3, 'day');
                var first_query = query({
                    'ids': view_id,
                    'dimensions': 'ga:pagePath',
                    'metrics': 'ga:pageviews,ga:uniquePageviews',
                    'filters' : '<?=$filter?>',
                    'sort': '-ga:pageviews',
                    'start-date': dateRangeCompareChanel1["start-date"],
                    'end-date': dateRangeCompareChanel1["end-date"]
                });


                if($('#compare')[0].checked == true){
                    var second_query = query({
                        'ids': view_id,
                        'dimensions': 'ga:pagePath',
                        'metrics': 'ga:pageviews,ga:uniquePageviews',
                        'filters' : '<?=$filter?>',
                        'start-date': dateRangeCompareChanel2["start-date"],
                        'end-date': dateRangeCompareChanel2["end-date"]
                    });
                    Promise.all([first_query,second_query]).then(function(results) {
                        var data1 = results[0].rows;
                        var data2 = results[1].rows;
                        var tbody = $('#filter-table').find('tbody');
                        tbody.html('');
                        var total_dif1 = (results[0].totalsForAllResults['ga:pageviews']-results[1].totalsForAllResults['ga:pageviews'])/(results[1].totalsForAllResults['ga:pageviews']/100);
                        var total_dif2 = (results[0].totalsForAllResults['ga:uniquePageviews']-results[1].totalsForAllResults['ga:uniquePageviews'])/(results[1].totalsForAllResults['ga:uniquePageviews']/100);

                        tbody.prepend("<tr>" +
                            "<td></td><td>Total "+results[1].query['start-date']+"-"+results[1].query['end-date']+"</td>" +
                            "<td>"+results[1].totalsForAllResults['ga:pageviews']+"</td>" +
                            "<td>"+results[1].totalsForAllResults['ga:uniquePageviews']+"</td>" +
                            "</tr>");
                        tbody.prepend("<tr>" +
                            "<td></td><td>Total "+results[0].query['start-date']+"-"+results[0].query['end-date']+"</td>" +
                            "<td>"+results[0].totalsForAllResults['ga:pageviews']+"</td>" +
                            "<td>"+results[0].totalsForAllResults['ga:uniquePageviews']+"</td>" +
                            "</tr>");

                        tbody.prepend("<tr>" +
                            "<td></td><td>Gain</td>" +
                            "<td>"+total_dif1.toFixed(2)+"%"+"</td>" +
                            "<td>"+total_dif2.toFixed(2)+"%"+"</td>" +
                            "</tr>");
                        if(data1 && data1 !== undefined){
                             data1.forEach(function (item,key) {
                                var tr = "<tr data-key='"+(key+1)+"'>" +
                                    "<td>"+(key+1)+"</td>" +
                                    "<td>"+item[0]+"</td>" +
                                    "<td class='pageviews'></td>" +
                                    "<td class='pageviews-uniq'></td>" +
                                    "</tr>";
                                var tr1 = "<tr class='date1 child-row' data-addkey='"+(key+1)+"'>" +
                                    "<td></td>" +
                                    "<td class='date-range'>"+results[0].query['start-date']+"-"+results[0].query['end-date']+"</td>" +
                                    "<td class='pageviews'>"+item[1]+"</td>" +
                                    "<td class='pageviews-uniq'>"+item[2]+"</td>" +
                                    "</tr>";
                                tr = tr+tr1;
                                tbody.append(tr);
                            });
                            data2.forEach(function (item,key) {
                                var tr = $('#filter-table').find('*[data-key="'+(key+1)+'"]');
                                var tr1 = $('#filter-table').find('.date1*[data-addkey="'+(key+1)+'"]');
                                var tr2 = "<tr class='date2 child-row' data-addkey='"+(key+1)+"'>" +
                                    "<td></td>" +
                                    "<td class='date-range'>"+results[1].query['start-date']+"-"+results[1].query['end-date']+"</td>" +
                                    "<td class='pageviews'>"+item[1]+"</td>" +
                                    "<td class='pageviews-uniq'>"+item[2]+"</td>" +
                                    "</tr>";
                                if(tr1.length>0){
                                    tr1.after(tr2);
                                }else{
                                    var tr = "<tr data-key='"+(key+1)+"'>" +
                                        "<td>"+(key+1)+"</td>" +
                                        "<td>"+item[0]+"</td>" +
                                        "<td class='pageviews'></td>" +
                                        "<td class='pageviews-uniq'></td>" +
                                        "</tr>";
                                    var tr1 = "<tr class='date1 child-row' data-addkey='"+(key+1)+"'>" +
                                        "<td></td>" +
                                        "<td class='date-range'>"+results[1].query['start-date']+"-"+results[1].query['end-date']+"</td>" +
                                        "<td class='pageviews'>"+item[1]+"</td>" +
                                        "<td class='pageviews-uniq'>"+item[2]+"</td>" +
                                        "</tr>";
                                    tr = tr+tr1;
                                    tbody.append(tr);
                                }
                                var pv1 = data1[key] ? data1[key][1] : 0;
                                var pv2 = item[1];
                                var pu1 = data1[key] ? data1[key][2] : 0;
                                var pu2 = item[2];
                                var change_views =(pv1 - pv2)/(pv2/100);
                                var change_uniq = (pu1 - pu2)/(pu2/100);
                                var pageviews_td = tr ? $(tr).find('.pageviews') : 0;
                                var pageviewsuniq_td = tr ? $(tr).find('.pageviews-uniq') : 0;
                                pageviews_td.text(change_views.toFixed(2)+"%");
                                pageviewsuniq_td.text(change_uniq.toFixed(2)+"%");

                            });
                        }

                    }).then(function(results) {
                        sortTable();
                    });
                }else{
                    Promise.all([first_query]).then(function(results) {
                        var data1 = results[0].rows;
                        var tbody = $('#filter-table').find('tbody');
                        tbody.text('');
                        if(data1 && data1 !== undefined){
                            var total_pageviews = results[0].totalsForAllResults['ga:pageviews'];
                            var total_uniquepageviews = results[0].totalsForAllResults['ga:uniquePageviews'];
                            data1.forEach(function (item,key) {
                                var tr = "<tr><td>"+(key+1)+"</td><td>"+item[0]+"</td><td>"+item[1]+"</td><td>"+item[2]+"</td></tr>";
                                tbody.append(tr);
                            });
                            tbody.prepend('<tr><td></td><td>Total</td><td>'+total_pageviews+'</td><td>'+total_uniquepageviews+'</td></tr>');
                        }

                    }).then(function(results) {
                        sortTable();
                    });
                }

            }

        });


    </script>

@endsection
