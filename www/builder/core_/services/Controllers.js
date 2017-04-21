var __C;_G_.set(__C=new(function(){
this.get=function(id){if(isString(__CT[id])){__CT[id]=_G_.get(__CT[id])}if(isFunction(__CT[id])){__CT[id]=new __CT[id]();_C_.initiate.call(__CT[id])}return __CT[id]};
this.load=function(ids){var ctr;if(!isArray(ids))ids=[ids];for(var i=0;i<ids.length;i++){ctr=this.get(ids[i]);if(isController(ctr)){ctr.doAction(null,'load')}}};
})(),'Controllers');