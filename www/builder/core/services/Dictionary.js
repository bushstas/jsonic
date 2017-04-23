var Dictionary;__G.set(Dictionary=new(function(){
var dictionaryUrl=__DU;var items={},callbacks,loaded={};
this.load=function(routeName){if(loaded[routeName])return;if(!isNone(dictionaryUrl)){Loader.get(dictionaryUrl,{'route':routeName},onLoad,this)}loaded[routeName]=true};
this.get=function(key,callbackOrPropName,thisObj){var item=Objects.get(items,key);if(item)return item;callbacks=callbacks||[];callbacks.push([callbackOrPropName,thisObj,key])};
this.set=function(key,value){items[key]=value};
this.setData=function(routeName,data){loaded[routeName]=true;for(var k in data)this.set(k,data[k])};
})(),'Dictionary');