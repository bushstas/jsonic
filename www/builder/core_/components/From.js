__G.set((c=function(params){
if(!this||this==window){
var createLevels=function(isUpdating){var p=this.params,f=p['f'];p=(isFunction(p['p'])?p['p']():p['p'])||[];var a=~~p[0],b=~~p[1],s=~~p[2]||1;for(var i=a;i<=b;i+=s){__A.createLevel.call(this,f(i),isUpdating)}};
p=c.prototype;
p.render=function(pe,pl){__A.initOperator.call(this,pe,pl);createLevels.call(this,false)};
p.update=function(){__A.disposeLevels.call(this);createLevels.call(this,true)};
p.dispose=function(){__A.disposeOperator.call(this)};
return c;
}
this.params=params;
})(),'From');