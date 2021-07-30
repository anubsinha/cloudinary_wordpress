!function(e){var t={};function o(r){if(t[r])return t[r].exports;var i=t[r]={i:r,l:!1,exports:{}};return e[r].call(i.exports,i,i.exports,o),i.l=!0,i.exports}o.m=e,o.c=t,o.d=function(e,t,r){o.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:r})},o.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},o.t=function(e,t){if(1&t&&(e=o(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var r=Object.create(null);if(o.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var i in e)o.d(r,i,function(t){return e[t]}.bind(null,i));return r},o.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return o.d(t,"a",t),t},o.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},o.p="",o(o.s=2)}([function(e,t){e.exports=function(e,t){(null==t||t>e.length)&&(t=e.length);for(var o=0,r=new Array(t);o<t;o++)r[o]=e[o];return r},e.exports.default=e.exports,e.exports.__esModule=!0},function(e,t,o){var r=o(3),i=o(4),n=o(5),a=o(6);e.exports=function(e){return r(e)||i(e)||n(e)||a()},e.exports.default=e.exports,e.exports.__esModule=!0},function(e,t,o){"use strict";o.r(t);var r=o(1),i=o.n(r),n={density:window.devicePixelRatio?window.devicePixelRatio:"auto",images:[],debounce:null,config:CLDLB||{},lazyThreshold:0,_init:function(){var e=this;this._calcThreshold(),i()(document.images).forEach((function(t){t.dataset.src&&(t.originalWidth=t.dataset.width,e.images.push(t))})),window.addEventListener("resize",(function(t){e._debounceBuild()})),window.addEventListener("scroll",(function(t){e._debounceBuild()})),this._build()},_calcThreshold:function(){var e=this.config.lazy_threshold.replace(/[^0-9]/g,""),t=0;switch(this.config.lazy_threshold.replace(/[0-9]/g,"").toLowerCase()){case"em":t=parseFloat(getComputedStyle(document.body).fontSize)*e;break;case"rem":t=parseFloat(getComputedStyle(document.documentElement).fontSize)*e;break;case"vh":t=window.innerHeight/e*100;break;default:t=e}this.lazyThreshold=window.innerHeight+parseInt(t,10)},_debounceBuild:function(){var e=this;this.debounce&&clearTimeout(this.debounce),this.debounce=setTimeout((function(){e._build()}),100)},_getDensity:function(){var e=CLDLB.dpr?CLDLB.dpr.replace("X",""):"off";if("off"===e)return 1;var t=this.density;return CLDLB.dpr_precise||"auto"===e||"auto"===t?"auto"===CLDLB.dpr&&"auto"!==t&&(t="auto"):t=t>Math.ceil(e)?e:t,t},_build:function(){var e=this;this.images.forEach((function(t){e.buildSize(t)}))},_shouldRebuild:function(e){var t=this.scaleSize(e.originalWidth,e.width,this.config.pixel_step),o=e.getBoundingClientRect(),r="auto"!==this.density?this._getDensity():1;return o.top<this.lazyThreshold&&(t>e.naturalWidth/r||!e.cld_loaded)},_shouldPlacehold:function(e){var t=this.scaleSize(e.originalWidth,e.width,this.config.pixel_step),o=e.getBoundingClientRect(),r="auto"!==this.density?this._getDensity():1;return e.dataset.placeholder&&!e.cld_loaded&&o.top<2*this.lazyThreshold&&(t>e.naturalWidth/r||!e.cld_placehold)},getResponsiveSteps:function(e){return Math.ceil(e.dataset.breakpoints?e.originalWidth/e.dataset.breakpoints:this.responsiveStep)},getQuality:function(){var e="q_auto";switch(navigator&&navigator.connection?navigator.connection.effectiveType:"none"){case"none":break;case"4g":e="q_auto:good";break;case"3g":e="q_auto:eco";break;case"2g":case"slow-2g":e="q_auto:low";break;default:e="q_auto:best"}return e},scaleSize:function(e,t,o){var r=e-o*Math.floor((e-t)/o);return(r>e||this.config.max_width<r)&&(r=e),r},buildSize:function(e){this._shouldRebuild(e)?e.dataset.srcset?e.srcset=e.dataset.srcset:e.src=this.getSizeURL(e):this._shouldPlacehold(e)&&(e.src=this.getPlaceholderURL(e))},getSizeURL:function(e){if(e.cld_loaded=!0,e.dataset.srcset)return e.srcset=e.dataset.srcset,delete e.dataset.srcset,"";var t=this.scaleSize(e.originalWidth,e.width,this.config.pixel_step),o=this._getDensity(),r="";return t&&(r+="w_"+t,1!==o&&(r+=",dpr_"+o)),e.dataset.src.replace("--size--",r).replace(/q_auto(?!:)/gi,this.getQuality())},getPlaceholderURL:function(e){e.cld_placehold=!0;var t=this.scaleSize(e.originalWidth,e.width,this.config.pixel_step),o=this._getDensity(),r="";return t&&(r+="w_"+t,1!==o&&(r+=",dpr_"+o)),e.dataset.placeholder.replace("--size--",r)}};window.addEventListener("load",(function(){n._init()}))},function(e,t,o){var r=o(0);e.exports=function(e){if(Array.isArray(e))return r(e)},e.exports.default=e.exports,e.exports.__esModule=!0},function(e,t){e.exports=function(e){if("undefined"!=typeof Symbol&&null!=e[Symbol.iterator]||null!=e["@@iterator"])return Array.from(e)},e.exports.default=e.exports,e.exports.__esModule=!0},function(e,t,o){var r=o(0);e.exports=function(e,t){if(e){if("string"==typeof e)return r(e,t);var o=Object.prototype.toString.call(e).slice(8,-1);return"Object"===o&&e.constructor&&(o=e.constructor.name),"Map"===o||"Set"===o?Array.from(e):"Arguments"===o||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(o)?r(e,t):void 0}},e.exports.default=e.exports,e.exports.__esModule=!0},function(e,t){e.exports=function(){throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")},e.exports.default=e.exports,e.exports.__esModule=!0}]);