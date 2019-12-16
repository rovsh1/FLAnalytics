<?php
$current_route = request()->route()->getPrefix();
if(!empty($query)){
    $filter = 'ga:pagePath=~^'.$query.'..*';
}else{
//    $filter = 'ga:pagePath=~^fixinglist..*';
//    $filter = 'ga:pagePath!~^'.$prefix[0].'..*'.'\;ga:pagePath!~^'.$prefix[1].'..*';


    if(strpos($site,'www.') !==false){

        $filter = 'ga:pagePath=~^'.str_replace('/','',$site).'..*\,ga:pagePath=~^'.str_replace('www.','',str_replace('/','',$site)).'..*';
            }else{
        $filter = 'ga:pagePath=~^'.str_replace('/','',$site).'..*';
    }


}
$goal_commpletions = 'ga:goal7Completions';
if(strpos($site,'ustabor.uz') !==false){
    $goal_commpletions = 'ga:goal9Completions';
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

        <h2><?=$h1_title ?? 'Device overview'?></h2>
        <div class="row">
            @include('analytics._date-range')

        </div>
        <div class="table-responsive">
            <table class="table table-bordered dataTable  " id="filter-table">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Device</th>
                    <th scope="col">Users</th>
                    <th scope="col">New users</th>
                    <th scope="col">Sessions</th>
                    <th scope="col">Bounce Rate</th>
                    <th scope="col">Page/Sessions</th>
                    <th scope="col">Avg. Session Duration</th>
                    <th scope="col" >Нажатие на кнопку Контакты (у проекта и у мастера на странице)</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
        <hr>
        <div id="table_div"></div>



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


            $(document).on('change','#compare', function () {
                if($(this)[0].checked == true){
                    $('#date-range-selector-chanel-2-container-compare').parent().show();
                    $('.date1').show();
                    $('.date2').show();
                    renderUrldata();
                }else{
                    $('#date-range-selector-chanel-2-container-compare').parent().hide();
                    $('.date1').hide();
                    $('.date2').hide();
                    renderUrldata();
                }

            });

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

            var  dateRangeCompareChanel1 = {
                'start-date': '7daysAgo',
                'end-date': 'yesterday'
            };
            var dateRangeSelectorCompareChanel1 = new gapi.analytics.ext.DateRangeSelector({
                container: 'date-range-selector-chanel-1-container-compare'
            })
                .set(dateRangeCompareChanel1)
                .execute();

            var  dateRangeCompareChanel2 = {
                'start-date': '14daysAgo',
                'end-date': '8daysAgo'
            };
            var dateRangeSelectorCompareChanel2 = new gapi.analytics.ext.DateRangeSelector({
                container: 'date-range-selector-chanel-2-container-compare'
            })
                .set(dateRangeCompareChanel2)
                .execute();

            dateRangeSelectorCompareChanel1.on('change', function(data) {
                var date_start1 = new Date(data["start-date"]); dateRangeCompareChanel1 = data;
                dateRangeSelectorCompareChanel1.setValues(); dateRangeSelectorCompareChanel1.setMinMax();

                if($('#compare')[0].checked !== true){
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
                renderUrldata();
            });
            /**
             * Draw the a chart.js line chart with data from the specified view that
             * overlays session data for the current week over session data for the
             * previous week.
             */

            renderUrldata();

            function renderUrldata() {

                // Adjust `now` to experiment with different days, for testing only...
                var now = moment(); // .subtract(3, 'day');
                var goal_completions = '<?=$goal_commpletions?>';
                var metrics = 'ga:users,ga:newUsers,ga:sessions,ga:bounceRate,ga:pageviewsPerSession,ga:avgSessionDuration,'+goal_completions;
                var first_query = query({
                    'ids': view_id,
                    'dimensions': 'ga:deviceCategory',
                    'metrics': metrics,
                    'sort': '-ga:users',
                 //   'filters' : '<?=$filter?>',
                    'segment': '<?=$segments?>',
                    'start-date': dateRangeCompareChanel1["start-date"],
                    'end-date': dateRangeCompareChanel1["end-date"]
                });


                if($('#compare')[0].checked == true){
                    var second_query = query({
                        'ids': view_id,
                        'dimensions': 'ga:deviceCategory',
                        'metrics': metrics,
                        'sort': '-ga:users',
                       // 'filters' : '<?=$filter?>',
                        'segment': '<?=$segments?>',
                        'start-date': dateRangeCompareChanel2["start-date"],
                        'end-date': dateRangeCompareChanel2["end-date"]
                    });
                    Promise.all([first_query,second_query]).then(function(results) {
                        var data1 = results[0].rows;
                        var data2 = results[1].rows;
                        var tbody = $('#filter-table').find('tbody');
                        tbody.html('');
                        if(data1 && data1 !== undefined){
                            data1.forEach(function (item,key) {
                                var tr = "<tr data-key='"+(key+1)+"'>" +
                                    "<td>"+(key+1)+"</td>" +
                                    "<td>"+item[0]+"</td>" +
                                    "<td class='pageviews'></td>" +
                                    "<td class='pageviews-uniq'></td>" +
                                    "<td class='sessions'></td>" +
                                    "<td class='bounce'></td>" +
                                    "<td class='pageSessions'></td>" +
                                    "<td class='avg'></td>" +
                                    "<td class='click11'></td>" +
                                    "</tr>";
                                var tr1 = "<tr class='date1 child-row' data-addkey='"+(key+1)+"'>" +
                                    "<td></td>" +
                                    "<td class='date-range'>"+results[0].query['start-date']+"-"+results[0].query['end-date']+"</td>" +
                                    "<td class='pageviews'>"+item[1]+"</td>" +
                                    "<td class='pageviews-uniq'>"+item[2]+"</td>" +
                                    "<td class='sessions'>"+item[3]+"</td>" +
                                    "<td class='bounce'>"+item[4]+"</td>" +
                                    "<td class='pageSessions'>"+item[5]+"</td>" +
                                    "<td class='avg'>"+item[6]+"</td>" +
                                    "<td class='click11 '>"+item[7]+"</td>" +
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
                                    "<td class='sessions'>"+item[3]+"</td>" +
                                    "<td class='bounce'>"+item[4]+"</td>" +
                                    "<td class='pageSessions'>"+item[5]+"</td>" +
                                    "<td class='avg'>"+item[6]+"</td>" +
                                    "<td class='click11'>"+item[7]+"</td>" +
                                    "</tr>";
                                tr1.after(tr2);
                                var pv1 = data1[key] ? data1[key][1] : 0;
                                var pv2 = item[1];
                                var pu1 = data1[key] ? data1[key][2] : 0;
                                var pu2 = item[2];
                                var change_views =(pv1 - pv2)/(pv2/100);
                                var change_uniq = (pu1 - pu2)/(pu2/100);
                                var pageviews_td = tr.find('.pageviews');
                                var pageviewsuniq_td = tr.find('.pageviews-uniq');
                                pageviews_td.text(change_views.toFixed(2)+"%");
                                pageviewsuniq_td.text(change_uniq.toFixed(2)+"%");

                            });
                        }

                    });
                }else{
                    Promise.all([first_query]).then(function(results) {
                        var data1 = results[0].rows;
                        var tbody = $('#filter-table').find('tbody');
                        tbody.text('');
                        if(data1 && data1 !== undefined){
                            var total_1 = 0;
                            var total_2 = 0;
                            var total_3 = 0;
                            var total_4 = 0;
                            var total_5 = 0;
                            var total_6 = 0;
                            var total_7 = 0;
                            var total_8 = 0;
                            data1.forEach(function (item,key) {
                                total_1+=parseInt(item[1]);
                                total_2+=parseInt(item[2]);
                                total_3+=parseInt(item[3]);
                                total_4+=parseInt(item[4]);
                                total_5+=parseInt(item[5]);
                                total_6+=parseInt(item[6]);
                                total_7+=parseInt(item[7]);
                                total_8+=parseInt(item[8]);
                                var tr = "<tr><td>"+(key+1)+"</td><td>"+item[0]+"</td><td>"+item[1]+"</td><td>"+item[2]+"</td>" +
                                    "<td>"+item[3]+"</td><td>"+parseFloat(item[4]).toFixed(2)+"</td><td>"+parseFloat(item[5]).toFixed(2)+
                                    "</td><td>"+parseFloat(item[6]).toFixed(2)+"</td>" +
                                    "<td>"+parseFloat(item[7]).toFixed(2)+"</td></tr>";
                                tbody.append(tr);
                            });
                            tbody.prepend('<tr><td></td><td>Total</td><td>'+total_1+'</td><td>'+total_2+'</td><td>'+total_3+'</td>' +
                                '<td>'+total_4+'</td><td>'+total_5+'</td><td>'+total_6+'</td>' +
                                '<td>'+total_7+'</td></tr>');
                        }

                    });
                }

            }

        });


    </script>

@endsection
