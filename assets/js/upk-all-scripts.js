;(function($, elementor){
'use strict';
'use strict';(function(){function h(c,b){if(!(c instanceof b))throw new TypeError("Cannot call a class as a function");}function u(c,b){for(var a=0;a<b.length;a++){var f=b[a];f.enumerable=f.enumerable||!1;f.configurable=!0;"value"in f&&(f.writable=!0);Object.defineProperty(c,f.key,f)}}function g(c,b,a){b&&u(c.prototype,b);a&&u(c,a);return c}function m(c,b){if("function"!==typeof b&&null!==b)throw new TypeError("Super expression must either be null or a function");c.prototype=Object.create(b&&b.prototype,
{constructor:{value:c,writable:!0,configurable:!0}});b&&r(c,b)}function k(c){k=Object.setPrototypeOf?Object.getPrototypeOf:function(b){return b.__proto__||Object.getPrototypeOf(b)};return k(c)}function r(c,b){r=Object.setPrototypeOf||function(a,b){a.__proto__=b;return a};return r(c,b)}function l(c){if(void 0===c)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return c}function n(c,b){return!b||"object"!==typeof b&&"function"!==typeof b?l(c):b}function p(c){if(Array.isArray(c)){var b=
0;for(var a=Array(c.length);b<c.length;b++)a[b]=c[b];b=a}else b=void 0;b||(b=Symbol.iterator in Object(c)||"[object Arguments]"===Object.prototype.toString.call(c)?Array.from(c):void 0);if(!(c=b))throw new TypeError("Invalid attempt to spread non-iterable instance");return c}var v=function(){function c(){h(this,c);this.handlers={}}g(c,[{key:"addEventListener",value:function(){var b=0<arguments.length&&void 0!==arguments[0]?arguments[0]:document,a=1<arguments.length&&void 0!==arguments[1]?arguments[1]:
"click";this.handlers[a]={func:2<arguments.length?arguments[2]:void 0,target:b};var f=a.split(".")[0];b.addEventListener(f,this.handlers[a].func)}},{key:"removeEventListener",value:function(){var b=0<arguments.length&&void 0!==arguments[0]?arguments[0]:"click",a=b.split(".")[0],f=this.handlers[b];f.target.removeEventListener(a,f.func);delete this.handlers[b]}},{key:"removeAll",value:function(){for(var b in this.handlers)this.removeEventListener(b)}}]);return c}(),w=function(){return"".concat(0<arguments.length&&
void 0!==arguments[0]?arguments[0]:"id","-").concat(Math.random().toString(36).substr(2,8))},e=function(){function c(){h(this,c);this.events=new v;this.callback=function(){};this.updateInstanceId()}g(c,[{key:"eventHandler",value:function(b,a){var f=this,d=a.share_url,c=a.windowTitle,q=a.windowWidth,h=a.windowHeight;b.preventDefault();a=Math.round((window.outerHeight||window.document.documentElement.offsetHeight)/2-h/2);var g=Math.round((window.outerWidth||window.document.documentElement.offsetWidth)/
2-q/2);q="width=".concat(q,",height=").concat(h);a="left=".concat(g,",top=").concat(a);var k="".concat(q,",").concat(a,",location=no,toolbar=no,menubar=no"),e=window.open(d,c,k),l=setInterval(function(){e.closed&&(f.callback(b,{share_url:d,windowTitle:c,windowOptions:k},e),clearInterval(l))},10);return e}},{key:"setShareCallback",value:function(b){this.callback=b}},{key:"createEvents",value:function(b){var a=this;p(b).forEach(function(b){var f=a.getPreparedData(b);a.events.addEventListener(b,"click.".concat(a.instanceId),
function(b){return a.eventHandler.call(a,b,f)})})}},{key:"getInstance",value:function(){"function"===typeof this.shareWindow&&this.shareWindow();"function"===typeof this.getCounter&&this.getCounter();return this}},{key:"updateInstanceId",value:function(){this.instanceId=w()}},{key:"reNewInstance",value:function(){this.events.removeAll();this.updateInstanceId();return this.getInstance()}}]);return c}(),t=function(c){function b(){var a=0<arguments.length&&void 0!==arguments[0]?arguments[0]:document.location.href,
f=1<arguments.length&&void 0!==arguments[1]?arguments[1]:document.title,d=2<arguments.length&&void 0!==arguments[2]?arguments[2]:document.querySelector('link[rel="apple-touch-icon"]');h(this,b);var c=n(this,k(b).call(this));c.url=encodeURIComponent(a);c.title=encodeURIComponent(f);c.image=d?encodeURIComponent(d.href):"";c.createEvents=c.createEvents.bind(l(c));return c}m(b,c);g(b,[{key:"getPreparedData",value:function(a){var b=a.dataset.url?encodeURIComponent(a.dataset.url):this.url,d=a.dataset.title?
encodeURIComponent(a.dataset.title):this.title;a=a.dataset.image?encodeURIComponent(a.dataset.image):this.image;b="https://vk.com/share.php?url=".concat(b,"&title=").concat(d,"&image=").concat(a);return{callback:this.callback,share_url:b,windowTitle:"Share this",windowWidth:640,windowHeight:480}}},{key:"shareWindow",value:function(){var a=document.querySelectorAll('[data-social="vkontakte"]');return this.createEvents(a)}},{key:"getCounter",value:function(){var a=document.createElement("script"),b=
document.querySelectorAll('[data-counter="vkontakte"]'),d="https://vk.com/share.php?act=count&index=1&url=".concat(this.url);window.VK=Object.assign({},{Share:{}},window.VK);0<b.length&&(window.VK.Share.count=function(f,d){p(b).forEach(function(a){a.innerHTML=d});null!==a.parentNode&&a.parentNode.removeChild(a)},a.src=d,document.body.appendChild(a))}}]);return b}(e),x=function(c){function b(){var a=0<arguments.length&&void 0!==arguments[0]?arguments[0]:document.location.href,f=1<arguments.length&&
void 0!==arguments[1]?arguments[1]:document.title;h(this,b);var d=n(this,k(b).call(this));d.url=encodeURIComponent(a);d.title=encodeURIComponent(f);d.createEvents=d.createEvents.bind(l(d));return d}m(b,c);g(b,[{key:"getPreparedData",value:function(a){var b=a.dataset.url?encodeURIComponent(a.dataset.url):this.url;a=a.dataset.title?encodeURIComponent(a.dataset.title):this.title;b="https://facebook.com/sharer/sharer.php?u=".concat(b,"&t=").concat(a);return{callback:this.callback,share_url:b,windowTitle:"Share this",
windowWidth:640,windowHeight:480}}},{key:"shareWindow",value:function(){var a=document.querySelectorAll('[data-social="facebook"]');return this.createEvents(a)}},{key:"getCounter",value:function(){var a=document.createElement("script"),b=("goodshare_"+Math.random()).replace(".",""),d=document.querySelectorAll('[data-counter="facebook"]'),c="https://graph.facebook.com/?id=".concat(this.url,"&callback=").concat(b);0<d.length&&(window[b]=function(b){p(d).forEach(function(a){a.innerHTML=b.share?b.share.share_count:
0});null!==a.parentNode&&a.parentNode.removeChild(a)},a.src=c,document.body.appendChild(a))}}]);return b}(e),y=function(c){function b(){var a=0<arguments.length&&void 0!==arguments[0]?arguments[0]:document.location.href,f=1<arguments.length&&void 0!==arguments[1]?arguments[1]:document.title;h(this,b);var d=n(this,k(b).call(this));d.url=encodeURIComponent(a);d.title=encodeURIComponent(f);d.createEvents=d.createEvents.bind(l(d));return d}m(b,c);g(b,[{key:"getPreparedData",value:function(a){var b=a.dataset.url?
encodeURIComponent(a.dataset.url):this.url;a=a.dataset.title?encodeURIComponent(a.dataset.title):this.title;b="https://connect.ok.ru/offer?url=".concat(b,"&title=").concat(a);return{callback:this.callback,share_url:b,windowTitle:"Share this",windowWidth:640,windowHeight:480}}},{key:"shareWindow",value:function(){var a=document.querySelectorAll('[data-social="odnoklassniki"]');return this.createEvents(a)}},{key:"getCounter",value:function(){var a=document.createElement("script"),b=document.querySelectorAll('[data-counter="odnoklassniki"]'),
d="https://connect.ok.ru/dk?st.cmd=extLike&uid=1&ref=".concat(this.url);window.ODKL={};0<b.length&&(window.ODKL.updateCount=function(f,d){p(b).forEach(function(a){a.innerHTML=d});null!==a.parentNode&&a.parentNode.removeChild(a)},a.src=d,document.body.appendChild(a))}}]);return b}(e),z=function(c){function b(){var a=0<arguments.length&&void 0!==arguments[0]?arguments[0]:document.location.href,f=1<arguments.length&&void 0!==arguments[1]?arguments[1]:document.title,d=2<arguments.length&&void 0!==arguments[2]?
arguments[2]:document.querySelector('meta[name="description"]'),c=3<arguments.length&&void 0!==arguments[3]?arguments[3]:document.querySelector('link[rel="apple-touch-icon"]');h(this,b);var e=n(this,k(b).call(this));e.url=encodeURIComponent(a);e.title=encodeURIComponent(f);e.description=d?encodeURIComponent(d.content):"";e.image=c?encodeURIComponent(c.href):"";e.createEvents=e.createEvents.bind(l(e));return e}m(b,c);g(b,[{key:"getPreparedData",value:function(a){var b=a.dataset.url?encodeURIComponent(a.dataset.url):
this.url,d=a.dataset.title?encodeURIComponent(a.dataset.title):this.title,c=a.dataset.description?encodeURIComponent(a.dataset.description):this.description;a=a.dataset.image?encodeURIComponent(a.dataset.image):this.image;b="https://connect.mail.ru/share?url=".concat(b,"&title=").concat(d,"&description=").concat(c,"&imageurl=").concat(a);return{callback:this.callback,share_url:b,windowTitle:"Share this",windowWidth:640,windowHeight:480}}},{key:"shareWindow",value:function(){var a=document.querySelectorAll('[data-social="moimir"]');
return this.createEvents(a)}},{key:"getCounter",value:function(){var a=document.createElement("script"),b=encodeURIComponent(this.url.replace(/^.*?:\/\//,"")),d=("goodshare_"+Math.random()).replace(".",""),c=document.querySelectorAll('[data-counter="moimir"]');0<c.length&&(window[d]=function(b){p(c).forEach(function(a){a.innerHTML=b.share_mm});null!==a.parentNode&&a.parentNode.removeChild(a)},a.src="https://appsmail.ru/share/count/"+b+"?callback="+d,document.body.appendChild(a))}}]);return b}(e),
A=function(c){function b(){var a=0<arguments.length&&void 0!==arguments[0]?arguments[0]:document.location.href,f=1<arguments.length&&void 0!==arguments[1]?arguments[1]:document.title,d=2<arguments.length&&void 0!==arguments[2]?arguments[2]:document.querySelector('meta[name="description"]');h(this,b);var c=n(this,k(b).call(this));c.url=encodeURIComponent(a);c.title=encodeURIComponent(f);c.description=d?encodeURIComponent(d.content):"";c.createEvents=c.createEvents.bind(l(c));return c}m(b,c);g(b,[{key:"getPreparedData",
value:function(a){var b=a.dataset.url?encodeURIComponent(a.dataset.url):this.url,d=a.dataset.title?encodeURIComponent(a.dataset.title):this.title;a=a.dataset.description?encodeURIComponent(a.dataset.description):this.description;b="https://www.tumblr.com/widgets/share/tool?canonicalUrl=".concat(b,"&title=").concat(d,"&caption=").concat(a,"&posttype=link");return{callback:this.callback,share_url:b,windowTitle:"Share this",windowWidth:640,windowHeight:480}}},{key:"shareWindow",value:function(){var a=
document.querySelectorAll('[data-social="tumblr"]');return this.createEvents(a)}},{key:"getCounter",value:function(){var a=document.createElement("script"),b=("goodshare_"+Math.random()).replace(".",""),d=document.querySelectorAll('[data-counter="tumblr"]'),c="https://api.tumblr.com/v2/share/stats?url=".concat(this.url,"&callback=").concat(b);0<d.length&&(window[b]=function(b){p(d).forEach(function(a){a.innerHTML=b.response.note_count});null!==a.parentNode&&a.parentNode.removeChild(a)},a.src=c,document.body.appendChild(a))}}]);
return b}(e),B=function(c){function b(){var a=0<arguments.length&&void 0!==arguments[0]?arguments[0]:document.location.href,f=1<arguments.length&&void 0!==arguments[1]?arguments[1]:document.querySelector('meta[name="description"]'),d=2<arguments.length&&void 0!==arguments[2]?arguments[2]:document.querySelector('link[rel="apple-touch-icon"]');h(this,b);var c=n(this,k(b).call(this));c.url=encodeURIComponent(a);c.description=f?encodeURIComponent(f.content):"";c.image=d?encodeURIComponent(d.href):"";
c.createEvents=c.createEvents.bind(l(c));return c}m(b,c);g(b,[{key:"getPreparedData",value:function(a){var b=a.dataset.url?encodeURIComponent(a.dataset.url):this.url,d=a.dataset.description?encodeURIComponent(a.dataset.description):this.description;a=a.dataset.image?encodeURIComponent(a.dataset.image):this.image;b="https://www.pinterest.com/pin/create/button/?url=".concat(b,"&description=").concat(d,"&media=").concat(a);return{callback:this.callback,share_url:b,windowTitle:"Share this",windowWidth:640,
windowHeight:480}}},{key:"shareWindow",value:function(){var a=document.querySelectorAll('[data-social="pinterest"]');return this.createEvents(a)}},{key:"getCounter",value:function(){var a=document.createElement("script"),b=("goodshare_"+Math.random()).replace(".",""),d=document.querySelectorAll('[data-counter="pinterest"]'),c="https://api.pinterest.com/v1/urls/count.json?url=".concat(this.url,"&callback=").concat(b);0<d.length&&(window[b]=function(b){p(d).forEach(function(a){a.innerHTML=0<b.length?
b.count:0});null!==a.parentNode&&a.parentNode.removeChild(a)},a.src=c,document.body.appendChild(a))}}]);return b}(e),C=function(c){function b(){var a=0<arguments.length&&void 0!==arguments[0]?arguments[0]:document.location.href,f=1<arguments.length&&void 0!==arguments[1]?arguments[1]:document.title;h(this,b);var d=n(this,k(b).call(this));d.url=encodeURIComponent(a);d.title=encodeURIComponent(f);d.createEvents=d.createEvents.bind(l(d));return d}m(b,c);g(b,[{key:"getPreparedData",value:function(a){var b=
a.dataset.url?encodeURIComponent(a.dataset.url):this.url;a=a.dataset.title?encodeURIComponent(a.dataset.title):this.title;b="https://reddit.com/submit?url=".concat(b,"&title=").concat(a);return{callback:this.callback,share_url:b,windowTitle:"Share this",windowWidth:640,windowHeight:480}}},{key:"shareWindow",value:function(){var a=document.querySelectorAll('[data-social="reddit"]');return this.createEvents(a)}}]);return b}(e),D=function(c){function b(){var a=0<arguments.length&&void 0!==arguments[0]?
arguments[0]:document.location.href,f=1<arguments.length&&void 0!==arguments[1]?arguments[1]:document.title;h(this,b);var d=n(this,k(b).call(this));d.url=encodeURIComponent(a);d.title=encodeURIComponent(f);d.createEvents=d.createEvents.bind(l(d));return d}m(b,c);g(b,[{key:"getPreparedData",value:function(a){var b=a.dataset.url?encodeURIComponent(a.dataset.url):this.url;a=a.dataset.title?encodeURIComponent(a.dataset.title):this.title;b="https://buffer.com/add?url=".concat(b,"&text=").concat(a);return{callback:this.callback,
share_url:b,windowTitle:"Share this",windowWidth:640,windowHeight:480}}},{key:"shareWindow",value:function(){var a=document.querySelectorAll('[data-social="buffer"]');return this.createEvents(a)}},{key:"getCounter",value:function(){var a=document.createElement("script"),b=("goodshare_"+Math.random()).replace(".",""),d=document.querySelectorAll('[data-counter="buffer"]'),c="https://api.bufferapp.com/1/links/shares.json?url=".concat(this.url,"&callback=").concat(b);0<d.length&&(window[b]=function(b){p(d).forEach(function(a){a.innerHTML=
b?b.shares:0});a.parentNode.removeChild(a)},a.src=c,document.body.appendChild(a))}}]);return b}(e),E=function(c){function b(){var a=0<arguments.length&&void 0!==arguments[0]?arguments[0]:document.location.href,f=1<arguments.length&&void 0!==arguments[1]?arguments[1]:document.title;h(this,b);var d=n(this,k(b).call(this));d.url=encodeURIComponent(a);d.title=encodeURIComponent(f);d.createEvents=d.createEvents.bind(l(d));return d}m(b,c);g(b,[{key:"getPreparedData",value:function(a){var b=a.dataset.url?
encodeURIComponent(a.dataset.url):this.url;a=a.dataset.title?encodeURIComponent(a.dataset.title):this.title;b="https://twitter.com/share?url=".concat(b,"&text=").concat(a);return{callback:this.callback,share_url:b,windowTitle:"Share this",windowWidth:640,windowHeight:480}}},{key:"shareWindow",value:function(){var a=document.querySelectorAll('[data-social="twitter"]');return this.createEvents(a)}}]);return b}(e),F=function(c){function b(){var a=0<arguments.length&&void 0!==arguments[0]?arguments[0]:
document.location.href,f=1<arguments.length&&void 0!==arguments[1]?arguments[1]:document.title;h(this,b);var d=n(this,k(b).call(this));d.url=encodeURIComponent(a);d.title=encodeURIComponent(f);d.createEvents=d.createEvents.bind(l(d));return d}m(b,c);g(b,[{key:"getPreparedData",value:function(a){var b=a.dataset.url?encodeURIComponent(a.dataset.url):this.url;a=a.dataset.title?encodeURIComponent(a.dataset.title):this.title;b="https://livejournal.com/update.bml?event=".concat(b,"&subject=").concat(a);
return{callback:this.callback,share_url:b,windowTitle:"Share this",windowWidth:640,windowHeight:480}}},{key:"shareWindow",value:function(){var a=document.querySelectorAll('[data-social="livejournal"]');return this.createEvents(a)}}]);return b}(e),G=function(c){function b(){var a=0<arguments.length&&void 0!==arguments[0]?arguments[0]:document.location.href,f=1<arguments.length&&void 0!==arguments[1]?arguments[1]:document.title,d=2<arguments.length&&void 0!==arguments[2]?arguments[2]:document.querySelector('meta[name="description"]');
h(this,b);var c=n(this,k(b).call(this));c.url=encodeURIComponent(a);c.title=encodeURIComponent(f);c.description=d?encodeURIComponent(d.content):"";c.createEvents=c.createEvents.bind(l(c));return c}m(b,c);g(b,[{key:"getPreparedData",value:function(a){var b=a.dataset.url?encodeURIComponent(a.dataset.url):this.url,d=a.dataset.title?encodeURIComponent(a.dataset.title):this.title;a=a.dataset.description?encodeURIComponent(a.dataset.description):this.description;b="https://www.linkedin.com/shareArticle?url=".concat(b,
"&text=").concat(d,"&summary=").concat(a,"&mini=true");return{callback:this.callback,share_url:b,windowTitle:"Share this",windowWidth:640,windowHeight:480}}},{key:"shareWindow",value:function(){var a=document.querySelectorAll('[data-social="linkedin"]');return this.createEvents(a)}}]);return b}(e),H=function(c){function b(){var a=0<arguments.length&&void 0!==arguments[0]?arguments[0]:document.location.href,f=1<arguments.length&&void 0!==arguments[1]?arguments[1]:document.title,d=2<arguments.length&&
void 0!==arguments[2]?arguments[2]:document.querySelector('meta[name="description"]');h(this,b);var c=n(this,k(b).call(this));c.url=encodeURIComponent(a);c.title=encodeURIComponent(f);c.description=d?encodeURIComponent(d.content):"";c.createEvents=c.createEvents.bind(l(c));return c}m(b,c);g(b,[{key:"getPreparedData",value:function(a){var b=a.dataset.url?encodeURIComponent(a.dataset.url):this.url,d=a.dataset.title?encodeURIComponent(a.dataset.title):this.title;a=a.dataset.description?encodeURIComponent(a.dataset.description):
this.description;b="https://www.evernote.com/clip.action?url=".concat(b,"&title=").concat(d,"&body=").concat(a);return{callback:this.callback,share_url:b,windowTitle:"Share this",windowWidth:640,windowHeight:480}}},{key:"shareWindow",value:function(){var a=document.querySelectorAll('[data-social="evernote"]');return this.createEvents(a)}}]);return b}(e),I=function(c){function b(){var a=0<arguments.length&&void 0!==arguments[0]?arguments[0]:document.location.href,f=1<arguments.length&&void 0!==arguments[1]?
arguments[1]:document.title;h(this,b);var d=n(this,k(b).call(this));d.url=encodeURIComponent(a);d.title=encodeURIComponent(f);d.createEvents=d.createEvents.bind(l(d));return d}m(b,c);g(b,[{key:"getPreparedData",value:function(a){var b=a.dataset.url?encodeURIComponent(a.dataset.url):this.url;a=a.dataset.title?encodeURIComponent(a.dataset.title):this.title;b="https://del.icio.us/save?url=".concat(b,"&title=").concat(a);return{callback:this.callback,share_url:b,windowTitle:"Share this",windowWidth:640,
windowHeight:480}}},{key:"shareWindow",value:function(){var a=document.querySelectorAll('[data-social="delicious"]');return this.createEvents(a)}}]);return b}(e),J=function(c){function b(){var a=0<arguments.length&&void 0!==arguments[0]?arguments[0]:document.location.href,f=1<arguments.length&&void 0!==arguments[1]?arguments[1]:document.title;h(this,b);var d=n(this,k(b).call(this));d.url=encodeURIComponent(a);d.title=encodeURIComponent(f);d.createEvents=d.createEvents.bind(l(d));return d}m(b,c);g(b,
[{key:"getPreparedData",value:function(a){var b=a.dataset.url?encodeURIComponent(a.dataset.url):this.url;a=a.dataset.title?encodeURIComponent(a.dataset.title):this.title;b="https://share.flipboard.com/bookmarklet/popout?ext=sharethis&title=".concat(a,"&url=").concat(b,"&v=2");return{callback:this.callback,share_url:b,windowTitle:"Share this",windowWidth:640,windowHeight:480}}},{key:"shareWindow",value:function(){var a=document.querySelectorAll('[data-social="flipboard"]');return this.createEvents(a)}}]);
return b}(e),K=function(c){function b(){var a=0<arguments.length&&void 0!==arguments[0]?arguments[0]:document.location.href;h(this,b);var f=n(this,k(b).call(this));f.url=encodeURIComponent(a);f.createEvents=f.createEvents.bind(l(f));return f}m(b,c);g(b,[{key:"getPreparedData",value:function(a){a=a.dataset.url?encodeURIComponent(a.dataset.url):this.url;a="https://mix.com/mixit?su=submit&url=".concat(a);return{callback:this.callback,share_url:a,windowTitle:"Share this",windowWidth:640,windowHeight:480}}},
{key:"shareWindow",value:function(){var a=document.querySelectorAll('[data-social="mix"]');return this.createEvents(a)}}]);return b}(e),L=function(c){function b(){var a=0<arguments.length&&void 0!==arguments[0]?arguments[0]:document.location.href;h(this,b);var f=n(this,k(b).call(this));f.url=encodeURIComponent(a);f.createEvents=f.createEvents.bind(l(f));return f}m(b,c);g(b,[{key:"getPreparedData",value:function(a){a=a.dataset.url?encodeURIComponent(a.dataset.url):this.url;a="https://www.meneame.net/submit?url=".concat(a);
return{callback:this.callback,share_url:a,windowTitle:"Share this",windowWidth:640,windowHeight:480}}},{key:"shareWindow",value:function(){var a=document.querySelectorAll('[data-social="meneame"]');return this.createEvents(a)}}]);return b}(e),M=function(c){function b(){var a=0<arguments.length&&void 0!==arguments[0]?arguments[0]:document.location.href,f=1<arguments.length&&void 0!==arguments[1]?arguments[1]:document.title;h(this,b);var d=n(this,k(b).call(this));d.url=encodeURIComponent(a);d.title=
encodeURIComponent(f);d.createEvents=d.createEvents.bind(l(d));return d}m(b,c);g(b,[{key:"getPreparedData",value:function(a){var b=a.dataset.url?encodeURIComponent(a.dataset.url):this.url;a=a.dataset.title?encodeURIComponent(a.dataset.title):this.title;b="https://www.blogger.com/blog-this.g?u=".concat(b,"&n=").concat(a);return{callback:this.callback,share_url:b,windowTitle:"Share this",windowWidth:640,windowHeight:480}}},{key:"shareWindow",value:function(){var a=document.querySelectorAll('[data-social="blogger"]');
return this.createEvents(a)}}]);return b}(e),N=function(c){function b(){var a=0<arguments.length&&void 0!==arguments[0]?arguments[0]:document.location.href,c=1<arguments.length&&void 0!==arguments[1]?arguments[1]:document.title;h(this,b);var d=n(this,k(b).call(this));d.url=encodeURIComponent(a);d.title=encodeURIComponent(c);d.createEvents=d.createEvents.bind(l(d));return d}m(b,c);g(b,[{key:"getPreparedData",value:function(a){var b=a.dataset.url?encodeURIComponent(a.dataset.url):this.url;a=a.dataset.title?
encodeURIComponent(a.dataset.title):this.title;b="https://getpocket.com/save?url=".concat(b,"&title=").concat(a);return{callback:this.callback,share_url:b,windowTitle:"Share this",windowWidth:640,windowHeight:480}}},{key:"shareWindow",value:function(){var a=document.querySelectorAll('[data-social="pocket"]');return this.createEvents(a)}}]);return b}(e),O=function(c){function b(){var a=0<arguments.length&&void 0!==arguments[0]?arguments[0]:document.location.href,c=1<arguments.length&&void 0!==arguments[1]?
arguments[1]:document.title;h(this,b);var d=n(this,k(b).call(this));d.url=encodeURIComponent(a);d.title=encodeURIComponent(c);d.createEvents=d.createEvents.bind(l(d));return d}m(b,c);g(b,[{key:"getPreparedData",value:function(a){var b=a.dataset.url?encodeURIComponent(a.dataset.url):this.url;a=a.dataset.title?encodeURIComponent(a.dataset.title):this.title;b="https://www.instapaper.com/edit?url=".concat(b,"&title=").concat(a);return{callback:this.callback,share_url:b,windowTitle:"Share this",windowWidth:640,
windowHeight:480}}},{key:"shareWindow",value:function(){var a=document.querySelectorAll('[data-social="instapaper"]');return this.createEvents(a)}}]);return b}(e),P=function(c){function b(){var a=0<arguments.length&&void 0!==arguments[0]?arguments[0]:document.location.href,c=1<arguments.length&&void 0!==arguments[1]?arguments[1]:document.title;h(this,b);var d=n(this,k(b).call(this));d.url=encodeURIComponent(a);d.title=encodeURIComponent(c);d.createEvents=d.createEvents.bind(l(d));return d}m(b,c);
g(b,[{key:"getPreparedData",value:function(a){var b=a.dataset.url?encodeURIComponent(a.dataset.url):this.url;a=a.dataset.title?encodeURIComponent(a.dataset.title):this.title;b="https://digg.com/submit?url=".concat(b,"&title=").concat(a);return{callback:this.callback,share_url:b,windowTitle:"Share this",windowWidth:640,windowHeight:480}}},{key:"shareWindow",value:function(){var a=document.querySelectorAll('[data-social="digg"]');return this.createEvents(a)}}]);return b}(e),Q=function(c){function b(){var a=
0<arguments.length&&void 0!==arguments[0]?arguments[0]:document.location.href,c=1<arguments.length&&void 0!==arguments[1]?arguments[1]:document.title;h(this,b);var d=n(this,k(b).call(this));d.url=encodeURIComponent(a);d.title=encodeURIComponent(c);d.createEvents=d.createEvents.bind(l(d));return d}m(b,c);g(b,[{key:"getPreparedData",value:function(a){var b=a.dataset.url?encodeURIComponent(a.dataset.url):this.url;a=a.dataset.title?encodeURIComponent(a.dataset.title):this.title;b="http://www.liveinternet.ru/journal_post.php?action=n_add&cnurl=".concat(b,
"&cntitle=").concat(a);return{callback:this.callback,share_url:b,windowTitle:"Share this",windowWidth:640,windowHeight:480}}},{key:"shareWindow",value:function(){var a=document.querySelectorAll('[data-social="liveinternet"]');return this.createEvents(a)}}]);return b}(e),R=function(c){function b(){var a=0<arguments.length&&void 0!==arguments[0]?arguments[0]:document.location.href,c=1<arguments.length&&void 0!==arguments[1]?arguments[1]:document.title,d=2<arguments.length&&void 0!==arguments[2]?arguments[2]:
document.querySelector('meta[name="description"]');h(this,b);var e=n(this,k(b).call(this));e.url=encodeURIComponent(a);e.title=encodeURIComponent(c);e.description=d?encodeURIComponent(d.content):"";e.createEvents=e.createEvents.bind(l(e));return e}m(b,c);g(b,[{key:"getPreparedData",value:function(a){var b=a.dataset.url?encodeURIComponent(a.dataset.url):this.url,c=a.dataset.title?encodeURIComponent(a.dataset.title):this.title;a=a.dataset.description?encodeURIComponent(a.dataset.description):this.description;
b="https://surfingbird.ru/share?url=".concat(b,"&title=").concat(c,"&description=").concat(a);return{callback:this.callback,share_url:b,windowTitle:"Share this",windowWidth:640,windowHeight:480}}},{key:"shareWindow",value:function(){var a=document.querySelectorAll('[data-social="surfingbird"]');return this.createEvents(a)}}]);return b}(e),S=function(c){function b(){var a=0<arguments.length&&void 0!==arguments[0]?arguments[0]:document.location.href;h(this,b);var c=n(this,k(b).call(this));c.url=encodeURIComponent(a);
c.createEvents=c.createEvents.bind(l(c));return c}m(b,c);g(b,[{key:"getPreparedData",value:function(a){a=a.dataset.url?encodeURIComponent(a.dataset.url):this.url;a="https://www.xing.com/spi/shares/new?url=".concat(a);return{callback:this.callback,share_url:a,windowTitle:"Share this",windowWidth:640,windowHeight:480}}},{key:"shareWindow",value:function(){var a=document.querySelectorAll('[data-social="xing"]');return this.createEvents(a)}}]);return b}(e),T=function(c){function b(){var a=0<arguments.length&&
void 0!==arguments[0]?arguments[0]:document.location.href,c=1<arguments.length&&void 0!==arguments[1]?arguments[1]:document.title,d=2<arguments.length&&void 0!==arguments[2]?arguments[2]:document.querySelector('meta[name="description"]'),e=3<arguments.length&&void 0!==arguments[3]?arguments[3]:document.querySelector('link[rel="apple-touch-icon"]');h(this,b);var g=n(this,k(b).call(this));g.url=encodeURIComponent(a);g.title=encodeURIComponent(c);g.description=d?encodeURIComponent(d.content):"";g.image=
e?encodeURIComponent(e.href):"";g.createEvents=g.createEvents.bind(l(g));return g}m(b,c);g(b,[{key:"getPreparedData",value:function(a){var b=a.dataset.url?encodeURIComponent(a.dataset.url):this.url,c=a.dataset.title?encodeURIComponent(a.dataset.title):this.title,e=a.dataset.description?encodeURIComponent(a.dataset.description):this.description;a=a.dataset.image?encodeURIComponent(a.dataset.image):this.image;b="https://wordpress.com/wp-admin/press-this.php?u=".concat(b,"&t=").concat(c,"&s=").concat(e,
"&i=").concat(a,"&v=2");return{callback:this.callback,share_url:b,windowTitle:"Share this",windowWidth:640,windowHeight:480}}},{key:"shareWindow",value:function(){var a=document.querySelectorAll('[data-social="wordpress"]');return this.createEvents(a)}}]);return b}(e),U=function(c){function b(){var a=0<arguments.length&&void 0!==arguments[0]?arguments[0]:document.location.href,c=1<arguments.length&&void 0!==arguments[1]?arguments[1]:document.title,d=2<arguments.length&&void 0!==arguments[2]?arguments[2]:
document.querySelector('meta[name="description"]');h(this,b);var e=n(this,k(b).call(this));e.url=encodeURIComponent(a);e.title=encodeURIComponent(c);e.description=d?encodeURIComponent(d.content):"";e.createEvents=e.createEvents.bind(l(e));return e}m(b,c);g(b,[{key:"getPreparedData",value:function(a){var b=a.dataset.url?encodeURIComponent(a.dataset.url):this.url,c=a.dataset.title?encodeURIComponent(a.dataset.title):this.title;a=a.dataset.description?encodeURIComponent(a.dataset.description):this.description;
b="https://cang.baidu.com/do/add?iu=".concat(b,"&it=").concat(c,"&dc=").concat(a,"&fr=ien");return{callback:this.callback,share_url:b,windowTitle:"Share this",windowWidth:640,windowHeight:480}}},{key:"shareWindow",value:function(){var a=document.querySelectorAll('[data-social="baidu"]');return this.createEvents(a)}}]);return b}(e),V=function(c){function b(){var a=0<arguments.length&&void 0!==arguments[0]?arguments[0]:document.location.href,c=1<arguments.length&&void 0!==arguments[1]?arguments[1]:
document.title;h(this,b);var d=n(this,k(b).call(this));d.url=encodeURIComponent(a);d.title=encodeURIComponent(c);d.createEvents=d.createEvents.bind(l(d));return d}m(b,c);g(b,[{key:"getPreparedData",value:function(a){var b=a.dataset.url?encodeURIComponent(a.dataset.url):this.url;a=a.dataset.title?encodeURIComponent(a.dataset.title):this.title;b="http://share.renren.com/share/buttonshare.do?url=".concat(b,"&title=").concat(a);return{callback:this.callback,share_url:b,windowTitle:"Share this",windowWidth:640,
windowHeight:480}}},{key:"shareWindow",value:function(){var a=document.querySelectorAll('[data-social="renren"]');return this.createEvents(a)}}]);return b}(e),W=function(c){function b(){var a=0<arguments.length&&void 0!==arguments[0]?arguments[0]:document.location.href,c=1<arguments.length&&void 0!==arguments[1]?arguments[1]:document.title;h(this,b);var d=n(this,k(b).call(this));d.url=encodeURIComponent(a);d.title=encodeURIComponent(c);d.createEvents=d.createEvents.bind(l(d));return d}m(b,c);g(b,
[{key:"getPreparedData",value:function(a){var b=a.dataset.url?encodeURIComponent(a.dataset.url):this.url;a=a.dataset.title?encodeURIComponent(a.dataset.title):this.title;b="https://service.weibo.com/share/share.php?url=".concat(b,"&title=").concat(a);return{callback:this.callback,share_url:b,windowTitle:"Share this",windowWidth:640,windowHeight:480}}},{key:"shareWindow",value:function(){var a=document.querySelectorAll('[data-social="weibo"]');return this.createEvents(a)}}]);return b}(e),X=function(c){function b(){var a=
0<arguments.length&&void 0!==arguments[0]?arguments[0]:document.location.href;h(this,b);var c=n(this,k(b).call(this));c.url=encodeURIComponent(a);c.createEvents=c.createEvents.bind(l(c));return c}m(b,c);g(b,[{key:"getPreparedData",value:function(a){a=a.dataset.url?encodeURIComponent(a.dataset.url):this.url;a="sms:?&body=".concat(a);return{callback:this.callback,share_url:a,windowTitle:"Share this",windowWidth:640,windowHeight:480}}},{key:"shareWindow",value:function(){var a=document.querySelectorAll('[data-social="sms"]');
return this.createEvents(a)}}]);return b}(e),Y=function(c){function b(){var a=0<arguments.length&&void 0!==arguments[0]?arguments[0]:document.location.href;h(this,b);var c=n(this,k(b).call(this));c.url=encodeURIComponent(a);c.createEvents=c.createEvents.bind(l(c));return c}m(b,c);g(b,[{key:"getPreparedData",value:function(a){a=a.dataset.url?encodeURIComponent(a.dataset.url):this.url;a="https://web.skype.com/share?".concat(a);return{callback:this.callback,share_url:a,windowTitle:"Share this",windowWidth:640,
windowHeight:480}}},{key:"shareWindow",value:function(){var a=document.querySelectorAll('[data-social="skype"]');return this.createEvents(a)}}]);return b}(e),Z=function(c){function b(){var a=0<arguments.length&&void 0!==arguments[0]?arguments[0]:document.location.href;h(this,b);var c=n(this,k(b).call(this));c.url=encodeURIComponent(a);c.createEvents=c.createEvents.bind(l(c));return c}m(b,c);g(b,[{key:"getPreparedData",value:function(a){var b=a.dataset.url?encodeURIComponent(a.dataset.url):this.url;
a=a.dataset.rhash?a.dataset.rhash:null;var c="https://t.me/share/url?url=".concat(b);null!==a&&(c="https://t.me/iv?url=".concat(b,"&rhash=").concat(a));return{callback:this.callback,share_url:c,windowTitle:"Share this",windowWidth:640,windowHeight:480}}},{key:"shareWindow",value:function(){var a=document.querySelectorAll('[data-social="telegram"]');return this.createEvents(a)}}]);return b}(e),aa=function(c){function b(){var a=0<arguments.length&&void 0!==arguments[0]?arguments[0]:document.location.href;
h(this,b);var c=n(this,k(b).call(this));c.url=encodeURIComponent(a);c.createEvents=c.createEvents.bind(l(c));return c}m(b,c);g(b,[{key:"getPreparedData",value:function(a){a=a.dataset.url?encodeURIComponent(a.dataset.url):this.url;a="viber://forward?text=".concat(a);return{callback:this.callback,share_url:a,windowTitle:"Share this",windowWidth:640,windowHeight:480}}},{key:"shareWindow",value:function(){var a=document.querySelectorAll('[data-social="viber"]');return this.createEvents(a)}}]);return b}(e),
ba=function(c){function b(){var a=0<arguments.length&&void 0!==arguments[0]?arguments[0]:document.location.href;h(this,b);var c=n(this,k(b).call(this));c.url=encodeURIComponent(a);c.createEvents=c.createEvents.bind(l(c));return c}m(b,c);g(b,[{key:"getPreparedData",value:function(a){a=a.dataset.url?encodeURIComponent(a.dataset.url):this.url;a="https://wa.me/?text=".concat(a);return{callback:this.callback,share_url:a,windowTitle:"Share this",windowWidth:640,windowHeight:480}}},{key:"shareWindow",value:function(){var a=
document.querySelectorAll('[data-social="whatsapp"]');return this.createEvents(a)}}]);return b}(e),ca=function(c){function b(){var a=0<arguments.length&&void 0!==arguments[0]?arguments[0]:document.location.href;h(this,b);var c=n(this,k(b).call(this));c.url=encodeURIComponent(a);c.createEvents=c.createEvents.bind(l(c));return c}m(b,c);g(b,[{key:"getPreparedData",value:function(a){a=a.dataset.url?encodeURIComponent(a.dataset.url):this.url;a="https://chart.apis.google.com/chart?cht=qr&chs=196x196&chld=Q%7C0&chl=".concat(a);
return{callback:this.callback,share_url:a,windowTitle:"Share this",windowWidth:640,windowHeight:480}}},{key:"shareWindow",value:function(){var a=document.querySelectorAll('[data-social="wechat"]');return this.createEvents(a)}}]);return b}(e);e=function(c){function b(){var a=0<arguments.length&&void 0!==arguments[0]?arguments[0]:document.location.href;h(this,b);var c=n(this,k(b).call(this));c.url=encodeURIComponent(a);c.createEvents=c.createEvents.bind(l(c));return c}m(b,c);g(b,[{key:"getPreparedData",
value:function(a){a=a.dataset.url?encodeURIComponent(a.dataset.url):this.url;a="line://msg/text/".concat(a);return{callback:this.callback,share_url:a,windowTitle:"Share this",windowWidth:640,windowHeight:480}}},{key:"shareWindow",value:function(){var a=document.querySelectorAll('[data-social="line"]');return this.createEvents(a)}}]);return b}(e);var da=[t,x,y,z,G,A,B,C,D,E,F,H,I,J,N,K,L,M,O,P,Q,R,S,T,U,V,W,X,Y,Z,aa,ba,ca,e];t=function(){function c(){h(this,c);this.providers=da;this.getProviders()}
g(c,[{key:"setShareCallback",value:function(b){this.providers=this.providers.map(function(a){return a.setShareCallback(b)})}},{key:"getProviders",value:function(){return this.providers=this.providers.map(function(b){return(new b).getInstance()})}},{key:"reNewAllInstance",value:function(){this.providers=this.providers.map(function(b){return b.reNewInstance()})}}]);return c}();window._goodshare=new t})();
(function($) {
    "use strict";
    $.upkNewsTicker = function(element, options) {

        var defaults = {
            effect         : 'fade',
            direction      : 'ltr',
            autoPlay       : false,
            interval       : 4000,
            scrollSpeed    : 2,
            pauseOnHover   : false,
            position       : 'auto',
            zIndex         : 99999
        }

        var ticker = this;
        ticker.settings = {};
        ticker._element = $(element);
        
        ticker._label            = ticker._element.children(".upk-news-ticker-label"),
        ticker._news             = ticker._element.children(".upk-news-ticker-content"),
        ticker._ul               = ticker._news.children("ul"),
        ticker._li               = ticker._ul.children("li"),
        ticker._controls         = ticker._element.children(".upk-news-ticker-controls"),
        ticker._prev             = ticker._controls.find(".upk-news-ticker-prev").parent(),
        ticker._action           = ticker._controls.find(".upk-news-ticker-action").parent(),
        ticker._next             = ticker._controls.find(".upk-news-ticker-next").parent();

        ticker._pause            = false;
        ticker._controlsIsActive = true;
        ticker._totalNews        = ticker._ul.children("li").length;
        ticker._activeNews       = 0;
        ticker._interval         = false;
        ticker._frameId          = null;

        /****************************************************/
        /**PRIVATE METHODS***********************************/
        /****************************************************/

        var setContainerWidth = function(){
            if (ticker._label.length > 0){
                if (ticker.settings.direction == 'rtl')
                    ticker._news.css({"right":ticker._label.outerWidth()});
                else
                    ticker._news.css({"left":ticker._label.outerWidth()});
            }

            if (ticker._controls.length > 0){
                var controlsWidth = ticker._controls.outerWidth();
                if (ticker.settings.direction == 'rtl')
                    ticker._news.css({"left":controlsWidth});
                else
                    ticker._news.css({"right":controlsWidth});
            }    

            if (ticker.settings.effect === 'scroll')
            {
                var totalW = 0;
                ticker._li.each(function(){
                    totalW += $(this).outerWidth();
                });
                totalW += 50;
                ticker._ul.css({'width':totalW});
            }
        }

        
        var startScrollAnimationLTR = function(){
            var _ulPosition = parseFloat(ticker._ul.css('marginLeft'));
            _ulPosition -= ticker.settings.scrollSpeed/2;
            ticker._ul.css({'marginLeft': _ulPosition });

            if (_ulPosition <= -ticker._ul.find('li:first-child').outerWidth())
            {
                ticker._ul.find('li:first-child').insertAfter(ticker._ul.find('li:last-child'));
                ticker._ul.css({'marginLeft': 0 });
            }
            if (ticker._pause === false){
                ticker._frameId = requestAnimationFrame(startScrollAnimationLTR);
                (window.requestAnimationFrame && ticker._frameId) || setTimeout(startScrollAnimationLTR, 16);
            }
        }

        var startScrollAnimationRTL = function(){
            var _ulPosition = parseFloat(ticker._ul.css('marginRight'));
            _ulPosition -= ticker.settings.scrollSpeed/2;
            ticker._ul.css({'marginRight': _ulPosition });

            if (_ulPosition <= -ticker._ul.find('li:first-child').outerWidth())
            {
                ticker._ul.find('li:first-child').insertAfter(ticker._ul.find('li:last-child'));
                ticker._ul.css({'marginRight': 0 });
            }
            if (ticker._pause === false)
                ticker._frameId = requestAnimationFrame(startScrollAnimationRTL);
                (window.requestAnimationFrame && ticker._frameId) || setTimeout(startScrollAnimationRTL, 16);
        }

        var scrollPlaying = function(){
            if (ticker.settings.direction === 'rtl')
            {
                if (ticker._ul.width() > ticker._news.width())
                    startScrollAnimationRTL();
                else
                	ticker._ul.css({'marginRight': 0 });
            }
            else
                if (ticker._ul.width() > ticker._news.width())
                    startScrollAnimationLTR();
                else
                	ticker._ul.css({'marginLeft': 0 });
        }
        
        var scrollGoNextLTR = function(){            
            ticker._ul.stop().animate({
                marginLeft : - ticker._ul.find('li:first-child').outerWidth()
            },300, function(){
                ticker._ul.find('li:first-child').insertAfter(ticker._ul.find('li:last-child'));
                ticker._ul.css({'marginLeft': 0 });
                ticker._controlsIsActive = true;
            });
        }

        var scrollGoNextRTL = function(){
            ticker._ul.stop().animate({
                marginRight : - ticker._ul.find('li:first-child').outerWidth()
            },300, function(){
                ticker._ul.find('li:first-child').insertAfter(ticker._ul.find('li:last-child'));
                ticker._ul.css({'marginRight': 0 });
                ticker._controlsIsActive = true;
            });
        }

        var scrollGoPrevLTR = function(){
            var _ulPosition = parseInt(ticker._ul.css('marginLeft'),10);
            if (_ulPosition >= 0)
            {
                ticker._ul.css({'margin-left' : -ticker._ul.find('li:last-child').outerWidth()});
                ticker._ul.find('li:last-child').insertBefore(ticker._ul.find('li:first-child'));                
            }

            ticker._ul.stop().animate({
                marginLeft : 0
            },300, function(){
                ticker._controlsIsActive = true;
            });
        }

        var scrollGoPrevRTL = function(){
            var _ulPosition = parseInt(ticker._ul.css('marginRight'),10);
            if (_ulPosition >= 0)
            {
                ticker._ul.css({'margin-right' : -ticker._ul.find('li:last-child').outerWidth()});
                ticker._ul.find('li:last-child').insertBefore(ticker._ul.find('li:first-child'));
            }

            ticker._ul.stop().animate({
                marginRight : 0
            },300, function(){
                ticker._controlsIsActive = true;
            });
        }

        var scrollNext = function(){
            if (ticker.settings.direction === 'rtl')
                scrollGoNextRTL();
            else
                scrollGoNextLTR();
        }

        var scrollPrev = function(){
            if (ticker.settings.direction === 'rtl')
                scrollGoPrevRTL();
            else
                scrollGoPrevLTR();
        }

        var effectTypography = function(){
            ticker._ul.find('li').hide();
            ticker._ul.find('li').eq(ticker._activeNews).width(30).show();
            ticker._ul.find('li').eq(ticker._activeNews).animate({
                width: '100%',
                opacity : 1
            },1500);
        }

        var effectFade = function(){
            ticker._ul.find('li').hide();
            ticker._ul.find('li').eq(ticker._activeNews).fadeIn();
        }

        var effectSlideDown = function(){
            if (ticker._totalNews <= 1)
            {
                 ticker._ul.find('li').animate({
                    'top':30,
                    'opacity':0
                },300, function(){
                    $(this).css({
                        'top': -30,
                        'opacity' : 0,
                        'display': 'block'
                    })
                    $(this).animate({
                        'top': 0,
                        'opacity' : 1
                    },300);
                });
            }   
            else
            {   
                ticker._ul.find('li:visible').animate({
                    'top':30,
                    'opacity':0
                },300, function(){
                    $(this).hide();
                });

                ticker._ul.find('li').eq(ticker._activeNews).css({
                    'top': -30,
                    'opacity' : 0
                }).show();

                ticker._ul.find('li').eq(ticker._activeNews).animate({
                    'top': 0,
                    'opacity' : 1
                },300);
            }
        }

        var effectSlideUp = function(){
            if (ticker._totalNews <= 1)
            {
                 ticker._ul.find('li').animate({
                    'top':-30,
                    'opacity':0
                },300, function(){
                    $(this).css({
                        'top': 30,
                        'opacity' : 0,
                        'display': 'block'
                    })
                    $(this).animate({
                        'top': 0,
                        'opacity' : 1
                    },300);
                });
            }   
            else
            {   
                ticker._ul.find('li:visible').animate({
                    'top':-30,
                    'opacity':0
                },300, function(){
                    $(this).hide();
                });

                ticker._ul.find('li').eq(ticker._activeNews).css({
                    'top': 30,
                    'opacity' : 0
                }).show();

                ticker._ul.find('li').eq(ticker._activeNews).animate({
                    'top': 0,
                    'opacity' : 1
                },300);
            }
        }

        var effectSlideRight = function(){  
            if (ticker._totalNews <= 1)
            {
                 ticker._ul.find('li').animate({
                    'left':'50%',
                    'opacity':0
                },300, function(){
                    $(this).css({
                        'left': -50,
                        'opacity' : 0,
                        'display': 'block'
                    })
                    $(this).animate({
                        'left': 0,
                        'opacity' : 1
                    },300);
                });
            }   
            else
            {       
                ticker._ul.find('li:visible').animate({
                    'left':'50%',
                    'opacity':0
                },300, function(){
                    $(this).hide();
                });

                ticker._ul.find('li').eq(ticker._activeNews).css({
                    'left': -50,
                    'opacity' : 0
                }).show();

                ticker._ul.find('li').eq(ticker._activeNews).animate({
                    'left': 0,
                    'opacity' : 1
                },300);
            }
        }

        var effectSlideLeft = function(){
            if (ticker._totalNews <= 1)
            {
                 ticker._ul.find('li').animate({
                    'left':'-50%',
                    'opacity':0
                },300, function(){
                    $(this).css({
                        'left': '50%',
                        'opacity' : 0,
                        'display': 'block'
                    })
                    $(this).animate({
                        'left': 0,
                        'opacity' : 1
                    },300);
                });
            }   
            else
            {   
                ticker._ul.find('li:visible').animate({
                    'left':'-50%',
                    'opacity':0
                },300, function(){
                    $(this).hide();
                });

                ticker._ul.find('li').eq(ticker._activeNews).css({
                    'left': '50%',
                    'opacity' : 0
                }).show();

                ticker._ul.find('li').eq(ticker._activeNews).animate({
                    'left': 0,
                    'opacity' : 1
                },300);
            }
        }


        var showThis = function(){            
            ticker._controlsIsActive = true;

            switch (ticker.settings.effect){
                case 'typography':
                    effectTypography();
                    break;
                case 'fade':
                    effectFade();
                    break;
                case 'slide-down':
                    effectSlideDown();
                    break;
                case 'slide-up':
                    effectSlideUp();
                    break;
                case 'slide-right':
                    effectSlideRight();
                    break;
                case 'slide-left':
                    effectSlideLeft();
                    break;
                default:
                    ticker._ul.find('li').hide();
                    ticker._ul.find('li').eq(ticker._activeNews).show();
            }
            
        }

        var nextHandler = function(){
            switch (ticker.settings.effect){
                case 'scroll':
                    scrollNext();
                    break;
                default:
                    ticker._activeNews++;
                    if (ticker._activeNews >= ticker._totalNews)
                        ticker._activeNews = 0;

                    showThis();
                    
            }
        }

        var prevHandler = function(){
            switch (ticker.settings.effect){
                case 'scroll':
                    scrollPrev();
                    break;
                default:
                    ticker._activeNews--;
                    if (ticker._activeNews < 0)
                        ticker._activeNews = ticker._totalNews-1;
                    
                    showThis();
            }
        }

        var playHandler = function(){
            ticker._pause = false;
            if (ticker.settings.autoPlay)
            {
                switch (ticker.settings.effect){
                    case 'scroll':
                        scrollPlaying();
                        break;
                    default:
                        ticker.pause();
                        ticker._interval = setInterval(function(){
                            ticker.next();
                        },ticker.settings.interval);
                }
            }
        }

        var resizeEvent = function(){
            if (ticker._element.width() < 480){
                ticker._label.hide();
                if (ticker.settings.direction == 'rtl')
                    ticker._news.css({"right":0});
                else
                    ticker._news.css({"left":0});
            }
            else{
                ticker._label.show();
                if (ticker.settings.direction == 'rtl')
                    ticker._news.css({"right":ticker._label.outerWidth()});
                else
                    ticker._news.css({"left":ticker._label.outerWidth()});
            }
        }

        /****************************************************/
        /**PUBLIC METHODS************************************/
        /****************************************************/
        ticker.init = function() {
            ticker.settings = $.extend({}, defaults, options);

            //ticker._element.append('<div class="upk-breaking-loading"></div>');
            //window.onload = function(){

            	//ticker._element.find('.upk-breaking-loading').hide();

	            //adding effect type class
	            ticker._element.addClass('upk-effect-'+ticker.settings.effect+' upk-direction-'+ticker.settings.direction);
	            
	            setContainerWidth();

                if (ticker.settings.effect != 'scroll')
                    showThis();

                playHandler();

	            //set playing status class
	            if (!ticker.settings.autoPlay)
	                ticker._action.find('span').removeClass('upk-news-ticker-pause').addClass('upk-news-ticker-play');
	            else
	                ticker._action.find('span').removeClass('upk-news-ticker-play').addClass('upk-news-ticker-pause');


	            ticker._element.on('mouseleave', function(e){                
	                var activePosition = $(document.elementFromPoint(e.clientX, e.clientY)).parents('.upk-breaking-news')[0];
	                if ($(this)[0] === activePosition) {
	                    return;
	                }
	                

	                if (ticker.settings.pauseOnHover === true)
	                {
	                    if (ticker.settings.autoPlay === true)
	                        ticker.play();
	                }
	                else
	                {
	                    if (ticker.settings.autoPlay === true && ticker._pause === true)
	                        ticker.play();
	                }                

	            });

	            ticker._element.on('mouseenter', function(){
	                if (ticker.settings.pauseOnHover === true)
	                    ticker.pause();
	            });

	            ticker._next.on('click', function(){
	                if (ticker._controlsIsActive){
	                    ticker._controlsIsActive = false;
	                    ticker.pause();
	                    ticker.next();
	                }                
	            });

	            ticker._prev.on('click', function(){
	                if (ticker._controlsIsActive){
	                    ticker._controlsIsActive = false;
	                    ticker.pause();
	                    ticker.prev();
	                } 
	            });

	            ticker._action.on('click', function(){
	                if (ticker._controlsIsActive){
	                    if (ticker._action.find('span').hasClass('upk-news-ticker-pause'))
	                    {
	                        ticker._action.find('span').removeClass('upk-news-ticker-pause').addClass('upk-news-ticker-play');
	                        ticker.stop();
	                    }
	                    else
	                    {
	                        ticker.settings.autoPlay = true;
	                        ticker._action.find('span').removeClass('upk-news-ticker-play').addClass('upk-news-ticker-pause');
	                        //ticker._pause = false;
	                    }
	                } 
	            });

	            resizeEvent();
	        //}

            $(window).on('resize', function(){
                resizeEvent();
                ticker.pause();
                ticker.play();
            });

        }

        ticker.pause = function() {
            ticker._pause = true;
            clearInterval(ticker._interval);
            cancelAnimationFrame(ticker._frameId);
        }

        ticker.stop = function() {
            ticker._pause = true;
            ticker.settings.autoPlay = false;
        }

        ticker.play = function() {
            playHandler();
        }

        ticker.next = function() {
            nextHandler();
        }

        ticker.prev = function() {
            prevHandler();
        }
        /****************************************************/
        /****************************************************/
        /****************************************************/
        ticker.init();

    }

    $.fn.upkNewsTicker = function(options) {

        return this.each(function() {
            if (undefined == $(this).data('upkNewsTicker')) {
                var ticker = new $.upkNewsTicker(this, options);
                $(this).data('upkNewsTicker', ticker);
            }
        });

    }

})(jQuery);
(function ($, elementor) {

    'use strict';

    var widgetAlexCarousel = function ($scope, $) {

        var $carousel = $scope.find('.upk-alex-carousel');

        if (!$carousel.length) {
            return;
        }

        var $carouselContainer = $carousel.find('.swiper-container'),
            $settings = $carousel.data('settings');

        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        async function initSwiper() {
            var swiper = await new Swiper($carouselContainer, $settings);

            if ($settings.pauseOnHover) {
                $($carouselContainer).hover(function () {
                    (this).swiper.autoplay.stop();
                }, function () {
                    (this).swiper.autoplay.start();
                });
            }
        };

    };


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/upk-alex-carousel.default', widgetAlexCarousel);
    });

}(jQuery, window.elementorFrontend));
(function ($, elementor) {

    'use strict';

    var widgetCategoryCarousel = function ($scope, $) {

        var $carousel = $scope.find('.upk-category-carousel');

        if (!$carousel.length) {
            return;
        }

        var $carouselContainer = $carousel.find('.swiper-container'),
            $settings = $carousel.data('settings');

        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        async function initSwiper() {
            var swiper = await new Swiper($carouselContainer, $settings);

            if ($settings.pauseOnHover) {
                $($carouselContainer).hover(function () {
                    (this).swiper.autoplay.stop();
                }, function () {
                    (this).swiper.autoplay.start();
                });
            }
        };

    };


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/upk-category-carousel.default', widgetCategoryCarousel);
    });

}(jQuery, window.elementorFrontend));
(function($, elementor) {

    'use strict';

    var widgetAlterCarousel = function($scope, $) {

        var $carousel = $scope.find('.upk-alter-carousel');

        if (!$carousel.length) {
            return;
        }

        var $carouselContainer = $carousel.find('.swiper-container'),
            $settings = $carousel.data('settings');

        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        async function initSwiper() {
            var swiper = await new Swiper($carouselContainer, $settings);

            if ($settings.pauseOnHover) {
                $($carouselContainer).hover(function () {
                    (this).swiper.autoplay.stop();
                }, function () {
                    (this).swiper.autoplay.start();
                });
            }
        };

    };


    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/upk-alter-carousel.default', widgetAlterCarousel);
    });

}(jQuery, window.elementorFrontend));
(function ($, elementor) {

    'use strict';

    var widgetEliteCarousel = function ($scope, $) {

        var $carousel = $scope.find('.upk-elite-carousel');

        if (!$carousel.length) {
            return;
        }

        //console.log(JSON.parse(JSON.stringify($settings)));

        var $carouselContainer = $carousel.find('.swiper-container'),
            $settings = $carousel.data('settings');

        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        async function initSwiper() {
            var swiper = await new Swiper($carouselContainer, $settings);

            if ($settings.pauseOnHover) {
                $($carouselContainer).hover(function () {
                    (this).swiper.autoplay.stop();
                }, function () {
                    (this).swiper.autoplay.start();
                });
            }
        };

    };


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/upk-elite-carousel.default', widgetEliteCarousel);
    });

}(jQuery, window.elementorFrontend));
(function ($, elementor) {

    'use strict';

    var widgetHazelCarousel = function ($scope, $) {

        var $carousel = $scope.find('.upk-hazel-carousel');

        if (!$carousel.length) {
            return;
        }

        var $carouselContainer = $carousel.find('.swiper-container'),
            $settings = $carousel.data('settings');

        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        async function initSwiper() {
            var swiper = await new Swiper($carouselContainer, $settings);

            if ($settings.pauseOnHover) {
                $($carouselContainer).hover(function () {
                    (this).swiper.autoplay.stop();
                }, function () {
                    (this).swiper.autoplay.start();
                });
            }
        };

    };


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/upk-hazel-carousel.default', widgetHazelCarousel);
    });

}(jQuery, window.elementorFrontend));
(function ($, elementor) {

    'use strict';

    var widgetMapleCarousel = function ($scope, $) {

        var $carousel = $scope.find('.upk-maple-carousel');

        if (!$carousel.length) {
            return;
        }

        var $carouselContainer = $carousel.find('.swiper-container'),
            $settings = $carousel.data('settings');

        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        async function initSwiper() {
            var swiper = await new Swiper($carouselContainer, $settings);

            if ($settings.pauseOnHover) {
                $($carouselContainer).hover(function () {
                    (this).swiper.autoplay.stop();
                }, function () {
                    (this).swiper.autoplay.start();
                });
            }
        };

    };


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/upk-maple-carousel.default', widgetMapleCarousel);
    });

}(jQuery, window.elementorFrontend));
(function ($, elementor) {

    'use strict';

    var widgetRambleCarousel = function ($scope, $) {

        var $carousel = $scope.find('.upk-ramble-carousel');

        if (!$carousel.length) {
            return;
        }

        var $carouselContainer = $carousel.find('.swiper-container'),
            $settings = $carousel.data('settings');

        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        async function initSwiper() {
            var swiper = await new Swiper($carouselContainer, $settings);

            if ($settings.pauseOnHover) {
                $($carouselContainer).hover(function () {
                    (this).swiper.autoplay.stop();
                }, function () {
                    (this).swiper.autoplay.start();
                });
            }
        };

    };


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/upk-ramble-carousel.default', widgetRambleCarousel);
    });

}(jQuery, window.elementorFrontend));
(function($, elementor) {

    'use strict';

    var widgetAliceCarousel = function($scope, $) {

        var $carousel = $scope.find('.upk-alice-carousel');

        if (!$carousel.length) {
            return;
        }

        var $carouselContainer = $carousel.find('.swiper-container'),
            $settings = $carousel.data('settings');

        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        async function initSwiper() {
            var swiper = await new Swiper($carouselContainer, $settings);

            if ($settings.pauseOnHover) {
                $($carouselContainer).hover(function () {
                    (this).swiper.autoplay.stop();
                }, function () {
                    (this).swiper.autoplay.start();
                });
            }
        };

    };


    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/upk-alice-carousel.default', widgetAliceCarousel);
    });

}(jQuery, window.elementorFrontend));
(function ($, elementor) {

    'use strict';

    var widgetParadoxSlider = function ($scope, $) {

        var $carousel = $scope.find('.upk-paradox-slider');

        if (!$carousel.length) {
            return;
        }

        var $carouselContainer = $carousel.find('.swiper-container'),
            $settings = $carousel.data('settings');

        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        async function initSwiper() {
            var swiper = await new Swiper($carouselContainer, $settings);

            if ($settings.pauseOnHover) {
                $($carouselContainer).hover(function () {
                    (this).swiper.autoplay.stop();
                }, function () {
                    (this).swiper.autoplay.start();
                });
            }
        };

    };


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/upk-paradox-slider.default', widgetParadoxSlider);
    });

}(jQuery, window.elementorFrontend));
/**
 * Start news ticker widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetNewsTicker = function( $scope, $ ) {

		var $newsTicker = $scope.find('.upk-news-ticker'),
            $settings = $newsTicker.data('settings');

        if ( ! $newsTicker.length ) {
            return;
        }

        $($newsTicker).upkNewsTicker($settings);

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/upk-news-ticker.default', widgetNewsTicker );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End news ticker widget script
 */


