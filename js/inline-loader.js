!function(e){var t={};function i(r){if(t[r])return t[r].exports;var n=t[r]={i:r,l:!1,exports:{}};return e[r].call(n.exports,n,n.exports,i),n.l=!0,n.exports}i.m=e,i.c=t,i.d=function(e,t,r){i.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:r})},i.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},i.t=function(e,t){if(1&t&&(e=i(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var r=Object.create(null);if(i.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var n in e)i.d(r,n,function(t){return e[t]}.bind(null,n));return r},i.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return i.d(t,"a",t),t},i.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},i.p="",i(i.s=0)}([function(e,t){window.Cloudinary_Inline_Loader={deviceDensity:window.devicePixelRatio?window.devicePixelRatio:"auto",density:null,images:[],config:CLDLB||{},lazyThreshold:0,enabled:!1,sizeBands:[],iObserver:null,pObserver:null,rObserver:null,bind:function(e){var t=this;if(!e.originalWidth){this.enabled||this._init();var i=e.dataset.size.split(" ");e.originalWidth=i[0],e.originalHeight=i[1],this.pObserver.observe(e),this.iObserver.observe(e),e.addEventListener("error",(function(i){e.src='data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg"><rect width="100%" height="100%" fill="rgba(0,0,0,0.1)"/><text x="50%" y="50%" fill="red" text-anchor="middle" dominant-baseline="middle">%26%23x26A0%3B︎</text></svg>',t.rObserver.unobserve(e)}))}},_init:function(){var e=this;this.enabled=!0,this._calcThreshold(),this._getDensity();for(var t=parseInt(this.config.max_width),i=parseInt(this.config.min_width),r=parseInt(this.config.pixel_step);t-r>=i;)t-=r,this.sizeBands.push(t);var n={rootMargin:this.lazyThreshold+"px 0px "+this.lazyThreshold+"px 0px"},a={rootMargin:2*this.lazyThreshold+"px 0px "+2*this.lazyThreshold+"px 0px"};this.rObserver=new ResizeObserver((function(t,i){t.forEach((function(t){t.target.cld_loaded&&t.contentRect.width>=t.target.cld_loaded&&(t.target.src=e.getSizeURL(t.target))}))})),this.iObserver=new IntersectionObserver((function(t,i){t.forEach((function(t){t.isIntersecting&&(t.target.dataset.srcset?(t.target.cld_loaded=!0,t.target.srcset=t.target.dataset.srcset):t.target.src=e.getSizeURL(t.target),i.unobserve(t.target),e.rObserver.observe(t.target))}))}),n),this.pObserver=new IntersectionObserver((function(t,i){t.forEach((function(t){t.isIntersecting&&(t.intersectionRatio<.5&&(t.target.src=e.getPlaceholderURL(t.target)),i.unobserve(t.target))}))}),a),this.enabled=!0},_calcThreshold:function(){var e=this.config.lazy_threshold.replace(/[^0-9]/g,""),t=0;switch(this.config.lazy_threshold.replace(/[0-9]/g,"").toLowerCase()){case"em":t=parseFloat(getComputedStyle(document.body).fontSize)*e;break;case"rem":t=parseFloat(getComputedStyle(document.documentElement).fontSize)*e;break;case"vh":t=window.innerHeight/e*100;break;default:t=e}this.lazyThreshold=parseInt(t,10)},_getDensity:function(){var e=this.config.dpr?this.config.dpr.replace("X",""):"off";if("off"===e)return this.density=1,1;var t=this.deviceDensity;"max"!==e&&"auto"!==t&&(e=parseFloat(e),t=t>Math.ceil(e)?e:t),this.density=t},scaleWidth:function(e){for(var t=e.width;-1===this.sizeBands.indexOf(t);)t++;return e.originalWidth<t&&(t=e.originalWidth),t},scaleSize:function(e,t){var i=(e.originalWidth/e.originalHeight).toFixed(3),r=(e.width/e.height).toFixed(3),n=this.scaleWidth(e),a=[];e.width!==e.originalWidth&&a.push(i===r?"c_scale":"c_fill,g_auto");var s=Math.round(n/r);return a.push("w_"+n),a.push("h_"+s),t&&1!==this.density&&a.push("dpr_"+this.density),e.cld_loaded=n,{transformation:a.join(","),nameExtension:n+"x"+s}},getSizeURL:function(e){var t=this.scaleSize(e,!0),i="auto"!==this.config.image_format&&"none"!==this.config.image_format?this.config.image_format:e.dataset.format,r=e.dataset.publicId.split("/").pop();return[this.config.base_url,"images",t.transformation,e.dataset.transformations,e.dataset.publicId,r+"-"+t.nameExtension+"."+i+"?_i=AA"].filter(this.empty).join("/")},getPlaceholderURL:function(e){e.cld_placehold=!0;var t=this.scaleSize(e,!1);return[this.config.base_url,"images",t.transformation,this.config.placeholder,e.dataset.publicId,"placeholder"].filter(this.empty).join("/")},empty:function(e){return 0!==e.length}}}]);