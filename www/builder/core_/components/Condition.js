_G_.set(function(params){
var isTrue=!!params['i'](),level,parentElement,parentLevel;
var createLevel=function(isUpdating){var l=_G_.get('Level');level=new l(parentLevel.getComponent());var nextSiblingChild=isUpdating?_G_.get('Core').getNextSiblingChild.call(this):null;level.render(getChildren.call(this),parentElement,parentLevel,nextSiblingChild)};
var disposeLevel=function(){if(level)level.dispose();level=null};
var getChildren=function(){if(isTrue)return isFunction(params['c'])?params['c']():params['c'];return isFunction(params['e'])?params['e']():params['e']};
this.render=function(pe,pl){parentElement=pe;parentLevel=pl;createLevel.call(this)};
this.update=function(){var i=!!params['i']();if(i!=isTrue){isTrue=i;disposeLevel();createLevel.call(this,true)}};
this.dispose=function(){_G_.get('Core').disposeLinks.call(this);disposeLevel();parentElement=null;parentLevel=null;params=null};
},'Condition');