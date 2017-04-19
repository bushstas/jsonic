_G_.set((c=function(){
if(!this||this==window){
var controllers,router,dictionary;var routes=__ROUTES;var errorRoutes=__ERRORROUTES;var viewContainerClass=__VIEWCONTAINER;var defaultPagetitle=__PAGETITLE;var parentalContainerClass=__VIEWCONTAINER2;
var getViewParams=function(route,allParams){var params;if(isObject(route['dynamicParams'])){params={};for(var k in route['dynamicParams']){params[k]=router.getPathPartAt(route['dynamicParams'][k])}}if(allParams){if(isObject(params)){Objects.merge(params,route['params'])}else{params=route['params']}}return params};
return c;
}
})(),'Application');