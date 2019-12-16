@extends('layouts.app')

@section('template_title')
    Showing Analytics
@endsection

@section('content')

<div class="Chartjs">
    <h3>Active users</h3>
    <input id="e1" name="e1">

    <div id="chart">
        <figure class="Chartjs-figure" id="chart-1-container"></figure>
        <ol class="Chartjs-legend" id="legend-1-container"></ol>
    </div>

</div>
@endsection
@section('template_linked_css')
    <!-- Include the CSS that styles the charts. -->
    <link rel="stylesheet" href={{asset('css/chartjs-visualizations.css')}}>
    <link rel="stylesheet" href={{asset('css/jquery.comiseo.daterangepicker.css')}}>

    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.0/themes/smoothness/jquery-ui.css">
    <style>
        .loading {
            opacity: 0.3;
        }
    </style>
@endsection

@section('template_scripts')


    <script>
        (function(w,d,s,g,js,fs){
            g=w.gapi||(w.gapi={});g.analytics={q:[],ready:function(f){this.q.push(f);}};
            js=d.createElement(s);fs=d.getElementsByTagName(s)[0];
            js.src='https://apis.google.com/js/platform.js';
            fs.parentNode.insertBefore(js,fs);js.onload=function(){g.load('analytics');};
        }(window,document,'script'));
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>

    <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/momentjs/2.3.1/moment.min.js"></script>
    <script src={{asset('js/jquery.comiseo.daterangepicker.min.js')}}></script>


    <script>
        $("#e1").daterangepicker({
            applyOnMenuSelect: false,
            dateFormat: 'yymmd',
            datepickerOptions: {
                // minDate: 0,
                maxDate: 0
            }
        });
        var yesterday = moment().subtract('days', 1).startOf('day').toDate();
        var sevenday = moment().subtract('days', 7).startOf('day').toDate();
        $("#e1").daterangepicker("setRange", {start: sevenday,end: yesterday});

        $("#e1").on('change', function(event) {
            var range =$("#e1").daterangepicker("getRange");


            var range1=     $.datepicker.formatDate( "yymmdd", range["start"] );
            var range2=     $.datepicker.formatDate( "yymmdd", range["end"] );
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            if(range1 && range2){
                $("#e1").attr('disable', 'disable');
                $('#chart').addClass('loading');
                $.ajax(
                    {
                        method: "POST",
                        url: "",
                        dataType: "json",
                        data:"date1="+range1+"&date2="+range2,

                        success: function(result){
                            var data = generateData(result);

                            new Chart(makeCanvas('chart-1-container')).Line(data);
                            generateLegendFirebase('legend-1-container', data.datasets);
                            $("#e1").removeAttr('disable');
                            $('#chart').removeClass('loading');
                        }
            });
            }


        });
    </script>

    <script>

        // == NOTE ==
        // This code uses ES6 promises. If you want to use this code in a browser
        // that doesn't supporting promises natively, you'll have to include a polyfill.
        var data = generateData(<?=json_encode($events)?>);
        function generateData(data_events){
            var labels_events = data_events.map(function (item, index) {
                return +item.date;
            });

            var data_events_daily = data_events.map(function (item, index) {
                return +item.days_01;
            });

            var data_events_weekly = data_events.map(function (item, index) {
                return +item.days_07;
            });

            var data_events_monthly = data_events.map(function (item, index) {
                return +item.days_30;
            });

            labels = labels_events.map(function (label) {
                return moment(label, 'YYYYMMDD').format('ddd');
            });


            var data = {
                labels: labels_events,
                datasets: [

                    {
                        label: 'Monthly',
                        fillColor: 'rgba(66,133,244,0.5)',
                        strokeColor: 'rgba(66,133,244,1)',
                        pointColor: 'rgba(66,133,244,1)',
                        pointStrokeColor: '#fff',
                        data: data_events_monthly
                    },
                    {
                        label: 'Weekly',
                        fillColor: 'rgba(171,71,188,0.5)',
                        strokeColor: 'rgb(171,71,188,1)',
                        pointColor: 'rgb(171,71,188,1)',
                        pointStrokeColor: '#fff',
                        data: data_events_weekly
                    },
                    {
                        label: 'Daily',
                        fillColor: 'rgba(0,172,193,0.5)',
                        strokeColor: 'rgba(0,172,193,1)',
                        pointColor: 'rgba(0,172,193,1)',
                        pointStrokeColor: '#fff',
                        data: data_events_daily
                    }
                ]
            };
            console.log(data);
            return data;
        }


        new Chart(makeCanvas('chart-1-container')).Line(data);
        generateLegendFirebase('legend-1-container', data.datasets);


            // Set some global Chart.js defaults.
            Chart.defaults.global.animationSteps = 60;
            Chart.defaults.global.animationEasing = 'easeInOutQuart';
            Chart.defaults.global.responsive = true;
            Chart.defaults.global.maintainAspectRatio = false;


    </script>
@endsection