_G_.set((c=function(params){
if(!this||this==window){
var getKeysInRandomOrder=function(){var keys=Objects.getKeys(getParam.call(this,'p'));keys.shuffle();return keys};
var createIfEmptyLevel=function(){if(!isUndefined(this.params['ie'])){_C_.createLevel.call(this,this.params['ie'])}};
var getParam=function(p){return(isFunction(this.params['p'])?this.params['p']():this.params)[p]};
var createLevels=function(isUpdating){var p=this.params;var items=getParam.call(this,'p'),limit=getParam.call(this,'l'),r;if(isArrayLike(items)){if(p['ra']){if(!Objects.empty(items)){var keys=getKeysInRandomOrder();for(var i=0;i<keys.length;i++){if(limit&&i+1>limit)break;r=p['h'](items[keys[i]],keys[i]);if(r=='_brk')break;_C_.createLevel.call(this,r,isUpdating)}return}}else if(isArray(items)){var from=getParam.call(this,'fr'),to=getParam.call(this,'to');if(!items.isEmpty()){var start;if(!p['r']){start=isNumber(from)?from:0;for(var i=start;i<items.length;i++){if(limit&&i+1>limit)break;if(isNumber(to)&&i>to)break;r=p['h'](items[i],i);if(r=='_brk')break;_C_.createLevel.call(this,r,isUpdating)}}else{var j=0;start=isNumber(from)?from:items.length-1;for(var i=start;i>=0;i--){j++;if(limit&&j>limit)break;if(isNumber(to)&&i<to)break;r=p['h'](items[i],i);if(r=='_brk')break;_C_.createLevel.call(this,r,isUpdating)}}return}}else if(isObject(items)){if(!Objects.empty(items)){if(!p['r']){var i=0;for(var k in items){i++;if(limit&&i>limit)break;r=p['h'](items[k],k);if(r=='_brk')break;_C_.createLevel.call(this,r,isUpdating)}}else{var keys=Objects.getKeys(items);keys.reverse();for(var i=0;i<keys.length;i++){if(limit&&i+1>limit)break;r=p['h'](items[keys[i]],keys[i]);if(r=='_brk')break;_C_.createLevel.call(this,r,isUpdating)}}return}}}createIfEmptyLevel.call(this)};
p=c.prototype;
p.render=function(pe,pl){_C_.initOperator.call(this,pe,pl);createLevels.call(this,false)};
p.update=function(){_C_.disposeLevels.call(this);createLevels.call(this,true)};
p.add=function(item,index){var r=this.params['h'](item,~~index);if(r!='_brk')_C_.createLevel.call(this,r,false,index)};
p.remove=function(index){if(this.levels[index]){this.levels[index].dispose();this.levels.splice(index,1)}};
p.dispose=function(){_C_.disposeOperator.call(this)};
return c;
}
this.params=params;
})(),'Foreach');