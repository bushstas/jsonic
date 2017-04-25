var StoreKeeper;__G.set(StoreKeeper=new(function(){
var x='stored_',s={'month':2592000,'day':86400,'hour':3600,'min':60};
var g=function(k){return x+k};
var gm=function(p){var n=~~p.replace(/[^\d]/g,'');var m=p.replace(/\d/g,'');if(!n)return 0;if(!s[m])return 0;return s[m]*n*1000};
var gi=function(k){var lk=g(k);var i=localStorage.getItem(lk);if(!i)return null;try{i=JSON.parse(i)}catch(e){return null}return i};
var ia=function(sm,p){var nm=Date.now(),pm=gm(p);if(isString(sm))sm=stringToNumber(sm);return pm&&sm&&nm-sm<pm};
this.set=function(k,v){var lk=g(k);var i=JSON.stringify({'data':v,'timestamp':Date.now().toString()});localStorage.setItem(lk,i)};
this.get=function(k){var i=gi(k);return __O.has(i,'data')?i['data']:null};
this.getActual=function(k,p){var i=gi(k);return __O.has(i,'data')&&ia(i['timestamp'],p)?i['data']:null};
this.remove=function(k){var lk=g(k);localStorage.removeItem(lk)};
})(),'StoreKeeper');