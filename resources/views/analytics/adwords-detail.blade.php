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
    <div style="margin:0 3%">


        <h2><?=$h1_title ?? 'Catalog'?></h2>
        <div>
            @include('analytics._date-range')

        </div>
        <div class="table-responsive">
            <table class="table table-bordered dataTable  " id="filter-table">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Week</th>
                    <th scope="col">Campaign</th>
                    <th scope="col">Clicks</th>
                    <th scope="col">Impr.</th>
                    <th scope="col">CTR</th>
                    <th scope="col">CPC</th>
                    <th scope="col">COST</th>
                    <th scope="col">Нажатие на кнопку Контакты</th>
                    <th scope="col">Cost per conversion</th>
                    <th scope="col">Conversion Rate</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
        <div id="chart">
            <figure class="Chartjs-figure" id="chart-1-container"></figure>
            <ol class="Chartjs-legend" id="legend-1-container"></ol>
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
            var url_go;
            if((window.location.search)){
                url_go = '<?=$current_route?>/catalog/filter'+window.location.search+'&query='+query;
            }else{
                url_go = '<?=$current_route?>/catalog/filter?query='+query;
            }
            window.location.href = url_go;
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

                let metrics = '<?=$metrics?>';
                let dimensions = '<?=$dimensions?>';
                let metricsArray = metrics.split(',');
                let columnsArray = dimensions.split(',').concat(metrics.split(','));
                let goalKey = columnsArray.map(function (element, index, array) {
                    if(element.indexOf('goal')!==-1){
                        return index;
                    }
                    return false;
                }).filter(function (item) {
                    return item;
                });
                let goalItem = columnsArray.map(function (element, index, array) {
                    if(element.indexOf('goal')!==-1){
                        return element;
                    }
                    return false;
                }).filter(function (item) {
                    return item;
                });


                let costKey = columnsArray.map(function (element, index, array) {
                    if(element.indexOf('adCost')!==-1){
                        return index;
                    }
                    return false;
                }).filter(function (item) {
                    return item;
                });
                let costItem = columnsArray.map(function (element, index, array) {
                    if(element.indexOf('adCost')!==-1){
                        return element;
                    }
                    return false;
                }).filter(function (item) {
                    return item;
                });
                let clickKey = columnsArray.map(function (element, index, array) {
                    if(element.indexOf('adClicks')!==-1){
                        return index;
                    }
                    return false;
                }).filter(function (item) {
                    return item;
                });
                let clickItem = columnsArray.map(function (element, index, array) {
                    if(element.indexOf('adClicks')!==-1){
                        return element;
                    }
                    return false;
                }).filter(function (item) {
                    return item;
                });
                let CPCKey = columnsArray.map(function (element, index, array) {
                    if(element.indexOf('CPC')!==-1){
                        return index;
                    }
                    return false;
                }).filter(function (item) {
                    return item;
                });

                var first_query = query({
                    'ids': view_id,
                    'dimensions': dimensions,
                    'metrics' : metrics,
                    'filters' : '<?=$filters?>',
                    'start-date': dateRangeCompareChanel1["start-date"],
                    'end-date': dateRangeCompareChanel1["end-date"],
                    // 'sort':'-ga:pageviews',
                    'max-results':5000
                });





                if($('#compare')[0].checked == true){
                    var second_query = query({
                        'ids': view_id,
                        'dimensions': dimensions,
                        'metrics' : metrics,
                        'filters' : '<?=$filters?>',
                        'start-date': dateRangeCompareChanel2["start-date"],
                        'end-date': dateRangeCompareChanel2["end-date"],
                        // 'sort':'-ga:pageviews',
                        'max-results':5000
                    });
                    Promise.all([first_query,second_query]).then(function(results) {
                        let data1 = results[0].rows;
                        let data2 = results[1].rows;
                        let tbody = $('#filter-table').find('tbody');
                        tbody.html('');



                        let kmcExlude1 = 0;
                        let kmcExlude2 = 0;
                        if(data1 && data1 !== undefined){
                            data1.forEach(function (item,key) {

                                let tr = "<tr data-key='"+(key+1)+"'>" +
                                    "<td>"+(key+1)+"</td>" +
                                    "<td>"+getWeekRange(parseInt(item[0]))+"</td>";
                                let tr1 = "<tr class='date1 child-row' data-addkey='"+(key+1)+"'>" +
                                    "<td></td>" +
                                    "<td class='date-range'>"+results[0].query['start-date']+"-"+results[0].query['end-date']+"</td>" ;
                                item.forEach(function (subitem,subkey) {
                                    if(subkey==1){
                                        tr+= "<td></td>";
                                        tr1+= "<td>"+subitem+"</td>";
                                    }
                                    if(subkey>1){
                                        tr+= "<td></td>";
                                        tr1+= "<td>"+parseFloat(subitem).toFixed(2)+"</td>";
                                    }
                                    if(subitem.indexOf('КМС')!==-1){
                                        kmcExlude1+=parseFloat(item[CPCKey]);
                                    }

                                });

                                tr+= "<td></td>";
                                tr+= "<td></td>";

                                tr1+="<td>" +(item[costKey]/item[goalKey]).toFixed(2)+"$</td>";
                                tr1+="<td>" +(item[goalKey]/item[clickKey]*100).toFixed(2)+"%</td>";
                                tr+= "</tr>";
                                tr1+= "</tr>";
                                tr = tr+tr1;
                                tbody.append(tr);
                            });
                            data2.forEach(function (item,key) {
                                let tr = $('#filter-table').find('*[data-key="'+(key+1)+'"]');
                                let tr1 = $('#filter-table').find('.date1*[data-addkey="'+(key+1)+'"]');
                                let tr2 = "<tr class='date2 child-row' data-addkey='"+(key+1)+"'>" +
                                    "<td></td>" +
                                    "<td class='date-range'>"+results[1].query['start-date']+"-"+results[1].query['end-date']+"</td>" ;
                                item.forEach(function (subitem,subkey) {
                                    if(subkey==1){
                                        tr+= "<td></td>";
                                        tr2+= "<td>"+subitem+"</td>";
                                    }
                                    if(subkey>1){
                                        tr2+= "<td>"+parseFloat(subitem).toFixed(2)+"</td>";
                                    }


                                });

                                tr2+="<td>" +(item[costKey]/item[goalKey]).toFixed(2)+"$</td>";
                                tr2+="<td>" +(item[goalKey]/item[clickKey]*100).toFixed(2)+"%</td>";

                                tr2+= "</tr>";


                                if(tr1.length>0){
                                    tr1.after(tr2);
                                }else{
                                    let tr = "<tr data-key='"+(key+1)+"'>" +
                                        "<td>"+(key+1)+"</td>" +
                                        "<td>"+getWeekRange(parseInt(item[0]))+"</td>" ;
                                    let tr1 = "<tr class='date1 child-row' data-addkey='"+(key+1)+"'>" +
                                        "<td></td>" +
                                        "<td class='date-range'>"+results[1].query['start-date']+"-"+results[1].query['end-date']+"</td>" ;

                                    item.forEach(function (subitem,subkey) {
                                        if(subkey==1){
                                            tr+= "<td></td>";
                                            tr1+= "<td>"+subitem+"</td>";
                                        }
                                        if(subkey>1){
                                            tr+= "<td></td>";
                                            tr1+= "<td>"+parseFloat(subitem).toFixed(2)+"</td>";
                                        }
                                        if(subitem.indexOf('КМС')!==-1){
                                            kmcExlude2+=parseFloat(item[CPCKey]);
                                        }

                                    });

                                    tr+= "<td></td>";
                                    tr+= "<td></td>";

                                    tr1+="<td>" +(item[costKey]/item[goalKey]).toFixed(2)+"$</td>";
                                    tr1+="<td>" +(item[goalKey]/item[clickKey]*100).toFixed(2)+"%</td>";
                                    tr+= "</tr>";
                                    tr1+= "</tr>";

                                    tr = tr+tr1;
                                    tbody.append(tr);
                                }

                                item.forEach(function (subitem,subkey) {
                                    // if(subkey>0){
                                    //     let pv1 = (data1[key] && data1[key][subkey]) ? data1[key][subkey] : 0;
                                    //     let pv2 = subitem;
                                    //     let change_item =(pv1 - pv2)/(pv2/100);
                                    //     console.log(tr);
                                    //     let change_td = tr ? $(tr).find('td:nth-child('+(subkey+2)+')') : 0;
                                    //     change_td.text(change_item.toFixed(2)+"%");
                                    // }
                                });


                            });
                            let addTotal1 = '';
                            let addTotal2 = '';
                            let totalDiff = '';
                            metricsArray.forEach(function (item) {
                                let total1= parseFloat(results[0].totalsForAllResults[item]);
                                let total2= parseFloat(results[1].totalsForAllResults[item]);

                                if(item.indexOf('CPC')!==-1){
                                    total1 = total1-kmcExlude1;
                                    total2 = total2-kmcExlude2;
                                }
                                addTotal1+='<td>'+total1.toFixed(2)+'</td>';
                                addTotal2+='<td>'+total2.toFixed(2)+'</td>';

                                totalDiff+="<td>"+((results[0].totalsForAllResults[item] - results[1].totalsForAllResults[item])/results[1].totalsForAllResults[item]*100).toFixed(2)+"%"+"</td>"
                            });

                            tbody.prepend("<tr>" +
                                "<td></td><td>Total "+results[1].query['start-date']+"-"+results[1].query['end-date']+"</td>" +
                                "<td></td>" +addTotal2+
                                "</tr>");
                            tbody.prepend("<tr>" +
                                "<td></td><td>Total "+results[0].query['start-date']+"-"+results[0].query['end-date']+"</td>" +
                                "<td></td>"+addTotal1+
                                "</tr>");

                            tbody.prepend("<tr>" +
                                "<td></td><td>Gain</td>" +
                                "<td></td>" +totalDiff+"</tr>");

                            let data_thisweek_clicks = results[0].rows.map(function(row) { return +row[2]; });
                            let data_lastweek_clicks = results[1].rows.map(function(row) { return +row[2]; });
                            let data_thisweek_cost = results[0].rows.map(function(row) { return +row[5]; });
                            let data_lastweek_cost = results[1].rows.map(function(row) { return +row[5]; });
                            let labels_chart = results[1].rows.map(function(row) { return +row[0]; });

                            labels_chart = labels_chart.map(function(label) {
                                return getWeekRange(label);
                            });

                            let data_chart = {
                                labels : labels_chart,
                                datasets : [
                                    {
                                        label: 'Prev Week Clicks',
                                        fillColor: 'rgba(66,133,244,0.5)',
                                        strokeColor: 'rgba(66,133,244,1)',
                                        pointColor: 'rgba(66,133,244,1)',
                                        pointStrokeColor: '#fff',
                                        data : data_lastweek_clicks
                                    },
                                    {
                                        label: 'This Week Clicks',
                                        fillColor: 'rgba(171,71,188,0.5)',
                                        strokeColor: 'rgb(171,71,188,1)',
                                        pointColor: 'rgb(171,71,188,1)',
                                        pointStrokeColor: '#fff',
                                        data : data_thisweek_clicks
                                    },
                                    {
                                        label: 'Prev  Week Cost',
                                        fillColor: 'rgba(0,172,193,0.5)',
                                        strokeColor: 'rgba(0,172,193,1)',
                                        pointColor: 'rgba(0,172,193,1)',
                                        pointStrokeColor: '#fff',
                                        data : data_lastweek_cost
                                    },
                                    {
                                        label: 'This Week Cost',
                                        fillColor : 'rgba(151,187,205,0.5)',
                                        strokeColor : 'rgba(151,187,205,1)',
                                        pointColor : 'rgba(151,187,205,1)',
                                        pointStrokeColor: '#fff',
                                        data : data_thisweek_cost
                                    }
                                ]
                            };
                            new Chart(makeCanvas('chart-1-container')).Bar(data_chart);
                            generateLegend('legend-1-container', data_chart.datasets);

                            // Set some global Chart.js defaults.
                            Chart.defaults.global.animationSteps = 60;
                            Chart.defaults.global.animationEasing = 'easeInOutQuart';
                            Chart.defaults.global.responsive = true;
                            Chart.defaults.global.maintainAspectRatio = false;


                        }





                    }).then(function(results) {
                        sortTable();
                    });
                }else{
                    Promise.all([first_query]).then(function(results) {
                        let data1 = results[0].rows;
                        let tbody = $('#filter-table').find('tbody');
                        tbody.text('');

                        if(data1 && data1 !== undefined){
                            let kmcExlude = 0;
                            data1.forEach(function (item,key) {
                                let tr = "<tr>" +"<td>"+(key+1)+"</td>"  ;

                                item.forEach(function (subitem,subkey) {
                                    if(subkey==0){
                                        tr+="<td>" +getWeekRange(parseInt(subitem))+"</td>";
                                    }
                                    if(subkey==1){
                                        tr+="<td>" +subitem+"</td>";
                                    }
                                    if(subkey>1){
                                        tr+="<td>" +parseFloat(subitem).toFixed(2)+"</td>";
                                    }
                                    if(subitem.indexOf('КМС')!==-1){
                                        kmcExlude+=parseFloat(item[CPCKey]);
                                    }
                                });
                                let consPerConversion =(parseFloat(item[costKey])!==0 && parseFloat(item[goalKey])!==0) ? (item[costKey]/item[goalKey]).toFixed(2) : 0;

                                tr+="<td>" +consPerConversion+"$</td>";
                                tr+="<td>" +(item[goalKey]/item[clickKey]*100).toFixed(2)+"%</td>";
                                tr+="</tr>";
                                tbody.append(tr);
                            });

                            let addTotal = '';
                            metricsArray.forEach(function (item) {

                                // if(item.indexOf('CPC')!==-1){
                                //
                                //     addTotal+='<td>'+parseFloat((results[0].totalsForAllResults[item])-kmcExlude).toFixed(2)+'</td>';
                                // }else{

                                    addTotal+='<td>'+parseFloat((results[0].totalsForAllResults[item])).toFixed(2)+'</td>';
                                // }


                            });
                            addTotal+='<td>'+parseFloat((results[0].totalsForAllResults[costItem])/(results[0].totalsForAllResults[goalItem])).toFixed(2)+'$</td>';
                            addTotal+='<td>'+parseFloat((results[0].totalsForAllResults[goalItem])/(results[0].totalsForAllResults[clickItem])).toFixed(2)*100+'%</td>';




                            tbody.prepend('<tr><td>Total</td><td></td><td></td>'
                                +addTotal+
                                // '<td>'+totalCostPerConversion.toFixed(2)+'</td>' +
                                '</tr>');



                            let data_thisweek_clicks = results[0].rows.map(function(row) { return +row[2]; });
                            let data_thisweek_cost = results[0].rows.map(function(row) { return +row[5]; });
                            let labels_chart = results[0].rows.map(function(row) { return +row[0]; });

                            labels_chart = labels_chart.map(function(label, key) {
                                return key+1;
                                // return getWeekRange(label);
                            });


                            let data_chart = {
                                labels : labels_chart,
                                datasets : [
                                    {
                                        label: 'Clicks',
                                        fillColor: 'rgba(171,71,188,0.5)',
                                        strokeColor: 'rgb(171,71,188,1)',
                                        pointColor: 'rgb(171,71,188,1)',
                                        pointStrokeColor: '#fff',
                                        data : data_thisweek_clicks
                                    },
                                    {
                                        label: 'Cost',
                                        fillColor : 'rgba(151,187,205,0.5)',
                                        strokeColor : 'rgba(151,187,205,1)',
                                        pointColor : 'rgba(151,187,205,1)',
                                        pointStrokeColor: '#fff',
                                        data : data_thisweek_cost
                                    }
                                ]
                            };
                            new Chart(makeCanvas('chart-1-container')).Bar(data_chart);
                            generateLegend('legend-1-container', data_chart.datasets);

                            // Set some global Chart.js defaults.
                            Chart.defaults.global.animationSteps = 60;
                            Chart.defaults.global.animationEasing = 'easeInOutQuart';
                            Chart.defaults.global.responsive = true;
                            Chart.defaults.global.maintainAspectRatio = false;
                        }



                    }).then(function(results) {
                        sortTable();
                    });
                }

            }



        });


    </script>

@endsection
