(()=>{(()=>{"use strict";var p={};(()=>{let e=0;const t=20,s=()=>{const n=()=>{var o;if(!(e>=t))if(e+=1,!((o=window==null?void 0:window.prestashop)!=null&&o.component))setTimeout(n,100);else{const d=new Event("PrestashopComponentsReady");document.dispatchEvent(d)}};return document.addEventListener("DOMContentLoaded",n),{onComponentsReady:o=>{document.addEventListener("PrestashopComponentsReady",o)}}},{onComponentsReady:i}=s();i(()=>{window.prestashop.component.initComponents(["Grid"]);const n=new window.prestashop.component.Grid("is_mainmenu_list");n.addExtension(new window.prestashop.component.GridExtensions.AsyncToggleColumnExtension),n.addExtension(new window.prestashop.component.GridExtensions.SortingExtension),n.addExtension(new window.prestashop.component.GridExtensions.PositionExtension(n)),n.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension),n.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension)})})()})();})();

//# sourceMappingURL=grid.js.map