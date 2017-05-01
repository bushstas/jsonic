__G.set((c=function(url,callback,params,thisObj){
if(!this||this==window){
var correctUrl=function(u){u=u.replace(/^[\.\/]+/,'');if(isString(__AD)){var regExp=new RegExp('^'+__AD+"\/");u=__AD+'/'+u.replace(regExp,'')}return'/'+u};
var createRequest=function(){this.request=new XMLHttpRequest();this.request.onreadystatechange=onReadyStateChange.bind(this)};
var getRequestContent=function(method,pars){if(Objects.empty(pars))return'';if(!isObject(pars)){return pars.toString()}else if(pars instanceof FormData){return pars}else if(method=='GET'){var content=[];for(var k in pars){content.push(k+'='+(!!pars[k]||pars[k]==0?pars[k]:'').toString())}return'?'+content.join('&')}return JSON.stringify(pars||'')};
var onReadyStateChange=function(e){var req=e.target;if(this.active&&req.readyState==4){this.active=false;var response=req.response;var data;try{data=JSON.parse(response)}catch(e){data=response}if(isFunction(this.callback)){this.callback.call(this.thisObj||null,data)}}};
p=c.prototype;
p.setHeaders=function(h){this.headers=h};
p.setResponseType=function(r){this.responseType=r};
p.setWithCredentials=function(w){this.withCredentials=w};
p.setCallback=function(cb){this.callback=cb};
p.execute=function(pars){this.active=true;pars=pars||this.params;var u=this.tempUrl||this.url,method=this.method||'POST',content=getRequestContent.call(this,method,pars);createRequest.call(this);if(method=='GET'){u+=content;content=''}try{this.request.open(method,correctUrl.call(this,u),true)}catch(err){log('Error opening XMLHttpRequest: '+err.message,'execute',this);return}if(isObject(this.headers)){for(var k in this.headers){this.request.setRequestHeader(k,this.headers[k])}}if(method!='GET'&&(!this.headers||!this.headers['Content-Type'])){this.request.setRequestHeader('Content-Type','application/json')}if(this.responseType){this.request.responseType=this.responseType}this.request.withCredentials=this.withCredentials;this.request.send(content)};
p.send=function(method,pars,u){this.method=method;this.tempUrl=u;this.execute(pars);this.method=null;this.tempUrl=null};
return c;
}
this.url=url;this.callback=callback;this.params=params;this.thisObj=thisObj;
})(),'AjaxRequest');