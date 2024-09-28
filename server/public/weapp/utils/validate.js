"use strict";const e=require("./cache.js");exports.isNewDay=(t,c)=>{const s=e.cache.get(c),r=(new Date).toDateString(),a=!s||s!==r;return a&&t&&e.cache.set(c,r),a};
