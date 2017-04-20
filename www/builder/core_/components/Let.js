_G_.set((c=function(params){
if(!this||this==window){
var createLevels=function(isUpdating){_C_.createLevel.call(this,this.params['l'](),isUpdating)};
p=c.prototype;
p.render=function(pe,pl){_C_.initOperator.call(this,pe,pl);createLevels.call(this,false)};
p.update=function(){_C_.disposeLevels.call(this);createLevels.call(this,true)};
p.dispose=function(){_C_.disposeOperator.call(this)};
return c;
}
this.params=params;
})(),'Let');