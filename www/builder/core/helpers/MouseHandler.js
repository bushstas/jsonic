__G.set((c=function(){
if(!this||this==window){
var extendOptions=function(index,opts){Objects.merge(this.options[index],opts)};
var onClick=function(subscriber,e){var index=this.subscribers.indexOf(subscriber);var opts=this.options[index];var target;for(var k in opts){target=e.getTargetWithClass(k,true);if(target){if(isFunction(opts[k])){opts[k].call(subscriber,target,e);e.stopPropagation();break}}}};
p=c.prototype;
p.subscribe=function(subscriber,opts){var index=this.subscribers.indexOf(subscriber);if(index==-1){this.options.push(opts);this.eventHandler.listen(subscriber.getElement(),'click',onClick.bind(null,subscriber));this.subscribers.push(subscriber)}else extendOptions(index,opts)};
p.unsubscribe=function(subscriber){var idx=this.subscribers.indexOf(subscriber);if(idx>-1){this.eventHandler.unlisten(subscriber.getElement(),'click');this.subscribers.splice(idx,1)}};
p.dispose=function(){this.subscribers=null;this.options=null;this.eventHandler.dispose();this.eventHandler=null};
return c;
}
this.subscribers=[];this.options=[];var eh=__G.get('EventHandler');this.eventHandler=new eh();
})(),'MouseHandler');