(function ($, elementor) {

    'use strict';

    var widgetHaroldCarousel = function ($scope, $) {

        var $carousel = $scope.find('.upk-harold-carousel');

        if (!$carousel.length) {
            return;
        }

        var $carouselContainer = $carousel.find('.swiper-container'),
            $settings = $carousel.data('settings');

        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        async function initSwiper() {
            var swiper = await new Swiper($carouselContainer, $settings);

            if ($settings.pauseOnHover) {
                $($carouselContainer).hover(function () {
                    (this).swiper.autoplay.stop();
                }, function () {
                    (this).swiper.autoplay.start();
                });
            }
        };

    };


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/upk-harold-carousel.default', widgetHaroldCarousel);
    });

}(jQuery, window.elementorFrontend));
(function ($, elementor) {

    'use strict';

    var widgetBuzzListCarousel = function ($scope, $) {

        var $carousel = $scope.find('.upk-buzz-list-carousel');

        if (!$carousel.length) {
            return;
        }

        var $carouselContainer = $carousel.find('.swiper-container'),
            $settings = $carousel.data('settings');

        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        async function initSwiper() {
            var swiper = await new Swiper($carouselContainer, $settings);

            if ($settings.pauseOnHover) {
                $($carouselContainer).hover(function () {
                    (this).swiper.autoplay.stop();
                }, function () {
                    (this).swiper.autoplay.start();
                });
            }
        };

    };


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/upk-buzz-list-carousel.default', widgetBuzzListCarousel);
    });

}(jQuery, window.elementorFrontend));

