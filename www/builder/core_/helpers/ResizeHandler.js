__G.set(new(function(){
var subscribers=[],timer;
var onResize=function(){window.clearTimeout(timer);timer=window.setTimeout(function(){for(var i=0;i<subscribers.length;i++){var callback=__O.get(subscribers[i][1],'callback');if(isFunction(callback))callback.call(subscribers[i][0])}},200)};
this.subscribe=function(subscriber,options){subscribers.push([subscriber,options])};
window.addEventListener('resize',onResize,false);
})(),'ResizeHandler');