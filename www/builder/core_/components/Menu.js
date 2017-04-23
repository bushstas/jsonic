__G.set((c=function(){
if(!this||this==window){
p=c.prototype;
p.onRenderComplete=function(){if(Router.hasMenu(this)){this.onNavigate(Router.getCurrentRouteName())}};
p.onNavigate=function(viewName){if(this.rendered){if(isElement(this.activeButton)){this.setButtonActive(this.activeButton,false)}var button=this.getButton(viewName);if(isElement(button)){this.setButtonActive(button,true)}}};
p.getButton=function(viewName){return this.findElement('a[role="'+viewName+'"]')};
p.setButtonActive=function(button,isActive){var activeClassName=this.activeButtonClass||'->> active';button.toggleClass(activeClassName,isActive);if(isActive){this.activeButton=button}};
p.disposeInternal=function(){this.activeButton=null};
return c;
}
})(),'Menu');