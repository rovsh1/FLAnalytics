<?php
if(!empty($query)){
    $filter = '\;ga:pagePath=~^'.$query.'..*';
    $filter_query = request()->route()->getPrefix().'/channels/'.$query;
}else{
    if(strpos($current_route,'www.') !==false){
        $filter = '\;ga:pagePath=~^'.str_replace('/','',$current_route).'..*\,ga:pagePath=~^'.str_replace('www.','',str_replace('/','',$current_route)).'..*';
    }else{
        $filter = '\;ga:pagePath=~^'.str_replace('/','',$current_route).'..*';
    }
//    $filter = '';
    $filter_query = request()->route()->getPrefix().'/channels';
}
$filter = '';
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

        <h2><?=$h1_title ?? 'Channels'?></h2>
        <div >
            @include('analytics._date-range')

        </div>
        <div class="table-responsive">
            <table class="table table-bordered dataTable  " id="filter-table">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Chanels</th>
                    <th scope="col">Users</th>
                    <th scope="col">New Users</th>
                    <th scope="col">Sessions</th>
                </tr>
                </thead>
                <tbody>
                <tr class="main">
                    <td>1</td>
                    <td data-dimension="source">Display</td>
                    <td class="users"></td>
                    <td class="uniq"></td>
                    <td class="sessions"></td>
                </tr>
                <tr class="date1 child-row" data-addkey="1" style="display: none;">
                    <td></td>
                    <td class="date-range"></td>
                    <td class="users"></td>
                    <td class="uniq"></td>
                    <td class="sessions"></td>
                </tr>
                <tr class="date2 child-row" data-addkey="1" style="display: none;">
                    <td></td>
                    <td class="date-range"></td>
                    <td class="users"></td>
                    <td class="uniq"></td>
                    <td class="sessions"></td>
                </tr>
                <tr class="main">
                    <td>2</td>
                    <td data-dimension="source">Paid</td>
                    <td class="users"></td>
                    <td class="uniq"></td>
                    <td class="sessions"></td>
                </tr>
                <tr class="date1 child-row" data-addkey="2" style="display: none;">
                    <td></td>
                    <td class="date-range"></td>
                    <td class="users"></td>
                    <td class="uniq"></td>
                    <td class="sessions"></td>
                </tr>
                <tr class="date2 child-row" data-addkey="2" style="display: none;">
                    <td></td>
                    <td class="date-range"></td>
                    <td class="users"></td>
                    <td class="uniq"></td>
                    <td class="sessions"></td>
                </tr>
                <tr class="main">
                    <td>3</td>
                    <td data-dimension="keyword">Organic</td>
                    <td class="users"></td>
                    <td class="uniq"></td>
                    <td class="sessions"></td>
                </tr>
                <tr class="date1 child-row" data-addkey="3" style="display: none;">
                    <td></td>
                    <td class="date-range"></td>
                    <td class="users"></td>
                    <td class="uniq"></td>
                    <td class="sessions"></td>
                </tr>
                <tr class="date2 child-row" data-addkey="3" style="display: none;">
                    <td></td>
                    <td class="date-range"></td>
                    <td class="users"></td>
                    <td class="uniq"></td>
                    <td class="sessions"></td>
                </tr>
                <tr class="main">
                    <td>4</td>
                    <td data-dimension="pagePath">Direct</td>
                    <td class="users"></td>
                    <td class="uniq"></td>
                    <td class="sessions"></td>
                </tr>
                <tr class="date1 child-row" data-addkey="4" style="display: none;">
                    <td></td>
                    <td class="date-range"></td>
                    <td class="users"></td>
                    <td class="uniq"></td>
                    <td class="sessions"></td>
                </tr>
                <tr class="date2 child-row" data-addkey="4" style="display: none;">
                    <td></td>
                    <td class="date-range"></td>
                    <td class="users"></td>
                    <td class="uniq"></td>
                    <td class="sessions"></td>
                </tr>
                <tr class="main">
                    <td>5</td>
                    <td data-dimension="socialNetwork">Social</td>
                    <td class="users"></td>
                    <td class="uniq"></td>
                    <td class="sessions"></td>
                </tr>
                <tr class="date1 child-row" data-addkey="5" style="display: none;">
                    <td></td>
                    <td class="date-range"></td>
                    <td class="users"></td>
                    <td class="uniq"></td>
                    <td class="sessions"></td>
                </tr>
                <tr class="date2 child-row" data-addkey="5" style="display: none;">
                    <td></td>
                    <td class="date-range"></td>
                    <td class="users"></td>
                    <td class="uniq"></td>
                    <td class="sessions"></td>
                </tr>
                <tr class="main">
                    <td>6</td>
                    <td  data-dimension="source">Other</td>
                    <td class="users"></td>
                    <td class="uniq"></td>
                    <td class="sessions"></td>
                </tr>
                <tr class="date1 child-row" data-addkey="6" style="display: none;">
                    <td></td>
                    <td class="date-range"></td>
                    <td class="users"></td>
                    <td class="uniq"></td>
                    <td class="sessions"></td>
                </tr>
                <tr class="date2 child-row" data-addkey="6" style="display: none;">
                    <td></td>
                    <td class="date-range"></td>
                    <td class="users"></td>
                    <td class="uniq"></td>
                    <td class="sessions"></td>
                </tr>
                <tr class="main">
                    <td>7</td>
                    <td data-dimension="source">Referal</td>
                    <td class="users"></td>
                    <td class="uniq"></td>
                    <td class="sessions"></td>
                </tr>
                <tr class="date1 child-row" data-addkey="7" style="display: none;">
                    <td></td>
                    <td class="date-range"></td>
                    <td class="users"></td>
                    <td class="uniq"></td>
                    <td class="sessions"></td>
                </tr>
                <tr class="date2 child-row" data-addkey="7" style="display: none;">
                    <td></td>
                    <td class="date-range"></td>
                    <td class="users"></td>
                    <td class="uniq"></td>
                    <td class="sessions"></td>
                </tr>
                <tr class="main">
                    <td>8</td>
                    <td data-dimension="pagePath">Email</td>
                    <td class="users"></td>
                    <td class="uniq"></td>
                    <td class="sessions"></td>
                </tr>
                <tr class="date1 child-row" data-addkey="8" style="display: none;">
                    <td></td>
                    <td class="date-range"></td>
                    <td class="users"></td>
                    <td class="uniq"></td>
                    <td class="sessions"></td>
                </tr>
                <tr class="date2 child-row" data-addkey="8" style="display: none;">
                    <td></td>
                    <td class="date-range"></td>
                    <td class="users"></td>
                    <td class="uniq"></td>
                    <td class="sessions"></td>
                </tr>
                </tbody>
            </table>
        </div>
                 <hr>



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



        $(document).on('click','.main a', function (e) {
                    e.preventDefault();
                    var query = $(this).data('filter');
                    var dimension = $(this).parent().data('dimension');

            var url_go;
            if((window.location.search)){
                url_go = '<?=$filter_query?>'+window.location.search+'&query='+query+'&dimension='+dimension;
            }else{
                url_go = '<?=$filter_query?>/?query='+query+'&dimension='+dimension;
            }
            window.location.href = url_go;
                });

        var time = 500;
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

                urlHistory('start-date1',data["start-date"]);
                urlHistory('end-date1',data["end-date"]);
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
                dateRangeSelectorCompareChanel2.setValues(); dateRangeSelectorCompareChanel2.setMinMax();
                urlHistory('start-date2',data["start-date"]);
                urlHistory('end-date2',data["end-date"]);
                renderTable();
            });



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

            /**
             * Draw the a chart.js line chart with data from the specified view that
             * overlays session data for the current week over session data for the
             * previous week.
             */

            renderUrldata();

            function renderUrldata() {
                var regex = '^(cpc|ppc|paidsearch)$';
                var filter = 'ga:medium=~^(display|cpm|banner)$\,ga:adDistributionNetwork==Content<?=$filter?>';
                var filter1 = 'ga:medium=~'+regex+'\;ga:adDistributionNetwork!=Content<?=$filter?>';
                var filter2 = 'ga:medium==organic<?=$filter?>';
                var filter3 = 'ga:source==(direct)<?=$filter?>';

                var query1 = query({
                    'ids': view_id,
                    'metrics': 'ga:users,ga:newUsers,ga:sessions',
                    'filters' : filter,
                    'segment': '<?=$segments?>',
                    'start-date': dateRangeCompareChanel1["start-date"],
                    'end-date': dateRangeCompareChanel1["end-date"]
                });

                var first_query = query({
                    'ids': view_id,
                    'metrics': 'ga:users,ga:newUsers,ga:sessions',
                    'filters' : filter1,
                    'segment': '<?=$segments?>',
                    'start-date': dateRangeCompareChanel1["start-date"],
                    'end-date': dateRangeCompareChanel1["end-date"]
                });
                var second_query = query({
                    'ids': view_id,
                    'metrics': 'ga:users,ga:newUsers,ga:sessions',
                    'filters' : filter2,
                    'segment': '<?=$segments?>',
                    'start-date': dateRangeCompareChanel1["start-date"],
                    'end-date': dateRangeCompareChanel1["end-date"]
                });
                var third_query = query({
                    'ids': view_id,
                    'metrics': 'ga:users,ga:newUsers,ga:sessions',
                    'filters' : filter3,
                    'segment': '<?=$segments?>',
                    'start-date': dateRangeCompareChanel1["start-date"],
                    'end-date': dateRangeCompareChanel1["end-date"]
                });
                var social_query = query({
                    'ids': view_id,
                    'metrics': 'ga:users,ga:newUsers,ga:sessions',
                    'filters' : 'ga:medium=~^(social|social-network|social-media|sm|social network|social media)$\,ga:hasSocialSourceReferral==Yes<?=$filter?>',
                    'segment': '<?=$segments?>',
                    'start-date': dateRangeCompareChanel1["start-date"],
                    'end-date': dateRangeCompareChanel1["end-date"]
                });
                var other_query = query({
                    'ids': view_id,
                    'metrics': 'ga:users,ga:newUsers,ga:sessions',
                    'filters' : 'ga:medium=~^(cpv|cpa|cpp|content-text|post|link|email-top-banner)$<?=$filter?>',
                    'segment': '<?=$segments?>',
                    'start-date': dateRangeCompareChanel1["start-date"],
                    'end-date': dateRangeCompareChanel1["end-date"]
                });
                var referal_query = query({
                    'ids': view_id,
                    'metrics': 'ga:users,ga:newUsers,ga:sessions',
                    'filters' : 'ga:medium==referral<?=$filter?>',
                    'segment': '<?=$segments?>',
                    'start-date': dateRangeCompareChanel1["start-date"],
                    'end-date': dateRangeCompareChanel1["end-date"]
                });
                var email_query = query({
                    'ids': view_id,
                    'metrics': 'ga:users,ga:newUsers,ga:sessions',
                    'filters' : 'ga:medium==email<?=$filter?>',
                    'segment': '<?=$segments?>',
                    'start-date': dateRangeCompareChanel1["start-date"],
                    'end-date': dateRangeCompareChanel1["end-date"]
                });

                if($('#compare')[0].checked == true){
                    var query12 = queryTimeout({
                        'ids': view_id,
                        'metrics': 'ga:users,ga:newUsers,ga:sessions',
                        'filters' : filter,
                        'segment': '<?=$segments?>',
                        'start-date': dateRangeCompareChanel2["start-date"],
                        'end-date': dateRangeCompareChanel2["end-date"]
                    });
                    var first_query2 = queryTimeout({
                        'ids': view_id,
                        'metrics': 'ga:users,ga:newUsers,ga:sessions',
                        'filters' : filter1,
                        'segment': '<?=$segments?>',
                        'start-date': dateRangeCompareChanel2["start-date"],
                        'end-date': dateRangeCompareChanel2["end-date"]
                    });
                    var second_query2 = queryTimeout({
                        'ids': view_id,
                        'metrics': 'ga:users,ga:newUsers,ga:sessions',
                        'filters' : filter2,
                        'segment': '<?=$segments?>',
                        'start-date': dateRangeCompareChanel2["start-date"],
                        'end-date': dateRangeCompareChanel2["end-date"]
                    });

                    var third_query2 = queryTimeout({
                        'ids': view_id,
                        'metrics': 'ga:users,ga:newUsers,ga:sessions',
                        'filters' : filter3,
                        'segment': '<?=$segments?>',
                        'start-date': dateRangeCompareChanel2["start-date"],
                        'end-date': dateRangeCompareChanel2["end-date"]
                    });
                    var social_query2 = queryTimeout({
                        'ids': view_id,
                        'metrics': 'ga:users,ga:newUsers,ga:sessions',
                        'filters' : 'ga:medium=~^(social|social-network|social-media|sm|social network|social media)$\,ga:hasSocialSourceReferral==Yes<?=$filter?>',
                        'segment': '<?=$segments?>',
                        'start-date': dateRangeCompareChanel2["start-date"],
                        'end-date': dateRangeCompareChanel2["end-date"]
                    });
                    var other_query2 = queryTimeout({
                        'ids': view_id,
                        'metrics': 'ga:users,ga:newUsers,ga:sessions',
                        'filters' : 'ga:medium=~^(cpv|cpa|cpp|content-text|post|link|email-top-banner)$<?=$filter?>',
                        'segment': '<?=$segments?>',
                        'start-date': dateRangeCompareChanel2["start-date"],
                        'end-date': dateRangeCompareChanel2["end-date"]
                    });
                    var referal_query2 = queryTimeout({
                        'ids': view_id,
                        'metrics': 'ga:users,ga:newUsers,ga:sessions',
                        'filters' : 'ga:medium==referral;ga:source!@facebook<?=$filter?>',
                        'segment': '<?=$segments?>',
                        'start-date': dateRangeCompareChanel2["start-date"],
                        'end-date': dateRangeCompareChanel2["end-date"]
                    });
                    var email_query2 = queryTimeout({
                        'ids': view_id,
                        'metrics': 'ga:users,ga:newUsers,ga:sessions',
                        'filters' : 'ga:medium==email<?=$filter?>',
                        'segment': '<?=$segments?>',
                        'start-date': dateRangeCompareChanel2["start-date"],
                        'end-date': dateRangeCompareChanel2["end-date"]
                    });

                    Promise.all([query1,first_query,second_query,third_query,
                        social_query,other_query,referal_query,email_query,
                        query12,first_query2,second_query2,third_query2,
                        social_query2,other_query2,referal_query2,email_query2]).then(function(results) {
                        if(results){
                            var tbody = $('#filter-table').find('tbody tr.main');
                            var offset = results.length/2;
                            tbody.each(function (key,item) {
                                var tr1 = $('#filter-table').find('.date1*[data-addkey="'+(key+1)+'"]');
                                var tr2 = $('#filter-table').find('.date2*[data-addkey="'+(key+1)+'"]');
                                var query1 = results[key].query['start-date']+"-"+results[key].query['end-date'];

                                var query2 = results[key+offset].query['start-date']+"-"+results[key+offset].query['end-date'];
                                tr1.find('.date-range').text(query1);
                                tr2.find('.date-range').text(query2);

                                tr1.find('.users').text(results[key].rows[0][0]);
                                tr1.find('.uniq').text(results[key].rows[0][1]);
                                tr1.find('.sessions').text(results[key].rows[0][2]);

                                tr2.find('.users').text(results[key+offset].rows[0][0]);
                                tr2.find('.uniq').text(results[key+offset].rows[0][1]);
                                tr2.find('.sessions').text(results[key+offset].rows[0][2]);
                                var change_users =(results[key].rows[0][0] - results[key+offset].rows[0][0])/(results[key+offset].rows[0][0]/100);
                                var change_uniq = (results[key].rows[0][1] - results[key+offset].rows[0][1])/(results[key+offset].rows[0][1]/100);
                                var change_sessions = (results[key].rows[0][2] - results[key+offset].rows[0][2])/(results[key+offset].rows[0][2]/100);


                                $(this).find('.users').text(change_users.toFixed(2)+"%");
                                $(this).find('.uniq').text(change_uniq.toFixed(2)+"%");
                                $(this).find('.sessions').text(change_sessions.toFixed(2)+"%");
                            })
                        }
                    }).then(function(results) {
                        sortTable();
                    });


                }else{
                    Promise.all([query1,first_query,second_query,third_query,
                        social_query,other_query,referal_query,email_query]).then(function(results) {

                        if(results){
                            var tbody = $('#filter-table').find('tbody tr.main');
                            tbody.each(function (key,item) {
                                var target_column = $(this).find('td:nth-child(2)');
                                var text = $(this).find('td:nth-child(2)').text();
                                target_column.html('<a href="" data-filter="'+results[key].query.filters+'">'+text+'</a>');
                                $(this).find('.users').text(results[key].rows[0][0]);
                                $(this).find('.uniq').text(results[key].rows[0][1]);
                                $(this).find('.sessions').text(results[key].rows[0][2]);
                            })
                        }

                    }).then(function(results) {
                        sortTable();
                    });
                }
            }
        });


    </script>

@endsection