/**
 * Start mailchimp widget script
 */
;(function($) {

    function gernerateHtml(type, className, text,) {
      return [
        '<div class="upk-newsletter-' + type + ' ' + className + '">',
          '<div class="upk-alert-box">',
              text,
          '</div>',
        '</div>'
      ].join('');
    }
 
    $.alert = function(text) {
      $.alert.hide();
      var options = {
        text: text
      };
      $(gernerateHtml('alert', '', options.text))
      .appendTo(document.body);
    };
    $.alert.hide = function() {
      $('.upk-newsletter-alert').slideUp(800).remove();
    };
  
    })(jQuery);
/**
 * Start mailchimp widget script
 */



( function( $, elementor ) {

    'use strict';

    var widgetMailChimp = function( $scope, $ ) {

        var $mailChimp = $scope.find('.upk-newsletter');
            
        if ( ! $mailChimp.length ) {
            return;
        }
 
        var langStr = window.UltimatePostKitConfig.mailchimp;

        $mailChimp.submit(function(){
            
            var mailchimpform = $(this);

            $.alert('<span class="uwk-newsletter-loader"></span>'+langStr.subscribing);

            $.ajax({
                url:mailchimpform.attr('action'),
                type:'POST',
                data:mailchimpform.serialize(),
                success:function(data){
                  setTimeout(function(){ 
                    $.alert(data);
                    setTimeout(function(){ 
                        $.alert.hide(); 
                    }, 5000);
                }, 2000);
                    
                }
            });
            return false;

        });

        return false;

    };


    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction( 'frontend/element_ready/upk-newsletter.default', widgetMailChimp );
    });

}( jQuery, window.elementorFrontend ) );

