var Dictionary;__G.set(Dictionary=new(function(){
var items={},callbacks,loaded={};
var onLoad=function(data){if(isObject(data)){for(var k in data)this.set(k,data[k]);if(!isArray(callbacks))return;for(var i=0;i<callbacks.length;i++){if(isFunction(callbacks[i][0])){callbacks[i][0].call(callbacks[i][1]||null)}else if(isString(callbacks[i][0])&&isComponentLike(callbacks[i][1])){callbacks[i][1].set(callbacks[i][0],items[callbacks[i][2]])}}callbacks=null}};
this.load=function(routeName){if(loaded[routeName])return;if(!isNone(__DU)){Loader.get(__DU,{'route':routeName},onLoad,this)}loaded[routeName]=true};
this.get=function(key,callbackOrPropName,thisObj){var item=Objects.get(items,key);if(item)return item;callbacks=callbacks||[];callbacks.push([callbackOrPropName,thisObj,key])};
this.set=function(key,value){items[key]=value};
this.setData=function(routeName,data){loaded[routeName]=true;for(var k in data)this.set(k,data[k])};
})(),'Dictionary');