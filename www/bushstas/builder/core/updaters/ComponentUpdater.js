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