/**
 * End mailchimp widget script
 */
 
(function ($, elementor) {

    'use strict';

    var widgetNoxeSlider = function ($scope, $) {

        var $carousel = $scope.find('.upk-noxe-slider');

        if (!$carousel.length) {
            return;
        }

        var $carouselContainer = $carousel.find('.swiper-container'),
            $settings = $carousel.data('settings');

        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        async function initSwiper() {
            var swiper = await new Swiper($carouselContainer, $settings);

            if ($settings.pauseOnHover) {
                $($carouselContainer).hover(function () {
                    (this).swiper.autoplay.stop();
                }, function () {
                    (this).swiper.autoplay.start();
                });
            }
        };

    };


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/upk-noxe-slider.default', widgetNoxeSlider);
    });

}(jQuery, window.elementorFrontend));
(function ($, elementor) {

    'use strict';

    var widgetSkideSlider = function ($scope, $) {

        var $carousel = $scope.find('.upk-skide-slider');
        if (!$carousel.length) {
            return;
        }

        var $carouselContainer = $carousel.find('.swiper-container'),
            $settings = $carousel.data('settings');

        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        async function initSwiper() {

            var mainSlider = await new Swiper($carouselContainer, $settings);
            if ($settings.pauseOnHover) {
                $($carouselContainer).hover(function () {
                    (this).swiper.autoplay.stop();
                }, function () {
                    (this).swiper.autoplay.start();
                });
            }

            var $mainWrapper = $scope.find('.upk-skide-slider-wrap'),
                $thumbs = $mainWrapper.find('.upk-skide-thumbs');
            var sliderThumbs = await new Swiper($thumbs, {
                spaceBetween: 10,
                slidesPerView: 2,
                touchRatio: 0.2,
                slideToClickedSlide: true,
                loop: ($settings.loop) ? $settings.loop : false,
                speed: ($settings.speed) ? $settings.speed : 500,
                loopedSlides: 4,
                breakpoints: {
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 20,
                    }
                }
            });

            mainSlider.controller.control = sliderThumbs;
            sliderThumbs.controller.control = mainSlider;
        }

    };


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/upk-skide-slider.default', widgetSkideSlider);
    });

}(jQuery, window.elementorFrontend));
(function ($, elementor) {

    'use strict';

    var widgetAmoxCarousel = function ($scope, $) {

        var $carousel = $scope.find('.upk-amox-carousel');

        if (!$carousel.length) {
            return;
        }

        var $carouselContainer = $carousel.find('.swiper-container'),
            $settings = $carousel.data('settings');

        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        async function initSwiper() {
            var swiper = await new Swiper($carouselContainer, $settings);

            if ($settings.pauseOnHover) {
                $($carouselContainer).hover(function () {
                    (this).swiper.autoplay.stop();
                }, function () {
                    (this).swiper.autoplay.start();
                });
            }
        };

    };


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/upk-amox-carousel.default', widgetAmoxCarousel);
    });

}(jQuery, window.elementorFrontend));
; (function ($) {
    $(window).on("elementor/frontend/init", function () {
        elementorFrontend.hooks.addAction("frontend/element_ready/upk-static-social-count.default", function (scope) {
            scope.find('.upk-static-social-count').each(function () {
                var element = $(this)[0];
                var counter = $(element).find('.counter-value');
                function intToString(num) {
                    num = num.toString().replace(/[^0-9.]/g, '');
                    if (num <= 999) {
                        return Math.ceil(num);
                    }
                    let si = [
                        { v: 1E3, s: "K" },
                        { v: 1E6, s: "M" },
                        { v: 1E9, s: "B" },
                        { v: 1E12, s: "T" },
                        { v: 1E15, s: "P" },
                        { v: 1E18, s: "E" }
                    ];
                    let index;
                    for (index = si.length - 1; index > 0; index--) {
                        if (num >= si[index].v) {
                            break;
                        }
                    }
                    return (num / si[index].v).toFixed(2).replace(/\.0+$|(\.[0-9]*[1-9])0+$/, "$1") + si[index].s;
                }

                if (element) {
                    $(counter).each(function () {
                        var $this = $(this);
                        jQuery({ Counter: 0 }).animate({ Counter: $this.text() }, {
                            duration: 3000,
                            easing: 'swing',
                            step: function () {
                                $this.text(intToString(this.Counter));
                            }
                        });
                    });
                }
            });
        });
    });
})(jQuery);

