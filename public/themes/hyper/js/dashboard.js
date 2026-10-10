!(function ($) {
    "use strict";

    const data = window.dashboardChartData || {};

    const labels = data.labels || [];

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
            data: data.processed_amount || []
        }],

        colors: ["#0acf97"],

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

    /*
    |--------------------------------------------------------------------------
    | Wallet Balance Trend
    |--------------------------------------------------------------------------
    */

    new ApexCharts(document.querySelector("#walletBalanceChart"), {
        chart: {
            type: "line",
            height: 300,
            toolbar: { show: false }
        },

        colors: ["#3b82f6", "#0acf97"],

        series: [
            {
                name: "Opening Balance",
                data: data.opening_balance || []
            },
            {
                name: "Closing Balance",
                data: data.closing_balance || []
            }
        ],

        dataLabels: {
            enabled: false
        },

        stroke: {
            width: 3,
            curve: "smooth"
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

    /*
    |--------------------------------------------------------------------------
    | Transaction Volume Trend
    |--------------------------------------------------------------------------
    */

    new ApexCharts(document.querySelector("#transactionVolumeChart"), {
        chart: {
            type: "line",
            height: 300,
            toolbar: { show: false }
        },

        colors: [
            "#3b82f6",
            "#0acf97",
            "#fa5c7c",
            "#f9bc0b"
        ],

        series: [
            {
                name: "Total Transactions",
                data: data.total_transactions || []
            },
            {
                name: "Completed",
                data: data.completed_transactions || []
            },
            {
                name: "Cancelled",
                data: data.failed_transactions || []
            },
            {
                name: "In Progress",
                data: data.pending_transactions || []
            }
        ],

        dataLabels: {
            enabled: false
        },

        stroke: {
            width: 3,
            curve: "smooth"
        },

        xaxis: {
            categories: labels
        },

        tooltip: {
            shared: true
        }
    }).render();

    /*
    |--------------------------------------------------------------------------
    | Transaction Breakdown
    |--------------------------------------------------------------------------
    */

    new ApexCharts(document.querySelector("#transactionTypeChart"), {
        chart: {
            type: "donut",
            height: 300
        },

        colors: [
            "#0acf97",
            "#3b82f6",
            "#f9bc0b",
            "#8b5cf6",
            "#fa5c7c"
        ],

        labels: data.transaction_breakdown?.labels || [],

        series: data.transaction_breakdown?.series || [],

        legend: {
            show: false
        },

        dataLabels: {
            enabled: false
        },

        plotOptions: {
            pie: {
                donut: {
                    size: "70%",

                    labels: {
                        show: true,

                        total: {
                            show: true,
                            label: "Total",

                            formatter: function(w) {
                                const total = w.globals.seriesTotals.reduce(
                                    (a, b) => a + b,
                                    0
                                );

                                return (
                                    "KES " +
                                    (total / 1000000).toFixed(2) +
                                    "M"
                                );
                            }
                        }
                    }
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