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
        <h2>Goals</h2>
        <div>
            <div class="col-lg-12">
                <div id="date-range-clicks-1"></div>

                <hr>
                <h4>
                    Number of people who clicked on the contact - Goal ID 9 / Goal Set 2 "Clicking on the Contacts button (from the project and the wizard on the page)" (Goal 9 Completions)</h4>
                <div id="clicks-7-container" style="width:100%"></div>

                <hr>
                <h4>Number of registered - Goal ID 3 / Goal Set 1 Goal 3 Completions</h4>
                <div id="clicks-8-container" style="width:100%"></div>

                <hr>
                <hr>

                <h4>Clicking on "View my projects" Goal ID 2 / Goal Set 1 (Goal 2 Completions)</h4>
                <div id="clicks-1-container" style="width:100%"></div>

                <hr>
                <h4>
                    Click - I'm the performer Goal ID 6 / Goal Set 2 (Goal 6 Completions)</h4>
                <div id="clicks-2-container" style="width:100%"></div>
                <hr>
                <h4>
                    Click on the handset icon in the header Goal ID 8 / Goal Set 2 (Goal 8 Completions)</h4>
                <div id="clicks-3-container" style="width:100%"></div>
                <hr>
                <h4>Click to Login Goal ID 7 / Goal Set 2 (Goal 7 Completions)</h4>
                <div id="clicks-4-container" style="width:100%"></div>

                <hr>
                <h4>Click - registration Goal ID 4 / Goal Set 1 (Goal 4 Completions)</h4>
                <div id="clicks-5-container" style="width:100%"></div>

                <hr>
                <h4>Click - I'm a customer Goal ID 5 / Goal Set 1 (Goal 5 Completions)</h4>
                <div id="clicks-6-container" style="width:100%"></div>
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

            var dateRangeClicks = new gapi.analytics.ext.DateRangeSelector({
                container: 'date-range-clicks-1'
            })
                .set(dateRange)
                .execute();
            var clickConfig = {
                query: {
                    metrics: ['ga:goal2Completions'],
                    dimensions: 'ga:date'
                },
                chart: {
                    type: 'LINE',
                    options: {
                        width: '100%',

                    }
                }
            };

            var clickConfig2 = {
                query: {
                    metrics: ['ga:goal6Completions'],

                    dimensions: 'ga:date'
                },
                chart: {
                    type: 'LINE',
                    options: {
                        width: '100%',
                    }
                }
            };
            var clickConfig3 = {
                query: {
                    metrics: ['ga:goal8Completions'],

                    dimensions: 'ga:date'
                },
                chart: {
                    type: 'LINE',
                    options: {
                        width: '100%',
                    }
                }
            };
            var clickConfig4 = {
                query: {
                    metrics: ['ga:goal7Completions'],

                    dimensions: 'ga:date'
                },
                chart: {
                    type: 'LINE',
                    options: {
                        width: '100%',
                    }
                }
            };

            var clickConfig5 = {
                query: {
                    metrics: ['ga:goal4Completions'],

                    dimensions: 'ga:date'
                },
                chart: {
                    type: 'LINE',
                    options: {
                        width: '100%',
                    }
                }
            };

            var clickConfig6 = {
                query: {
                    metrics: ['ga:goal5Completions'],

                    dimensions: 'ga:date'
                },
                chart: {
                    type: 'LINE',
                    options: {
                        width: '100%',
                    }
                }
            };
            var clickConfig7 = {
                query: {
                    metrics: ['ga:goal9Completions'],

                    dimensions: 'ga:date'
                },
                chart: {
                    type: 'LINE',
                    options: {
                        width: '100%',
                    }
                }
            };
            var clickConfig8 = {
                query: {
                    metrics: ['ga:goal3Completions'],

                    dimensions: 'ga:date'
                },
                chart: {
                    type: 'LINE',
                    options: {
                        width: '100%',
                    }
                }
            };


            var dataChartClicks1 = new gapi.analytics.googleCharts.DataChart(clickConfig)
                .set({query: dateRange})
                .set({chart: {container: 'clicks-1-container'}});

            var dataChartClicks2 = new gapi.analytics.googleCharts.DataChart(clickConfig2)
                .set({query: dateRange})
                .set({chart: {container: 'clicks-2-container'}});
            var dataChartClicks3 = new gapi.analytics.googleCharts.DataChart(clickConfig3)
                .set({query: dateRange})
                .set({chart: {container: 'clicks-3-container'}});
            var dataChartClicks4 = new gapi.analytics.googleCharts.DataChart(clickConfig4)
                .set({query: dateRange})
                .set({chart: {container: 'clicks-4-container'}});
            var dataChartClicks5 = new gapi.analytics.googleCharts.DataChart(clickConfig5)
                .set({query: dateRange})
                .set({chart: {container: 'clicks-5-container'}});
            var dataChartClicks6 = new gapi.analytics.googleCharts.DataChart(clickConfig6)
                .set({query: dateRange})
                .set({chart: {container: 'clicks-6-container'}});

           var dataChartClicks7 = new gapi.analytics.googleCharts.DataChart(clickConfig7)
                .set({query: dateRange})
                .set({chart: {container: 'clicks-7-container'}});

           var dataChartClicks8 = new gapi.analytics.googleCharts.DataChart(clickConfig8)
                .set({query: dateRange})
                .set({chart: {container: 'clicks-8-container'}});

            dateRangeClicks.on('change', function(data) {
                dataChartClicks1.set({query: data}).execute();
                dataChartClicks2.set({query: data}).execute();
                dataChartClicks3.set({query: data}).execute();
                dataChartClicks4.set({query: data}).execute();
                dataChartClicks5.set({query: data}).execute();
                dataChartClicks6.set({query: data}).execute();
                dataChartClicks7.set({query: data}).execute();
                dataChartClicks8.set({query: data}).execute();
            });
            dataChartClicks1.set({query: {ids: view_id}}).execute();
            dataChartClicks2.set({query: {ids: view_id}}).execute();
            dataChartClicks3.set({query: {ids: view_id}}).execute();
            dataChartClicks4.set({query: {ids: view_id}}).execute();
            dataChartClicks5.set({query: {ids: view_id}}).execute();
            dataChartClicks6.set({query: {ids: view_id}}).execute();
            dataChartClicks7.set({query: {ids: view_id}}).execute();
            dataChartClicks8.set({query: {ids: view_id}}).execute();
        });


    </script>

@endsection
