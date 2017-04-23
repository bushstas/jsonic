__G.set((c=function(params){
if(!this||this==window){
var createLevel=function(isUpdating){var l=__G.get('Level');this.level=new l(this.parentLevel.getComponent());var nextSiblingChild=isUpdating?__A.getNextSiblingChild.call(this):null;this.level.render(getChildren.call(this),this.parentElement,this.parentLevel,nextSiblingChild)};
var disposeLevel=function(){if(this.level)this.level.dispose();this.level=null};
var getChildren=function(){var p=this.params;if(this.isTrue)return isFunction(p['c'])?p['c']():p['c'];return isFunction(p['e'])?p['e']():p['e']};
p=c.prototype;
p.render=function(pe,pl){this.parentElement=pe;this.parentLevel=pl;createLevel.call(this)};
p.update=function(){var i=!!this.params['i']();if(i!=this.isTrue){this.isTrue=i;disposeLevel.call(this);createLevel.call(this,1)}};
p.dispose=function(){__A.disposeLinks.call(this);disposeLevel.call(this);this.parentElement=null;this.parentLevel=null;this.params=null};
return c;
}
this.params=params;this.isTrue=!!params['i']();
})(),'Condition');