(function ($, elementor) {

	'use strict';

	var widgetCamuxSlider = function ($scope, $) {

		var $carousel = $scope.find('.upk-camux-slider');
        if (!$carousel.length) {
            return;
        }

        var $carouselContainer = $carousel.find('.swiper-container'),
            $settings = $carousel.data('settings');

        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        async function initSwiper() {
            var mainSlider = await new Swiper($carouselContainer, $settings);

            if ($settings.pauseOnHover) {
                $($carouselContainer).hover(function () {
                    (this).swiper.autoplay.stop();
                }, function () {
                    (this).swiper.autoplay.start();
                });
            }

            var $mainWrapper = $scope.find('.upk-camux-slider-wrap'),
            $thumbs          = $mainWrapper.find('.upk-camux-thumbs');

            var sliderThumbs = await new Swiper($thumbs, {
                spaceBetween: 10,
                slidesPerView: 4,
                touchRatio: 0.2,
                slideToClickedSlide: true,
                loop: ($settings.loop) ? $settings.loop : false,
                speed: ($settings.speed) ? $settings.speed : 500,
                loopedSlides: 4,
                breakpoints: {
                    0: {
                        slidesPerView: 2,
                    },
                    768: {
                        slidesPerView: 3,
                    },
                    1024: {
                        slidesPerView: 4,
                    },
                },
            });
    
            mainSlider.controller.control = sliderThumbs;
            sliderThumbs.controller.control = mainSlider;
        };
	};


	jQuery(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/upk-camux-slider.default', widgetCamuxSlider);
	});

}(jQuery, window.elementorFrontend));
(function($, elementor) {

    'use strict';

    var widgetCrystalSlider = function($scope, $) {

        var $carousel = $scope.find('.upk-crystal-slider');

        if (!$carousel.length) {
            return;
        }

        var $carouselContainer = $carousel.find('.swiper-container'),
            $settings = $carousel.data('settings');

        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        async function initSwiper() {
            var swiper = await new Swiper($carouselContainer, $settings);

            if ($settings.pauseOnHover) {
                $($carouselContainer).hover(function () {
                    (this).swiper.autoplay.stop();
                }, function () {
                    (this).swiper.autoplay.start();
                });
            }
        };

    };


    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/upk-crystal-slider.default', widgetCrystalSlider);
    });

}(jQuery, window.elementorFrontend));
/**
 * Start news ticker widget script
 */

