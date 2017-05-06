var Loader;__G.set(Loader=new(function(){
var requests={};
var getRequest=function(url,th){return requests[url]||createRequest(url,th)};
var createRequest=function(url,th){var ajr=__G.get('AjaxRequest');requests[url]=new ajr(url,null,null,th);return requests[url]};
this.get=function(url,data,callback,th){this.doAction('GET',url,data,callback,th)};
this.post=function(url,data,callback,th){this.doAction('POST',url,data,callback,th)};
this.put=function(url,data,callback,th){this.doAction('PUT',url,data,callback,th)};
this.delete=function(url,data,callback,th){this.doAction('DELETE',url,data,callback,th)};
this.doAction=function(method,url,data,callback,th){var req=getRequest(url,th);if(isFunction(callback))req.setCallback(callback);req.send(method,data)};
})(),'Loader');