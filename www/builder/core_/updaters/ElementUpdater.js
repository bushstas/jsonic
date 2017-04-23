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