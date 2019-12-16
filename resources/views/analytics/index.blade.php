@extends('layouts.app')

@section('template_title')
  Showing Analytics
@endsection

@section('template_linked_css')

    <!-- Include the CSS that styles the charts. -->
    <link rel="stylesheet" href={{asset('css/chartjs-visualizations.css')}}>
@endsection

@section('content')
    <div class="container">
        <div id="active-users-container"></div>
        <div class="row">
            <div id="embed-api-auth-container"></div></div>
        <div class="row">
            <div id="chart-1-container"></div></div>
        <div class="row">
            <div id="chart-2-container"></div>
        </div>
        <div class="row">
            <div id="chart-3-container"></div>
        </div>
        <div class="row">
            <div id="view-selector-1-container"></div>
        </div>
        <div class="row">
            <div id="view-selector-2-container"></div>
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
    <!-- Include the ActiveUsers component script. -->
    <script src="{{asset('js/active-users.js')}}"></script>
    <!-- Include the ViewSelector2 component script. -->
    <script src={{asset('js/view-selector2.js')}}></script>

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


            /**
             * Create a ViewSelector for the first view to be rendered inside of an
             * element with the id "view-selector-1-container".
             */

            var viewSelector = new gapi.analytics.ext.ViewSelector2({
                container: 'view-selector-1-container',
            })
                .execute();


            var  dateRange3 = {
                'start-date': '30daysAgo',
                'end-date': 'yesterday'
            };
            var commonConfig = {
                query: {
                    metrics: ['ga:sessions,ga:users,ga:pageviews,ga:organicSearches,ga:exits'],
                    dimensions: 'ga:date'
                },
                chart: {
                    type: 'LINE',
                    options: {
                        width: '100%',
                        colors: ['black', 'blue', 'red', 'green', 'yellow', 'gray'],
                        'animation.duration':200,
                        animation:{
                            duration:1000,
                            startup:true
                        }
                    }
                }
            };

            var dataChart3 = new gapi.analytics.googleCharts.DataChart(commonConfig)
                .set({query: dateRange3})
                .set({chart: {container: 'chart-3-container'}});





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

            viewSelector.on('viewChange', function(data) {
                dataChart3.set({query: {ids: data.ids}}).execute();
                console.log(data);
                activeUsers.set(data).execute();
            });

        });
    </script>
@endsection