(function ($, elementor) {

	'use strict';

	var widgetReadingProgress = function ($scope, $) {

		var $scrolline = $scope.find('.upk-reading-progress'),
			$settings = $scrolline.data('settings');

		if (!$scrolline.length) {
			return;
		}
		if ($('.upk-reading-progress-wrap')) {
			$('.upk-reading-progress-wrap').remove();
		}

		jQuery.scrolline($settings);

		
		 if ($("body.admin-bar").length && $settings.position == 'top') {
		 	$('.upk-reading-progress-wrap').css('margin-top', 32);
		 }

	};

	jQuery(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/upk-reading-progress.default', widgetReadingProgress);
	});

}(jQuery, window.elementorFrontend));

/**
 * End news ticker widget script
 */
(function($, elementor) {

    'use strict';

    var widgetCarbonSlider = function($scope, $) {

        var $carousel = $scope.find('.upk-carbon-main');
        if (!$carousel.length) {
            return;
        }

        var $carouselContainer = $carousel.find('.swiper-container'),
            $settings = $carousel.data('settings');

        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        async function initSwiper() {

            var mainSlider = await new Swiper($carouselContainer, $settings);
            if ($settings.pauseOnHover) {
                $($carouselContainer).hover(function() {
                    (this).swiper.autoplay.stop();
                }, function() {
                    (this).swiper.autoplay.start();
                });
            }

            var $mainWrapper = $scope.find('.upk-carbon-slider-wrap'),
                $thumbs = $mainWrapper.find('.upk-carbon-thumbs');
            var sliderThumbs = await new Swiper($thumbs, {
                spaceBetween: 10,
                slidesPerView: 2,
                touchRatio: 0.2,
                slideToClickedSlide: true,
                centeredSlides: true,
                loop: ($settings.loop) ? $settings.loop : false,
                speed: ($settings.speed) ? $settings.speed : 500,
                loopedSlides: 4,
                breakpoints: {
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 20,
                    }
                },

            });

            mainSlider.controller.control = sliderThumbs;
            sliderThumbs.controller.control = mainSlider;
        }

    };


    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/upk-carbon-slider.default', widgetCarbonSlider);
    });

}(jQuery, window.elementorFrontend));
(function ($, elementor) {

    'use strict';

    var widgetPholoxSlider = function ($scope, $) {

        var $slider = $scope.find('.upk-pholox-slider'),
            $settings = $slider.data('settings'),
            $widgetSettings = $slider.data('widget');

        if (!$slider.length) {
            return;
        }

 
        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        async function initSwiper() {

            var $thumbs = $slider.find('.upk-thumbs-slider .swiper-container');
            var $preview = $slider.find('.upk-main-slider .swiper-container');

            var mainSlider = await new Swiper($preview, $settings);

            var sliderThumbs = await new Swiper($thumbs, {
                spaceBetween: 15,
                slidesPerView: 2,
                touchRatio: 0.2,
                slideToClickedSlide: true,
                loop: ($settings.loop) ? $settings.loop : false,
                speed: ($settings.speed) ? $settings.speed : 500,
                loopedSlides: 4,
                breakpoints: {
                    768: {
                        slidesPerView: 3,
                        spaceBetween: 20,
                    },
                    1440: {
                        slidesPerView: 4,
                        spaceBetween: 30,
                    }
                }
            });

            mainSlider.controller.control = sliderThumbs;
            sliderThumbs.controller.control = mainSlider;

            mainSlider.on('slideChange', function () {
                stopVideos();

            });

            var stopVideos = function () {
                $slider.find('.upk-video-wrap').css('z-index', -1);
                var videos = $slider.find('.upk-video-iframe');
                Array.prototype.forEach.call(videos, function (video) {
                    var src = video.src;
                    video.src = src.replace("autoplay=1", "");
                    $slider.find('.upk-video-iframe').prop("src", "");
                });
            };

            $slider.find('.upk-pholox-video-trigger').on('click', function () {
                console.log('s');
                var videoURL = $(this).data('src').split('?')[0],
                    sliderWrapper = $slider.find('.upk-main-slider .swiper-slide-active .upk-img-wrap');

                    $slider.find('.upk-img-wrap').removeClass('upk-width-100');
                    $(sliderWrapper).addClass('upk-width-100');

                sliderWrapper.find('.upk-video-iframe').attr("src", videoURL + "?autoplay=1&modestbranding=1&showinfo=0&rel=0&controls=0&loop=1");
                sliderWrapper.find('.upk-video-wrap').css('z-index', 10);

            });

        }


    };


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/upk-pholox-slider.default', widgetPholoxSlider);
    });

}(jQuery, window.elementorFrontend));
(function ($, elementor) {
  "use strict";

  var widgetSnogSlider = function ($scope, $) {
    var $carousel = $scope.find(".upk-snog-slider");
    if (!$carousel.length) {
      return;
    }

    var $carouselContainer = $carousel.find(".swiper-container"),
      $settings = $carousel.data("settings"),
      $widgetSettings = $carousel.data("widget-settings");

    const Swiper = elementorFrontend.utils.swiper;
    initSwiper();
    async function initSwiper() {
      var mainSlider = await new Swiper($carouselContainer, $settings);

      if ($settings.pauseOnHover) {
        $($carouselContainer).hover(
          function () {
            this.swiper.autoplay.stop();
          },
          function () {
            this.swiper.autoplay.start();
          }
        );
      }

      var $mainWrapper = $scope.find(".upk-snog-slider-wrap"),
      $thumbs = $mainWrapper.find(".upk-snog-thumbs");

      var sliderThumbs = await new Swiper($thumbs, {
        spaceBetween: 0,
        effect: "slide",

        lazy: true,
        slidesPerView: 2,
        touchRatio: 0.2,
        loop: $settings.loop ? $settings.loop : false,
        speed: $settings.speed ? $settings.speed : 800,
        loopedSlides: 4,
        // allowTouchMove: false,
      });

      var contentslide = await new Swiper( $($widgetSettings.id).find(".upk-content-slider"), {
        parallax: true,
        effect: "fade",
        slidesPerView: 1,
        loop: $settings.loop ? $settings.loop : false,
        speed: $settings.speed ? $settings.speed : 800,
        loopedSlides: 4,
        allowTouchMove: false,
        pagination: {
          el: $widgetSettings.id + " .upk-pagination",
          clickable: true,
        },

        // navigation: {
        //   nextEl: ".upk-navigation-next",
        //   prevEl: ".upk-navigation-prev",
        // },
      });

      // mainSlider.controller.control = sliderThumbs;
      // sliderThumbs.controller.control = mainSlider;
      // contentslide.controller.control = sliderThumbs;
      // contentslide.controller.control = mainSlider;

      mainSlider.controller.control = sliderThumbs;
      sliderThumbs.controller.control = mainSlider;

      $(document).ready(function () {
        setTimeout(() => {
            var mainSwiperInstance = $($widgetSettings.id).find('.upk-main-slider')[0].swiper;

            var $slideActive = $($widgetSettings.id).find('.upk-main-slider .swiper-slide-active');
            var realIndex = $slideActive.data('swiper-slide-index');

            if (typeof realIndex === 'undefined') {
                realIndex = $slideActive.index();
            }

            $($widgetSettings.id).find('.upk-navigation-prev').on("click", function () {
                mainSwiperInstance.slidePrev();
            });

            $($widgetSettings.id).find('.upk-navigation-next').on("click", function () {
                mainSwiperInstance.slideNext();
            });

            $($widgetSettings.id).find('.swiper-pagination-bullet').on("click", function () {
              var index = $(this).index();

              if ($settings.loop) {
                  mainSwiperInstance.slideToLoop(index);
              } else {
                  mainSwiperInstance.slideTo(index);
              }

              $($widgetSettings.id).addClass('wait--'); //todo future

          });

          mainSwiperInstance.on('slideChange', function (e) {
            contentslide.slideToLoop(mainSwiperInstance.realIndex);
          });

        }, 3000);

      });
      
    }
  };

  jQuery(window).on("elementor/frontend/init", function () {
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/upk-snog-slider.default",
      widgetSnogSlider
    );
  });
})(jQuery, window.elementorFrontend);

