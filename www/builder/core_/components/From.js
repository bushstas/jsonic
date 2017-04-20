_G_.set((c=function(params){
if(!this||this==window){
var createLevels=function(isUpdating){var p=this.params,f=p['f'];p=(isFunction(p['p'])?p['p']():p['p'])||[];var a=~~p[0],b=~~p[1],s=~~p[2]||1;for(var i=a;i<=b;i+=s){_C_.createLevel.call(this,f(i),isUpdating)}};
p=c.prototype;
p.render=function(pe,pl){_C_.initOperator.call(this,pe,pl);createLevels.call(this,false)};
p.update=function(){_C_.disposeLevels.call(this);createLevels.call(this,true)};
p.dispose=function(){_C_.disposeOperator.call(this)};
return c;
}
this.params=params;
})(),'From');