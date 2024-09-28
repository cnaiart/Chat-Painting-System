import { Component } from 'vue'

const widgets: Record<string, any> = import.meta.glob('./*.vue', {
    eager: true
})
console.log(widgets)

const exportWidgets: Record<string, Component> = {}
Object.keys(widgets).forEach((key) => {
    const widgetName = key.replace(/^\.\/([\w-]+).*/gi, '$1')
    exportWidgets[widgetName] = widgets[key]?.default
})
export default exportWidgets