(function ($, elementor) {

    'use strict';

    var extensionAnimations = function ($scope, $) {

        var $animations = $scope.find('.upk-in-animation');

        if (!$animations.length) {
            return;
        }

        var itemQueue = [];
        var delay = ($animations.data('in-animation-delay')) ? $animations.data('in-animation-delay') : 200;
        var queueTimer;

        function processItemQueue() {
            if (queueTimer) return // We're already processing the queue

            queueTimer = window.setInterval(function () {
                if (itemQueue.length) {
                    jQuery(itemQueue.shift()).addClass('is-inview');
                    processItemQueue();
                } else {
                    window.clearInterval(queueTimer)
                    queueTimer = null
                }
            }, delay)
        }

        elementorFrontend.waypoint(jQuery('.upk-in-animation .upk-item'), function () {
            itemQueue.push($(this));
            processItemQueue();
        }, {
            offset: '90%'
        });

    };

    jQuery(window).on('elementor/frontend/init', function () {

        var $widgets = [ 'alex-grid', 'alice-grid', 'alter-grid', 'amox-grid', 'buzz-list', 'classic-list', 'elite-grid', 'fanel-list', 'featured-list', 'harold-list', 'hazel-grid', 'kalon-grid', 'maple-grid', 'ramble-grid', 'recent-comments', 'scott-list', 'tiny-list', 'welsh-list', 'wixer-grid' ];

        $.each($widgets, function(index, value) {
            elementorFrontend.hooks.addAction('frontend/element_ready/upk-' + value +'.default', extensionAnimations);
        });
    });

}(jQuery, window.elementorFrontend));



})(jQuery, window.elementorFrontend);