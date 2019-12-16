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
        <div class="row">
            <div class="col-lg-4">
                <div id="active-users-container"></div>
            </div>
        </div>
        <h2>Bounces</h2>
        <div>
            <div>
                <div id="date-range-bounces-1"></div>
                <div id="bounces-1-container" style="width:100%"></div>
                <hr>
                <div id="bounces-2-container" style="width:100%"></div>
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

            var dateRangeBounces = new gapi.analytics.ext.DateRangeSelector({
                container: 'date-range-bounces-1'
            })
                .set(dateRange)
                .execute();

            var bounceConfig = {
                query: {
                    metrics: ['ga:bounces,ga:bounceRate,ga:sessions'],
                    dimensions: 'ga:date'
                },
                chart: {
                    type: 'LINE',
                    options: {
                        width: '100%',
                    }
                }
            };

            var bounceConfig2 = {
                query: {
                    metrics: ['ga:bounces,ga:bounceRate,ga:sessions'],
                    dimensions: 'ga:date'
                },
                chart: {
                    type: 'COLUMN',
                    options: {
                        width: '100%',
                    }
                }
            };

            var dataChartBounces1 = new gapi.analytics.googleCharts.DataChart(bounceConfig)
                .set({query: dateRange})
                .set({chart: {container: 'bounces-1-container'}});

            var dataChartBounces2 = new gapi.analytics.googleCharts.DataChart(bounceConfig2)
                .set({query: dateRange})
                .set({chart: {container: 'bounces-2-container'}});


            dateRangeBounces.on('change', function(data) {
                dataChartBounces1.set({query: data}).execute();
                // dataChartBounces2.set({query: data}).execute();
            });


                dataChartBounces1.set({query: {ids: view_id}}).execute();
                // dataChartBounces2.set({query: {ids: view_id}}).execute();


        });


    </script>

@endsection
