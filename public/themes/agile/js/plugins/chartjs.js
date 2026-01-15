(function ($, theme) {
	"use strict";

  var gradientLinePlugin = {
    // Called at start of update.
    beforeDatasetUpdate: function(chartInstance) {
      if (chartInstance.options.linearGradientLine) {
        // The context, needed for the creation of the linear gradient.
        var ctx = chartInstance.chart.ctx;
        var width = chartInstance.width;
        var height = chartInstance.height;
        // The first (and, assuming, only) dataset.
        var dataset = chartInstance.data.datasets[0];
        // Create the gradient.
        var gradient = ctx.createLinearGradient(0, 0, width, 0);
        // A kind of red for min.
        gradient.addColorStop(0, theme.color.primary);

        // A kind of blue for max.
        gradient.addColorStop(1, theme.color.success);
        // Assign the gradient to the dataset's border color.
        dataset.borderColor = gradient;
        // Uncomment this for some effects, especially together with commenting the `fill: false` option below.
        // dataset.backgroundColor = gradient;
      }
    }
  };

  Chart.pluginService.register(gradientLinePlugin);

  var getGradient = function(color, height){
    var ctx = $('<canvas/>').get(0).getContext("2d");
    var gradient = ctx.createLinearGradient(0, 0, 0, height);
    gradient.addColorStop(0, hexToRGB(color, 0.35));
    gradient.addColorStop(1, hexToRGB(color, 0));
    return gradient;
  }

  var getRandomData = function(total) {
    var data = [];
    while (data.length < total) {
      var prev = data.length > 0 ? data[data.length - 1] : 50,
        y = prev + Math.random() * 10 - 5;
      if (y < 0) {
        y = 0;
      } else if (y > 100) {
        y = 100;
      }
      data.push(Math.round(y*100)/100);
    }

    return data;
  }

  var getLables = function(total, pre){
    var data = [], i=1;
    while (data.length < total) {
      data.push(pre+' '+i);
      i++;
    }
    return data;
  }

})(jQuery, theme);
