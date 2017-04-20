_G_.set((c=function(){
if(!this||this==window){
p=c.prototype;
p.onRenderComplete=function(){var router=_G_.get('Router');if(router.hasMenu(this)){this.onNavigate(router.getCurrentRouteName())}};
p.onNavigate=function(viewName){if(this.rendered){if(isElement(this.activeButton)){this.setButtonActive(this.activeButton,false)}var button=this.getButton(viewName);if(isElement(button)){this.setButtonActive(button,true)}}};
p.getButton=function(viewName){return this.findElement('a[role="'+viewName+'"]')};
p.setButtonActive=function(button,isActive){var activeClassName=this.activeButtonClass||'->> active';button.toggleClass(activeClassName,isActive);if(isActive){this.activeButton=button}};
p.disposeInternal=function(){this.activeButton=null};
return c;
}
})(),'Menu');