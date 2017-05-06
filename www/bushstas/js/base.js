'use strict';
var __G;
new (function() {
(function(){
var cs={};
this.create=function(k){var c=this.get(k);if(c instanceof Function)cs[k]=new c()}
this.get=function(k,i){if(i){this.create(k)}return cs[k]}
this.set=function(c,k,i) {if(cs[k])return;cs[k]=c;if(i){this.create(k)}}
}).call(__G=this);
(function(){var __A,c,p;var __,__T,__DU;
var CONFIG={'user':{'get':'user/get.php'},'filters':{'load':'filters/get.php','save':'filters/add.php','set':'filters/set.php','subscribe':'filters/subscribe.php'},'support':{'send':'support/send.php'},'orderCall':{'send':'orderCall/send.php'},'favorites':{'get':'favorites/get.php','add':'favorites/add.php','remove':'favorites/remove.php'},'filterStat':{'load':'filters/count.php'},'settings':{'subscr':'settings/get.php','set':'settings/set.php'},'keywords':{'get':'keywords/get.php','recommendations':'keywords/getRecommendations.php'}},
__LU='/../bushstas-api/loadApp.php',
__V=function(){return [[{'value':'0','title':__[14],'tooltip':'morph'},{'value':'1','title':__[15],'tooltip':'nonmorph'}],{'text':__[18]},{'text':__[19]},{'text':__[20]},{'url':CONFIG.keywords.get},[{'value':'1','title':__[32]},{'value':'2','title':__[33]}],[{'value':'1','title':__[34]},{'value':'2','title':__[35]},{'value':'3','title':__[36]},{'value':'4','title':__[37]},{'value':'5','title':__[38]},{'value':'6','title':__[39]},{'value':'7','title':__[40]}],[{'value':'1','title':__[42]},{'value':'2','title':__[43]}],{'caption':__[55],'class':'half-width','controlClass':'Input','controlProps':{'type':'text','name':'name'}},{'caption':__[57],'class':'half-width','controlClass':'Input','controlProps':{'type':'text','name':'email'}},{'caption':__[56],'class':'half-width','controlClass':'Input','controlProps':{'type':'text','name':'phone'}},[{'title':__[112],'value':84},{'title':__[113],'value':7},{'title':__[114],'value':3}],{'tabs':[{'title':__[95],'active':true},{'title':__[96]},{'title':__[97]}],'rest':{'showCount':true},'containerClass':'tab-content'},{'title':__[98],'extended':true,'className':'main-page-filter-stat'},{'key':'calendar','className':'float-right'},{'aaa':'Привет'}]},
__TG=['div','span','table','tbody','thead','tr','td','th','ul','ol','li','p','a','form','input','img','video','audio','aside','article','b','big','blockquote','button','canvas','caption','code','col','colgroup','footer','h1','h2','h3','h4','h5','h6','header','hr','i','iframe','label','menu','pre','s','section','select','strong','textarea','small','nav','abbr','address','area','map','source','basefont','cite','datalist','dt','dl','dd','del','details','dfn','em','embed','fieldset','figcaption','figure','ins','kbd','keygen','main','mark','meter','optgroup','option','output','param','progress','q','samp','sub','summary','sup','tfoot','time','var','wbr'],
__AT={'c':'class','i':'id','v':'value','t':'title','p':'placeholder','tp':'type','h':'href','s':'src','tr':'target','m':'method','st':'style','w':'width','ht':'height','sz':'size','mx':'maxlength','a':'action','n':'name','sc':'scope','r':'role','cp':'cellpadding','cs':'cellspacing'},
__DW={'filter':['фильтр','фильтра','фильтров'],'subscr':['подписка на рассылку','подписки на рассылку','подписок на рассылку']},
__ET=['click','mouseover','mouseout','mouseenter','mouseleave','mousemove','contextmenu','dblclick','mousedown','mouseup','keydown','keyup','keypress','blur','change','focus','focusin','focusout','input','invalid','reset','search','select','submit','drag','dragend','dragenter','dragleave','dragover','dragstart','drop','copy','cut','paste','popstate','wheel','storage','show','toggle','touchend','touchmove','touchstart','touchcancel','message','error','open','transitionend','abort','play','pause','load','durationchange','progress','resize','scroll','unload','hashchange','beforeunload','pageshow','pagehide'],
__RT=[{'name':'main','view':'Main','accessLevel':10,'title':'Home','load':[0,1],'params':{'text':'blablabla','name':'$2'}},{'name':'search','view':'Search','accessLevel':0,'title':'Поиск','load':[1]},{'name':'favorite','view':'Favorite','accessLevel':0,'title':'Избранное'}],
__ER={'404':'Error404','401':'Error401'},
__HR=true,
__IR='main',
__DR=null,
__VC='app-view-container',
__PV='parental-view-container',
__TC='TooltipPopup',
__TA='',
__AD='../bushstas-api',
__PT='Page title',
__UO={'login':'user/login.php','logout':'user/logout.php','save':'user/save.php','fullAccess':11,'adminAccess':100},
__CT=['Favorites','Filters','FiltersStat','RecommendationsLoader','Subscription','UserInfoLoader'],
__FN=function(){return},
__SP=function(e){e.stopPropagation()},
__PD=function(e){e.preventDefault()},
__F=function(){return new Function};
__G.set((c=function(){
if(!this||this==window){
var controllers,dictionary;
var getViewParams=function(route,allParams){var params;if(isObject(route['dynamicParams'])){params={};for(var k in route['dynamicParams']){params[k]=Router.getPathPartAt(route['dynamicParams'][k])}}if(allParams){if(isObject(params)){Objects.merge(params,route['params'])}else{params=route['params']}}return params};
var loadControllers=function(route){if(isArray(route['load'])||isNumber(route['load'])){controllers.load(route['load'])}};
var loadView=function(route){var script=document.createElement('script');script.src='/js/base_'+route['name']+'_chunk.js';document.body.appendChild(script);script.onload=onViewLoaded.bind(this,route)};
var onViewLoaded=function(route){route['view']=__G.get(route['view']);renderView.call(this,route)};
var activateView=function(view,isSameView){if(!view)return;var parentElement=__A.getParentElement.call(view);var params=getViewParams.call(this,this.currentRoute);if(isObject(params)){view.set(params)}if(!isSameView){this.viewContainer.appendChild(parentElement)}view.activate(true)};
var disactivateView=function(){var view=this.views[this.currentView];if(view){var parentElement=__A.getParentElement.call(view);this.viewContainer.removeChild(parentElement);view.activate(false)}};
var renderView=function(route){loadControllers(route);if(!isUndefined(dictionary)){dictionary.load(route['name'])}var view=this.views[route['name']]=new route['view']();var viewParams=getViewParams.call(this,route,true);__A.initiate.call(view,viewParams);view.setOnReadyHandler(onViewReady.bind(this));var viewContentElement=createViewContentElement.call(this,route['name']);view.render(viewContentElement);if(isNumber(route['error'])){this.onError(route['error'])}else{this.onNoErrors()}};
var handleNavigation=function(route,changeTitle){if(this.currentRoute&&route['name']!=this.currentRoute){disactivateView.call(this)}this.isChangeTitle=changeTitle;this.currentRoute=route;var isSameView=this.currentView==route['name'];this.currentView=route['name'];var view=this.views[route['name']];if(!view){view=__G.get(route['view']);if(!view){loadView.call(this,route)}else{route['view']=view;renderView.call(this,route)}}else{activateView.call(this,view,isSameView)}};
var defineViews=function(){for(var i=0;i<__RT.length;i++){this.views[__RT[i]['name']]=null;if(isArray(__RT[i]['children'])){this.defineViews(__RT[i]['children'])}}if(isObject(__ER)){for(var k in __ER){this.views[k]=null}}};
var createViewContainer=function(){var viewContainer;if(__VC){viewContainer=document.body.querySelector('.'+__VC)}if(!viewContainer){viewContainer=document.createElement('div');if(__VC){viewContainer.className=__VC}this.element.appendChild(viewContainer)}this.viewContainer=viewContainer};
var onViewReady=function(){if(this.isChangeTitle){var title=this.currentRoute['title'];if(isString(title)){var titleParams=this.views[this.currentView].getTitleParams();if(isObject(titleParams)){var regExp;for(var k in titleParams){regExp=new RegExp("\$"+k);title=title.replace(regExp,titleParams[k])}}}this.setPageTitle(title||__PT||'')}};
var createViewContentElement=function(name){var element=document.createElement('div');element.className=__PV;element.setData('name',name);this.viewContainer.appendChild(element);return element};
p=c.prototype;
p.initiate=function(){this.views={}};
p.run=function(){dictionary=__G.get('Dictionary');controllers=__G.get('__C');defineViews.call(this);Router.setNavigationHandler(handleNavigation.bind(this));Router.init();this.element=document.createElement('div');document.body.appendChild(this.element);this.render(this.element);createViewContainer.call(this);Router.run()};
p.setPageTitle=function(title){var titleElement=document.getElementsByTagName('title')[0];if(!isElement(titleElement)){var headElement=document.getElementsByTagName('head')[0];if(!isElement(headElement)){var htmlElement=document.getElementsByTagName('html')[0];headElement=htmlElement.appendChild(document.createElement('head'))}titleElement=headElement.appendChild(document.createElement('title'))}titleElement.innerHTML=title};
p.getView=function(viewName){return this.views[viewName]};
p.disposeView=function(viewName){if(isObject(this.views[viewName])){this.views[viewName].dispose();this.views[viewName]=null}};
p.onNoErrors=function(){};
p.onError=function(){};
return c;
}
})(),'Application');
__G.set((c=function(){
if(!this||this==window){
var load=function(){var loader=Objects.get(this.initials,'loader');if(isObject(loader)&&isObject(loader['controller'])){this.preset('__loading',true);var isAsync=!!loader['async'];var options=loader['options'];if(isFunction(options))options=options();loader['controller'].addSubscriber('load',{'initiator':this,'callback':onDataLoad.bind(this,isAsync)},!!loader['private']);loader['controller'].doAction(this,'load',options);if(!isAsync){renderTempPlaceholder.call(this);return}}onReadyToRender.call(this)};
var renderTempPlaceholder=function(){this.tempPlaceholder=document.createElement('span');this.parentElement.appendChild(this.tempPlaceholder)};
var onDataLoad=function(isAsync,data){this.toggle('__loading');this.onLoaded(data);var loader=this.initials['loader'];if(isFunction(loader['callback'])){loader['callback'].call(this)}if(!isAsync)onReadyToRender.call(this)};
var onReadyToRender=function(){if(!this.isRendered()){render.call(this);if(this.tempPlaceholder){this.parentElement.removeChild(this.tempPlaceholder);this.tempPlaceholder=null}__A.processPostRenderInitials.call(this)}};
var render=function(){var lvl=__G.get('Level');this.level=new lvl(this);var content=this.getTemplateMain(this.props,this);if(content)this.level.render(content,this.parentElement,this,this.tempPlaceholder);this.rendered=true;this.onRendered();this.onRenderComplete();for(var i=0;i<this.inheritedSuperClasses.length-1;i++){this.inheritedSuperClasses[i].prototype.onRenderComplete.call(this)}this.forEachChild(function(child){if(isFunction(child.onParentRendered))child.onParentRendered.call(child)});delete this.waiting};
var propagatePropertyChange=function(changedProps){if(!this.updaters)return;var updated=[];for(var k in changedProps){if(this.updaters[k]){for(var i=0;i<this.updaters[k].length;i++){if(updated.indexOf(this.updaters[k][i])==-1){this.updaters[k][i].react(changedProps);updated.push(this.updaters[k][i])}}}}updated=null;callFollowers.call(this,changedProps)};
var callFollowers=function(changedProps){for(var k in changedProps){callFollower.call(this,k,changedProps[k])}};
var callFollower=function(propName,propValue){if(Objects.has(this.followers,propName))this.followers[propName].call(this,propValue)};
var updateForeach=function(propName,index,item){var updaters=this.updaters[propName],o;if(isArray(updaters)){for(var i=0;i<updaters.length;i++){if(updaters[i]instanceof __G.get('OperatorUpdater')){o=updaters[i].getOperator();if(o instanceof __G.get('Foreach')){if(!isUndefined(item))o.add(item,index);else o.remove(index)}}}}};
p=c.prototype;
p.render=function(parentElement){this.parentElement=parentElement;load.call(this)};
p.isDisabled=function(){return!!this.disabled};
p.isRendered=function(){return!!this.rendered};
p.isDisposed=function(){return!!this.disposed};
p.instanceOf=function(classFunc){if(isString(classFunc))classFunc=__G.get(classFunc);return this instanceof classFunc||(this.inheritedSuperClasses&&this.inheritedSuperClasses.indexOf(classFunc)>-1)};
p.disable=function(isDisabled){this.disabled=isDisabled;this.addClass('disabled',!isDisabled)};
p.dispatchEvent=function(eventType){var args=Array.prototype.slice.call(arguments),l;args.splice(0,1);if(isArray(this.listeners)){for(var i=0;i<this.listeners.length;i++){l=this.listeners[i];if(isNumber(l['type']))l['type']=__ET[l['type']];if(l['type']==eventType){l['handler'].apply(l['subscriber'],args)}}}};
p.addListener=function(target,eventType,handler){if(isElement(target)){var eh=__G.get('EventHandler');this.eventHandler=this.eventHandler||new eh();this.eventHandler.listen(target,eventType,handler.bind(this))}else target.subscribe(eventType,handler,this)};
p.removeValueFrom=function(propName,value){var prop=this.get(propName);if(isArray(prop))this.removeByIndexFrom(propName,prop.indexOf(value))};
p.removeByIndexFrom=function(propName,index){var prop=this.get(propName);if(isString(index)&&isNumeric(index))index=~~index;if(isArray(prop)&&isNumber(index)&&index>-1&&!isUndefined(prop[index])){prop.splice(index,1);updateForeach.call(this,propName,index);callFollower.call(this,propName,prop)}};
p.change=function(propName,add,sign){var prop=this.get(propName);if(!sign||sign=='+'){if(isNumber(prop)||isString(prop))this.set(propName,prop+add)}else if(isNumber(prop)&&isNumber(add)){var v;if(sign=='-')v=prop-add;else if(sign=='*')v=prop*add;else if(sign=='/')v=prop/add;else if(sign=='%')v=prop%add;this.set(propName,v)}};
p.addOneTo=function(propName,item,index){this.addTo(propName,[item],index)};
p.addTo=function(propName,items,index){var prop=this.get(propName);if(!isArray(items))items=[items];if(isArray(prop)){for(var j=0;j<items.length;j++){if(!isNumber(index))prop.push(items[j]);else if(index==0)prop.unshift(items[j]);else prop.insertAt(items[j],index);updateForeach.call(this,propName,index,items[j]);if(isNumber(index))index++}callFollower.call(this,propName,prop)}};
p.get=function(propName){var prop=this.props[propName];if(isUndefined(arguments[1])||!isArrayLike(prop))return prop;var end;for(var i=1;i<arguments.length;i++){prop=prop[arguments[i]];if(isUndefined(prop))return'';end=arguments.length==i+1;if(end||!isArrayLike(prop))break}return end?prop||'':''};
p.setVisible=function(isVisible){if(this.isRendered()&&!this.isDisposed())this.getElement().show(isVisible)};
p.addClass=function(className,isAdding){if(this.isRendered()){if(isAdding||isUndefined(isAdding))this.getElement().addClass(className);else this.getElement().removeClass(className)}};
p.each=function(propName,callback){var ar=this.get(propName);if(isArrayLike(ar)&&isFunction(callback)){if(isArray(ar))for(var i=0;i<ar.length;i++)callback.call(this,ar[i],i,ar);else for(var k in ar)callback.call(this,ar[k],k,ar)}};
p.toggle=function(propName){this.set(propName,!this.get(propName))};
p.set=function(propName,propValue){this.props=this.props||{};var props;if(!isUndefined(propValue)){props={};props[propName]=propValue}else if(isObject(propName)){props=propName}else return;var isChanged=false;var changedProps={};var currentValue;for(var k in props){if(Objects.has(this.correctors,k))props[k]=this.correctors[k].call(this,props[k],props);currentValue=this.props[k];if(currentValue==props[k])continue;if(isArray(currentValue)&&isArray(props[k])&&Objects.equals(currentValue,props[k]))continue;isChanged=true;this.props[k]=props[k];changedProps[k]=props[k]}if(this.isRendered()){if(isChanged)propagatePropertyChange.call(this,changedProps)}changedProps=null};
p.preset=function(propName,propValue){this.props=this.props||{};this.props[propName]=propValue};
p.delay=function(f,n,p){window.clearTimeout(this.timeout);if(isFunction(f))this.timeout=window.setTimeout(f.bind(this,p),n||200)};
p.addChild=function(child,parentElement){this.level.renderComponent(child,parentElement)};
p.removeChild=function(child){if(!child)return;var childId=child;if(isString(child))child=this.getChild(child);else childId=Objects.getKey(this.children,child);if(isComponentLike(child))child.dispose();if((isString(childId)||isNumber(childId))&&isObject(this.children)){this.children[childId]=null;delete this.children[childId]}};
p.forEachChild=function(callback){if(isArrayLike(this.children)){var result;for(var k in this.children){if(!this.children[k].isDisabled()){result=callback.call(this,this.children[k],k);if(result)return result}}}};
p.forChildren=function(className,callback){var children=this.getChildren(className),result;for(var i=0;i<children.length;i++){result=callback.call(this,children[i],i);if(result)return result}};
p.getControl=function(name){return Objects.get(this.controls,name)||this.forEachChild(function(child){return child.getControl(name)})};
p.setControlValue=function(name,value){var control=this.getControl(name);if(control)control.setValue(value)};
p.enableControl=function(name,isEnabled){var control=this.getControl(name);if(control)control.setEnabled(isEnabled)};
p.forEachControl=function(callback){if(isObject(this.controls))Objects.each(this.controls,callback,this)};
p.hasControls=function(){return!Objects.empty(this.controls)};
p.getControlsData=function(data){data=data||{};this.forEachChild(function(child){if(!isControl(child))child.getControlsData(data);else data[child.getName()]=child.getValue()});return data};
p.setControlsData=function(data){this.forEachChild(function(child){if(!isControl(child))child.setControlsData(data);else child.setValue(data[child.getName()])});return data};
p.getChildAt=function(index){return Objects.getByIndex(this.children,index)};
p.getChildIndex=function(child,same){var idx=-1;this.forEachChild(function(ch){if(!same||(same&&ch.constructor==child.constructor))idx++;if(ch==child)return true});return idx};
p.getChildren=function(className){if(!isString(className))return this.children;var children=[];this.forEachChild(function(child){if(isComponentLike(child)&&child.instanceOf(className))children.push(child)});return children};
p.getChild=function(id){return Objects.get(this.children,id)};
p.getId=function(){return this.componentId};
p.getElement=function(id){if(isString(id))return Objects.get(this.elements,id);else return this.scope||this.parentElement};
p.findElement=function(selector,scopeElement){return(scopeElement||this.getElement()).querySelector(selector)};
p.findElements=function(selector,scopeElement){return Array.prototype.slice.call((scopeElement||this.scope||this.parentElement).querySelectorAll(selector))};
p.fill=function(element,data){if(isString(element))element=this.findElement(element);if(isElement(element)){var callback=function(el){for(var i=0;i<el.childNodes.length;i++){if(isElement(el.childNodes[i])){callback(el.childNodes[i])}else if(isText(el.childNodes[i])&&!isUndefined(data[el.childNodes[i].placeholderName])){el.childNodes[i].textContent=data[el.childNodes[i].placeholderName]}}};callback(element)}};
p.setAppended=function(isAppended){if(this.level)this.level.setAppended(isAppended)};
p.placeTo=function(element){if(this.level)this.level.placeTo(element)};
p.placeBack=function(){this.setAppended(true)};
p.appendChild=function(child,isAppended){if(isString(child))child=this.getChild(child);if(isComponentLike(child))child.setAppended(isAppended)};
p.setScope=function(scope){this.scope=scope};
p.getUniqueId=function(){return this.uniqueId=this.uniqueId||generateRandomKey()};
p.dispose=function(){__G.get('State').dispose(this);var core=__A;core.disposeLinks.call(this);core.disposeInternal.call(this);if(this.mouseHandler){this.mouseHandler.dispose();this.mouseHandler=null}this.level.dispose();this.elements=null;this.level=null;this.listeners=null;this.updaters=null;this.parentElement=null;this.props=null;this.children=null;this.initials=null;this.followers=null;this.correctors=null;this.controls=null;this.disposed=true};
p.a=function(n){return __G.get('State').get(n)};
var f=function(){return};
p.initOptions=f;
p.onRendered=f;
p.onRenderComplete=f;
p.onLoaded=f;
p.getTemplateMain=f;
p.disposeInternal=f;
p.g=p.get;
p.d=p.dispatchEvent;
return c;
}
})(),'Component');
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
__G.set((c=function(){
if(!this||this==window){
var onChangeChildControl=function(e){this.dispatchChange()};
p=c.prototype;
p.initiate=function(){this.preset('enabled',true)};
p.onChange=function(e){};
p.dispatchChange=function(){var params=this.getChangeEventParams();this.onChange(params);this.dispatchEvent('change',params)};
p.getChangeEventParams=function(){return{value:this.getValue()}};
p.registerControl=function(control,name){__G.get('Component').prototype.registerControl.call(this,control,name);this.addListener(control,'change',onChangeChildControl.bind(this))};
p.setName=function(name){this.name=name};
p.getName=function(){return this.name};
p.getValue=function(){var value;if(this.hasControls()){value={};for(var k in this.controls){if(isArray(this.controls[k])){value[k]=[];for(var i=0;i<this.controls[k].length;i++)value[k].push(this.controls[k][i].getValue())}else value[k]=this.controls[k].getValue()}}else value=this.getControlValue();return value};
p.getControlValue=function(){return this.get('value')};
p.getProperValue=function(value){return value};
p.setValue=function(value,fireChange){if(this.hasControls()){this.setControlsData(value)}this.setControlValue(value);if(fireChange)this.dispatchChange()};
p.setControlValue=function(value){this.set('value',value)};
p.isEnabled=function(){return!!this.get('enabled')};
p.setEnabled=function(isEnabled){this.set('enabled',isEnabled)};
p.clear=function(fireChange){this.clearControl();if(fireChange)this.dispatchChange()};
p.clearControl=function(){this.setControlValue('')};
p.disposeInternal=function(){this.controls=null;this.options=null};
return c;
}
})(),'Control');
__G.set((c=function(){
if(!this||this==window){
var makeUrl=function(url,options){var regExp,tmpUrl;for(var k in options){if(isString(options[k])||isNumber(options[k])){regExp=new RegExp('\$'+k);tmpUrl=url;url=url.replace(regExp,options[k]);if(tmpUrl!=url)delete options[k]}}return url};
var gotFromStore=function(actionName,options,initiator){if(actionName=='load'&&shouldStore.call(this)){var storeAs=getStoreAs.call(this,options);if(isString(storeAs)&&typeof StoreKeeper!='undefined'){var storedData=StoreKeeper.getActual(storeAs,Objects.get(this.options,'storePeriod'));if(isArrayLike(storedData)){onActionComplete.call(this,actionName,true,initiator,storedData);return true}}}return false};
var isPrivate=function(initiator){return initiator&&this.privateSubscribers.has(initiator.getUniqueId())};
var onActionComplete=function(actionName,isFromStorage,initiator,data){this.activeRequests.removeItem(actionName);if(initiator&&!isPrivate.call(this,initiator))initiator=null;this.data=this.data||{};this.data[actionName]=data;var action=getAction.call(this,actionName);if(isObject(action)&&isFunction(action['callback'])){action['callback'].call(this,data)}if(action['autoset'])autoset.call(this,action['autoset'],data,initiator);this.dispatchEvent(actionName,data,initiator);if(!isFromStorage&&actionName=='load'&&shouldStore.call(this)){store.call(this,true,data)}};
var autoset=function(opts,data,initiator){var props={};if(isString(opts)){props[opts]=data}else if(isObject(opts)){for(var k in opts)props[opts[k]]=data[k]}if(initiator)initiator.set(props);else if(isArray(this.subscribers['load'])){for(var i=0;i<this.subscribers['load'].length;i++){this.subscribers['load'][i]['initiator'].set(props)}}};
var shouldStore=function(){var should=Objects.get(this.options,'store');if(should===false)return false;return Objects.has(this.options,'storeAs')};
var store=function(isAdding,data){if(typeof StoreKeeper=='undefined')return;var storeAs=getStoreAs.call(this,data);if(isAdding){StoreKeeper.set(storeAs,data)}else{StoreKeeper.remove(storeAs)}};
var getStoreAs=function(data){var storeAs=Objects.get(this.options,'storeAs');if(isArrayLike(data)&&isString(storeAs)&&(/$[a-z_]/i).test(storeAs)){var parts=storeAs.split('$');storeAs=parts[0];for(var i=1;i<parts.length;i++){if(data[parts[i]])storeAs+=data[parts[i]];else storeAs+=parts[i]}}return storeAs};
var getPrimaryKey=function(){return Objects.get(this.options,'key','id')};
var initActionRouteOptions=function(action){var value;this.currentRouteOptions={};var routeOptions={};for(var k in action['routeOptions']){value=Router.getPathPartAt(action['routeOptions'][k]);if(isString(value)){routeOptions[k]=value}}setCurrentRouteOptions.call(this,routeOptions,action);Router.subscribe(action['routeOptions'],this)};
var setCurrentRouteOptions=function(routeOptions,action){this.currentRouteOptions=routeOptions;if(!isObject(action['options'])){action['options']={}}for(var k in routeOptions){action['options'][k]=routeOptions[k]}};
var getAction=function(actionName){var actions=Objects.get(this.initials,'actions');if(isObject(actions)){var action=actions[actionName];if(isObject(action)){if(!isString(action['name'])){if(isObject(action['routeOptions'])&&actionName=='load'){initActionRouteOptions.call(this,action)}action['name']=actionName}return action}}return null};
var getNewRequest=function(){var ajr=__G.get('AjaxRequest');return new ajr()};
var getRequest=function(action){return this.requests[action['name']]=this.requests[action['name']]||getNewRequest()};
var getOptions=function(options,action,initiator){if(!isObject(options))options={};if(isObject(action['options']))Objects.merge(options,action['options']);if(isPrivate.call(this,initiator)){Objects.merge(options,getPrivateOptions.call(this,initiator))}return options};
var getPrivateOptions=function(initiator){return this.privateOptions[initiator.getUniqueId()]};
var send=function(action,options,initiator){var url=makeUrl(action['url'],options);var req=getRequest.call(this,action);req.setCallback(onActionComplete.bind(this,action['name'],false,initiator));req.send(action['method'],options,url);this.activeRequests.push(action['name'])};
p=c.prototype;
p.initiate=function(){this.subscribers={};this.requests={};this.activeRequests=[];this.privateSubscribers=[];this.privateOptions={}};
p.addSubscriber=function(actionName,data,isPriv,options){this.subscribers[actionName]=this.subscribers[actionName]||[];this.subscribers[actionName].push(data);if(isPriv){var uid=data['initiator'].getUniqueId();this.privateSubscribers.push(uid);if(options)this.privateOptions[uid]=options}};
p.removeSubscriber=function(initiator){this.privateSubscribers.removeItem(initiator.getUniqueId());var done=false;for(var actionName in this.subscribers){for(var i=0;i<this.subscribers[actionName].length;i++){if(this.subscribers[actionName][i]['initiator']==initiator){this.subscribers[actionName].splice(i,1);break}}}};
p.dispatchEvent=function(actionName,data,initiator){var dataToDispatch=data;if(Objects.has(this.options,'clone',true))dataToDispatch=Objects.clone(data);var s=this.subscribers[actionName],i,p;if(isArray(s)){for(i=0;i<s.length;i++){p=(!initiator&&!isPrivate.call(this,s[i]['initiator']))||initiator==s[i]['initiator'];if(p&&isFunction(s[i]['callback'])){s[i]['callback'].call(s[i]['initiator'],dataToDispatch,this)}}}};
p.instanceOf=function(classFunc){if(isString(classFunc))classFunc=__G.get(classFunc);return this instanceof classFunc||(this.inheritedSuperClasses&&this.inheritedSuperClasses.indexOf(classFunc)>-1)};
p.getData=function(actionName){return!!action&&!!this.data&&isObject(this.data)?this.data[action]:this.data};
p.getItemById=function(id){var primaryKey=getPrimaryKey.call(this);var data=this.data['load'];if(isArray(data)){for(var i=0;i<data.length;i++){if(Objects.has(data[i],primaryKey,id))return data[i]}}return null};
p.getItem=function(nameOrIndex,actionName){actionName=actionName||'load';return isArrayLike(this.data[actionName])?this.data[actionName][nameOrIndex]:null};
p.doAction=function(initiator,actionName,options){if(this.activeRequests.has(actionName))return;var action=getAction.call(this,actionName);if(isObject(action)&&!gotFromStore.call(this,actionName,options,initiator)){options=getOptions.call(this,options,action,initiator);send.call(this,action,options,initiator)}};
p.handleRouteOptionsChange=function(routeOptions){if(!Objects.equals(routeOptions,this.currentRouteOptions)){setCurrentRouteOptions.call(this,routeOptions,getAction.call(this,'load'));this.doAction(null,'load')}};
return c;
}
})(),'Controller');
__G.set((c=function(params){
if(!this||this==window){
var getKeysInRandomOrder=function(){var keys=Objects.getKeys(getParam.call(this,'p'));keys.shuffle();return keys};
var createIfEmptyLevel=function(){if(!isUndefined(this.params['ie'])){__A.createLevel.call(this,this.params['ie'])}};
var getParam=function(p){return(isFunction(this.params['p'])?this.params['p']():this.params)[p]};
var createLevels=function(isUpdating){var p=this.params;var items=getParam.call(this,'p'),limit=getParam.call(this,'l'),r;if(isArrayLike(items)){if(p['ra']){if(!Objects.empty(items)){var keys=getKeysInRandomOrder();for(var i=0;i<keys.length;i++){if(limit&&i+1>limit)break;r=p['h'](items[keys[i]],keys[i]);if(r=='_brk')break;__A.createLevel.call(this,r,isUpdating)}return}}else if(isArray(items)){var from=getParam.call(this,'fr'),to=getParam.call(this,'to');if(!items.isEmpty()){var start;if(!p['r']){start=isNumber(from)?from:0;for(var i=start;i<items.length;i++){if(limit&&i+1>limit)break;if(isNumber(to)&&i>to)break;r=p['h'](items[i],i);if(r=='_brk')break;__A.createLevel.call(this,r,isUpdating)}}else{var j=0;start=isNumber(from)?from:items.length-1;for(var i=start;i>=0;i--){j++;if(limit&&j>limit)break;if(isNumber(to)&&i<to)break;r=p['h'](items[i],i);if(r=='_brk')break;__A.createLevel.call(this,r,isUpdating)}}return}}else if(isObject(items)){if(!Objects.empty(items)){if(!p['r']){var i=0;for(var k in items){i++;if(limit&&i>limit)break;r=p['h'](items[k],k);if(r=='_brk')break;__A.createLevel.call(this,r,isUpdating)}}else{var keys=Objects.getKeys(items);keys.reverse();for(var i=0;i<keys.length;i++){if(limit&&i+1>limit)break;r=p['h'](items[keys[i]],keys[i]);if(r=='_brk')break;__A.createLevel.call(this,r,isUpdating)}}return}}}createIfEmptyLevel.call(this)};
p=c.prototype;
p.render=function(pe,pl){__A.initOperator.call(this,pe,pl);createLevels.call(this,false)};
p.update=function(){__A.disposeLevels.call(this);createLevels.call(this,true)};
p.add=function(item,index){var r=this.params['h'](item,~~index);if(r!='_brk')__A.createLevel.call(this,r,false,index)};
p.remove=function(index){if(this.levels[index]){this.levels[index].dispose();this.levels.splice(index,1)}};
p.dispose=function(){__A.disposeOperator.call(this)};
return c;
}
this.params=params;
})(),'Foreach');
__G.set((c=function(params){
if(!this||this==window){
var createLevels=function(isUpdating){var p=this.params,f=p['f'];p=(isFunction(p['p'])?p['p']():p['p'])||[];var a=~~p[0],b=~~p[1],s=~~p[2]||1;for(var i=a;i<=b;i+=s){__A.createLevel.call(this,f(i),isUpdating)}};
p=c.prototype;
p.render=function(pe,pl){__A.initOperator.call(this,pe,pl);createLevels.call(this,false)};
p.update=function(){__A.disposeLevels.call(this);createLevels.call(this,true)};
p.dispose=function(){__A.disposeOperator.call(this)};
return c;
}
this.params=params;
})(),'From');
__G.set((c=function(params){
if(!this||this==window){
var isChanged=function(){var v=this.params['is']()['is'],c=this.cur;if(!isArray(v))v=[v];for(var i=0;i<v.length;i++){if(!!v[i]){this.cur=i;return i!==c}}this.cur=null;return c!==null};
var createLevels=function(isUpdating){var p=this.params['is']();var c=p['c'],d=p['d'];if(!isArray(c))c=[c];if(this.cur!==null){__A.createLevel.call(this,c[this.cur],isUpdating)}else if(!isUndefined(d)){__A.createLevel.call(this,d,isUpdating)}};
p=c.prototype;
p.render=function(pe,pl){__A.initOperator.call(this,pe,pl);isChanged.call(this);createLevels.call(this,false)};
p.update=function(){if(isChanged.call(this)){__A.disposeLevels.call(this);createLevels.call(this,true)}};
p.dispose=function(){__A.disposeOperator.call(this);this.cur=null};
return c;
}
this.params=params;this.cur=null;
})(),'IfSwitch');
__G.set((c=function(params){
if(!this||this==window){
var createLevels=function(isUpdating){__A.createLevel.call(this,this.params['l'](),isUpdating)};
p=c.prototype;
p.render=function(pe,pl){__A.initOperator.call(this,pe,pl);createLevels.call(this,false)};
p.update=function(){__A.disposeLevels.call(this);createLevels.call(this,true)};
p.dispose=function(){__A.disposeOperator.call(this)};
return c;
}
this.params=params;
})(),'Let');
__G.set((c=function(component){
if(!this||this==window){
var renderItems=function(items){if(isArray(items)){for(var i=0;i<items.length;i++){if(!isArray(items[i]))renderItem.call(this,items[i]);else renderItems.call(this,items[i])}}else renderItem.call(this,items)};
var renderItem=function(i){if(!i&&i!==0)return;if(isFunction(i)){renderItems.call(this,i());return}if(!isObject(i))createTextNode.call(this,i);else if(i.hasOwnProperty('t'))createElement.call(this,i);else if(i.hasOwnProperty('v'))createPropertyNode.call(this,i);else if(i.hasOwnProperty('i'))createCondition.call(this,i);else if(i.hasOwnProperty('h'))createForeach.call(this,i);else if(i.hasOwnProperty('tmp'))includeTemplate.call(this,i);else if(i.hasOwnProperty('cmp'))renderComponent.call(this,i);else if(i.hasOwnProperty('is'))createIfSwitch.call(this,i);else if(i.hasOwnProperty('sw'))createSwitch.call(this,i);else if(i.hasOwnProperty('pl'))createPlaceholder.call(this,i);else if(i.hasOwnProperty('l'))createLet.call(this,i);else if(i.hasOwnProperty('f'))createFrom.call(this,i)};
var createLevel=function(items,pe){var lvl=__G.get('Level');var level=new lvl(this.cmp);level.render(items,pe,this);this.children.push(level)};
var createTextNode=function(content){if(content=='<br>')appendChild.call(this,document.createElement('br'));else appendChild.call(this,document.createTextNode(content))};
var createUpdater=function(u,s,p){this.updaters=this.updaters||[];if(p['n'])__A.createUpdater(u,p['$']||this.cmp,s,p,this.updaters);if(p['g'])__G.get('State').createUpdater(u,p['$']||this.cmp,s,p)};
var createPropertyNode=function(props){var v='',pv=props['v'];if(isFunction(pv))pv=pv();if(!isUndefined(pv))v=pv;var node=document.createTextNode(v);appendChild.call(this,node);createUpdater.call(this,__G.get('NodeUpdater'),node,props)};
var createElement=function(props){var element=document.createElement(__TG[props['t']]||'span');appendChild.call(this,element);if(props['p']){var pr=isFunction(props['p'])?props['p']():props['p'];var a;for(var k in pr){a=__AT[k]||k;if(a=='scope')this.cmp.setScope(element);else if(a=='as')__A.registerElement.call(this.cmp,element,pr[k]);else if(isPrimitive(pr[k])&&pr[k]!==''){element.attr(a,pr[k])}}if(props['n']||props['g']){createUpdater.call(this,__G.get('ElementUpdater'),element,props)}}if(isArray(props['e'])){var eventType,callback,isOnce,i;this.eventHandler=this.eventHandler||new(__G.get('EventHandler'))();for(i=0;i<props['e'].length;i++){eventType=__ET[props['e'][i]]||eventType;callback=props['e'][i+1];isOnce=props['e'][i+2]===true;if(isString(eventType)&&isFunction(callback)){if(isOnce){this.eventHandler.listenOnce(element,eventType,callback.bind(this.cmp));i++}else this.eventHandler.listen(element,eventType,callback.bind(this.cmp))}i++}}createLevel.call(this,props['c'],element)};
var appendChild=function(child){if(this.nextSiblingChild)this.parentElement.insertBefore(child,this.nextSiblingChild);else this.parentElement.appendChild(child);registerChild.call(this,child)};
var createCondition=function(props){if(isFunction(props['i'])){var condition=new(__G.get('Condition'))(props);condition.render(this.parentElement,this);registerChild.call(this,condition);createUpdater.call(this,__G.get('OperatorUpdater'),condition,props)}else if(!!props['i']){renderItems.call(this,props['c'])}else if(!isUndefined(props['e'])){renderItem.call(this,props['e'])}};
var createForeach=function(props){var foreach=new(__G.get('Foreach'))(props);foreach.render(this.parentElement,this);if(props['n']||props['g']){registerChild.call(this,foreach);createUpdater.call(this,__G.get('OperatorUpdater'),foreach,props)}};
var createFrom=function(props){var fr=new(__G.get('From'))(props);fr.render(this.parentElement,this);if(props['n']||props['g']){registerChild.call(this,fr);createUpdater.call(this,__G.get('OperatorUpdater'),fr,props)}};
var createIfSwitch=function(props){if(props['n']||props['g']){var swtch=new(__G.get('IfSwitch'))(props);swtch.render(this.parentElement,this);registerChild.call(this,swtch);createUpdater.call(this,__G.get('OperatorUpdater'),swtch,props)}else{for(var i=0;i<props['is'].length;i++){if(!!props['is'][i]){renderItems.call(this,props['c'][i]);return}}if(!isUndefined(props['d']))renderItems.call(this,props['d'])}};
var createSwitch=function(props){if(props['n']||props['g']){var swtch=new(__G.get('Switch'))(props);swtch.render(this.parentElement,this);registerChild.call(this,swtch);createUpdater.call(this,__G.get('OperatorUpdater'),swtch,props)}else{if(!isArray(props['cs']))props['cs']=[props['cs']];if(!isArray(props['c']))props['c']=[props['c']];for(var i=0;i<props['cs'].length;i++){if(props['sw']===props['cs'][i]){renderItems.call(this,props['c'][i]);return}}if(!isUndefined(props['d']))renderItems.call(this,props['d'])}};
var createLet=function(props){if(props['n']||props['g']){var l=new(__G.get('Let'))(props);l.render(this.parentElement,this);registerChild.call(this,l);createUpdater.call(this,__G.get('OperatorUpdater'),l,props)}};
var createPlaceholder=function(props){var placeholderNode=document.createTextNode('');if(isString(props['d']))placeholderNode.textContent=props['d'];placeholderNode.placeholderName=props['pl'];appendChild.call(this,placeholderNode)};
var registerChild=function(child,isComponent){var isNodeChild=isNode(child);if(this.prevChild)__A.setNextSiblingChild.call(this.prevChild,child);this.prevChild=isNodeChild?null:child;if(!this.firstChild)this.firstChild=child;if(isNodeChild){if(!this.firstNodeChild)this.firstNodeChild=child;this.lastNodeChild=child}else this.children.push(child);if(isComponent)__A.registerChildComponent.call(this.cmp,child)};
var includeTemplate=function(item){var props=item['p'];if(isObject(props)&&isObject(props['props'])){var tempProps=props['props'];delete props['props'];for(var k in props)tempProps[k]=props[k];props=tempProps}if(item['c']){props=props||{};props['children']=item['c']}if(isNumber(item['tmp']))item['tmp']=__G.get('i_'+item['tmp']);else if(isString(item['tmp']))item['tmp']=__A.getTemplateById.call(this.cmp,item['tmp']);if(isFunction(item['tmp'])){var items=item['tmp'].call(this.cmp,props,this.cmp);renderItems.call(this,items)}};
var renderComponent=function(item,pe){pe=pe||this.parentElement;item['cmp']=__G.get(item['cmp']);if(isFunction(item['cmp'])){var cmp=new item['cmp']();var ir=isFunction(item['p']);var i,k,p=ir?item['p']():item['p'];var props,data;if(isObject(p)){if(p['p']||p['ap'])props=initComponentProps.call(this,p['p'],p['ap']);if(isString(p['i'])){__A.setId.call(cmp,p['i']);var waiting=__A.getWaitingChild.call(this.cmp,p['i']);if(isArray(waiting)){for(i=0;i<waiting.length;i++){waiting[i][0].set(waiting[i][1],cmp)}}}}if(ir)createUpdater.call(this,__G.get('ComponentUpdater'),cmp,item);if(isArray(item['w'])){for(i=0;i<item['w'].length;i+=2){__A.provideWithComponent.call(this.cmp,item['w'][i],item['w'][i+1],cmp)}}if(item['c']){props=props||{};props['children']=item['c']}__A.initiate.call(cmp,props);cmp.render(pe);registerChild.call(this,cmp,true);if(isArray(item['e'])){for(i=0;i<item['e'].length;i++){__A.subscribe.call(cmp,item['e'][i],item['e'][i+1],this.cmp);i++}}if(item['nm'])__A.registerControl.call(this.cmp,cmp,item['nm'])}else if(item&&isObject(item)){if(!item.isRendered())item.render(pe);registerChild.call(this,item,true)}};
var initComponentProps=function(p,ap){var props={},k;var f=function(pr){if(isObject(pr)){for(k in pr)props[k]=pr[k]}};f(p);f(ap);return props};
var getElements=function(){var elements=[];if(this.firstNodeChild&&this.lastNodeChild){var isAdding=false,p=this.parentElement;for(var i=0;i<p.childNodes.length;i++){if(p.childNodes[i]==this.firstNodeChild)isAdding=true;if(isAdding)elements.push(p.childNodes[i]);if(p.childNodes[i]==this.lastNodeChild)break}}return elements};
var disposeDom=function(){var elementsToDispose=getElements.call(this);for(var i=0;i<elementsToDispose.length;i++)this.parentElement.removeChild(elementsToDispose[i]);elementsToDispose=null};
p=c.prototype;
p.render=function(items,pe,pl,nsc){this.parentElement=pe;this.parentLevel=pl;this.nextSiblingChild=nsc;renderItems.call(this,items);this.prevChild=null;this.nextSiblingChild=null};
p.getParentElement=function(){return this.parentElement};
p.getFirstNodeChild=function(){if(isNode(this.firstChild))return this.firstChild;var firstLevel=this.children[0];if(firstLevel instanceof __G.get('Level')){return __A.getParentElement.call(firstLevel)}else if(firstLevel){return __A.getFirstNodeChild.call(firstLevel)}return null};
p.getComponent=function(){return this.cmp};
p.setAppended=function(isAppended,p){var isDetached=!isAppended;if(isDetached===!!this.detached)return;this.detached=isDetached;var elements=getElements.call(this);if(isDetached){this.realParentElement=this.parentElement;this.parentElement=p||document.createElement('div');for(var i=0;i<elements.length;i++)this.parentElement.appendChild(elements[i])}else{this.nextSiblingChild=__A.getNextSiblingChild.call(this.parentLevel);this.parentElement=this.realParentElement;this.realParentElement=null;for(var i=0;i<elements.length;i++)appendChild.call(this,elements[i])}};
p.placeTo=function(element){this.setAppended(false,element)};
p.dispose=function(){if(this.updaters){for(var i=0;i<this.updaters.length;i++){__A.disposeUpdater.call(this.cmp,this.updaters[i],this.updaters[i+1]);this.updaters[i+1]=null;i++}}for(var i=0;i<this.children.length;i++){if(isComponentLike(this.children[i])){__A.unregisterChildComponent.call(this.cmp,this.children[i])}this.children[i].dispose();this.children[i]=null}if(this.eventHandler){this.eventHandler.dispose();this.eventHandler=null}disposeDom.call(this);this.updaters=null;this.children=null;this.parentElement=null;this.parentLevel=null;this.firstChild=null;this.firstNodeChild=null;this.lastNodeChild=null;this.realParentElement=null;this.cmp=null};
return c;
}
this.children=[];this.cmp=component;
})(),'Level');
__G.set((c=function(){
if(!this||this==window){
p=c.prototype;
p.onRenderComplete=function(){if(Router.hasMenu(this)){this.onNavigate(Router.getCurrentRouteName())}};
p.onNavigate=function(viewName){if(this.rendered){if(isElement(this.activeButton)){this.setButtonActive(this.activeButton,false)}var button=this.getButton(viewName);if(isElement(button)){this.setButtonActive(button,true)}}};
p.getButton=function(viewName){return this.findElement('a[role="'+viewName+'"]')};
p.setButtonActive=function(button,isActive){var activeClassName=this.activeButtonClass||'active';button.toggleClass(activeClassName,isActive);if(isActive){this.activeButton=button}};
p.disposeInternal=function(){this.activeButton=null};
return c;
}
})(),'Menu');
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
__G.set((c=function(){
if(!this||this==window){
p=c.prototype;
p.onRenderComplete=function(){this.dispatchReadyEvent()};
p.setOnReadyHandler=function(handler){this.onReadyHandler=handler};
p.dispatchReadyEvent=function(){if(isFunction(this.onReadyHandler)){this.onReadyHandler()}this.onReady()};
p.activate=function(isActivated){if(isActivated){this.dispatchReadyEvent()}};
p.getTitleParams=function(){};
p.onReady=function(){};
return c;
}
})(),'View');
__G.set(new(function(){
var subscribers=[];var options=[];var defaultCheckboxClass='checkbox';var defaultCheckboxCheckedClass='checked';var currentOptions,currentObject,currentScope,checkbox,labelClass,checkboxClass,targetClasses,currentCheckedClass,currentTarget;
var onClick=function(index,e){currentTarget=e.target;defineOptions(index);defineTargetClasses();if(isProperTarget()){defineCheckbox();defineCheckedClass();var checked=!isChecked();if(currentTarget)currentTarget.toggleClass(currentCheckedClass,checked);if(checkbox){checkbox.toggleClass(currentCheckedClass,checked);currentOptions['callback'].call(currentObject,{'target':checkbox,'name':getName(),'value':getValue(),'checked':checked,'intChecked':checked?1:0})}}};
var defineOptions=function(index){currentOptions=options[index];currentObject=subscribers[index];currentScope=currentObject.getElement()};
var defineTargetClasses=function(){targetClasses=[];defineCheckboxClass();if(checkboxClass)targetClasses.push(checkboxClass);labelClass=Objects.get(currentOptions,'labelClass');if(isString(labelClass))targetClasses.push(labelClass);else if(isArray(labelClass))targetClasses=targetClasses.concat(labelClass)};
var defineCheckboxClass=function(options){checkboxClass=Objects.get(options||currentOptions,'checkboxClass',defaultCheckboxClass)};
var isProperTarget=function(){while(currentTarget){if(targetClasses.hasIntersections(currentTarget.getClasses()))return true;currentTarget=currentTarget.parentNode;if(currentTarget==currentScope)break}return false};
var defineCheckbox=function(){if(currentTarget.hasClass(checkboxClass)){checkbox=currentTarget;currentTarget=null;if(isString(labelClass))currentTarget=checkbox.getAncestor('.'+labelClass);else if(isArray(labelClass)){for(var i=0;i<labelClass.length;i++){currentTarget=checkbox.getAncestor('.'+labelClass[i]);if(currentTarget)break}}}else checkbox=currentTarget.find('.'+checkboxClass)};
var defineCheckedClass=function(){currentCheckedClass=Objects.get(currentOptions,'checkboxCheckedClass',defaultCheckboxCheckedClass)};
var isChecked=function(){if(checkbox)return checkbox.hasClass(currentCheckedClass);return currentTarget.hasClass(currentCheckedClass)};
var getValue=function(){var value;if(checkbox)value=checkbox.getData('value');return isIntValue()?~~value:value};
var getName=function(){if(checkbox)return checkbox.getData('name')};
var isIntValue=function(){return Objects.has(currentOptions,'intValue',true)};
var getOptionsOfSubscriber=function(subscriber){return options[subscribers.indexOf(subscriber)]};
var getCheckboxByName=function(name,subscriber){currentOptions=getOptionsOfSubscriber(subscriber);defineCheckboxClass();return subscriber.findElement('.'+checkboxClass+'[_name="'+name+'"]')};
this.subscribe=function(subscriber,opts){if(isFunction(opts['callback'])&&subscribers.indexOf(subscriber)==-1){subscribers.push(subscriber);options.push(opts||null);var element=subscriber.getElement();if(element){var index=subscribers.length-1;element.addEventListener('click',onClick.bind(null,index),false)}}};
this.isChecked=function(name,subscriber){var checkbox=getCheckboxByName(name,subscriber);defineCheckedClass();return checkbox&&checkbox.hasClass(currentCheckedClass)};
this.getValue=function(name,subscriber){var checkbox=getCheckboxByName(name,subscriber);if(checkbox)return checkbox.getData('value')};
})(),'CheckboxHandler');
__G.set(new(function(){
var subscribers=[];var options=[];
var onKeyup=function(index,e){var charCode=e.charCode;var keyName=getKeyName(charCode);var opts=options[index];var subscriber=subscribers[index];var cb=opts['callbacks'];var value=e.target.value;if(keyName&&isFunction(cb[keyName]))callSubscriber(index,keyName,value);else if(isFunction(cb[charCode]))callSubscriber(index,charCode,value)};
var getKeyName=function(charCode){return({'13':'enter','27':'esc','38':'up','40':'down','37':'left','39':'right'})[charCode]};
var onEvent=function(index,eventName,e){callSubscriber(index,eventName,e.target.value)};
var callSubscriber=function(index,eventName,value){var s=subscribers[index],r;var cb=Objects.get(options[index]['callbacks'],eventName);if(isFunction(cb))r=cb.call(s,value);if(r!==false&&isString(eventName))s.dispatchEvent(eventName,value)};
this.subscribe=function(subscriber,opts){if(isObject(opts['callbacks'])&&isString(opts['inputSelector'])&&subscribers.indexOf(subscriber)==-1){var input=subscriber.findElement(opts['inputSelector']);var actions=Objects.getKeys(opts['callbacks']);if(input){opts['input']=input;subscribers.push(subscriber);options.push(opts);var index=subscribers.length-1;if(actions.hasExcept('focus','blur','input'))input.addEventListener('keyup',onKeyup.bind(null,index),false);if(actions.has('input'))input.addEventListener('input',onEvent.bind(null,index,'input'),false);if(actions.has('focus'))input.addEventListener('focus',onEvent.bind(null,index,'focus'),false);if(actions.has('blur'))input.addEventListener('blur',onEvent.bind(null,index,'blur'),false)}}};
})(),'InputHandler');
__G.set((c=function(){
if(!this||this==window){
var extendOptions=function(index,opts){Objects.merge(this.options[index],opts)};
var onClick=function(subscriber,e){var index=this.subscribers.indexOf(subscriber);var opts=this.options[index];var target;for(var k in opts){target=e.getTargetWithClass(k,true);if(target){if(isFunction(opts[k])){opts[k].call(subscriber,target,e);e.stopPropagation();break}}}};
p=c.prototype;
p.subscribe=function(subscriber,opts){var index=this.subscribers.indexOf(subscriber);if(index==-1){this.options.push(opts);this.eventHandler.listen(subscriber.getElement(),'click',onClick.bind(null,subscriber));this.subscribers.push(subscriber)}else extendOptions(index,opts)};
p.unsubscribe=function(subscriber){var idx=this.subscribers.indexOf(subscriber);if(idx>-1){this.eventHandler.unlisten(subscriber.getElement(),'click');this.subscribers.splice(idx,1)}};
p.dispose=function(){this.subscribers=null;this.options=null;this.eventHandler.dispose();this.eventHandler=null};
return c;
}
this.subscribers=[];this.options=[];var eh=__G.get('EventHandler');this.eventHandler=new eh();
})(),'MouseHandler');
__G.set(new(function(){
var subscribers=[],timer;
var onResize=function(){window.clearTimeout(timer);timer=window.setTimeout(function(){for(var i=0;i<subscribers.length;i++){var callback=Objects.get(subscribers[i][1],'callback');if(isFunction(callback))callback.call(subscribers[i][0])}},200)};
this.subscribe=function(subscriber,options){subscribers.push([subscriber,options])};
window.addEventListener('resize',onResize,false);
})(),'ResizeHandler');
__G.set(function(){
var target,eventHandler,event,request,tooltipElement,timer,tooltipClass='tooltiped',addClass,text,position,key,caption,delay,corrector,tooltip;
var onBodyMouseOver=function(e){event=e;target=e.target;if(target.hasClass(tooltipClass)){init();if(text){if(delay){showWithDelay()}else{show()}}else if(key){load()}}};
var init=function(){window.clearTimeout(timer);key='';text=target.getData('text');addClass=target.getData('class');position=target.getData('position');caption=target.getData('caption');delay=target.getData('delay');corrector=target.getData('corrector');if(!text){key=target.getData('key')}eventHandler.listenOnce(target,'mouseleave',onLeave)};
var showWithDelay=function(){timer=window.setTimeout(show,500)};
var show=function(){tooltip.set({'shown':true,'corrector':corrector,'caption':caption,'text':text});var coords=getCoords();tooltip.set({'left':Math.round(coords.x),'top':Math.round(coords.y)})};
var getCoords=function(){var marginLeft=0,marginTop=0;var rect=target.getRect();var tooltipRect=tooltipElement.getRect();var coordX=rect.left;var coordY=rect.top;var coords={x:coordX,y:coordY};switch(position){case'left':coords.y+=Math.round(rect.height/2)-20;break;case'bottom':coords.x+=Math.round(rect.width/2);coords.y+=rect.height+5;break;case'top':coords.x+=Math.round(rect.width/2);break;case'left-bottom':coords.y+=rect.height+5;break;case'right-bottom':coords.x+=rect.width;coords.y+=rect.height+5;break;case'left-top':coords.x+=rect.width;break;case'right-top':coords.x+=rect.width;break;default:coords.x+=rect.width+15;coords.y+=Math.round(rect.height/2)-20}if(position=='left'){marginLeft=-tooltipRect.width-10}else if(position=='top'||position=='bottom'){marginLeft=-Math.round(tooltipRect.width/2)}else if(position=='right-top'||position=='right-bottom'){marginLeft=-tooltipRect.width}else if(position=='left-top'){marginLeft=-rect.width}if(position=='top'||position=='left-top'||position=='right-top'){marginTop=-tooltipRect.height-10}if(rect.width<30&&['left-bottom','right-bottom','bottom','left-top','right-top','top'].indexOf(position)!=-1){coords.x-=15}coords.x+=marginLeft;coords.y+=marginTop;return coords};
var onLeave=function(){window.clearTimeout(timer);tooltip.set('shown',false)};
var load=function(){if(isString(__TA)){if(isUndefined(request)){var ajr=__G.get('AjaxRequest');request=new ajr(__TA,onLoad)}request.execute({'name':key})}};
var onLoad=function(data){text=Objects.get(data,'text');var cap=Objects.get(data,'caption');if(cap&&isString(cap)){caption=cap;target.setData('caption',cap)}if(text&&isString(text)){target.setData('text',text);show()}};
if(isFunction(__TC)){var eh=__G.get('EventHandler');eventHandler=new eh();document.documentElement.addEventListener('mouseover',onBodyMouseOver,false);tooltip=new __TC();__A.initiate.call(tooltip);tooltip.render(document.body);tooltipElement=tooltip.getElement()}
},'Tooltiper');
;(function(){
p=Array.prototype;
p.contains=function(v){var iv=~~v;if(iv==v)return this.indexOf(iv)>-1||this.indexOf(v+'')>-1;return this.has(v)};
p.has=function(v){return this.indexOf(v)>-1};
p.hasAny=function(a){if(!isArray(a))a=arguments;for(var i=0;i<a.length;i++){if(this.indexOf(a[i])>-1)return true}};
p.hasExcept=function(){var args=Array.prototype.slice.call(arguments);for(var i=0;i<this.length;i++){if(args.indexOf(this[i])==-1)return true}};
p.removeDuplicates=function(){this.filter(function(item,pos,self){return self.indexOf(item)==pos});return this};
p.getIntersections=function(arr){return this.filter(function(n){return arr.indexOf(n)!=-1})};
p.hasIntersections=function(arr){return!isUndefined(this.getIntersections(arr)[0])};
p.removeIndexes=function(indexes){var deleted=0;for(var i=0;i<indexes.length;i++){this.splice(indexes[i]-deleted,1);deleted++}};
p.isEmpty=function(){return this.length==0};
p.removeItems=function(items){for(var i=0;i<items.length;i++)this.removeItem(items[i])};
p.removeItem=function(item){var index=this.indexOf(item);if(index>-1)this.splice(index,1)};
p.insertAt=function(item,index){if(!isNumber(index)||index>=this.length)this.push(item);else this.splice(index,0,item)};
p.shuffle=function(){var tmp;for(var i=this.length-1;i>0;i--){var j=Math.floor(Math.random()*(i+1));tmp=this[i];this[i]=this[j];this[j]=tmp}};
p.addUnique=function(item){if(!this.has(item))this.push(item)};
p.addRemove=function(item,add,addUnique){if(add){if(addUnique)this.addUnique(item);else this.push(item)}else this.removeItem(item)};
})();
;(function(){
var cache={};
p=Element.prototype;
p.setClass=function(className){this.className=className.trim()};
p.toggleClass=function(className,isAdding){if(isUndefined(isAdding)){isAdding=!this.hasClass(className)}if(isAdding){this.addClass(className)}else{this.removeClass(className)}};
p.switchClasses=function(className1,className2){var classes=this.getClasses();if(classes.has(className1)){this.removeClass(className1);this.addClass(className2)}else if(classes.has(className2)){this.removeClass(className2);this.addClass(className1)}};
p.addClass=function(className){if(isString(className)){var classNames=this.getClasses();var addedClasses=className.split(' ');for(var i=0;i<addedClasses.length;i++){if(classNames.indexOf(addedClasses[i])==-1){classNames.push(addedClasses[i])}}this.className=classNames.join(' ')}};
p.removeClass=function(className){if(isString(className)){var classNames=this.getClasses();var removedClasses=className.split(' ');var newClasses=[];for(var i=0;i<classNames.length;i++){if(removedClasses.indexOf(classNames[i])==-1){newClasses.push(classNames[i])}}this.className=newClasses.join(' ')}};
p.hasClass=function(className){return this.getClasses().has(className)};
p.getClasses=function(){if(!this.className)return[];var classNames=this.className.trim().replace(/ {2,}/g,' ');return classNames.split(' ')};
p.getAncestor=function(selector){if(isNone(selector)||!isString(selector)){return null}if(isFunction(this.closest)){return this.closest(selector)}var parts=selector.trim().split(' ');var properSelector=parts[parts.length-1];var classes=properSelector.split('.');var selectorTag;var thisTag=this.tagName.toLowerCase();if(!isNone(classes[0])){selectorTag=classes[0].toLowerCase()}classes.splice(0,1);var element=this,isSameTag,foundClasses,elementClasses;while(element){elementClasses=element.getClasses();isSameTag=isUndefined(selectorTag)||selectorTag==thisTag;foundClasses=0;for(var i=0;i<elementClasses.length;i++){if(classes.indexOf(elementClasses[i])>-1){foundClasses++}}if(foundClasses==classes.length&&isSameTag){return element}element=element.parentNode}return null};
p.getData=function(name){return this.getAttribute('_'+name)||''};
p.setData=function(name,value){this.setAttribute('_'+name,value)};
p.getRect=function(){return this.getBoundingClientRect()};
p.setWidth=function(width){this.style.width=isNumber(width)?width+'px':width};
p.setHeight=function(height){this.style.height=isNumber(height)?height+'px':height};
p.getWidth=function(){return this.getRect().width};
p.getHeight=function(){return this.getRect().height};
p.getTop=function(){return this.getRect().top};
p.getLeft=function(){return this.getRect().left};
p.css=function(style){var element=this;var set=function(value,style){var propertyName=getVendorJsStyleName(style);if(propertyName){element.style[propertyName]=value}};var getVendorJsStyleName=function(style){var propertyName=cache[style];if(!propertyName){propertyName=toCamelCase(style);cache[style]=propertyName}return propertyName};if(typeof style=='string'){set(value,style)}else{for(var key in style){set(style[key],key)}}};
p.getChildAt=function(index){return this.childNodes[index]};
p.attr=function(attrName){if(!isUndefined(arguments[1])){if(attrName=='class'){this.setClass(arguments[1])}else if(attrName=='value'){this.value=arguments[1]}else{this.setAttribute(attrName,arguments[1])}}else{return this.getAttribute(attrName)}};
p.show=function(isShown){var display=isString(isShown)?isShown:(isUndefined(isShown)||isShown?'block':'none');this.style.display=display};
p.hide=function(){this.show(false)};
p.find=function(selector){return this.querySelector(selector)};
p.finds=function(selector){return this.querySelectorAll(selector)};
p.getParent=function(){return this.parentNode};
p.scrollTo=function(pxy,duration){if(isElement(pxy))pxy=pxy.getRelativePosition(this).y;if(!duration||!isNumber(duration))this.scrollTop=pxy;else{var px=pxy-this.scrollTop,ratio=15,steps=duration/ratio,step=Math.round(px/steps),currentStep=0,e=this,cb=function(){currentStep++;e.scrollTop=e.scrollTop+step;if(currentStep<steps)setTimeout(cb,ratio);else e.scrollTop=pxy};if(px!=0)cb()}};
p.getRelativePosition=function(element){var a=this.getRect();var b=element.getRect();return{x:Math.round(a.left-b.left+element.scrollLeft),y:Math.round(a.top-b.top+element.scrollTop)}};
p.clear=function(){if(isString(this.value))this.value='';else this.innerHTML=''};
p.prev=function(){return this.previousSibling};
p.next=function(){return this.nextSibling};
})();
;(function(){
p=Function.prototype;
p.b=p.bind;
})();
;(function(){
p=MouseEvent.prototype;
p.getTarget=function(selector){return this.target.getAncestor(selector)};
p.getTargetData=function(selector,dataAttr){var target=this.getTarget(selector);return!!target?target.getData(dataAttr):''};
p.targetHasAncestor=function(element){if(isElement(element)){var target=this.target;while(target){if(target==element){return true}target=target.parentNode}}return false};
p.targetHasClass=function(className){return this.target.hasClass(className)||(!!this.target.parentNode&&this.target.parentNode.hasClass(className))};
p.getTargetWithClass=function(className,strict){if(this.target.hasClass(className))return this.target;if(!strict||!this.target.className){if(!!this.target.parentNode&&this.target.parentNode.hasClass(className))return this.target.parentNode}return null};
})();
;(function(){
p=String.prototype;
p.isEmpty=function(){return!(/[^\s]/).test(this)};
p.toArray=function(delimiter){delimiter=delimiter||',';var ar=[];var parts=this.split(delimiter);for(var i=0;i<parts.length;i++){if(parts[i])ar.push(parts[i].trim())}return ar};
})();
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
var __C;__G.set(__C=new(function(){
this.get=function(id){if(isString(__CT[id])){__CT[id]=__G.get(__CT[id])}if(isFunction(__CT[id])){__CT[id]=new __CT[id]();__A.initiate.call(__CT[id])}return __CT[id]};
this.load=function(ids){var ctr;if(!isArray(ids))ids=[ids];for(var i=0;i<ids.length;i++){ctr=this.get(ids[i]);if(isController(ctr)){ctr.doAction(null,'load')}}};
})(),'__C');
__G.set(__A=new(function(){
var extendInitials=function(initials1,initials2){if(isNull(initials1)){initials1=initials2}else{for(var k in initials2){if(isUndefined(initials1[k]))initials1[k]=initials2[k];else if(isObject(initials1[k])||isObject(initials2[k]))Objects.merge(initials1[k],initials2[k]);else if(isArray(initials1[k])||isArray(initials2[k]))Objects.concat(initials1[k],initials2[k])}}return initials1};
var addProps=function(initialProps){for(var k in initialProps){if(isUndefined(this.props[k])){this.props[k]=initialProps[k]}}};
var processInitials=function(){var initials=this.initials;if(isObject(initials)){if(isController(this)){this.options=initials['options']}for(var k in initials){if(isArrayLike(initials[k])){if(k=='correctors'){for(var j in initials[k])addCorrector.call(this,j,initials[k][j])}else if(k=='followers'){for(var j in initials[k])addFollower.call(this,j,initials[k][j])}else if(k=='controllers'){for(var i=0;i<initials[k].length;i++)attachController.call(this,initials[k][i])}else if(k=='props'){addProps.call(this,initials[k])}}}}};
var getInitial=function(initialName){return Objects.get(this.initials,initialName)};
var attachController=function(options){if(isObject(options['on'])){var data,ctr;for(var actionName in options['on']){data={'initiator':this,'callback':options['on'][actionName]};options['controller'].addSubscriber(actionName,data,!!options['private'],Objects.get(options['options'],actionName))}}};
var addCorrector=function(name,handler){if(isFunction(handler)){this.correctors=this.correctors||{};this.correctors[name]=handler}};
var addFollower=function(name,handler){if(isFunction(handler)){this.followers=this.followers||{};this.followers[name]=handler}};
var subscribeToHelper=function(options){var helper=__G.get(options['helper']);if(helper&&isObject(options['options']))helper.subscribe(this,options['options'])};
var isProperMethod=function(child,parent,method){if(!!child.prototype[method])return false;return parent.prototype[method]!=parent.prototype.initiate&&parent.prototype[method]!=parent.prototype.getInitials};
var addUpdater=function(u,l){this.updaters=this.updaters||{};var keys=u.getKeys();for(var i=0;i<keys.length;i++){this.updaters[keys[i]]=this.updaters[keys[i]]||[];l.push(keys[i],u);this.updaters[keys[i]].push(u)}};
this.processPostRenderInitials=function(){var events=getInitial.call(this,'events');if(isObject(events)){var mh=__G.get('MouseHandler');this.mouseHandler=new mh(this,events)}var helpers=getInitial.call(this,'helpers');if(isArray(helpers)){for(var i=0;i<helpers.length;i++)subscribeToHelper.call(this,helpers[i])}var listeners=getInitial.call(this,'listeners');var s=__G.get('State');if(isObject(listeners)){for(var j in listeners)s.listen(this,j,listeners[j])}var globals=getInitial.call(this,'globals');if(isObject(globals)){for(var j in globals)s.subscribe(this,j,globals[j])}};
this.inherits=function(list){var children,parent,child,initials,sc;for(var k=0;k<list.length;k++){parent=__G.get(list[k]);children=list[++k];for(var i=0;i<children.length;i++){child=__G.get(children[i]);if(!child.prototype.inheritedSuperClasses){child.prototype.inheritedSuperClasses=[]}sc=child.prototype.inheritedSuperClasses;var cb=function(p){if(sc.indexOf(p)==-1)sc.push(p);var psc=p.prototype.inheritedSuperClasses;if(isArray(psc)){for(var n=0;n<psc.length;n++)cb(psc[n])}};cb(parent);for(var method in parent.prototype){if(isProperMethod(child,parent,method)){child.prototype[method]=parent.prototype[method]}}}}};
this.initiate=function(props){var initials=null;var proto=this.constructor.prototype;if(isFunction(proto.getInitials)){initials=proto.getInitials()}var initiateParental=function(superClasses,object){var parentInitials,pproto;for(var i=0;i<superClasses.length;i++){pproto=superClasses[i].prototype;if(isFunction(pproto.initiate)){pproto.initiate.call(object)}if(isFunction(pproto.getInitials)){parentInitials=pproto.getInitials();if(isObject(parentInitials)){initials=extendInitials(initials||null,parentInitials)}}if(isArray(pproto.inheritedSuperClasses)){initiateParental(pproto.inheritedSuperClasses,object)}}};if(isArray(this.inheritedSuperClasses)){initiateParental(this.inheritedSuperClasses,this)}if(isObject(this.props))Objects.merge(this.props,props);else this.props=props||{};if(isFunction(proto.initiate)){proto.initiate.call(this)}this.initials=initials;processInitials.call(this)};
this.getNextSiblingChild=function(){if(!this.nextSiblingChild)return null;if(this.nextSiblingChild instanceof Node)return this.nextSiblingChild;var firstNodeChild=__A.getFirstNodeChild.call(this.nextSiblingChild);if(firstNodeChild)return firstNodeChild;return __A.getNextSiblingChild.call(this.nextSiblingChild,this)};
this.setNextSiblingChild=function(nextSiblingChild){this.nextSiblingChild=nextSiblingChild;if(!(nextSiblingChild instanceof Node))__A.setPrevSiblingChild.call(this.nextSiblingChild,this)};
this.setPrevSiblingChild=function(prevSiblingChild){this.prevSiblingChild=prevSiblingChild};
this.disposeLinks=function(){if(this.prevSiblingChild)__A.setNextSiblingChild.call(this.prevSiblingChild,this.nextSiblingChild);this.prevSiblingChild=null;this.nextSiblingChild=null};
this.getFirstNodeChild=function(){if(this.levels)return this.levels[0].getFirstNodeChild();if(this.level)return this.level.getFirstNodeChild();return null};
this.getWaitingChild=function(componentName){return Objects.get(this.waiting,componentName)};
this.getTemplateById=function(tmpid){if(isObject(this.templatesById))return this.templatesById[tmpid];var parents=this.inheritedSuperClasses;if(isArrayLike(parents)){for(var i=0;i<parents.length;i++){if(isObject(parents[i].prototype.templatesById)&&isFunction(parents[i].prototype.templatesById[tmpid])){return parents[i].prototype.templatesById[tmpid]}}}};
this.subscribe=function(eventType,handler,subscriber){this.listeners=this.listeners||[];this.listeners.push({'type':eventType,'handler':handler,'subscriber':subscriber})};
this.registerElement=function(element,id){this.elements=this.elements||{};this.elements[id]=element};
this.registerChildComponent=function(child){this.childrenCount=this.childrenCount||0;this.children=this.children||{};this.children[child.getId()||this.childrenCount]=child;this.childrenCount++};
this.unregisterChildComponent=function(child){if(isControl(child))__A.unregisterControl.call(this,child);var id=child.getId();if(!id){for(var k in this.children){if(this.children[k]==child){id=k;break}}}if(isString(id)){this.children[id]=null;delete this.children[id]}};
this.registerControl=function(control,name){this.controls=this.controls||{};if(!isUndefined(this.controls[name])){if(!isArray(this.controls[name]))this.controls[name]=[this.controls[name]];this.controls[name].push(control)}else this.controls[name]=control;control.setName(name)};
this.unregisterControl=function(control){if(this.controls){var name=control.getName();if(isArray(this.controls[name]))this.controls[name].removeItem(control);else{this.controls[name]=null;delete this.controls[name]}}};
this.provideWithComponent=function(propName,componentName,waitingChild){var cmp=this.getChild(componentName);if(cmp)waitingChild.set(propName,cmp);else{this.waiting=this.waiting||{};this.waiting[componentName]=this.waiting[componentName]||[];this.waiting[componentName].push([waitingChild,propName])}};
this.getParentElement=function(){return this.parentElement};
this.createUpdater=function(u,c,s,p,l){var updater=new u(s,p,p['n']);addUpdater.call(c,updater,l)};
this.disposeUpdater=function(t,u){if(this.updaters&&this.updaters[t]){var i=this.updaters[t].indexOf(u);if(i>-1){this.updaters[t][i].dispose();this.updaters[t].splice(i,1)}}};
this.setId=function(id){this.componentId=id};
this.createLevel=function(items,isUpdating,index){var level=new(__G.get('Level'))(this.parentLevel.getComponent());var nextSiblingChild;if(isNumber(index)&&this.levels[index]){nextSiblingChild=this.levels[index].getFirstNodeChild()}else{nextSiblingChild=isUpdating?__A.getNextSiblingChild.call(this):null}level.render(items,this.parentElement,this.parentLevel,nextSiblingChild);this.levels.insertAt(level,index)};
this.initOperator=function(pe,pl){this.parentElement=pe;this.parentLevel=pl;this.levels=[]};
this.disposeLevels=function(){for(var i=0;i<this.levels.length;i++){this.levels[i].dispose()}this.levels=[]};
this.disposeOperator=function(){__A.disposeLinks.call(this);__A.disposeLevels.call(this);this.levels=null;this.parentElement=null;this.parentLevel=null;this.params=null};
})(),'__A');
var Dialoger;__G.set(Dialoger=new(function(){
var ds={};var cid,dc,d,opts;
var defineId=function(c,id){dc=c;if(!isFunction(c))return'_';cid=c.name+(isPrimitive(id)?'_'+id:'')};
var defineDialog=function(){if(isUndefined(ds[cid])){ds[cid]=new dc();__A.initiate.call(ds[cid]);ds[cid].render(document.body)}d=ds[cid]};
var showDialog=function(){if(isObject(opts))d.set(opts);d.show()};
var closeAll=function(){for(var k in ds)ds[k].hide()};
this.show=function(c,options){if(isString(c))c=__G.get(c);if(isFunction(c)){var id;if(isObject(options)){id=options['did']}opts=options;defineId(c,id);defineDialog();showDialog()}};
this.hide=function(c,id){defineId(c,id);if(ds[cid])ds[cid].close()};
this.get=function(c,id){defineId(c,id);return ds[cid]};
this.expand=function(c,id){defineId(c,id);if(ds[cid])ds[cid].expand(true)};
this.minimize=function(c,id){defineId(c,id);if(ds[cid])ds[cid].expand(false)};
this.dispose=function(c,id){defineId(c,id);if(ds[cid])ds[cid].dispose();delete ds[cid]};
window.addEventListener('popstate',closeAll);
})(),'Dialoger');
var Dictionary;__G.set(Dictionary=new(function(){
var items={},callbacks,loaded={};
var onLoad=function(data){if(isObject(data)){for(var k in data)this.set(k,data[k]);if(!isArray(callbacks))return;for(var i=0;i<callbacks.length;i++){if(isFunction(callbacks[i][0])){callbacks[i][0].call(callbacks[i][1]||null)}else if(isString(callbacks[i][0])&&isComponentLike(callbacks[i][1])){callbacks[i][1].set(callbacks[i][0],items[callbacks[i][2]])}}callbacks=null}};
this.load=function(routeName){if(loaded[routeName])return;if(!isNone(__DU)){Loader.get(__DU,{'route':routeName},onLoad,this)}loaded[routeName]=true};
this.get=function(key,callbackOrPropName,thisObj){var item=Objects.get(items,key);if(item)return item;callbacks=callbacks||[];callbacks.push([callbackOrPropName,thisObj,key])};
this.set=function(key,value){items[key]=value};
this.setData=function(routeName,data){loaded[routeName]=true;for(var k in data)this.set(k,data[k])};
})(),'Dictionary');
__G.set((c=function(){
if(!this||this==window){
p=c.prototype;
p.listen=function(element,type,handler){this.listeners.push([element,type,handler]);element.addEventListener(type,handler,false)};
p.listenOnce=function(element,type,handler){var cb=function(){handler();element.removeEventListener(type,cb,false)};element.addEventListener(type,cb,false)};
p.unlisten=function(element,type){var l,i;for(i=0;i<this.listeners.length;i++){l=this.listeners[i];if(l&&l[0]==element&&l[1]==type){l[0].removeEventListener(l[1],l[2],false);this.listeners[i]=null}}};
p.dispose=function(){var l,i;for(i=0;i<this.listeners.length;i++){l=this.listeners[i];if(l){l[0].removeEventListener(l[1],l[2],false)}}this.listeners=null};
return c;
}
this.listeners=[];
})(),'EventHandler');
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
var Logger;__G.set(Logger=new(function(){
this.log=function(message,method,object,opts){window.console.log(message)};
})(),'Logger');
var Popuper;__G.set(Popuper=new(function(){
var components,elements,skippedAll;
var reset=function(){components=[];elements=[]};
var onBodyMouseDown=function(e){if(skippedAll)return;var element;for(var i=0;i<components.length;i++){element=elements[i];if(!isElement(element)||!e.targetHasAncestor(element)){components[i].hide();reset()}}};
this.watch=function(component,element){if(components.indexOf(component)==-1){components.push(component);if(isString(element))element=component.findElement(element);elements.push(element||component.getElement()||null)}};
this.skipAll=function(isSkipped){skippedAll=isSkipped};
reset();var body=document.documentElement;body.addEventListener('mousedown',onBodyMouseDown,false);
})(),'Popuper');
__G.set(function(){
var properRoutes={},routes=__RT,isHashRouter=!!__HR,defaultRoute=__DR,indexRoute=__IR,errorRoutes=__ER,handler,bodyElement,menues,currentRoute,subscribers,pathParts;
var getRoute=function(){var params=window.location.search;var path;if(isHashRouter){path=window.location.hash}else{path=window.location.pathname}pathParts=[];var properPaths=[];path=path.replace(/^[\#\/]+|\/$/g,'').split('/');if(!path[0]){path[0]=indexRoute}for(var i=0;i<path.length;i++){pathParts.push(path[i]);var pathName=pathParts.join('/');if(properRoutes[pathName]){properPaths.push(pathName)}}path=properPaths[properPaths.length-1];if(path){return properRoutes[path]}else if(defaultRoute&&properRoutes[defaultRoute]){return properRoutes[defaultRoute]}return getErrorRoute(404)};
var initRoutes=function(rts,parents){parents=parents||[];var tempParents=Objects.clone(parents);var name,path;for(var i=0;i<rts.length;i++){name=rts[i]['name'];tempParents.push(name);var children=Objects.clone(rts[i]['children']);delete rts[i]['children'];path=rts[i]['path']=tempParents.join('/');initRouteParams(rts[i]);properRoutes[path]=rts[i];if(isArray(children)){initRoutes(children,Objects.clone(tempParents))}tempParents=Objects.clone(parents)}};
var initRouteParams=function(route){if(isObject(route['params'])){var dinamicParams={};for(var k in route['params']){if((/^$\d+$/).test(route['params'][k])){dinamicParams[k]=route['params'][k].replace(/[^\d]/g,'')}}for(var k in dinamicParams){delete route['params'][k]}route['dinamicParams']=dinamicParams}};
var onNavigate=function(){var route=getRoute();bodyElement.setClass(route['name']+'-page');var accessLevel=route['accessLevel'];if(isNumber(accessLevel)&&!User.hasAccessLevel(accessLevel)){route=getErrorRoute(401)}changeRoute(route,true)};
var getRouteByName=function(viewName){for(var k in properRoutes){if(properRoutes[k]['name']==viewName){return properRoutes[k]}}};
var getErrorRoute=function(errorCode){if(isString(errorRoutes[errorCode])){errorRoutes[errorCode]={'name':errorCode,'view':errorRoutes[errorCode],'error':errorCode}}return errorRoutes[errorCode]};
var changeRoute=function(route,changeTitle){currentRoute=route;if(isFunction(handler)){handler(route,changeTitle)}else{log('navigation handler is not function','changeRoute',this,{'handler':handler})}if(!isObject(route)){log('route is invalid','changeRoute',this,{'route':route})}informSubscribers();informMenues()};
var replaceState=function(route){if(isHashRouter){window.history.replaceState({},'','#'+route['path'])}else{window.location.href='/'+route['path']}};
var informSubscribers=function(){if(isArray(subscribers)){var subscrView,opts,subscriber;for(var i=0;i<subscribers.length;i++){subscrView=subscribers[i][0];opts=subscribers[i][1];subscriber=subscribers[i][2];if(isObject(opts)&&subscrView==currentRoute['name']){var routeOptions={};for(var k in opts){routeOptions[k]=this.getPathPartAt(opts[k])}subscriber.handleRouteOptionsChange(routeOptions)}}}};
var informMenues=function(){if(isArray(menues)){for(var i=0;i<menues.length;i++){menues[i].onNavigate(currentRoute['name'])}}};
this.setNavigationHandler=function(h){handler=h};
this.init=function(){bodyElement=document.querySelector('body');initRoutes(routes);if(isHashRouter){window.addEventListener('popstate',onNavigate.bind(this))}};
this.run=function(){onNavigate()};
this.getPathPartAt=function(index){return isArray(pathParts)?pathParts[index]:''};
this.reload=function(){window.location.reload()};
this.redirect=function(viewName,replState){var route;var intViewName=~~viewName;if(intViewName==viewName){viewName=intViewName}if(isNumber(viewName)){route=getErrorRoute(viewName)}else if(isString(viewName)){route=getRouteByName(viewName)}else{log('redirect view name is invalid','redirect',this,{'viewName':viewName});return}if(!isObject(route)){log('redirect route is invalid','redirect',this,{'route':route})}else{if(replState&&!isNumber(viewName)){replaceState(route)}changeRoute(route,!!replState)}};
this.subscribe=function(options,controller){var routeName=currentRoute['name'];subscribers=subscribers||[];subscribers.push([routeName,options,controller])};
this.addMenu=function(menu){if(isObject(menu)&&isFunction(menu.onNavigate)){menues=menues||[];menues.push(menu)}};
this.hasMenu=function(menu){return menues.indexOf(menu)>-1};
this.getCurrentRoute=function(){return currentRoute||getRoute()};
this.getCurrentRouteName=function(){if(currentRoute)return currentRoute['name']};
},'Router');
var State;__G.set(State=new(function(){
var listeners={},subscribers={},updaters={},vars={};
this.subscribe=function(subscriber,name,callback){var s=subscribers[name]=subscribers[name]||[];s.push([callback,subscriber])};
this.unsubscribe=function(subscriber,name){var s=subscribers[name];if(isArray(s)){var done=false;while(!done){done=true;for(var i=0;i<s.length;i++){if(s[i][1]==subscriber){s.splice(i,1);done=false;break}}}}};
this.get=function(name){return vars[name]};
this.set=function(name,value){var updated,data=name;if(!isUndefined(value)){data={};data[name]=value}var changed={},isChanged=false;for(var k in data){if(vars[k]==data[k])continue;if(isArray(vars[k])&&isArray(data[k])&&Objects.equals(vars[k],data[k]))continue;isChanged=true;changed[k]=data[k]}if(isChanged){for(var k in changed){vars[k]=changed[k];var s=subscribers[k];if(isArray(s)){for(var i=0;i<s.length;i++){if(isFunction(s[i][0])){s[i][0].call(s[i][1]||null,changed[k],k)}}}var u=updaters[k];if(isArray(u)){updated=[];for(var i=0;i<u.length;i++){if(updated.indexOf(u[i])==-1){u[i].react(changed);updated.push(u[i])}}}}}updated=changed=data=null};
this.listen=function(listener,name,callback){if(!isArray(listeners[name]))listeners[name]=[];listeners[name].push([callback,listener])};
this.unlisten=function(name,listener){if(isArray(listeners[name])){var indexes=[];for(var i=0;i<listeners[name].length;i++){if(listeners[name][i][1]==listener)indexes.push(i)}listeners[name].removeIndexes(indexes)}};
this.dispatchEvent=function(name,args){if(isArray(listeners[name])){for(var i=0;i<listeners[name].length;i++){if(isFunction(listeners[name][i][0])){listeners[name][i][0].apply(listeners[name][i][1]||null,args)}}}};
this.createUpdater=function(updater,component,obj,props){var u=new updater(obj,props,props['g']);var keys=u.getKeys();for(var i=0;i<keys.length;i++){updaters[keys[i]]=updaters[keys[i]]||[];updaters[keys[i]].push(u)}};
this.dispose=function(subscriber){var k,i,s;for(k in subscribers){s=[];for(i=0;i<subscribers[k].length;i++){if(subscribers[k][i]!=subscriber)s.push(subscribers[k][i]);else alert(111222)}subscribers[k]=s}};
})(),'State');
var StoreKeeper;__G.set(StoreKeeper=new(function(){
var x='stored_',s={'month':2592000,'day':86400,'hour':3600,'min':60};
var g=function(k){return x+k};
var gm=function(p){var n=~~p.replace(/[^\d]/g,'');var m=p.replace(/\d/g,'');if(!n)return 0;if(!s[m])return 0;return s[m]*n*1000};
var gi=function(k){var lk=g(k);var i=localStorage.getItem(lk);if(!i)return null;try{i=JSON.parse(i)}catch(e){return null}return i};
var ia=function(sm,p){var nm=Date.now(),pm=gm(p);if(isString(sm))sm=stringToNumber(sm);return pm&&sm&&nm-sm<pm};
this.set=function(k,v){var lk=g(k);var i=JSON.stringify({'data':v,'timestamp':Date.now().toString()});localStorage.setItem(lk,i)};
this.get=function(k){var i=gi(k);return Objects.has(i,'data')?i['data']:null};
this.getActual=function(k,p){var i=gi(k);return Objects.has(i,'data')&&ia(i['timestamp'],p)?i['data']:null};
this.remove=function(k){var lk=g(k);localStorage.removeItem(lk)};
})(),'StoreKeeper');
__G.set(function(){
var logs=[];var views=[];
this.assert=function(t,a,k,e,c,m){var i=this.check(t,a,k);if(!i)this.log(e,c,m);return i};
this.check=function(t,a,k){var d=[],isa=isArray(k);if(isa){for(var i=0;i<k.length;i++){d.push(k[i]);if(i<k.length-1&&!this.check('arrayLike',a,d))return false}}d=null;if(isa){for(var i=0;i<k.length;i++)a=a[k[i]]}switch(t){case'string':return isString(a);case'number':return isNumber(a);case'numeric':return isNumeric(a);case'bool':return isBool(a);case'function':return isFunction(a);case'array':return isArray(a);case'object':return isObject(a);case'arrayLike':return isArrayLike(a);case'element':return isElement(a);case'node':return isNode(a);case'text':return isText(a);case'componentLike':return isComponentLike(a);case'component':return isComponent(a);case'control':return isControl(a);case'null':return isNull(a);case'undefined':return isUndefined(a);case'empty':return isNone(a);case'notEmptyString':return isNotEmptyString(a);case'zero':return isZero(a)}return true};
this.log=function(t,c,m){t=c+'.'+m+': '+t;window.console.log(t);logs.push(t)};
this.onTested=function(view){};
},'Tester');
__G.set(function(){
var userOptions=__UO;var app,status={},attributes={},settings={},loaded=false,loadRequest,saveRequest;
var initOptions=function(){if(isObject(userOptions)){if(userOptions['login']&&isString(userOptions['login'])){var ajr=__G.get('AjaxRequest');loadRequest=new ajr(userOptions['login'],this.setData.bind(this))}}};
var getDefaultAttributes=function(){return{'type':'guest','accessLevel':0}};
this.load=function(application){if(!loaded){initOptions.call(this);app=application;if(loadRequest){loadRequest.execute();return}}onLoad(getDefaultAttributes())};
this.setData=function(data){status=data['status'];attributes=data['attributes'];settings=data['settings'];loaded=true;if(isComponentLike(app)){app.run()}};
this.hasFullAccess=function(){var fullAccess=Objects.get(userOptions,'fullAccess',null);var accessLevel=~~status['accessLevel'];return!isNumber(fullAccess)||accessLevel>=fullAccess};
this.isAdmin=function(){var adminAccess=Objects.get(userOptions,'adminAccess',null);var accessLevel=~~status['accessLevel'];return!isNumber(adminAccess)||accessLevel>=adminAccess};
this.isBlocked=function(){return!!status['isBlocked']};
this.getBlockedReason=function(){return status['blockReason']};
this.hasAccessLevel=function(accessLevel,isEqual){if(!isEqual){return status['accessLevel']>=accessLevel}return status['accessLevel']==accessLevel};
this.hasType=function(userType){return status['type']==userType};
this.isAuthorized=function(){return status['accessLevel']>0};
this.getAttributes=function(){return attributes};
this.getAttribute=function(attributeName){return attributes[attributeName]};
this.setAttribute=function(attributeName,attributeValue,isToSave){var attrs={};attrs[attributeName]=attributeValue;this.setAttributes(attrs,isToSave)};
this.setAttributes=function(attrs, isToSave){if(isObject(attrs)){for(var k in attrs){attributes[k]=attrs[k]}if(isToSave&&saveRequest){saveRequest.execute(attributes)}}};
this.getSettings=function(){return settings};
this.getSetting=function(settingName){return settings[settingName]};
this.setSetting=function(settingName,settingValue){settings[settingName]=settingValue;if(saveRequest){saveRequest.execute({'isSetting':true,'name':settingName,'value':settingValue})}};
},'User');
__G.set(function(){
this.assert=function(v,m,e){if(!m(v))console.log(e);return v};
},'Validator');
__G.set((c=function(cmp,params){
if(!this||this==window){
p=c.prototype;
p.getKeys=function(){var a=[],p=this.params;for(var k in p['n']){if(a.indexOf(p['n'][k])==-1){if(isString(p['n'][k]))a.push(p['n'][k]);else a.push.apply(a,p['n'][k])}}return a};
p.react=function(d){var p=this.params,pp=p['p'](),cp={},pc=!!p['n']['props'];if(pc&&isObject(pp['p'])){cp=pp['p']}for(var k in p['n']){if(isString(p['n'][k])&&!isUndefined(d[p['n'][k]])){cp[k]=pc&&pp['ap']?pp['ap'][k]:pp['p'][k]}}this.cmp.set(cp)};
p.dispose=function(){this.cmp=null;this.params=null};
return c;
}
this.cmp=cmp;this.params=params;
})(),'ComponentUpdater');
__G.set((c=function(element,params,names){
if(!this||this==window){
p=c.prototype;
p.getKeys=function(){var a=[],n=this.names;for(var k in n){if(isString(n[k]))a.push(n[k]);else a.push.apply(a,n[k])}return a};
p.react=function(d){var n=this.names,p=this.params,k,i,pn;for(k in n){pn=n[k];if(isString(pn))pn=[pn];for(i=0;i<pn.length;i++){if(!isUndefined(d[pn[i]])){this.element.attr(__AT[k]||k,p['p']()[k]||'');break}}}};
p.dispose=function(){this.element=null;this.params=null;this.names=null};
return c;
}
this.element=element;this.params=params;this.names=names;
})(),'ElementUpdater');
__G.set((c=function(node,params,names){
if(!this||this==window){
p=c.prototype;
p.getKeys=function(){return this.names};
p.react=function(d){var t;if(isFunction(this.params['v']))t=this.params['v']();else t=d[this.names[0]];this.node.textContent=t||''};
p.dispose=function(){this.node=null;this.params=null;this.names=null};
return c;
}
this.node=node;this.params=params;this.names=isArray(names)?names:[names];
})(),'NodeUpdater');
__G.set((c=function(operator,params,names){
if(!this||this==window){
p=c.prototype;
p.getKeys=function(){return this.names};
p.react=function(){this.operator.update()};
p.dispose=function(){this.operator=null;this.names=null};
return c;
}
this.operator=operator;this.names=isArray(names)?names:[names];
})(),'OperatorUpdater');
var Dates;__G.set(Dates=new(function(){
var date,months=["\u042f\u043d\u0432\u0430\u0440\u044c","\u0424\u0435\u0432\u0440\u0430\u043b\u044c","\u041c\u0430\u0440\u0442","\u0410\u043f\u0440\u0435\u043b\u044c","\u041c\u0430\u0439","\u0418\u044e\u043d\u044c","\u0418\u044e\u043b\u044c","\u0410\u0432\u0433\u0443\u0441\u0442","\u0421\u0435\u043d\u0442\u044f\u0431\u0440\u044c","\u041e\u043a\u0442\u044f\u0431\u0440\u044c","\u041d\u043e\u044f\u0431\u0440\u044c","\u0414\u0435\u043a\u0430\u0431\u0440\u044c"],months2=["\u044f\u043d\u0432\u0430\u0440\u044f","\u0444\u0435\u0432\u0440\u0430\u043b\u044f","\u043c\u0430\u0440\u0442\u0430","\u0430\u043f\u0440\u0435\u043b\u044f","\u043c\u0430\u044f","\u0438\u044e\u043d\u044f","\u0438\u044e\u043b\u044f","\u0430\u0432\u0433\u0443\u0441\u0442\u0430","\u0441\u0435\u043d\u0442\u044f\u0431\u0440\u044f","\u043e\u043a\u0442\u044f\u0431\u0440\u044f","\u043d\u043e\u044f\u0431\u0440\u044f","\u0434\u0435\u043a\u0430\u0431\u0440\u044f"];
var get=function(){return new Date()};
this.getYear=function(){return get().getFullYear()};
this.getDay=function(){return get().getDate()};
this.getMonth=function(){return get().getMonth()};
this.getMonthName=function(){if(isNumber(arguments[0])){return months[arguments[0]]}return months[this.getMonth()]};
this.getDate=function(){var date=get();return{day:date.getDate(),month:date.getMonth(),year:date.getFullYear()}};
this.getTimeStamp=function(){return new Date().getTime()};
this.getDays=function(month,year){return new Date(year,month,0).getDate()};
this.getWeekDay=function(day,month,year){return new Date(year,month,day).getDay()};
this.getFormattedDate=function(stringDate,format){format=format.toLowerCase();stringDate=stringDate.split(/[ \.-]+/);var y,y2,ys,m2,d,d2;var s0=~~stringDate[0],m=~~stringDate[1],s2=~~stringDate[2];if(s2){m2=m<10?'0'+m:m;if(s2>100){y=s2;d=s0}else{d=s2;y=s0}d2=d<10?'0'+d:d;ys=y+'';y2=ys.charAt(2)+ys.charAt(3)}else{return stringDate}format=format.replace(/y{4}/,y);format=format.replace(/y{2}/,y2);format=format.replace(/month/,months2[m-1]);format=format.replace(/m{2}/,m2);format=format.replace(/m{1}/,m);format=format.replace(/d{2}/,d2);format=format.replace(/d{1}/,d);return format};
})(),'Dates');
var Decliner;__G.set(Decliner=new(function(){
var getVariant=function(num){var n,m;num=num.toString();m=num.charAt(num.length-1);if(num.length>1)n=num.charAt(num.length-2);else n=0;if(n==1)return 2;else{if(m==1)return 0;else if(m>1&&m<5)return 1;else return 2}};
this.getCount=function(key,num){if(isArray(num))num=num.length;return num+' '+this.get(key,num)};
this.get=function(key,num){if(isArray(num))num=num.length;if(!isNumber(num))return'';return Objects.get(Objects.get(__DW,key,''),getVariant(num),'')};
})(),'Decliner');
var Objects;__G.set(Objects=new(function(){
this.each=function(obj,callback,thisObj){if(isArrayLike(obj)){if(thisObj)callback=callback.bind(thisObj);for(var k in obj)if(callback(obj[k],k)=='break')break}};
this.remove=function(obj,item){if(isArray(obj)){var idx=obj.indexOf(item);if(idx>0)obj.splice(idx,1)}else if(isObject(obj))delete obj[obj.getKey(item)]};
this.equals=function(a,b){return a===b&&a!==0||_equals(a,b);function _equals(a,b){var s,l,p,x,y;if((s=toString.call(a))!==toString.call(b))return false;switch(s){default:return a.valueOf()===b.valueOf();case'[object Function]':return false;case'[object Array]':if((l=a.length)!=b.length)return false;while(l--){if((x=a[l])===(y=b[l])&&x!==0||_equals(x,y))continue;return false}return true;case'[object Object]':l=0;for(p in a){if(a.hasOwnProperty(p)){++l;if((x=a[p])===(y=b[p])&&x!==0||_equals(x,y))continue;return false}}for(p in b)if(b.hasOwnProperty(p)&&--l<0)return false;return true}}};
this.merge=function(){var objs=arguments;if(!isArrayLike(objs[0]))objs[0]={};for(var i=1;i<objs.length;i++){if(isArrayLike(objs[i])){for(var k in objs[i]){if(!isUndefined(objs[i][k]))objs[0][k]=objs[i][k]}}}return objs[0]};
this.concat=function(){var arrs=arguments;if(!isArray(arrs[0]))arrs[0]=[];for(var i=1;i<arrs.length;i++){if(isArray(arrs[i])){for(var j=0;j<arrs[i].length;j++){arrs[0].push(arrs[i][j])}}}return arrs[0]};
this.clone=function(obj){if(!isArrayLike(obj))return obj;return JSON.parse(JSON.stringify(obj))};
this.get=function(obj,key,defaultValue){return this.has(obj,key)?obj[key]:defaultValue};
this.getByIndex=function(obj,idx){if(!isArrayLike(obj))return;if(isArray(obj))return obj[idx];var count=0;for(var k in obj){if(count==idx)return obj[k];count++}};
this.has=function(obj,key,value){if(!isArrayLike(obj))return false;var has=!isUndefined(obj[key]);if(has&&!isUndefined(value))return obj[key]==value;return has};
this.empty=function(obj){if(!isArrayLike(obj))return true;if(isObject(obj)){for(var k in obj)return false;return true}return isUndefined(obj[0])};
this.getKey=function(obj,value){for(var k in obj)if(obj[k]==value)return k};
this.getValues=function(obj){var vals=[];for(var k in obj)vals.push(obj[k]);return vals};
this.getKeys=function(obj){var keys=[];if(isObject(obj)){for(var k in obj)keys.push(k)}else if(isArray(obj)){for(var i=0;i<obj.length;i++)keys.push(i)}return keys};
this.flatten=function(obj,flattened,transformed){var top=isUndefined(transformed);flattened=flattened||{};transformed=transformed||[];if(!isObject(obj))return obj;for(var k in obj){if(isObject(obj[k]))this.flatten(obj[k],flattened,transformed);else{if(!isUndefined(flattened[k])){if(transformed.indexOf(k)==-1||!isArray(flattened[k])){flattened[k]=[flattened[k]];transformed.push(k)}flattened[k].push(obj[k])}else flattened[k]=obj[k]}}if(top)transformed=null;return flattened};
})(),'Objects');
function generateRandomKey(){var x=2147483648,now=+new Date();return Math.floor(Math.random()*x).toString(36)+Math.abs(Math.floor(Math.random()*x)^now).toString(36)};
function toCamelCase(str){return String(str).replace(/\-([a-z])/g,function(all,match){return match.toUpperCase()})};
function isComponentLike(a){return isObject(a)&&isFunction(a.instanceOf)};
function isComponent(a){return isComponentLike(a)&&a.instanceOf('Component')};
function isController(a){return isComponentLike(a)&&a.instanceOf('Controller')};
function isControl(a){return isComponentLike(a)&&a.instanceOf('Control')};
function isObject(a){return!!a&&typeof a=='object'&&!isNode(a)&&!isArray(a)};
function isArray(a){return a instanceof Array};
function isArrayLike(a){return isArray(a)||isObject(a)};
function isElement(a){return a instanceof Element};
function isNode(a){return a instanceof Node};
function isText(a){return a instanceof Text};
function isFunction(a){return a instanceof Function};
function isBool(a){return typeof a=='boolean'};
function isString(a){return typeof a=='string'};
function isNumber(a){return typeof a=='string'};
function isPrimitive(a){return isString(a)||isNumber(a)||isBool(a)};
function isNumeric(a){return isNumber(a)||(isString(a)&&(/^\d+$/).test(a))};
function isUndefined(a){return a===undefined};
function isNull(a){return a===null};
function isNone(a){return isUndefined(a)||isNull(a)||a===false||a===0||a==='0'||a===''};
function isZero(a){return a===0||a==='0'};
function isNotEmptyString(a){return isString(a)&&(/[^\s]/).test(a)};
function stringToNumber(str){return Number(str)};
function getCount(a){return isArray(a)?a.length:0};
var __CB = function(__DT) {
__T = __DT['texts'];
__ = __DT['textsConstants'];
__V = __V();
__G.set(__T,'__T');
__G.set(__,'__');
__G.set(__V,'__V');
__G.set(c=__F(),'DataTable');
p=c.prototype;
p.handleClick=function(){this.set('a',[10,20,30,40,50,60,70])
this.set('i',40)};
p.getTemplateMain=function(_,$){return{'p':{'c':'data-table_outer-container'},'t':0,'e':[0,$.handleClick],'c':{'t':8,'c':{'l':function(){var count=$.g('a').length-1;return{'h':function(f,i){return{'t':10,'c':count-i}},'p':_['a']}},'n':'a'}}}};
p.getInitials=function(){return{'props':{a:[0,1,2,3,4,5,6,7,8,9,10],i:10}}};
__G.set(c=__F(),'DataTableFragmets');
p=c.prototype;
p.getTemplateMain=function(_,$){return{'p':{'c':'data-table-fragmets ','sc':1},'t':0}};
__G.set(c=__F(),'DataTableRow');
p=c.prototype;
p.getTemplateMain=function(_,$){};
p.getTemplateControls=function(_,$){return[{'p':{'c':'data-table-row_color-mark datatable-control'},'t':0},{'p':{'c':'data-table-row_checkbox-container datatable-control'},'t':0,'c':{'tmp':2}},{'p':{'c':'data-table-row_star datatable-control'},'t':0}]};
p.getTemplateHotMark=function(_,$){return{'p':{'_title':'fuck','c':'tooltiped datatable-tooltiped datatable-hot-tender','_timeout':'true'},'t':0}};
p.getTemplateCount=function(_,$){return{'p':{'c':'data-table-row_count'},'t':1,'c':_['count']}};
p.templatesById={'name':p.getTemplateHotMark};
__G.set(c=__F(),'DataTableStandartRow');
p=c.prototype;
p.getTemplateMain=function(_,$){return[!_['nocontrols']?{'tmp':$.getTemplateControls}:'',{'p':{'c':'data-table-standart-row ','h':'#tender/'+_['Id'],'tr':'_blank','_id':_['Id'],'sc':1},'t':12,'c':[{'p':{'c':'data-table-standart-row_top'},'t':0,'c':[{'p':{'c':'data-table-standart-row_top-item'},'t':0,'c':getFzName(_['type'])},_['multiregion']?{'p':{'c':'data-table-standart-row_top-item tooltiped','txt':_['regionnames'],'cap':__[0],'del':'1','cor':'list','pos':'left-top'},'t':0,'c':[{'p':{'count':_['multiregion']},'tmp':$.getTemplateCount},_['regionName']]}:{'p':{'c':'data-table-standart-row_top-item'},'t':0,'c':_['regionName']},_['multicategory']?{'p':{'c':'data-table-standart-row_top-item tooltiped','txt':_['subcategories'],'cap':__[1],'del':'1','cor':'list','pos':'left-top'},'t':0,'c':[{'p':{'count':_['multicategory']},'tmp':$.getTemplateCount},_['subcategory']]}:{'p':{'c':'data-table-standart-row_top-item'},'t':0,'c':_['subcategory']},{'p':{'c':'data-table-standart-row_top-item'},'t':0,'c':_['razm']=='N/A'?{'p':{'tariff':_['isUnavailable'],'width':'66px'},'tmp':1}:_['razm']}]},{'p':{'c':'data-table-standart-row_price'},'t':0,'c':_['price']},{'p':{'c':'data-table-standart-row_name'},'t':0,'c':[_['hot']?{'tmp':$.getTemplateHotMark}:'',_['name']]},{'p':{'c':'data-table-standart-row_bottom'},'t':0},_['fragments']?{'p':{'p':{'data':_['fragments']}},'cmp':'DataTableFragmets'}:'']}]};
__G.set(c=__F(),'TenderDataTable');
p=c.prototype;
__G.set(c=__F(),'FilterStatistics');
p=c.prototype;
p.onRendered=function(){this.refresh()};
p.onRefreshButtonClick=function(){var a=this.findElement('.filter-statistics');this.each('filters',function(filter){StoreKeeper.remove('filterStat_'+filter.filterId)});this.refresh()};
p.onFilterClick=function(){};
p.refresh=function(){this.getElement('rb').hide();this.currentFilterIndex=0;this.getCountForFilterWithIndex(0)};
p.onLoaded=function(filters){this.set('filters',filters)};
p.updateFilterCount=function(data){this.fill('.row'+data.filterId,data.numbers);this.currentFilterIndex++;this.getCountForFilterWithIndex(this.currentFilterIndex)};
p.getCountForFilterWithIndex=function(index){var filter=Objects.get(this.get('filters'),index);if(isObject(filter)){__C.get(2).doAction(this,'load',{'filterId':filter.filterId})}else{this.getElement('rb').show()}};
p.getTemplateMain=function(_,$){return{'p':{'c':'filter-statistics '+_['className'],'sc':1},'t':0,'c':[{'p':{'c':'filter-statistics_title'},'t':0,'c':[_['title'],{'p':{'as':'rb','c':'filter-statistics_refresh'},'t':0,'c':__[99]}]},{'p':{'c':'filter-statistics_head','cp':'0','cs':'0'},'t':2,'c':{'t':5,'c':[{'t':7,'c':__[100]},{'t':7,'c':__[101]},{'t':7,'c':__[102]},{'t':7,'c':__[103]},_['extended']?[{'t':7,'c':__[104]},{'t':7,'c':__[105]}]:'']}},{'p':{'c':'filter-statistics_content'},'t':0,'c':{'h':function(filter){return{'p':{'c':'filter-statistics_row row'+filter.filterId},'t':0,'c':[{'p':{'c':'filter-statistics_row-name'},'t':1,'c':filter.header},{'t':1,'c':['+',{'pl':'today'}]},{'t':1,'c':['+',{'pl':'yesterday'}]},{'t':1,'c':{'pl':'current'}},_['extended']?[{'t':1,'c':{'pl':'week'}},{'t':1,'c':{'pl':'month'}}]:'']}},'n':'filters','p':function(){return{'p':$.g('filters')}}}}]}};
p.getInitials=function(){return{'loader':{'controller':__C.get(1),'async':false},'controllers':[{'controller':__C.get(2),'on':{'load':this.updateFilterCount},'private':true}],'events':{'click':{'filter-statistics_refresh':this.onRefreshButtonClick,'filter-statistics_row-name':this.onFilterClick}}}};
__G.set(c=__F(),'SearchForm');
p=c.prototype;
p.onResetButtonClick=function(){this.set('reset',true);this.delay(function(){this.set('reset',false)},2500)};
p.onResetConfirmed=function(){};
p.getProperData=function(data){return Objects.flatten(this.getControlsData())};
p.getTemplateMain=function(_,$){return{'p':{'c':'search-form ','sc':1},'t':0,'c':[{'p':{'c':'search-form_title'},'t':0,'c':_['title']},{'p':{'c':'search-form_close-side'},'t':0,'e':[0,$.d.b($,'expand')]},{'p':{'c':'search-form_close'},'t':0,'e':[0,$.d.b($,'expand')]},{'tmp':$.getTemplateContent}]}};
p.getTemplateReset=function(_,$){return{'p':function(){return{'c':'search-form_reset '+($.g('reset')?'active':'')}},'t':0,'n':{'c':'reset'},'c':[{'p':{'c':'hover-label'},'t':0,'e':[0,$.onResetButtonClick],'c':__[10]},{'p':{'c':'confirm-label'},'t':0,'e':[0,$.onResetConfirmed],'c':[__[11],' ',{'p':{'c':'confirm-reset-filter'},'t':38,'c':__[12]}]}]}};
__G.set(c=__F(),'SearchFormButton');
p=c.prototype;
p.getTemplateMain=function(_,$){return{'p':{'c':'search-form_button '+_['className']},'t':0,'c':{'tmp':$.getTemplateContent}}};
__G.set(c=__F(),'SearchFormPanel');
p=c.prototype;
p.show=function(){this.addClass('shown');Popuper.watch(this)};
p.hide=function(){this.addClass('shown',false)};
p.getTemplateMain=function(_,$){return{'p':{'c':'app-search-form-panel '+_['className'],'sc':1},'t':0,'c':[{'p':{'c':'app-search-form-panel-close'},'t':0,'e':[0,$.hide]},{'p':{'c':'app-search-form-panel-title'},'t':0,'c':_['title']},{'tmp':$.getTemplateContent}]}};
__G.set(c=__F(),'SearchFormPanelButton');
p=c.prototype;
p.onClick=function(){this.get('panel').show()};
p.getTemplateMain=function(_,$){return{'p':{'c':'search-form_button '+_['className']},'t':0,'e':[0,$.onClick],'c':{'tmp':$.getTemplateContent}}};
__G.set(c=__F(),'Keywords');
p=c.prototype;
p.setControlValue=function(value){this.set('keywords',value['tags'])};
p.onChange=function(){State.dispatchEvent('TenderSearchFormChanged')};
p.addRequest=function(){this.addOneTo('keywords',[],0)};
p.removeRequest=function(index,isExact){this.removeByIndexFrom('keywords',isExact?index:this.get('keywordsCount')-index-1)};
p.onKeywordsChange=function(kw){var kwlen=kw.length,tabs=[],i;for(i=1;i<=kwlen;i++){tabs.push(__[28]+' '+i)}
this.set({'keywordsCount':kwlen,'tabs':tabs,'activeTab':kwlen-1});this.appendChild('tabs',kwlen>1);this.forChildren('KeywordsControl',function(child,i){child.set('index',kwlen-i)})};
p.onSelectTab=function(index){index=this.get('keywordsCount')-index-1;this.getElement('area').scrollTo(this.findElements('.keywords_block')[index],300)};
p.onTagEdit=function(tag){this.getChild('editor').edit(tag);Popuper.skipAll(true)};
p.onTagEdited=function(){Popuper.skipAll(false)};
p.onRemoveRequestClick=function(target){var block=target.getAncestor('.keywords_block');var blocks=this.findElements('.keywords_block');this.removeRequest(blocks.indexOf(block),true)};
p.getTemplateMain=function(_,$){return[{'p':{'c':'keywords_options'},'t':0,'c':[{'p':{'c':'bold'},'t':1,'c':__[16]},{'p':{'p':{'options':__V[0],'className':'g30','tooltip':true}},'cmp':'Select','nm':'nonmorph'},{'p':{'c':'bold'},'t':1,'c':__[17]},{'p':{'p':__V[1]},'cmp':'Checkbox','nm':'searchInDocumentation'},{'p':{'p':__V[2]},'cmp':'Checkbox','nm':'registryContracts'},{'p':{'p':__V[3]},'cmp':'Checkbox','nm':'registryProducts'},{'p':{'c':'keywords_add-request'},'t':0,'c':[{'t':1,'c':__[21]},{'p':{'className':'g3n','key':'keywordsNewReq'},'tmp':3}]},{'p':{'c':'tooltip keywords-hint'},'t':0}]},{'p':function(){return{'p':{'items':$.g('tabs'),'activeTab':$.g('activeTab')},'i':'tabs'}},'cmp':'Tabs','e':[22,$.onSelectTab,'remove',$.removeRequest],'n':{'items':'tabs','activeTab':'activeTab'}},{'p':function(){return{'as':'area','c':'keywords_area '+($.g('keywordsCount')>1?'multi':'')}},'t':0,'n':{'c':'keywordsCount'},'c':{'h':function(item,i){return{'p':{'p':{'items':item}},'cmp':'KeywordsControl','e':['edit',$.onTagEdit,14,$.onChange],'nm':'tags'}},'n':'keywords','p':function(){return{'p':$.g('keywords')}}}},{'p':{'i':'editor'},'cmp':'KeywordTagEditor','e':['hide',$.onTagEdited]}]};
p.getInitials=function(){return{'events':{'click':{'keywords_add-request':this.addRequest,'keywords_remove-request':this.onRemoveRequestClick}},'followers':{'keywords':this.onKeywordsChange}}};
__G.set(c=__F(),'KeywordsButton');
p=c.prototype;
p.getTemplateContent=function(_,$){return{'t':0,'c':__[13]}};
p.getInitials=function(){return{'props':{'className':'search-keywords'}}};
__G.set(c=__F(),'KeywordsControl');
p=c.prototype;
p.onFocus=function(switched){this.set('switched',switched)};
p.onRecommendationsChange=function(count){this.set('hasRecomm',count>0)};
p.getTemplateMain=function(_,$){return{'p':function(){return{'c':'keywords_block'+($.g('switched')?' switched':'')+($.g('hasRecomm')?' with-recommendations':''),'sc':1}},'t':0,'n':{'c':['hasRecomm','switched']},'c':[{'p':{'c':'keywords_left'},'t':0,'c':[{'p':{'c':'keywords_tags-title'},'t':0,'c':[__[22],{'p':{'c':'keywords_index'},'t':1,'c':[__[28],' ',{'v':$.g('index'),'n':'index'},{'p':{'c':'keywords_remove-request'},'t':1,'c':__[29]}]}]},{'p':{'p':{'items':_['items'][0]}},'cmp':'ContainKeywordTags','e':[15,$.onFocus.b($,false),'edit',$.d.b($,'edit'),'recchange',$.onRecommendationsChange,14,$.d.b($,'change')],'nm':'containKeyword'}]},{'p':{'c':'keywords_right'},'t':0,'c':[{'p':{'c':'keywords_tags-title'},'t':0,'c':__[23]},{'p':{'p':{'items':_['items'][1]}},'cmp':'ExcludeKeywordTags','e':[15,$.onFocus.b($,true),'edit',$.d.b($,'edit'),14,$.d.b($,'change')],'nm':'notcontainKeyword'}]}]}};
__G.set(c=__F(),'KeywordsPanel');
p=c.prototype;
p.getTemplateContent=function(_,$){return{'cmp':'Keywords','nm':'keywords'}};
p.getInitials=function(){return{'props':{'className':'keywords-panel','title':__[13]}}};
__G.set(c=__F(),'TenderSearchForm');
p=c.prototype;
p.onRendered=function(){this.setParams({'registryContracts':1});this.delay(function(){State.set('aaa',[9,8,7,6,5,4,3,2,1])
State.dispatchEvent('aaa');this.delay(function(){State.set('aaa',[100,200,300,400])
this.delay(function(){State.set('aaa',null)},3000)},3000)},6000)};
p.onChange=function(){var data=this.getProperData()};
p.setParams=function(params){params=__G.get('SearchFormCrr').correct(params);this.setControlsData(params)};
p.getTemplateContent=function(_,$){return[{'p':{'i':'keywordsPanel'},'cmp':'KeywordsPanel'},{'tmp':$.getTemplateReset},{'p':{'c':'tender-search-form ','sc':1},'t':0,'c':[{'cmp':'SearchFormFilters'},{'p':{'c':'tender-search-form_content'},'t':0,'c':{'cmp':'KeywordsButton','w':['panel','keywordsPanel']}}]}]};
p.getInitials=function(){return{'props':{'title':__[4]},'listeners':{'TenderSearchFormChanged':this.onChange,'TenderSearchFormGotParams':this.setParams}}};
__G.set(c=__F(),'SearchFormCreateFilterMenu');
p=c.prototype;
p.onCreateButtonClick=function(){alert('create filter')};
p.onWizardButtonClick=function(){alert('create filter with wizard')};
p.getInitials=function(){return{'props':{'className':'create-filters-menu','buttons':[{'name':__[5],'handler':this.onCreateButtonClick},{'name':__[6],'handler':this.onWizardButtonClick}]}}};
__G.set(c=__F(),'SearchFormFilterMenu');
p=c.prototype;
p.onLoadFilters=function(filters){this.renderButtons(filters)};
p.onCheckboxChange=function(e){__C.get(1).doAction(this,'set',{'filterId':e.value,'param':'isAutoOpen','value':e.checked})};
p.getButtonData=function(item){return{'value':item['filterId'],'name':item['header'],'isAutoOpen':item['isAutoOpen']}};
p.handleClick=function(value,button){App.getView('search').openFilter(value)};
p.getTemplateContent=function(_,$){return{'p':{'c':'checkbox '+(_['item']['isAutoOpen']?'checked':''),'_value':_['item']['value']},'t':0}};
p.getInitials=function(){return{'props':{'className':'filters-menu','maxHeight':400},'controllers':[{'controller':__C.get(1),'on':{'load':this.onLoadFilters}}],'helpers':[{'helper':'CheckboxHandler','options':{'callback':this.onCheckboxChange,'intValue':true}}]}};
__G.set(c=__F(),'SearchFormFilters');
p=c.prototype;
p.onLoadFilters=function(filters){this.set('quantity',filters.length)};
p.onSaveFilterClick=function(){Dialoger.show('FilterEdit',{'filterId':State.get('filterId')})};
p.getTemplateMain=function(_,$){return{'p':{'c':'search-form-filters ','sc':1},'t':0,'c':[{'p':{'c':'search-form-filters_create-button'},'t':0,'c':[{'t':1,'c':__[7]},{'cmp':'SearchFormCreateFilterMenu'}]},{'p':function(){return{'c':'search-form-filters_button'+(!$.g('quantity')?' with-plus':'')}},'t':0,'n':{'c':'quantity'},'c':[{'p':{'c':'search-form-filters_button-inner'},'t':0,'c':__[8]},{'p':{'c':'search-form-filters_button-side'},'t':0,'c':[{'p':{'c':'search-form-filters_button-quantity'},'t':40,'c':{'v':$.g('quantity'),'n':'quantity'}},{'p':{'c':'search-form-filters_button-plus'},'t':0}]},{'cmp':'SearchFormFilterMenu'}]},{'p':{'c':'search-form-filters_name'},'t':0,'e':[0,$.onSaveFilterClick],'c':{'v':$.g('filterName'),'n':'filterName'}},{'p':{'c':'search-form-filters_save-button'},'t':0,'e':[0,$.onSaveFilterClick],'c':__[9]}]}};
p.getInitials=function(){return{'controllers':[{'controller':__C.get(1),'on':{'load':this.onLoadFilters}}],'props':{'filterName':'Master'}}};
__G.set(c=__F(),'Favorites');
p=c.prototype;
p.getInitials=function(){return{'actions':{'load':{'url':CONFIG.favorites.get},'add':{'url':CONFIG.favorites.add},'put':{'url':CONFIG.favorites.remove}}}};
__G.set(c=__F(),'Filters');
p=c.prototype;
p.onLoadFilters=function(data){};
p.onLoad=function(data){};
p.onAdd=function(data){};
p.onSubscribe=function(){this.doAction(null,'load')};
p.getInitials=function(){return{'options':{'key':'filterId','store':false,'storeAs':'filters','storePeriod':'1day','clone':true},'actions':{'load':{'url':CONFIG.filters.load,'method':'GET','callback':this.onLoad},'save':{'url':CONFIG.filters.save,'method':'POST','callback':this.onAdd},'set':{'url':CONFIG.filters.set,'method':'POST'},'subscribe':{'url':CONFIG.filters.subscribe,'method':'POST','callback':this.onSubscribe}}}};
__G.set(c=__F(),'FiltersStat');
p=c.prototype;
p.getInitials=function(){return{'options':{'key':'filterId','store':false,'storeAs':'filterStat_$filterId','storePeriod':'4hour'},'actions':{'load':{'url':CONFIG.filterStat.load,'method':'GET'}}}};
__G.set(c=__F(),'RecommendationsLoader');
p=c.prototype;
p.getInitials=function(){return{'actions':{'load':{'url':CONFIG.keywords.recommendations,'method':'POST','autoset':{'data':'items'}}}}};
__G.set(c=__F(),'Subscription');
p=c.prototype;
p.getInitials=function(){return{'actions':{'load':{'url':CONFIG.settings.subscr,'method':'GET','autoset':{'options':'opts'}},'save':{'url':CONFIG.settings.set,'method':'GET'}}}};
__G.set(c=__F(),'UserInfoLoader');
p=c.prototype;
p.getInitials=function(){return{'actions':{'load':{'url':CONFIG.user.get,'method':'GET'}}}};
__G.set(c=__F(),'Checkbox');
p=c.prototype;
p.onClick=function(){this.toggle('checked');this.dispatchChange()};
p.getControlValue=function(){return this.get('checked')?1:0};
p.setControlValue=function(value){this.set('checked',!!value)};
p.getTemplateMain=function(_,$){return{'p':{'c':'checkbox_label'},'t':1,'e':[0,$.onClick],'c':[{'p':function(){return{'c':'checkbox '+($.g('checked')?'checked':'')}},'t':1,'n':{'c':'checked'}},_['text']]}};
__G.set(c=__F(),'Input');
p=c.prototype;
p.getControlValue=function(){return this.findElement('input').value};
p.getTemplateMain=function(_,$){return{'p':function(){return{'tp':_['type'],'n':_['name'],'p':$.g('placeholder'),'v':$.g('value'),'readonly':!$.g('enabled')?'readonly':'','accept':_['accept']}},'t':14,'e':[18,$.onChange],'n':{'p':'placeholder','v':'value','readonly':'enabled'}}};
__G.set(c=__F(),'Select');
p=c.prototype;
p.onRendered=function(){var value=this.get('value');var selected;if(!isUndefined(value)){selected=this.selectByValue(value,true)}
if(!selected){this.selectByIndex(0)}};
p.getChangeEventParams=function(){return{value:this.get('value'),title:this.get('title')}};
p.selectByValue=function(value,forced){if(!forced&&this.get('value')==value)return;var options=this.get('options');if(isArray(options)){for(var i=0;i<options.length;i++){if(options[i]['value']==value){this.selectedIndex=i;if(!forced)this.set('value',value);this.set('title',options[i]['title']);this.syncTooltip(i);return true}}}
return false};
p.selectByIndex=function(index){var options=this.get('options');this.selectedIndex=index;if(isObject(options[index])){if(this.get('value')==options[index]['value'])return;this.set({'value':options[index]['value'],'title':options[index]['title']});this.syncTooltip(index)}};
p.syncTooltip=function(index){var optionElement=this.getOptionElementAt(index);var tooltipElement=this.findElement('.tooltip,optionElement')};
p.enableOption=function(index,isEnabled){this.getOptionElementAt(index).toggleClass('disabled',!isEnabled);if(index==this.selectedIndex){this.selectByIndex(index==0?index+1:0)}};
p.onOptionsClick=function(e){var target=e.getTarget('.select_option');if(target&&!target.hasClass('disabled')){var value=target.getData('value');if(this.selectByValue(value)){this.dispatchChange()}
this.hide()}};
p.getOptionElementAt=function(index){return this.findElement('.select_options').getChildAt(index)};
p.setProperValue=function(value){this.selectByValue(value)};
p.getControlValue=function(){return this.findElement('input').value};
p.onClick=function(){this.toggle('active');Popuper.watch(this)};
p.hide=function(){this.set('active',false)};
p.getTemplateMain=function(_,$){return{'p':function(){return{'c':'select '+(_['className']?_['className']:'')+' '+($.g('active')?'active':''),'sc':1}},'t':0,'n':{'c':'active'},'c':[{'p':{'c':'select_value'},'t':0,'e':[0,$.onClick],'c':[{'v':$.g('title'),'n':'title'},_['tooltip']?{'p':{'className':'g3n','key':$.g('tooltip')},'tmp':3}:'']},{'p':{'c':'select_options'},'t':0,'e':[0,$.onOptionsClick],'c':{'h':function(option){return{'p':{'c':'select_option','_value':option.value},'t':0,'c':[option.title,option.tooltip?{'p':{'className':'g3n','key':option.tooltip},'tmp':3}:'']}},'n':'options','p':function(){return{'p':$.g('options')}}}},{'p':function(){return{'tp':'hidden','n':$.g('name'),'v':$.g('value')}},'t':14,'n':{'n':'name','v':'value'}}]}};
__G.set(c=__F(),'ContainKeywordTags');
p=c.prototype;
p.onRendered=function(){this.resetOptions()};
p.onPickRecommendation=function(value){};
p.onEnter=function(value){__G.get('KeywordTags').prototype.onEnter.call(this,value);var items=this.get('items').join(',').replace(/\#\d/g,'').split(',');this.getChild('recommendations').load(items)};
p.getCorrectedText=function(text){var opt1=this.get('opt1value');var opt2=this.get('opt2value');if(opt1>1||opt2>1){return text+'#'+opt1+'#'+opt2}
return text};
p.resetOptions=function(){this.set({'opt1':__[32],'opt2':__[34]});this.set('opt1value',1);this.set('opt2value',1)};
p.getTemplateTopContent=function(_,$){return{'p':{'c':'tags_top'},'t':0,'c':[{'p':{'c':'tags_select-button','_index':'1'},'t':1,'c':[{'v':$.g('opt1'),'n':'opt1'},{'p':{'p':{'options':__V[5],'title':__[30]},'i':'opt1'},'cmp':'PopupSelect','e':[14,$.onChangeOption]}]},{'p':{'c':'tags_select-button','_index':'2'},'t':1,'c':[{'v':$.g('opt2'),'n':'opt2'},{'p':{'p':{'options':__V[6],'title':__[31]},'i':'opt2'},'cmp':'PopupSelect','e':[14,$.onChangeOption]}]},{'tmp':$.getTemplateTopButtons}]}};
p.getTemplateInput=function(_,$){return{'p':{'p':{'placeholder':__[24],'options':__V[4]}},'cmp':'KeywordsAutoComplete','e':[15,$.d.b($,'focus'),'enter',$.onEnter,'pick',$.onPickVariant]}};
p.getTemplateBottomContent=function(_,$){return{'p':{'i':'recommendations'},'cmp':'Recommendations','e':['pick',$.onPickRecommendation,14,$.d.b($,'recchange')]}};
__G.set(c=__F(),'ExcludeKeywordTags');
p=c.prototype;
p.onRendered=function(){this.resetOptions()};
p.getCorrectedText=function(text){var opt1value=this.get('opt1value');if(opt1value>1){return text+'#'+opt1value}
return text};
p.resetOptions=function(){this.set({'opt1':__[32],'opt1value':1})};
p.getTemplateTopContent=function(_,$){return{'p':{'c':'tags_top'},'t':0,'c':[{'p':{'c':'tags_select-button','_index':'1'},'t':1,'c':[{'v':$.g('opt1'),'n':'opt1'},{'p':{'p':{'options':__V[7],'title':__[41]},'i':'opt1'},'cmp':'PopupSelect','e':[14,$.onChangeOption]}]},{'tmp':$.getTemplateTopButtons}]}};
p.getTemplateInput=function(_,$){return{'p':{'p':{'placeholder':__[24]}},'cmp':'AutoComplete','e':[15,$.d.b($,'focus'),'enter',$.onEnter]}};
__G.set(c=__F(),'KeywordTags');
p=c.prototype;
p.onEnter=function(value){__G.get('Tags').prototype.onEnter.call(this,value);this.resetOptions()};
p.onOptionClick=function(target){var select=this.getChild('opt'+target.getData('index'));if(select){select.show()}};
p.onChangeOption=function(e,target){var cmpid=target.getId();this.set(cmpid,e.title);this.set(cmpid+'value',e.value);target.hide()};
p.hasOption=function(text){return!!text.split('#')[1]};
p.getProperTagText=function(text){return text.split('#')[0]};
p.tagExists=function(text){var items=this.get('items').join(',').replace(/\#\d/g,'');return items.split(',').has(text)};
p.resetOptions=function(){};
p.getTemplateTopButtons=function(_,$){return{'p':{'c':'tags_top-buttons'},'t':0,'c':[{'p':{'c':'tags_top-buttons-item'},'t':0,'c':[__[26],' ',{'v':$.g('count'),'n':'count'}]},{'p':{'c':'tags_top-buttons-item'},'t':0,'c':{'p':{'c':'tags_remove-all'},'t':1,'c':__[27]}}]}};
p.getTemplateTag=function(_,$){return{'p':{'c':'tags_item '+($.hasOption(_['text'])?'optioned':'')},'t':0,'c':[{'p':{'c':'tags_item-text','_text':_['text']},'t':1,'c':$.getProperTagText(_['text'])},{'p':{'c':'tags_remove'},'t':1}]}};
p.getInitials=function(){return{'events':{'click':{'app-tags-remove-all':this.clear,'app-tags-select-button':this.onOptionClick}}}};
__G.set(c=__F(),'Tags');
p=c.prototype;
p.onEnter=function(value){value=value.split(',');var a=[],tv;var b=value,idx;for(idx=0;idx<b.length;idx++){var v=b[idx];tv=v.trim().toLowerCase();if(!tv.isEmpty()&&!this.tagExists(tv)){a.push(this.getCorrectedText(tv))}}
if(!a.isEmpty()){this.addTo('items',a,0);this.dispatchChange()}};
p.tagExists=function(text){return this.get('items').has(text)};
p.getCorrectedText=function(text){return text};
p.onPickVariant=function(value){this.onEnter(value)};
p.onRemoveButtonClick=function(target){this.removeValueFrom('items',target.prev().getData('text'));this.dispatchChange()};
p.onTagClick=function(target){this.dispatchEvent('edit',target)};
p.getControlValue=function(){return this.get('items').join(',')};
p.clearControl=function(){this.set('items',[])};
p.onItemsChange=function(items){this.set('count',items.length)};
p.getTemplateMain=function(_,$){return{'p':{'c':'tags ','sc':1},'t':0,'c':[{'p':{'c':'tags_container'},'t':0,'c':[{'tmp':$.getTemplateTopContent},{'tmp':$.getTemplateInput},{'p':{'c':'tags_content'},'t':0,'c':{'p':{'c':'tags_placeholder'},'t':0,'c':{'h':function(item){return{'p':{'text':item},'tmp':$.getTemplateTag}},'n':'items','p':function(){return{'p':$.g('items')}}}}}]},{'tmp':$.getTemplateBottomContent}]}};
p.getTemplateInput=function(_,$){return{'cmp':'AutoComplete','e':[15,$.d.b($,'focus'),'enter',$.onEnter,'pick',$.onPickVariant]}};
p.getTemplateTag=function(_,$){return{'p':{'c':'tags_item'},'t':0,'c':[{'p':{'c':'tags_item-text','_text':_['text']},'t':1,'c':_['text']},{'p':{'c':'tags_remove'},'t':1}]}};
p.getInitials=function(){return{'props':{'items':[],'count':0},'events':{'click':{'app-tags-remove':this.onRemoveButtonClick,'app-tags-item-text':this.onTagClick}},'followers':{'items':this.onItemsChange}}};
__G.set(c=__F(),'Textarea');
p=c.prototype;
p.getControlValue=function(){return this.findElement('textarea').value};
p.getTemplateMain=function(_,$){return{'p':function(){return{'n':$.g('name'),'p':$.g('placeholder'),'readonly':$.g('enabled')?'readonly':''}},'t':47,'e':[18,$.onChange],'n':{'n':'name','p':'placeholder','readonly':'enabled'},'c':{'v':$.g('value'),'n':'value'}}};
__G.set(c=__F(),'SearchFormCrr',1);
p=c.prototype;
p.correct=function(params){var tags=[];var maxlen=Objects.get(params['containKeyword'],'length',0);maxlen=Math.max(maxlen,Objects.get(params['notcontainKeyword'],'length',0));if(maxlen>0){var ck,nck;for(var i=0;i<maxlen;i++){ck=Objects.get(params['containKeyword'],i,'').toArray();nck=Objects.get(params['notcontainKeyword'],i,'').toArray();tags.push([ck,nck])}}else{tags.push([])}
return{'keywords':{'nonmorph':params['nonmorph'],'registryContracts':params['registryContracts'],'registryProducts':params['registryProducts'],'searchInDocumentation':params['searchInDocumentation'],'tags':tags}}};
__G.set(c=__F(),'CalendarFavorites');
p=c.prototype;
p.getTemplateContent=function(_,$){return{'p':{'c':'simple-datatable calendar-favorites-datatable'},'t':0,'c':{'h':function(tender){return{'p':{'ap':{'nocontrols':'1'},'p':tender},'cmp':'DataTableStandartRow'}},'n':'tenders','p':function(){return{'p':$.g('tenders')}}}}};
p.getInitials=function(){return{'props':{'expandable':true,'width':1000}}};
__G.set(c=__F(),'FilterEdit');
p=c.prototype;
p.getInitials=function(){return{'props':{'title':__[74]}}};
__G.set(c=__F(),'OrderCall');
p=c.prototype;
p.onSupportButtonClick=function(){this.hide();Dialoger.show('Support')};
p.onShow=function(){var form=this.getChildAt(0);var handler=form.validateTime.bind(form);this.interval=setInterval(handler,60000);handler()};
p.onHide=function(){clearInterval(this.interval)};
p.getTemplateContent=function(_,$){return{'cmp':'OrderCallForm'}};
p.getTemplateButtons=function(_,$){return{'p':{'c':'standart-button order-support'},'t':0,'e':[0,$.onSupportButtonClick],'c':__[47]}};
p.getInitials=function(){return{'props':{'title':__[46]}}};
__G.set(c=__F(),'Support');
p=c.prototype;
p.onOrderCallButtonClick=function(){this.hide();Dialoger.show('OrderCall')};
p.getTemplateContent=function(_,$){return{'cmp':'SupportForm'}};
p.getTemplateButtons=function(_,$){return{'p':{'c':'standart-button order-support'},'t':0,'e':[0,$.onOrderCallButtonClick],'c':__[49]}};
p.getInitials=function(){return{'props':{'title':__[48]}}};
__G.set(c=__F(),'App');
p=c.prototype;
p.onNoErrors=function(){this.appendChild('menu',true)};
p.onError=function(errorCode){this.appendChild('menu',false)};
p.getTemplateMain=function(_,$){return[{'p':{'i':'menu'},'cmp':'TopMenu'},{'p':{'c':'app-view-container'},'t':0}]};
__G.set(c=__F(),'AuthForm');
p=c.prototype;
p.onSuccess=function(){Router.reload()};
p.getTemplateContent=function(_,$){return{'p':{'c':'app-authform-logo'},'t':0,'c':'LOGO'}};
p.getInitials=function(){return{'props':{'action':'user/login.php','className':'app-authform-inputs','controls':[{'caption':__[51],'controlClass':'Input','controlProps':{'type':'text','name':'login','placeholder':__[50]}},{'caption':__[53],'controlClass':'Input','controlProps':{'type':'password','name':'password','placeholder':__[52]}}],'submit':{'value':__[54],'class':'app-submit'}}}};
__G.set(c=__F(),'OrderCallForm');
p=c.prototype;
p.onRendered=function(){this.setControlValue('name',User.getAttribute('name'));this.setControlValue('phone',User.getAttribute('phone'));var email=User.getAttribute('email');if(email){this.setControlValue('email',email);this.enableControl('email',false)}};
p.getDateOptions=function(){var monthNames=Dictionary.get('monthNames'),
date=new Date(),
year=date.getFullYear(),
time=date.getHours,
month=date.getMonth()+1,
day=date.getDate(),
days=33-new Date(year,month-1,33).getDate(),
dates=[],d,m=month,dayInWeek,count=0,index=0,txt;var prev=0;while(count<10){d=day+index;if(day+index>days){d=d-days;m=month+1;if(m>12){break}}
dayInWeek=new Date(year,m-1,d).getDay();if(dayInWeek==0||dayInWeek>5){index++;continue}
dayInWeek=Dictionary.get('dayNames')[dayInWeek];txt=count>1||(!!prev&&prev!=d-1)?d+' '+monthNames[m]+', '+dayInWeek:(count==0?__[62]:__[63])+', '+d+' '+monthNames[m];dates.push({'value':txt,'title':txt});count++;index++;prev=d}
date=day+' '+monthNames[month];return dates};
p.validateTime=function(){var dateSelect=this.getControl('date');var timeSelect=this.getControl('time');var dateValue=dateSelect.getValue();var isToday=(new RegExp(__[62])).test(dateValue);if(isToday){var d=new Date();var hours=[11,13,15];var minutes=[0,0,30];var moscowTime=d.getUTCHours()+3;var minute=d.getMinutes();var disabledIndexes=[];for(var i=0;i<hours.length;i++){if(moscowTime>hours[i]||(moscowTime==hours[i]&&minute>=minutes[i])){disabledIndexes.push(i)}}
if(disabledIndexes.length==hours.length){dateSelect.enableOption(0,false)}else{for(i=0;i<disabledIndexes.length;i++){timeSelect.enableOption(disabledIndexes[i],false)}}}else{timeSelect.enableOption(0,true);timeSelect.enableOption(1,true);timeSelect.enableOption(2,true)}};
p.getInitials=function(){return{'props':{'action':CONFIG.orderCall.send,'method':'POST','className':'app-order-call','controls':[__V[8],__V[9],__V[10],{'caption':__[58],'class':'half-width','controlClass':'Select','controlProps':{'name':'topic','options':Dictionary.get('orderCallTopics')}},{'caption':__[59],'class':'half-width','controlClass':'Select','controlProps':{'name':'date','options':this.getDateOptions()}},{'caption':__[60],'class':'half-width','controlClass':'Select','controlProps':{'name':'time','options':Dictionary.get('timeOptions')}},{'caption':__[61],'controlClass':'Textarea','controlProps':{'name':'comment'}}],'submit':{'value':__[93],'class':__[]+'send-button'}}}};
__G.set(c=__F(),'SupportForm');
p=c.prototype;
p.getInitials=function(){return{'props':{'action':CONFIG.support.send,'className':'app-order-call','controls':[__V[8],__V[9],__V[10],{'caption':__[64],'controlClass':'Textarea','controlProps':{'name':'comment'}},{'caption':__[65],'controlClass':'Input','controlProps':{'name':'screenshot','type':'file','accept':'image/*'}}],'submit':{'value':__[66],'class':__[]+'send-button'}}}};
__G.set(c=function(){
Router.addMenu(this);
this.isRouteMenu=true}
,'TopMenu');
p=c.prototype;
p.getTemplateMain=function(_,$){return{'p':{'c':'top-menu ','sc':1},'t':0,'c':{'p':{'c':'top-menu_inner'},'t':0,'c':[{'p':{'h':'#main','c':'top-menu_logo'},'t':12},{'p':{'h':'#main','r':'main'},'t':12,'c':__T[0]},{'p':{'h':'#search','r':'search'},'t':12,'c':__T[1]},{'p':{'h':'#favorite','r':'favorite'},'t':12,'c':__T[2]},{'p':{'h':'#planzakupok','r':'planzakupok'},'t':12,'c':__T[3]},{'p':{'h':'#analytics','r':'analytics'},'t':12,'c':__T[4]}]}}};
__G.set(c=__F(),'AutoComplete');
p=c.prototype;
p.onInput=function(value){var options=this.get('options');var len=value.length;var minLength=Objects.get(options,'minLength',3);if(isString(Objects.get(options,'url'))&&len>=minLength){this.delay(this.load,1000,value)}else if(len==0){this.delay();this.set('variants',[])}};
p.load=function(value){Loader.get(Objects.get(this.get('options'),'url'),{'token':value},this.onLoad,this)};
p.onLoad=function(data){this.set('currentVariant',null);this.set('variants',data['items'])};
p.onFocus=function(){if(this.get('variantsCount')>0){this.set('active',true)}};
p.onChangeVariants=function(variants){var count=isArray(variants)?variants.length:0;this.set({'variantsCount':count,'active':count>0})};
p.onBlur=function(){this.delay(function(){this.set('active',false)},200)};
p.onEnter=function(value){var currentVariant=this.get('currentVariant');if(isNumber(currentVariant)){var e=this.findElement('.auto-complete_variant.active');this.dispatchEvent('enter',e.getData('value'));this.onEscape();return false}else{this.clear()}};
p.setValue=function(value){this.findElement('input').value=value};
p.onEscape=function(){this.clear()};
p.clear=function(){this.delay();this.getElement('input').clear();this.set('variants',[])};
p.onVariantPick=function(target){this.dispatchEvent('pick',target.getData('value'));this.clear()};
p.onUp=function(){this.highlightVariant(-1)};
p.onDown=function(){this.highlightVariant(1)};
p.highlightVariant=function(step){var variants=this.get('variants');var currentVariant=this.get('currentVariant');if(isArray(variants)&&variants.length>0){var total=variants.length;if(!isNumber(currentVariant)){currentVariant=-1}
currentVariant+=step;if(currentVariant<0){currentVariant=total-1}else if(currentVariant==total){currentVariant=0}
this.set('currentVariant',currentVariant)}};
p.onChangeCurrentVariant=function(index){var e=this.findElement('.auto-complete_variant.active');if(e)e.removeClass('active');e=this.findElements('.auto-complete_variant')[index];if(e)e.addClass('active')};
p.onChangeActive=function(isActive){if(!isActive)this.set('currentVariant',null)};
p.getTemplateMain=function(_,$){return{'p':{'c':'auto-complete input-container','sc':1},'t':0,'c':{'p':{'as':'input','tp':'text','p':_['placeholder']},'t':14,'c':[{'tmp':$.getTemplateContent},{'p':function(){return{'as':'variants','c':'auto-complete_variants'+($.g('active')?' shown':'')}},'t':0,'n':{'c':'active'},'c':{'p':{'c':'auto-complete_variants-inner'},'t':0,'c':{'h':function(variant,i){return{'p':{'props':variant,'index':i},'tmp':$.getTemplateVariant}},'n':'variants','p':function(){return{'p':$.g('variants')}}}}}]}}};
p.getTemplateVariant=function(_,$){return{'p':{'c':'auto-complete_variant','_index':_['index'],'_value':_['name']},'t':0,'c':_['name']}};
p.getInitials=function(){return{'helpers':[{'helper':'InputHandler','options':{'callbacks':{'enter':this.onEnter,'esc':this.onEscape,'focus':this.onFocus,'blur':this.onBlur,'input':this.onInput,'up':this.onUp,'down':this.onDown},'inputSelector':'input'}}],'events':{'click':{'auto-complete_variant':this.onVariantPick}},'followers':{'variants':this.onChangeVariants,'currentVariant':this.onChangeCurrentVariant,'active':this.onChangeActive}}};
__G.set(c=__F(),'KeywordsAutoComplete');
p=c.prototype;
p.onAddButtonClick=function(){var value=this.findElement('input').value;this.onEnter(value);this.dispatchEvent('enter',value)};
p.getTemplateContent=function(_,$){return{'p':{'c':'standart-button green-button add'},'t':0,'e':[0,$.onAddButtonClick],'c':__[25]}};
__G.set(c=__F(),'Calendar');
p=c.prototype;
p.onRendered=function(){this.reset()};
p.redraw=function(){var day=this.isCurrentMonth()?Dates.getDay():0,
month=this.month,
year=this.year,
curDays=Dates.getDays(month,year),
prevMonth=month-1>=0?month-1:11,
prevYear=prevMonth<12?year:year-1,
prevDays=Dates.getDays(prevMonth,prevYear),
firstDay=Dates.getWeekDay(1,month,year),
firstCell=firstDay>0?firstDay-1:6,
count=1,
lastCell=0,
days=[];for(var i=0;i<firstCell;i++){days.push({num:prevDays-i,another:true})}
days=days.reverse();for(var i=firstCell;i<curDays+firstCell;i++){days.push({num:count,current:count==day,marked:this.isMarked(count,month,year)});lastCell=i;count++}
var len=days.length;var more=len<=35?35-len:42-len;for(var i=1;i<=more;i++){days.push({num:i,another:true})}
this.set({'year':year,'month':Dates.getMonthName(month),'days':days})};
p.isCurrentMonth=function(){return this.month==Dates.getMonth()&&this.year==Dates.getYear()};
p.reset=function(){this.month=Dates.getMonth();this.year=Dates.getYear();this.redraw()};
p.isMarked=function(){return false};
p.onPrevClick=function(){this.changeMonth(-1)};
p.onNextClick=function(){this.changeMonth(1)};
p.changeMonth=function(value){this.month+=value;if(this.month==12){this.month=0;this.year++}else if(this.month==-1){this.month=11;this.year--}
this.redraw()};
p.getTemplateMain=function(_,$){return{'p':{'c':'calendar '},'t':0,'c':[{'p':{'c':'calendar_header'},'t':0,'c':[{'p':{'c':'calendar_prev'},'t':0,'e':[0,$.onPrevClick]},{'p':{'c':'calendar_month'},'t':0,'c':[{'v':$.g('month'),'n':'month'},{'p':{'c':'calendar_year'},'t':1,'c':{'v':$.g('year'),'n':'year'}}]},{'p':{'c':'calendar_next'},'t':0,'e':[0,$.onNextClick]}]},{'p':{'c':'calendar_content'},'t':0,'c':[{'p':{'c':'calendar_day-names'},'t':0,'c':[{'t':1,'c':__[67]},{'t':1,'c':__[68]},{'t':1,'c':__[69]},{'t':1,'c':__[70]},{'t':1,'c':__[71]},{'t':1,'c':__[72]},{'t':1,'c':__[73]}]},{'p':{'c':'calendar_days'},'t':0,'c':{'h':function(day){return{'p':{'c':(day.another?'another':'')+' '+(day.current?'current':'')+' '+(day.marked?'marked':'')},'t':1,'c':day.num}},'n':'days','p':function(){return{'p':$.g('days')}}}}]}]}};
__G.set(c=__F(),'FavoritesCalendar');
p=c.prototype;
p.onRendered=function(){this.month=Dates.getMonth();this.year=Dates.getYear()};
p.isMarked=function(d,m,y){return Objects.has(this.tenderByDates,d+'.'+(m+1)+'.'+y)};
p.onLoadFavorites=function(data){var timestamp;this.tenderByDates={};for(var i=0;i<data.length;i++){if(data[i]['phase_']==1){timestamp=data[i]['finishdocdate'].replace(/\.(\d+)$/,".20$1").replace(/0(?=\d\.)/g,'');this.tenderByDates[timestamp]=this.tenderByDates[timestamp]||[];this.tenderByDates[timestamp].push(data[i])}}
this.redraw()};
p.onMarkedDayClick=function(target){var timestamp=target.innerHTML+'.'+(this.month+1)+'.'+this.year;if(isArray(this.tenderByDates[timestamp])){Dialoger.show(CalendarFavorites,{'title':__[44]+' '+Dates.getFormattedDate(timestamp,__[45]),'tenders':this.tenderByDates[timestamp]})}};
p.getInitials=function(){return{'controllers':[{'controller':__C.get(0),'on':{'load':this.onLoadFavorites}}],'events':{'click':{'marked':this.onMarkedDayClick}}}};
__G.set(c=__F(),'Dialog');
p=c.prototype;
p.show=function(){this.set('shown',true);this.reposition();this.onShow()};
p.reposition=function(){var rect=this.getElement().getRect();this.set({'marginTop':Math.round(rect.height/-2)+'px','marginLeft':Math.round(rect.width/-2)+'px'})};
p.hide=function(){this.set('shown',false)};
p.close=function(){this.hide();this.onHide()};
p.expand=function(isExpanded){if(isBool(isExpanded)){this.set('expanded',isExpanded)}else{this.toggle('expanded')}};
p.onShow=function(){};
p.onHide=function(){};
p.getTemplateMain=function(_,$){return[{'p':function(){return{'c':'dialog_mask '+($.g('shown')?'shown':'')}},'t':0,'e':[0,$.close],'n':{'c':'shown'}},{'p':function(){return{'c':'dialog '+($.g('expanded')?'expanded':'')+' '+($.g('shown')?'shown':''),'st':'width:'+$.g('width')+'px;margin-left:'+$.g('marginLeft')+';margin-top:'+$.g('marginTop')+';','sc':1}},'t':0,'n':{'c':['expanded','shown'],'st':['marginLeft','marginTop','width']},'c':[{'c':{'p':{'c':'dialog_close'},'t':0,'e':[0,$.close]},'i':function(){return $.g('closable')},'n':['closable']},{'c':{'p':{'c':'dialog_expand'},'t':0,'e':[0,$.expand]},'i':function(){return $.g('expandable')},'n':['expandable']},{'p':{'c':'dialog_title'},'t':0,'c':{'v':$.g('title'),'n':'title'}},{'p':function(){return{'c':'dialog_content','st':$.g('height')?'max-height:'+$.g('height')+'px;':''}},'t':0,'n':{'st':'height'},'c':{'tmp':$.getTemplateContent}},{'p':{'c':'dialog_buttons'},'t':0,'c':{'tmp':$.getTemplateButtons}}]}]};
p.getInitials=function(){return{'props':{'closable':true,'width':600},'followers':{'width':this.reposition,'height':this.reposition}}};
__G.set(c=__F(),'Editor');
p=c.prototype;
p.edit=function(element){this.editedElement=element;this.set({'text':element.innerHTML,'shown':true});this.reposition()};
p.reposition=function(text){this.placeTo(document.body);var rect=this.editedElement.getRect();this.getElement().setPosition(rect.left,rect.top)};
p.onChangeText=function(text){var input=this.findElement('input');input.value=text;input.focus()};
p.onEnter=function(value){this.editedElement.innerHTML=value;this.hide()};
p.hide=function(){this.close();this.placeBack();this.dispatchEvent('hide')};
p.close=function(){this.set('shown',false)};
p.getTemplateMain=function(_,$){return[{'p':function(){return{'c':'editor_mask '+($.g('shown')?'shown':'')}},'t':0,'e':[0,$.hide],'n':{'c':'shown'}},{'p':function(){return{'c':'editor '+($.g('shown')?'shown':''),'sc':1}},'t':0,'n':{'c':'shown'},'c':[{'p':{'c':'editor_close'},'t':0,'e':[0,$.close]},{'p':{'c':'editor_title'},'t':0,'c':__[75]},{'p':function(){return{'p':{'options':__V[4],'active':$.g('withAutoComplete')}}},'cmp':'AutoComplete','e':['enter',$.onEnter],'n':{'active':'withAutoComplete'}}]}]};
p.getInitials=function(){return{'followers':{'text':this.onChangeText}}};
__G.set(c=__F(),'KeywordTagEditor');
p=c.prototype;
__G.set(c=__F(),'Form');
p=c.prototype;
p.onSubmit=function(){if(this.isValid()){this.send()}};
p.send=function(){var action=this.get('action');var method=this.get('method');if(action){Loader.doAction(method||'POST',action,this.getControlsData(),this.handleResponse,this)}};
p.isValid=function(){return true};
p.handleResponse=function(data){if(isString(data)){try{data=JSON.parse(data)}catch(e){log('incorrect form response','handleResponse',this,{'data':data})}}
if(isObject(data)&&!data['error']){this.onSuccess(data)}else{this.onFailure(data)}};
p.onSuccess=function(data){};
p.onFailure=function(data){};
p.getTemplateMain=function(_,$){return{'p':{'c':'form '+(_['className']?_['className']:''),'sc':1},'t':0,'c':[{'tmp':$.getTemplateContent},{'h':function(control){return{'p':{'p':control},'cmp':'FormField'}},'p':_['controls']},_['submit']?{'p':{'p':_['submit']},'cmp':'Submit','e':[23,$.onSubmit]}:'']}};
__G.set(c=__F(),'PopupMenu');
p=c.prototype;
p.onRendered=function(){this.button=this.getElement().parentNode;this.addListener(this.button,'click',this.onShowButtonClick)};
p.onClick=function(e){var target=e.getTarget('.popup-menu_button');if(!isNull(target)){var buttons=this.get('buttons');var idx=target.getData('index');var value=target.getData('value');if(isArray(buttons)&&isObject(buttons[idx])&&isFunction(buttons[idx]['handler'])){buttons[idx]['handler'].call(this,e);return}
this.handleClick(value,target)}};
p.onShowButtonClick=function(){this.onBeforeShow();this.show()};
p.show=function(){var innerElement=this.findElement('.popup-menu_inner-container');var rect=innerElement.getRect();var height=Math.min(rect.height,Objects.get(this.options,'maxHeight',400));this.getElement().css({maxHeight:height+'px',height:height+'px'});this.button.addClass('active');Popuper.watch(this)};
p.hide=function(){this.getElement().css({maxHeight:'0',height:'0'});this.button.removeClass('active')};
p.renderButtons=function(items){var buttons=[];var a=items,idx;for(idx=0;idx<a.length;idx++){var item=a[idx];buttons.push(this.getButtonData(item))}
this.set('buttons',buttons)};
p.getButtonData=function(item){return{'value':item['value'],'name':item['name']}};
p.handleClick=function(){};
p.onBeforeShow=function(){};
p.getTemplateMain=function(_,$){return{'p':{'c':'popup-menu_outer-container '+_['className'],'sc':1},'t':0,'c':{'p':{'c':'popup-menu_inner-container','st':_['maxHeight']?'max-height:'+_['maxHeight']+'px;':''},'t':0,'e':[0,$.onClick],'c':{'h':function(button,idx){return{'p':{'c':'popup-menu_button','_value':button['value'],'_index':idx},'t':0,'c':[button['name'],{'p':{'item':button},'tmp':$.getTemplateContent}]}},'n':'buttons','p':function(){return{'p':$.g('buttons')}}}}}};
__G.set(c=__F(),'PopupSelect');
p=c.prototype;
p.show=function(){this.set('shown',true);Popuper.watch(this)};
p.hide=function(){this.set('shown',false)};
p.getTemplateMain=function(_,$){return{'p':function(){return{'c':'app-popup-select'+($.g('shown')?' shown':''),'sc':1}},'t':0,'n':{'c':'shown'},'c':[{'p':{'c':'app-popup-select-title'},'t':0,'c':{'v':$.g('title'),'n':'title'}},{'p':{'p':{'options':_['options']}},'cmp':'Select','e':[14,$.d.b($,'change')],'nm':'option'}]}};
__G.set(c=__F(),'Recommendations');
p=c.prototype;
p.load=function(words){__C.get(3).doAction(this,'load',{'excepcions':words})};
p.onChangeItems=function(items){var itemsCount=items.length;this.set('itemsCount',itemsCount);this.dispatchEvent('change',itemsCount)};
p.getTemplateMain=function(_,$){return{'p':{'c':'recommendations ','sc':1},'t':0,'c':{'h':function(item){return{'p':{'c':'recommendations_item'},'t':0,'c':item['keyword']}},'n':'items','p':function(){return{'p':$.g('items')}}}}};
p.getInitials=function(){return{'controllers':[{'controller':__C.get(3)}],'followers':{'items':this.onChangeItems}}};
__G.set(c=__F(),'TabPanel');
p=c.prototype;
p.onRendered=function(){var tabs=this.get('tabs');this.tabWidth=this.get('tabWidth')||200;this.tabMargin=this.get('tabMargin')||4;if(isArray(tabs)){var a=tabs,idx;for(idx=0;idx<a.length;idx++){var tab=a[idx];this.activateTab(idx,!!tab['active'])}}
this.redraw()};
p.redraw=function(){this.hiddenTabs=[];var tabPanelWidth=this.getElement().getWidth();var controlWidth=this.getControlsWidth();var tabs=this.findElements('.tab-panel_tab');var totalWidth=0;var a=tabs,idx;for(idx=0;idx<a.length;idx++){var tab=a[idx];tab.toggleClass('first',idx==0);if(totalWidth+controlWidth+this.tabWidth+this.tabMargin>tabPanelWidth){tab.hide();this.hiddenTabs.push(idx)}else{tab.style.left=totalWidth+'px';totalWidth+=this.tabWidth+this.tabMargin}}
this.set('count',this.hiddenTabs.length)};
p.getControlsWidth=function(){var width=0;var restButton=this.findElement('.tab-rest');if(restButton)width+=restButton.getWidth()+this.tabMargin;var plusButton=this.findElement('.tab-plus');if(plusButton)width+=plusButton.getWidth()+this.tabMargin;return width};
p.onRestTabClick=function(){};
p.onTabClick=function(target){if(isNumeric(this.activeTab)){this.activateTab(this.activeTab,false)}
this.activateTab(target.getData('index'),true)};
p.activateTab=function(tabIndex,isShown){var contents=this.getElement().finds('.'+(this.get('containerClass')||'tab-content'));console.log(contents)
if(contents[tabIndex])contents[tabIndex].show(isShown);if(isShown){this.dispatchEvent('select',tabIndex);this.activeTab=tabIndex}
this.findElements('.content-tab')[tabIndex].toggleClass('active',isShown)};
p.getTemplateMain=function(_,$){return{'p':{'c':'tab-panel ','sc':1},'t':0,'c':[{'p':{'c':'tab-panel_tabs'},'t':0,'c':[{'h':function(tab,idx){return{'p':{'c':'tab-panel_tab content-tab','_index':idx},'t':0,'c':tab['title']}},'p':_['tabs']},isObject(_['rest'])?{'p':function(){return{'c':'tab-panel_tab tab-rest'+($.g('count')?' shown':'')}},'t':0,'n':{'c':'count'},'c':[_['rest']['title']||__[76],_['rest']['showCount']?[' (',{'v':$.g('count'),'n':'count'},')']:'']}:'']},{'p':{'c':'tab-panel_contents'},'t':0,'c':_['children']}]}};
p.getInitials=function(){return{'events':{'click':{'tab-rest':this.onRestTabClick,'content-tab':this.onTabClick}}}};
__G.set(c=__F(),'Tabs');
p=c.prototype;
p.onSelect=function(target){var index=target.getData('index');this.set('activeTab',index);this.dispatchEvent('select',index)};
p.onRemove=function(target){var index=target.getParent().getData('index');this.dispatchEvent('remove',index)};
p.getTemplateMain=function(_,$){return{'p':{'c':'tabs ','sc':1},'t':0,'c':{'h':function(item,i){return{'p':function(){return{'c':'tabs_item'+($.g('activeTab')==i?' active':''),'_index':i}},'t':0,'n':{'c':'activeTab'},'c':[item,{'p':{'c':'tabs_remove'},'t':0}]}},'n':'items','p':function(){return{'p':$.g('items')}}}}};
p.getInitials=function(){return{'events':{'click':{'tabs_item':this.onSelect,'tabs_remove':this.onRemove}}}};
__G.set(c=__F(),'TooltipPopup');
p=c.prototype;
p.correctAndSetText=function(text,changedProps){var corrector=changedProps['corrector'];if(corrector=='list'){var textParts=text.split('|');if(textParts[1]){var temp=[];for(var i=0;i<textParts.length;i++){textPart=textParts[i].split('^');textPart=textPart[1]||textPart[0];if(textPart.charAt(0)==__[3])textPart=__[2];temp.push(textPart)}
text=temp.removeDuplicates()}}
return text};
p.getTemplateMain=function(_,$){return{'p':function(){return{'c':'@'+($.g('className')?' '+$.g('className'):'')+($.g('shown')?' shown':''),'st':'left:'+$.g('left')+'px;top:'+$.g('top')+'px;','sc':1}},'t':0,'n':{'c':['className','shown'],'st':['left','top']},'c':[{'p':{'c':'tooltip-popup_caption'},'t':0,'c':{'v':$.g('caption'),'n':'caption'}},{'p':{'c':'tooltip-popup_text'},'t':0,'c':{'c':{'h':function(item){return{'p':{'c':'tooltip-popup_item'},'t':0,'c':item}},'n':'text','p':function(){return{'p':$.g('text')}}},'sw':$.g('corrector'),'cs':'list','d':{'v':$.g('text'),'n':'text'}}}]}};
p.getInitials=function(){return{'correctors':{'text':this.correctAndSetText},'props':{control:'Input'}}};
__G.set(c=__F(),'Error401');
p=c.prototype;
p.onRendered=function(){};
p.getTemplateMain=function(_,$){return{'p':{'c':'app-auth-form-container'},'t':0,'c':{'cmp':'AuthForm'}}};
__G.set(c=__F(),'Error404');
p=c.prototype;
p.onRendered=function(){};
p.getTemplateMain=function(_,$){return{'p':{'c':'app-404-container'},'t':0,'c':[{'p':{'c':'app-404-title'},'t':0,'c':'404'},{'p':{'c':'app-404-text'},'t':0,'c':__T[5]}]}};
__G.set(c=__F(),'Favorite');
p=c.prototype;
p.onRendered=function(){};
p.getTemplateMain=function(_,$){return{'p':{'c':'view-content'},'t':0}};
__G.set(c=__F(),'FilterSubscription');
p=c.prototype;
p.onLoaded=function(filters){this.set('filters',filters);this.set({'total':this.getTotalCount(),'subscribed':this.getSubscribedCount()})};
p.getTotalCount=function(){return Decliner.getCount('filter',this.get('filters'))};
p.getSubscribedCount=function(){var subscribedCount=0;this.each('filters',function(filter){if(filter['isSubs']==1)subscribedCount++});return Decliner.getCount('subscr',subscribedCount)};
p.onFreqChange=function(e){};
p.onSubscribeButtonClick=function(target,e){var filterId=e.getTargetData('.filter-subscription_filter-row','filterId');if(filterId){__C.get(1).doAction(this,'subscribe',{'filterId':filterId,'value':target.hasClass('subscribed')?'0':'1'})}};
p.getTemplateMain=function(_,$){return{'p':{'c':'filter-subscription ','sc':1},'t':0,'c':[{'p':{'c':'filter-subscription_title'},'t':0,'c':[__[110],' ',{'t':20,'c':__[111]}]},{'p':{'c':'filter-subscription_head'},'t':0,'c':[{'p':{'c':'filter-subscription_head-total'},'t':0,'c':{'v':$.g('total'),'n':'total'}},{'p':{'c':'filter-subscription_head-subscribed'},'t':0,'c':{'v':$.g('subscribed'),'n':'subscribed'}}]},{'p':{'c':'filter-subscription_table','cp':'0px','cs':'0px'},'t':2,'c':[{'t':4,'c':{'t':5,'c':[{'t':7,'c':__[115]},{'t':7,'c':__[116]},{'t':7,'c':__[117]},{'t':7,'c':__T[6]}]}},{'t':3,'c':{'h':function(filter){return{'p':{'_filterid':filter.filterId,'c':'filter-subscription_filter-row'},'t':5,'c':[{'t':6,'c':{'p':{'c':'filter-subscription_filter'},'t':1,'c':filter.header}},{'t':6,'c':{'p':{'p':{'options':__V[11],'value':filter.freqSubs}},'cmp':'Select','e':[14,$.onFreqChange],'nm':'freqSubs'}},{'t':6,'c':{'p':{'c':'standart-button filter-subscription_button '+(filter.isSubs==1?'green-button subscribed':'white-button')},'t':0}},{'t':6,'c':__T[7]}]}},'n':'filters','p':function(){return{'p':$.g('filters')}}}}]}]}};
p.getInitials=function(){return{'loader':{'controller':__C.get(1)},'events':{'click':{'filter-subscription_button':this.onSubscribeButtonClick}}}};
__G.set(c=__F(),'FilterSubscriptionOptions');
p=c.prototype;
p.onCheckboxChange=function(e){var params={};params[e['name']]=e['intChecked'];__C.get(4).doAction(this,'save',params)};
p.getTemplateMain=function(_,$){return{'p':{'c':'filter-subscription-options ','sc':1},'t':0,'c':[{'p':{'c':'filter-subscription-options_option'},'t':0,'c':[{'p':{'name':'tenderOfFavorite','checked':$.g('opts').tenderOfFavorite},'tmp':2},' ',__[106],' ',{'t':20,'c':__[107]}]},{'p':{'c':'filter-subscription-options_option'},'t':0,'c':[{'p':{'name':'protocolOfFavorite','checked':$.g('opts').protocolOfFavorite},'tmp':2},' ',__[106],' ',{'t':20,'c':__[108]}]},{'p':{'c':'filter-subscription-options_option'},'t':0,'c':[{'p':{'name':'protocolOfFilter','checked':$.g('opts').protocolOfFilter},'tmp':2},' ',__[106],' ',{'t':20,'c':__[109]}]}]}};
p.getInitials=function(){return{'loader':{'controller':__C.get(4)},'helpers':[{'helper':'CheckboxHandler','options':{'callback':this.onCheckboxChange,'labelClass':'filter-subscription-options_option'}}]}};
__G.set(c=__F(),'Main');
p=c.prototype;
p.onRendered=function(){this.onResize()};
p.onResize=function(){var element=this.findElement('.mainpage-content');element.setHeight('');var height=element.getHeight();var bodyHeight=document.body.getHeight();if(bodyHeight-100-height>0){element.setHeight(bodyHeight-100)}};
p.getTemplateMain=function(_,$){return{'p':{'c':'main view-content','sc':1},'t':0,'c':{'p':{'c':'main_table','cp':'0px','cs':'0px'},'t':2,'c':{'t':5,'c':[{'p':{'c':'main_left-column'},'t':6,'c':{'p':{'c':'main_left-column-area'},'t':0,'c':[{'p':{'c':'main_left-column-title'},'t':0,'c':__[77]},{'cmp':'UserInfo'},{'p':{'c':'main_leftcolumn-title bold'},'t':0,'c':[__[94],{'p':{'props':__V[14]},'tmp':3}]},{'cmp':'FavoritesCalendar'}]}},{'p':{'c':'main_content-column'},'t':6,'c':{'p':{'p':__V[12]},'cmp':'TabPanel','c':[{'p':{'c':'tab-content'},'t':0,'c':{'p':{'p':__V[13]},'cmp':'FilterStatistics'}},{'p':{'c':'tab-content'},'t':0,'c':[{'cmp':'FilterSubscriptionOptions'},{'cmp':'FilterSubscription'}]},{'p':{'c':'tab-content'},'t':0,'c':__T[8]}]}}]}}}};
p.getInitials=function(){return{'helpers':[{'helper':'ResizeHandler','options':{'callback':this.onResize}}]}};
__G.set(c=__F(),'UserInfo');
p=c.prototype;
p.onLoaded=function(data){if(!User.hasFullAccess()){data['prolongButtonText']=__[88]}else if(data['needToProlong']){data['prolongButtonText']=__[89]}
this.set(data)};
p.onOrderCallButtonClick=function(){Dialoger.show('OrderCall')};
p.getTemplateMain=function(_,$){return{'p':{'c':'user-info ','sc':1},'t':0,'c':[{'p':{'cp':'0px','cs':'0px'},'t':2,'c':[{'t':5,'c':[{'t':6,'c':[__[78],':']},{'t':6,'c':{'v':$.g('userName'),'n':'userName'}}]},{'t':5,'c':[{'t':6,'c':[__[79],':']},{'t':6,'c':{'v':$.g('companyName'),'n':'companyName'}}]},{'t':5,'c':[{'t':6,'c':[__[80],':']},{'t':6,'c':{'v':$.g('userEmail'),'n':'userEmail'}}]},{'t':5,'c':[{'t':6,'c':[__[81],':']},{'p':{'c':' bold'},'t':6,'c':{'v':$.g('typeAccess'),'n':'typeAccess'}}]},{'t':5,'c':[{'t':6,'c':[__[82],':']},{'t':6,'c':{'v':$.g('beginAccessDate'),'n':'beginAccessDate'}}]},{'t':5,'c':[{'t':6,'c':[__[83],':']},{'p':function(){return{'c':$.g('needToProlong')?'red':''}},'t':6,'n':{'c':'needToProlong'},'c':{'v':$.g('endAccessDate'),'n':'endAccessDate'}}]}]},{'c':function(){return{'p':{'h':__[87],'c':'standart-button access red-button','tr':'_blank'},'t':12,'c':{'v':$.g('prolongButtonText'),'n':'prolongButtonText'}}},'i':function(){return $.g('prolongButtonText')},'n':['prolongButtonText']},{'p':{'h':__[84],'c':'standart-button tariffs white-button','tr':'_blank'},'t':12,'c':__[85]},{'p':{'c':'user-info_leftcolumn-title bold'},'t':0,'c':__[86]},{'p':{'c':'user-info_manager-info'},'t':0,'c':[{'p':{'c':'user-info_manager-name'},'t':0,'c':{'v':$.g('managerName'),'n':'managerName'}},{'p':{'c':'user-info_manager-phone'},'t':0,'c':[{'p':{'c':'user-info_free-call'},'t':0,'c':[' ',{'p':{'c':'user-info_manager-large-phone'},'t':0,'c':__[90]},{'p':{'c':'user-info_manager-free'},'t':0,'c':__[91]}]},{'v':$.g('managerPhone'),'n':'managerPhone'},__T[9],{'t':48,'c':__[92]}]},{'p':{'c':'user-info_manager-email'},'t':0,'c':{'v':$.g('managerEmail'),'n':'managerEmail'}}]},{'p':{'c':'standart-button green-button'},'t':0,'e':[0,$.onOrderCallButtonClick],'c':__[93]}]}};
p.getInitials=function(){return{'loader':{'controller':__C.get(5),'async':true}}};
__G.set(c=__F(),'Search');
p=c.prototype;
p.onRendered=function(){this.openInformer()};
p.openInformer=function(){var datatable=this.getChild('datatable')};
p.openFilter=function(filterId){};
p.onFormExpand=function(){this.toggle('expanded')};
p.getTemplateMain=function(_,$){return{'p':function(){return{'c':'view-content'+($.g('expanded')?' form-expanded':''),'sc':1}},'t':0,'n':{'c':'expanded'},'c':[{'p':{'i':'form'},'cmp':'TenderSearchForm','e':['expand',$.onFormExpand]},{'p':{'i':'datatable'},'cmp':'TenderDataTable'}]}};
p.getInitials=function(){return{'props':{'expanded':true}}};
__G.set(c=__F(),'FormField');
p=c.prototype;
p.getTemplateMain=function(_,$){return{'p':{'c':'input-container'+(_['class']?' '+_['class']:''),'sc':1},'t':0,'c':[_['caption']?{'p':{'c':'input-caption'},'t':0,'c':_['caption']}:'',{'p':{'p':_['controlProps']},'cmp':_['controlClass'],'nm':_['controlProps']['name']}]}};
__G.set(c=__F(),'Submit');
p=c.prototype;
p.getTemplateMain=function(_,$){return{'p':{'c':'app-submit-container'},'t':0,'c':{'p':function(){return{'c':$.g('class')}},'t':0,'e':[0,$.d.b($,'submit')],'n':{'c':'class'},'c':{'v':$.g('value'),'n':'value'}}}};
__G.set(function(_,$){return _['children']},'i_0');
__G.set(function(_,$){return {'p':{'c':'app-unavailable-info '+(_['tariff']?'unavailable':'auth'),'st':_['width']?'width:'+_['width']:''},'t':0}},'i_1');
__G.set(function(_,$){return {'p':{'c':'checkbox '+(_['checked']?'checked':''),'_name':_['name'],'_value':_['value']},'t':0}},'i_2');
__G.set(function(_,$){return {'p':{'c':'tooltiped tooltip'+(_['className']?' '+_['className']:''),'_text':_['text'],'_key':_['key'],'_class':_['class'],'_caption':_['caption']},'t':0}},'i_3');
function getFzName(type){var types=Dictionary.get('fztypes');if(type>4400)return types['44'];if(type<128)return types['94'];if(type==256)return types['223'];if(type==128)return types['com'];return''}
__A.inherits(['Component',['Application','View','Control','Menu','DataTable','DataTableFragmets','DataTableRow','FilterStatistics','SearchForm','SearchFormButton','SearchFormPanel','SearchFormFilters','AutoComplete','Calendar','Dialog','Editor','Form','PopupMenu','PopupSelect','Recommendations','TabPanel','Tabs','TooltipPopup','FilterSubscription','FilterSubscriptionOptions','UserInfo','FormField','Submit'],'DataTableRow',['DataTableStandartRow'],'DataTable',['TenderDataTable'],'SearchFormButton',['SearchFormPanelButton'],'Control',['Keywords','KeywordsControl','Checkbox','Input','Select','Tags','Textarea'],'SearchFormPanelButton',['KeywordsButton'],'SearchFormPanel',['KeywordsPanel'],'SearchForm',['TenderSearchForm'],'Controller',['Favorites','Filters','FiltersStat','RecommendationsLoader','Subscription','UserInfoLoader'],'Application',['App'],'Menu',['TopMenu'],'AutoComplete',['KeywordsAutoComplete'],'Calendar',['FavoritesCalendar'],'Editor',['KeywordTagEditor'],'View',['Error401','Error404','Favorite','Main','Search'],'PopupMenu',['SearchFormCreateFilterMenu','SearchFormFilterMenu'],'Tags',['KeywordTags'],'Dialog',['CalendarFavorites','FilterEdit','OrderCall','Support'],'Form',['AuthForm','OrderCallForm'],'OrderCallForm',['SupportForm'],'KeywordTags',['ContainKeywordTags','ExcludeKeywordTags']]);
if (isObject(__DT['user'])) User.setData(__DT['user']);
if (isObject(__DT['dictionary'])) Dictionary.setData(Router.getCurrentRoute()['name'], __DT['dictionary']);
var App=__G.get('App',1);
__A.initiate.call(App);
App.run();
};
var Router=__G.get('Router',1);
var User=__G.get('User',1);
Loader.get(__LU, {'route': Router.getCurrentRoute()['name']},__CB);
})();
});