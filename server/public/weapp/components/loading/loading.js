"use strict";const e=require("../../common/vendor.js"),a=e.defineComponent({__name:"loading",props:{size:{default:"22rpx"},color:{default:"#ffffff"},alpha:{default:.3}},setup(a){const o=a;e.useCssVars((o=>({"92c09dac":a.color,de6d6af4:e.unref(s),ba5aed4c:a.size})));const s=e.computed((()=>e.color(o.color).alpha(o.alpha).rgbaString()||"#fff"));return(a,o)=>({a:e.s(a.__cssVars())})}}),o=e._export_sfc(a,[["__scopeId","data-v-a736803e"]]);wx.createComponent(o);
