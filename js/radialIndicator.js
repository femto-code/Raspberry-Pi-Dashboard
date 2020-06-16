/*
    radialIndicator.js v 2.0.0
    Author: Sudhanshu Yadav
    Copyright (c) 2015,2016 Sudhanshu Yadav - ignitersworld.com , released under the MIT license.
    Updated: Jannik Gysi, 20.08.2019
*/
;(function (factory) {
  /** support UMD ***/
  var global = Function('return this')() || (42, eval)('this'); // eslint-disable-line
  if (typeof define === 'function' && define.amd) { // eslint-disable-line
    define(['jquery'], function ($) { // eslint-disable-line
      return (global.radialIndicator = factory($, global));
    });
  } else if (typeof module === 'object' && module.exports) {
    module.exports = global.document
        ? factory(require('jquery'), global)
        : function (w) {
          if (!w.document) {
            throw new Error('radialIndiactor requires a window with a document');
          }
          return factory(require('jquery')(w), w);
        };
  } else {
    global.radialIndicator = factory(global.jQuery, global);
  }
}(function ($, window, undef) {
  var document = window.document;
  var circ = Math.PI * 2;
  var quart = Math.PI / 2;
  // function to smooth canvas drawing for retina devices
  // method to manage device pixel ratio in retina devices
  var smoothCanvas = (function () {
    var ctx = document.createElement('canvas').getContext('2d');
    var dpr = window.devicePixelRatio || 1;
    var bsr = ctx.webkitBackingStorePixelRatio ||
        ctx.mozBackingStorePixelRatio ||
        ctx.msBackingStorePixelRatio ||
        ctx.oBackingStorePixelRatio ||
        ctx.backingStorePixelRatio || 1;

    var ratio = dpr / bsr; // PIXEL RATIO

    return function (w, h, canvasElm) {
      var can = canvasElm || document.createElement('canvas');
      can.width = w * ratio;
      can.height = h * ratio;
      can.style.width = w + 'px';
      can.style.height = h + 'px';
      can.getContext('2d').setTransform(ratio, 0, 0, ratio, 0, 0);
      return can;
    };
  }());

  // function to convert hex to rgb
  function hexToRgb (hex) {
    // Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
    var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
    hex = hex.replace(shorthandRegex, function (m, r, g, b) {
      return r + r + g + g + b + b;
    });

    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? [parseInt(result[1], 16), parseInt(result[2], 16), parseInt(result[3], 16)] : null;
  }

  function getPropVal (curShift, perShift, bottomRange, topRange) {
    return Math.round(bottomRange + ((topRange - bottomRange) * curShift / perShift));
  }

  // function to get current color in case of
  function getCurrentColor (curPer, bottomVal, topVal, bottomColor, topColor) {
    var rgbAryTop = topColor.indexOf('#') !== -1 ? hexToRgb(topColor) : topColor.match(/\d+/g);
    var rgbAryBottom = bottomColor.indexOf('#') !== -1 ? hexToRgb(bottomColor) : bottomColor.match(/\d+/g);
    var perShift = topVal - bottomVal;
    var curShift = curPer - bottomVal;

    if (!rgbAryTop || !rgbAryBottom) { return null; }

    return 'rgb(' + getPropVal(curShift, perShift, rgbAryBottom[0], rgbAryTop[0]) + ',' + getPropVal(curShift, perShift, rgbAryBottom[1], rgbAryTop[1]) + ',' + getPropVal(curShift, perShift, rgbAryBottom[2], rgbAryTop[2]) + ')';
  }

  // to merge object
  function merge () {
    var arg = arguments;
    var target = arg[0];
    for (var i = 1, ln = arg.length; i < ln; i++) {
      var obj = arg[i];
      for (var k in obj) {
        if (Object.prototype.hasOwnProperty.call(obj, k)) {
          target[k] = obj[k];
        }
      }
    }
    return target;
  }

  // function to apply formatting on number depending on parameter
  function formatter (pattern, precision) {
    return function (num) {
      if (!pattern || pattern.length === 0) { return num.toFixed(precision).toString(); }
      var patternDigitsDecimals = pattern.split('.');
      var patternDecimals = patternDigitsDecimals.length > 1 ? patternDigitsDecimals[1].replace(/[^#]/g, '').length : 0;
      var number = num || 0;

      if (pattern.includes('.')) {
        number = parseFloat(number).toFixed(patternDecimals);
        var numSplit = number.toString().split('.');
        var digits = replaceHashes(patternDigitsDecimals[0], parseFloat(numSplit[0]).toFixed(0));
        var decimals = replaceHashes(patternDigitsDecimals[1], numSplit[1]);
        return (digits + "." + decimals);
      }

      return replaceHashes(pattern, parseFloat(number).toFixed(0));
    };
  }

  // helper function of formatter function
  function replaceHashes (pattern, num) {
    var numRev = num.toString().split(new RegExp('(-?[0-9])', 'g')).filter(function (d) { return d !== ''; }).reverse();
    var output = pattern.split('').reverse();
    var i = 0;
    var lastHashReplaced = 0;

    // changes hash with numbers
    for (var ln = output.length; i < ln; i++) {
      if (!numRev.length) { break; }
      if (output[i] === '#') {
        lastHashReplaced = i;
        output[i] = numRev.shift();
      }
    }
    // add overflowing numbers before prefix
    output.splice(lastHashReplaced + 1, output.lastIndexOf('#') - lastHashReplaced, numRev.reverse().join(''));
    return output.reverse().join('');
  }

  // circle bar class
  function Indicator (container, indOption) {
    var self = this;

    indOption = indOption || {};
    indOption = merge({}, radialIndicator.defaults, indOption);

    this.indOption = indOption;

    // create a queryselector if a selector string is passed in container
    if (typeof container === 'string') {
      container = document.querySelector(container);
    }

    // get the first element if container is a node list
    if (container.length) {
      container = container[0];
    }

    this.container = container;

    // create a canvas element
    var canElm = document.createElement('canvas');
    container.appendChild(canElm);

    this.canElm = canElm; // dom object where drawing will happen

    this.ctx = canElm.getContext('2d'); // get 2d canvas context

    // add intial value
    this.current_value = indOption.initValue || indOption.minValue || 0;

    // handeling user interaction
    var startListener = function (e) {
      if (!indOption.interaction) { return; }

      var touchMove = e.type === 'touchstart' ? 'touchmove' : 'mousemove';
      var touchEnd = e.type === 'touchstart' ? 'touchend' : 'mouseup';
      var position = canElm.getBoundingClientRect();
      var cy = position.top + canElm.offsetHeight / 2;
      var cx = position.left + canElm.offsetWidth / 2;

      var moveListener = function (e) {
        e.preventDefault();

        // get the cordinates
        var mx = e.clientX || e.touches[0].clientX;
        var my = e.clientY || e.touches[0].clientY;
        var radian = (circ + quart + Math.atan2((my - cy), (mx - cx))) % (circ + 0.0175);
        var radius = (indOption.radius - 1 + indOption.barWidth / 2);
        var circum = circ * radius;
        var precision = indOption.precision != null ? indOption.precision : 0;
        var precisionNo = Math.pow(10, precision);
        var val = Math.round(precisionNo * radian * radius * (indOption.maxValue - indOption.minValue) / circum) / precisionNo;

        self.value(val);
      };

      var endListener = function () {
        document.removeEventListener(touchMove, moveListener, false);
        document.removeEventListener(touchEnd, endListener, false);
      };

      document.addEventListener(touchMove, moveListener, false);
      document.addEventListener(touchEnd, endListener, false);
    };

    canElm.addEventListener('touchstart', startListener, false);
    canElm.addEventListener('mousedown', startListener, false);

    canElm.addEventListener('mousewheel', MouseWheelHandler, false);
    canElm.addEventListener('DOMMouseScroll', MouseWheelHandler, false);

    function MouseWheelHandler (e) {
      if (!indOption.interaction) { return; }
      e.preventDefault();

      // cross-browser wheel delta
      var delta = -(Math.max(-1, Math.min(1, (e.wheelDelta || -e.detail))));
      var precision = indOption.precision != null ? indOption.precision : 0;
      var precisionNo = Math.pow(10, precision);
      var diff = indOption.maxValue - indOption.minValue;
      var val = self.current_value + Math.round(precisionNo * delta * diff / Math.min(diff, 100)) / precisionNo;

      self.value(val);

      return false;
    }
  }

  Indicator.prototype = {
    constructor: radialIndicator,
    _init: function () {
      var indOption = this.indOption;
      var canElm = this.canElm;
      var dim = (indOption.radius + indOption.barWidth) * 2; // elm width and height

      // create a formatter function
      this.formatter = typeof indOption.format === 'function' ? indOption.format : formatter(indOption.format);

      // maximum text length;
      this.maxLength = indOption.percentage ? 4 : this.formatter(indOption.maxValue).length;

      // smooth the canvas elm for ratina display
      smoothCanvas(dim, dim, canElm);

      // draw background bar
      this._drawBarBg();

      // put the initial value if defined
      this.value(this.current_value);

      return this;
    },
    // draw background bar
    _drawBarBg: function () {
      var indOption = this.indOption;
      var ctx = this.ctx;
      var dim = (indOption.radius + indOption.barWidth) * 2; // elm width and height
      var center = dim / 2; // center point in both x and y axis

      // draw nackground circle
      ctx.strokeStyle = indOption.barBgColor; // background circle color
      ctx.lineWidth = indOption.barWidth;
      if (indOption.barBgColor !== 'transparent') {
        ctx.beginPath();
        ctx.arc(center, center, indOption.radius - 1 + indOption.barWidth / 2, 0, 2 * Math.PI);
        ctx.stroke();
      }
    },
    // update the value of indicator without animation
    value: function (val) {
      // return the val if val is not provided
      if (val === undef || isNaN(val)) {
        return this.current_value;
      }

      val = parseFloat(val);

      var ctx = this.ctx;
      var indOption = this.indOption;
      var dim = (indOption.radius + indOption.barWidth) * 2;
      var minVal = indOption.minValue;
      var maxVal = indOption.maxValue;
      var center = dim / 2;
      var curColor = indOption.barColor;

      // limit the val in range of minumum and maximum value
      val = val < minVal ? minVal : val > maxVal ? maxVal : val;

      var precision = indOption.precision != null ? indOption.precision : 0;
      var precisionNo = Math.pow(10, precision);
      var perVal = (((val - minVal) * precisionNo / (maxVal - minVal)) * 100) / precisionNo; // percentage value tp two decimal precision
      var dispVal = indOption.percentage ? perVal.toFixed(precision) + '%' : this.formatter(val, precision); // formatted value

      // save val on object
      this.current_value = val;

      // draw the bg circle
      ctx.clearRect(0, 0, dim, dim);
      this._drawBarBg();

      // get current color if color range is set
      if (typeof curColor === 'object') {
        var range = Object.keys(curColor);

        for (var i = 1, ln = range.length; i < ln; i++) {
          var bottomVal = range[i - 1];
          var topVal = range[i];
          var bottomColor = curColor[bottomVal];
          var topColor = curColor[topVal];
          var newColor = (void 0);

          if (val.toString() === bottomVal) {
            newColor = bottomColor;
          } else {
            if (val.toString() === topVal) {
              newColor = topColor;
            } else {
              if (val > bottomVal && val < topVal) {
                newColor = indOption.interpolate ? getCurrentColor(val, bottomVal, topVal, bottomColor, topColor) : topColor;
              } else {
                newColor = false;
              }
            }
          }

          if (newColor !== false) {
            curColor = newColor;
            break;
          }
        }
      }

      // draw th circle value
      ctx.strokeStyle = curColor;

      // add linecap if value setted on options
      if (indOption.roundCorner) { ctx.lineCap = 'round'; }

      ctx.beginPath();
      var start, end;
      if (indOption.reverse) {
        start = circ * ((100 - perVal) / 100) - quart;
        // Start and end can't be equal or nothing is rendered
        // so shave of a tiny amount of the end
        end = -quart - 0.00001;
      } else {
        start = -quart;
        end = (circ * perVal) / 100 - quart;
      }
      ctx.arc(center, center, indOption.radius - 1 + indOption.barWidth / 2, start, end, false);
      ctx.stroke();

      // add percentage text
      if (indOption.displayNumber) {
        var cFont = ctx.font.split(' ');
        var weight = indOption.fontWeight;
        var fontSize = indOption.fontSize || dim / (this.maxLength - (Math.floor((this.maxLength * 1.4) / 4) - 1));

        cFont = indOption.fontFamily || cFont[cFont.length - 1];

        ctx.fillStyle = indOption.fontColor || curColor;
        ctx.font = weight + ' ' + fontSize + 'px ' + cFont;
        ctx.textAlign = 'center';
        ctx.textBaseline = indOption.textBaseline;
        ctx.fillText(dispVal, center, center);
      }

      // call onChange callback
      indOption.onChange.call(this.container, val);

      return this;
    },
    // animate progressbar to the value
    animate: function (val, animationDuration) {
      var this$1 = this;

      var indOption = this.indOption;
      var startingPoint = this.current_value;
      var self = this;
      var minVal = indOption.minValue;
      var maxVal = indOption.maxValue;
      var frameNum = indOption.frameNum || (indOption.percentage ? 100 : 500);
      var counter = this.current_value;

      // limit the val in range of minumum and maximum value
      val = val < minVal ? minVal : val > maxVal ? maxVal : val;

      var back = val < counter;
      var valDelta = Math.abs(this.current_value - val);
      var minMaxDelta = Math.abs(maxVal - minVal);
      var valDeltaMultiplier = valDelta / minMaxDelta;
      var calcDuration = animationDuration || indOption.duration * valDeltaMultiplier || indOption.frameTime * frameNum * valDeltaMultiplier; // frameTime legacysupport
      var start;

      var processAnimation = function (now) {
        if (!start) { start = now; }
        var runtime = now - start;
        var progress = runtime / calcDuration;
        var step = valDelta * indOption.easing(progress);
        counter = back ? startingPoint - step : startingPoint + step;
        this$1.value(counter); // display the value
        if (runtime >= calcDuration) {
          this$1.value(val);
          cancelAnimationFrame(processAnimation);
          if (indOption.onAnimationComplete) { indOption.onAnimationComplete(self.current_value); }
        } else {
          // if duration not met yet
          requestAnimationFrame(processAnimation);
        }
      };
      requestAnimationFrame(processAnimation);
      return this;
    },
    // method to update options
    option: function (key, val) {
      if (val === undef) { return this.option[key]; }

      if (['radius', 'barWidth', 'barBgColor', 'format', 'maxValue', 'percentage'].indexOf(key) !== -1) {
        this.indOption[key] = val;
        this._init().value(this.current_value);
      }
      this.indOption[key] = val;
    },

  };

  /** Initializer function **/
  function radialIndicator (container, options) {
    var progObj = new Indicator(container, options);
    progObj._init();
    return progObj;
  }

  // radial indicator defaults
  radialIndicator.defaults = {
    reverse: false,
    radius: 50, // inner radius of indicator
    barWidth: 5, // bar width
    barBgColor: '#eeeeee', // unfilled bar color
    barColor: '#99CC33', // filled bar color , can be a range also having different colors on different value like {0 : "#ccc", 50 : '#333', 100: '#000'}
    format: null, // format indicator numbers, can be a # formator ex (##,###.##) or a function
    duration: null, // duration in ms from minValue to maxValue
    frameTime: 10, // @deprecated miliseconds to move from one frame to another
    frameNum: null, // @deprecated Defines numbers of frame in indicator, defaults to 100 when showing percentage and 500 for other values
    fontColor: null, // font color
    fontFamily: null, // defines font family
    fontWeight: 'bold', // defines font weight
    fontSize: null, // define the font size of indicator number
    textBaseline: 'middle', // define the text base line of indicator number
    interpolate: true, // interpolate color between ranges
    percentage: false, // show percentage of value
    precision: null, // default value for precision depend on difference between min and max divided by number of frames
    displayNumber: true, // display indicator number
    roundCorner: false, // have round corner in filled bar
    minValue: 0, // minimum value
    maxValue: 100, // maximum value
    initValue: 0, // define initial value of indicator,
    interaction: false, // if true it allows to change radial indicator value using mouse or touch interaction
    easing: function (y) {
      return y;
    }, // default linear, y = progress will be passed to any function, should return updated progress
    onChange: function () {
    },
  };

  window.radialIndicator = radialIndicator;

  // add as a jquery plugin
  if ($) {
    $.fn.radialIndicator = function (options) {
      return this.each(function () {
        var newPCObj = radialIndicator(this, options);
        $.data(this, 'radialIndicator', newPCObj);
      });
    };
  }

  return radialIndicator;
}));

