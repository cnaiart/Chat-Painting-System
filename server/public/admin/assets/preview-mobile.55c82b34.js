import{E as _}from"./element-plus.38726bbd.js";import{e as s}from"./index.9513597e.js";import{d as f,o as r,c as m,V as v,M as b,W as y,aa as h,Q as l,a as k,I as C,L as V,R as x,u as n,al as B,am as z}from"./@vue.581f5ce0.js";import{_ as D}from"./vue-drag-resize.3665149e.js";import"./lodash-es.b552e321.js";import"./async-validator.fb49d0f5.js";import"./@vueuse.788ba4d8.js";import"./@element-plus.048d13fe.js";import"./dayjs.3bbbdfdd.js";import"./axios.ef5f3479.js";import"./@ctrl.82a509e0.js";import"./@popperjs.36402333.js";import"./escape-html.e5dfadb9.js";import"./normalize-wheel-es.8aeb3683.js";import"./attr.vue_vue_type_script_setup_true_lang.6518d4ca.js";import"./index.ca229a30.js";import"./index.0aa35fa2.js";import"./lodash.ccf63c29.js";import"./vue-router.f90229e4.js";import"./pinia.8de84ecb.js";import"./vue-demi.ebc8116b.js";import"./css-color-function.7bd56a8d.js";import"./color.4d72ae60.js";import"./clone.f3914b59.js";import"./color-convert.755d189f.js";import"./color-name.e7a4e1d3.js";import"./color-string.e356f5de.js";import"./balanced-match.d2a36341.js";import"./debug.e3c4d4cf.js";import"./ms.a9ae1d6d.js";import"./nprogress.db25c43d.js";import"./vue-clipboard3.37da43ca.js";import"./clipboard.51333a27.js";import"./echarts.8d7a50ae.js";import"./zrender.1084fa23.js";import"./tslib.60310f1a.js";import"./highlight.js.4ebdf9a4.js";import"./@highlightjs.18a086c3.js";import"./picker.08214076.js";import"./index.1c6c033d.js";import"./picker.a9db97c4.js";import"./index.8a8e47a8.js";import"./index.vue_vue_type_script_setup_true_lang.c6066b25.js";import"./index.850f9b25.js";import"./index.vue_vue_type_script_setup_true_lang.8d1a4714.js";import"./usePaging.2d3fb421.js";import"./vue3-video-play.35f34caf.js";import"./vuedraggable.9c55756f.js";import"./vue.56b77d04.js";import"./sortablejs.0eba38f1.js";import"./content.4b3c2676.js";import"./decoration-img.a9b780dd.js";import"./attr.vue_vue_type_script_setup_true_lang.6a921729.js";import"./content.vue_vue_type_script_setup_true_lang.421d4e5d.js";import"./attr.vue_vue_type_script_setup_true_lang.946438f6.js";import"./content.15b3c2f1.js";import"./attr.02a4bbc5.js";import"./index.vue_vue_type_script_setup_true_lang.dd8cb852.js";import"./content.822e5904.js";import"./code.7d55339d.js";import"./attr.vue_vue_type_script_setup_true_lang.4284cdde.js";import"./content.b76f6761.js";import"./attr.vue_vue_type_script_setup_true_lang.249f8253.js";import"./content.d690fed6.js";import"./attr.vue_vue_type_script_setup_true_lang.8fe8cd2a.js";import"./content.vue_vue_type_script_setup_true_lang.2f48c337.js";import"./attr.e4708602.js";import"./content.79606ab0.js";import"./attr.vue_vue_type_script_setup_true_lang.b30bd6a3.js";import"./content.e1022a51.js";import"./attr.vue_vue_type_script_setup_true_lang.08dbaf3c.js";import"./add-nav.vue_vue_type_script_setup_true_lang.7e1f64a0.js";import"./content.b46f38b1.js";import"./attr.vue_vue_type_script_setup_true_lang.c74c056a.js";import"./content.vue_vue_type_script_setup_true_lang.ed1dd526.js";import"./attr.vue_vue_type_script_setup_true_lang.e6760fab.js";import"./content.8e01298f.js";import"./decoration.bf2d4f6f.js";import"./attr.vue_vue_type_script_setup_true_lang.b14e41ef.js";import"./content.a7d9a624.js";import"./attr.vue_vue_type_script_setup_true_lang.da46119e.js";import"./content.ef7e9d6e.js";import"./attr.vue_vue_type_script_setup_true_lang.23104ba7.js";import"./content.vue_vue_type_script_setup_true_lang.285f5dbf.js";import"./attr.vue_vue_type_script_setup_true_lang.c11b4d04.js";import"./content.9fce05e8.js";import"./attr.vue_vue_type_script_setup_true_lang.6fb8287b.js";import"./content.c9ac0049.js";import"./attr.vue_vue_type_script_setup_true_lang.4993a7aa.js";import"./content.vue_vue_type_script_setup_true_lang.14fb2703.js";import"./attr.vue_vue_type_script_setup_true_lang.628d420d.js";import"./content.fef75901.js";import"./attr.vue_vue_type_script_setup_true_lang.0affa8e3.js";import"./content.vue_vue_type_script_setup_true_lang.e1a408dd.js";import"./attr.vue_vue_type_script_setup_true_lang.bec7456a.js";import"./content.8028bd46.js";import"./attr.vue_vue_type_script_setup_true_lang.5fd072c0.js";import"./content.ee2879bf.js";const E={class:"shadow mx-[30px] pages-preview"},N=["onClick"],P=f({__name:"preview-mobile",props:{pageData:{type:Array,default:()=>[]},modelValue:{type:Number,default:0}},emits:["update:modelValue"],setup(i,{emit:c}){const u=(t,e)=>{t.disabled||c("update:modelValue",e)};return(t,e)=>{const d=_;return r(),m("div",E,[v(d,null,{default:b(()=>[(r(!0),m(y,null,h(i.pageData,(o,a)=>(r(),m("div",{key:o.id,class:l(["relative",{"cursor-pointer":!(o!=null&&o.disabled)}]),onClick:p=>u(o,a)},[k("div",{class:l(["absolute w-full h-full z-[100] border-dashed",{select:a==i.modelValue,"border-[#dcdfe6] border-2":!(o!=null&&o.disabled)}])},null,2),C(t.$slots,"default",B(z(n(s))),()=>{var p;return[(r(),V(x((p=n(s)[o==null?void 0:o.name])==null?void 0:p.content),{content:o.content,styles:o.styles,key:o.id},null,8,["content","styles"]))]},!0)],10,N))),128))]),_:3})])}}});const Cr=D(P,[["__scopeId","data-v-c3502e92"]]);export{Cr as default};
