!(function ($) {
    "use strict";

    const data = window.dashboardChartData || {};

    console.log(data);

    const labels = data.dates || [];

    function formatKES(value) {
        return "KES " + Number(value || 0).toLocaleString("en-KE", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Daily Processed Amount
    |--------------------------------------------------------------------------
    */

    new ApexCharts(document.querySelector("#processedAmountChart"), {
        chart: {
            type: "area",
            height: 300,
            toolbar: { show: false }
        },

        series: [{
            name: "Processed Amount",
            data: data.values || []
        }],

        colors: ["#000000"],

        dataLabels: {
            enabled: false
        },

        stroke: {
            curve: "smooth",
            width: 3
        },

        fill: {
            type: "gradient",
            gradient: {
                opacityFrom: 0.25,
                opacityTo: 0
            }
        },

        xaxis: {
            categories: labels
        },

        yaxis: {
            labels: {
                formatter: function(val) {
                    return "KES " + (val / 1000000).toFixed(1) + "M";
                }
            }
        },

        tooltip: {
            y: {
                formatter: formatKES
            }
        }
    }).render();

})(jQuery);