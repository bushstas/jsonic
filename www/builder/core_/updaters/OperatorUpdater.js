__G.set((c=function(operator,params,names){
if(!this||this==window){
p=c.prototype;
p.getKeys=function(){return this.names};
p.react=function(){this.operator.update()};
p.dispose=function(){this.operator=null;this.names=null};
return c;
}
this.operator=operator;this.names=isArray(names)?names:[names];
})(),'OperatorUpdater');