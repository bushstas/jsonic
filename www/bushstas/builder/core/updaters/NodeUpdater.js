__G.set((c=function(node,params,names){
if(!this||this==window){
p=c.prototype;
p.getKeys=function(){return this.names};
p.react=function(d){var t;if(isFunction(this.params['v']))t=this.params['v']();else t=d[this.names[0]];this.node.textContent=t||''};
p.dispose=function(){this.node=null;this.params=null;this.names=null};
return c;
}
this.node=node;this.params=params;this.names=isArray(names)?names:[names];
})(),'NodeUpdater');