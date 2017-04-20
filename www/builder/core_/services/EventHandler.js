_G_.set((c=function(){
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