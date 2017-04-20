var __C;_G_.set(__C=new(function(){
this.get=function(id){if(isString(__CTR[id])){__CTR[id]=_G_.get(__CTR[id])}if(isFunction(__CTR[id])){__CTR[id]=new __CTR[id]();_C_.initiate.call(__CTR[id])}return __CTR[id]};
this.load=function(ids){var ctr;if(!isArray(ids))ids=[ids];for(var i=0;i<ids.length;i++){ctr=this.get(ids[i]);if(isController(ctr)){ctr.doAction(null,'load')}}};
})(),'Controllers');