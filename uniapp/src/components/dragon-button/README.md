#拖拽按钮

| 参数 | 说明 | 类型 | 默认值 |
| :---- | :---- | :---- |
| size | 按钮大小 | Number | 200 |
| zIndex | 按钮层级 | Number | 999 |
| xEdge | x轴边界限制 | Number | 0 |
| yEdge | y轴边界限制 | Number | 50 |
| autoDocking | 松手自动就近停靠 | Boolean | true |


##使用方式
	
```vue
<template>
	<view class="content">
		<DragButton>
			<view class="btn">拖动</view>
		</DragButton>
	</view>
</template>
```
引入组件

```js
import DragButton from '@/components/dragon-dragButton/index.vue'
export default {
	components: { DragButton }
};
```
