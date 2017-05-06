var __C;__G.set(__C=new(function(){
this.get=function(id){if(isString(__CT[id])){__CT[id]=__G.get(__CT[id])}if(isFunction(__CT[id])){__CT[id]=new __CT[id]();__A.initiate.call(__CT[id])}return __CT[id]};
this.load=function(ids){var ctr;if(!isArray(ids))ids=[ids];for(var i=0;i<ids.length;i++){ctr=this.get(ids[i]);if(isController(ctr)){ctr.doAction(null,'load')}}};
})(),'__C');