/**
 * scroll to active menu item
 */
$(document).ready(function() {
    var active_menu_item = $('#mainNav.navbar-dark .navbar-collapse #exampleAccordion li > a.active');

    if(active_menu_item[0] && active_menu_item.length>0){
        $('#exampleAccordion').animate({
            scrollTop: active_menu_item.offset().top-200
        }, 1000);
    }else {
        active_menu_item = $('#mainNav.navbar-dark .navbar-collapse #exampleAccordion  li  ul.show');
        if(active_menu_item[0] && active_menu_item.length>0){
            $('#exampleAccordion').animate({
                scrollTop: active_menu_item.offset().top-200
            }, 1000);
        }
    }

});


/**
 * tables sorting
 */



function sortTable() {
    $(document).find( 'table')
        .tablesorter({
            headers: {
                5 : { sorter: 'digit' },
                3 : { sorter: 'digit' },
                4 : { sorter: 'digit' }
            },
            theme : 'blue',
            debug : false
            // this is the default setting
            // cssChildRow : 'child-row',
            // initialize zebra and filter widgets

        });
}



/**
 * Change url history
 * @param MainElement
 * @param ChildElement
 */
function urlHistory(MainElement, ChildElement){
    var extra = window.location.pathname + window.location.search;
    var regex = new RegExp("[&\\?]"+MainElement+'='+getParameterByName(MainElement), 'g');
    var isReplace = extra.match(regex);


    if(isReplace){
        extra = extra.replace(MainElement+'='+getParameterByName(MainElement), MainElement+'='+ChildElement);
    }else{
        extra = extra+ ( extra.indexOf("?") > -1 ? "&" : "?" ) +MainElement+ '=' +ChildElement;
    }
    window.history[isReplace?'replaceState':'pushState'](null, null, extra);
}


/**
 * Get parametr from Url
 * @param name
 * @param url
 * @returns {*}
 */
function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

/**
 * Extend the Embed APIs `gapi.analytics.report.Data` component to
 * return a promise the is fulfilled with the value returned by the API.
 * @param {Object} params The request parameters.
 * @return {Promise} A promise.
 */
function query(params) {
    console.table(params);
    return new Promise(function(resolve, reject) {
        var data = new gapi.analytics.report.Data({query: params});
        data.once('success', function(response) { resolve(response); })
            .once('error', function(response) {
                if(response.error && response.error.code && response.error.code == 403){
                    // query(params);
                }
                reject(response);
            })
            .execute();
    });
}


/**
 * With timeout
 * Extend the Embed APIs `gapi.analytics.report.Data` component to
 * return a promise the is fulfilled with the value returned by the API.
 * @param {Object} params The request parameters.
 * @return {Promise} A promise.
 */
function queryTimeout(params) {
    console.table(params);
    window.time+=200;
    return new Promise(function(resolve, reject) {
        setTimeout(function () {
            var data = new gapi.analytics.report.Data({query: params});
            data.once('success', function(response) { resolve(response); })
                .once('error', function(response) { reject(response); })
                .execute();
        },window.time);
    });
}


/**
 * Create a new canvas inside the specified element. Set it to be the width
 * and height of its container.
 * @param {string} id The id attribute of the element to host the canvas.
 * @return {RenderingContext} The 2D canvas context.
 */
function makeCanvas(id){
    var container = document.getElementById(id);
    var canvas = document.createElement('canvas');
    var ctx = canvas.getContext('2d');
    container.innerHTML = '';
    canvas.width = container.offsetWidth;
    canvas.height = container.offsetHeight;
    container.appendChild(canvas);
    return ctx;
}



/**
 * Escapes a potentially unsafe HTML string.
 * @param {string} str An string that may contain HTML entities.
 * @return {string} The HTML-escaped string.
 */
function escapeHtml(str) {
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}

/**
 * Format date
 * @param date
 * @returns {string}
 */
function formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;

    return [year, month, day].join('-');
}
/**
 * Create a visual legend inside the specified element based off of a
 * Chart.js dataset.
 * @param {string} id The id attribute of the element to host the legend.
 * @param {Array.<Object>} items A list of labels and colors for the legend.
 */
function generateLegend(id, items) {
    var legend = document.getElementById(id);
    legend.innerHTML = items.map(function(item) {
        var color = item.color || item.fillColor;
        var label = item.label;
        return '<li><i style="background:' + color + '"></i>' +
            escapeHtml(label) + '</li>';
    }).join('');
}
/**
 * Create a visual legend inside the specified element based off of a
 * Chart.js dataset.
 * @param {string} id The id attribute of the element to host the legend.
 * @param {Array.<Object>} items A list of labels and colors for the legend.
 */
function generateLegendFirebase(id, items) {
    var legend = document.getElementById(id);
    legend.innerHTML = items.map(function(item) {
        var color = item.color || item.fillColor;
        var label = item.label;
        var active = item.data[item.data.length-1];
        var active_prev = item.data[item.data.length-2];
        return '<li><i style="background:' + color + '"></i>' +
            escapeHtml(label) + '<hr><span class="current">'+active+'</span></li>';
    }).join('');
}


/**
 * Promise with timeout(for work with query limits)
 * @param promises
 * @param timeout
 * @param resolvePartial
 * @returns {Promise<any>}
 */
function promiseAllTimeout(promises, timeout, resolvePartial=false) {
    return new Promise(function(resolve, reject) {
        let results = [],
            finished = 0,
            numPromises = promises.length;
        let onFinish = function() {
            if (finished < numPromises) {
                if (resolvePartial) {
                    (resolve)(results);
                } else {
                    throw new Error("Not all promises completed within the specified time");
                }
            } else {
                (resolve)(results);
            }
            onFinish = null;
        };
        for (let i = 0; i < numPromises; i += 1) {
            results[i] = undefined;
            promises[i].then(
                function(res) {
                    results[i] = res;
                    finished += 1;
                    if (finished === numPromises && onFinish) {
                        onFinish();
                    }
                },
                reject
            );
        }
        setTimeout(function() {
            if (onFinish) {
                onFinish();
            }
        }, timeout);
    });
}

function getWeekRange(item) {

    // let from = moment(''+moment().year()+'').add(item, 'weeks').startOf('week').format('DD.MM');
    // let to= moment(''+moment().year()+'').add(item, 'weeks').endOf('week').format('DD.MM');
    let from = moment().day("Monday").isoWeek(item).format('DD.MM');
    let to = moment().day("Sunday").isoWeek(item).format('DD.MM');
    return from+"-"+to;
}
function getWeekStartDate(item) {
    return moment().day("Monday").isoWeek(item).format('YYYY-MM-DD');
}
function getWeekEndDate(item) {

    return  moment().day("Sunday").isoWeek(item).format('YYYY-MM-DD');
}