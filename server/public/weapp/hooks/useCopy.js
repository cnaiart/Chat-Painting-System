"use strict";const t=require("../common/vendor.js");exports.useCopy=function(){return{copy:e=>{try{t.index.setClipboardData({data:String(e)})}catch(o){t.index.$u.toast(o)}}}};
