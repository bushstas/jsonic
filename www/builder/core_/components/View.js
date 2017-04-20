_G_.set((c=function(){
if(!this||this==window){
p=c.prototype;
p.onRenderComplete=function(){this.dispatchReadyEvent()};
p.setOnReadyHandler=function(handler){this.onReadyHandler=handler};
p.dispatchReadyEvent=function(){if(isFunction(this.onReadyHandler)){this.onReadyHandler()}this.onReady()};
p.activate=function(isActivated){if(isActivated){this.dispatchReadyEvent()}};
p.getTitleParams=function(){};
p.onReady=function(){};
return c;
}
})(),'View');