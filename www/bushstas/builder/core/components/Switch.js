__G.set((c=function(params){
if(!this||this==window){
var isChanged=function(){var p=this.params['sw']();var v=p['sw'],vs=p['cs'],c=this.cur;if(!isUndefined(vs)){if(!isArray(vs))vs=[vs];for(var i=0;i<vs.length;i++){if(v===vs[i]){this.cur=i;return i!==c}}}this.cur=null;return c!==null};
var createLevels=function(isUpdating){var p=this.params['sw']();var c=p['c'],d=p['d'];if(this.cur!==null){__A.createLevel.call(this,c[this.cur],isUpdating)}else if(!isUndefined(d)){__A.createLevel.call(this,d,isUpdating)}};
p=c.prototype;
p.render=function(pe,pl){__A.initOperator.call(this,pe,pl);isChanged.call(this);createLevels.call(this,false)};
p.update=function(){if(isChanged.call(this)){__A.disposeLevels.call(this);createLevels.call(this,true)}};
p.dispose=function(){__A.disposeOperator.call(this);this.cur=null};
return c;
}
this.params=params;this.cur=null;
})(),'Switch');