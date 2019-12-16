<?php
$current_route = request()->route()->getPrefix();
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
        <div>
            <div class="col-lg-12">
                <div id="active-users-container"></div>
            </div>
        </div>
        <h2>Users</h2>
        <div >
            <div class="col-lg-12" >
                <div id="date-range-selector-4-container"></div>
                <div class="col-md-6">
                    <h4>User type</h4>
                    <div id="chart-4-container" style="width:100%;"></div>
                </div>
                <div class="col-md-6">
                    <h4>User countries</h4>
                    <div id="chart-5-container" style="width:100%;"></div>
                </div>
            </div>

            <hr>
            <div class="col-lg-12" >
                <div class="col-md-6">
                    <h4>User gender</h4>
                    <div id="chart-6-container" style="width:100%;"></div>
                </div>
                <div class="col-md-6">
                    <h4>User ages</h4>
                    <div id="chart-7-container" style="width:100%;"></div>
                </div>
            </div>
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

            var  dateRange = {
                'start-date': '30daysAgo',
                'end-date': 'yesterday'
            };

            var commonConfig4 = {
                query: {
                    metrics: 'ga:users',
                    dimensions: 'ga:userType'
                },
                chart: {
                    type: 'COLUMN',
                    options: {
                        width: '100%',

                    }
                }
            };
            var commonConfig5 = {
                query: {
                    metrics: 'ga:users',
                    dimensions: 'ga:country'
                },
                chart: {
                    type: 'PIE',
                    options: {
                        width: '100%',

                    }
                }
            };

            var commonConfig6 = {
                query: {
                    metrics: 'ga:users',
                    dimensions: 'ga:userGender'
                },
                chart: {
                    type: 'COLUMN',
                    options: {
                        width: '100%',

                    }
                }
            };


            var commonConfig7 = {
                query: {
                    metrics: 'ga:users',
                    dimensions: 'ga:userAgeBracket'
                },
                chart: {
                    type: 'COLUMN',
                    options: {
                        width: '100%',

                    }
                }
            };


            var dateRangeSelector4 = new gapi.analytics.ext.DateRangeSelector({
                container: 'date-range-selector-4-container'
            })
                .set(dateRange)
                .execute();
            var dataChart4 = new gapi.analytics.googleCharts.DataChart(commonConfig4)
                .set({query: dateRange})
                .set({chart: {container: 'chart-4-container'}});


            var dataChart5 = new gapi.analytics.googleCharts.DataChart(commonConfig5)
                .set({query: dateRange})
                .set({chart: {container: 'chart-5-container'}});



            var dataChart6 = new gapi.analytics.googleCharts.DataChart(commonConfig6)
                .set({query: dateRange})
                .set({chart: {container: 'chart-6-container'}});



            var dataChart7 = new gapi.analytics.googleCharts.DataChart(commonConfig7)
                .set({query: dateRange})
                .set({chart: {container: 'chart-7-container'}});


            dateRangeSelector4.on('change', function(data) {
                dataChart4.set({query: data}).execute();
                dataChart5.set({query: data}).execute();
                dataChart6.set({query: data}).execute();
                dataChart7.set({query: data}).execute();

            });

            dataChart4.set({query: {ids: view_id}}).execute();
            dataChart5.set({query: {ids: view_id}}).execute();
            dataChart6.set({query: {ids: view_id}}).execute();
            dataChart7.set({query: {ids: view_id}}).execute();

        });


    </script>

@endsection
