__G.set((c=function(params){
if(!this||this==window){
var createLevels=function(isUpdating){__A.createLevel.call(this,this.params['l'](),isUpdating)};
p=c.prototype;
p.render=function(pe,pl){__A.initOperator.call(this,pe,pl);createLevels.call(this,false)};
p.update=function(){__A.disposeLevels.call(this);createLevels.call(this,true)};
p.dispose=function(){__A.disposeOperator.call(this)};
return c;
}
this.params=params;
})(),'Let');