<template>
    <view @click="test.getImages(imgValue!.image)">
        <!-- <button @click="test.getImages(originalImgPath)">获取图片</button> -->
        <text :path="imgValue!.image" :change:path="test.onChange"></text>
        <slot></slot>
    </view>
</template>

<script lang="ts">
import { ref, shallowRef, computed } from 'vue'
import { base64ToPath } from '@/utils/imgPath.js'
import { string } from 'mathjs'
export default {
    props: {
        imgPath: String,
        imgValue: Object
    },
    // emits:['update:modelValue'],
    setup(props, context) {
        const imgSrcList = ref<string[]>([])
        //接受renderjs事件
        const acceptDataFromRenderjs = (value: { content: string[] }) => {
            imgSrcList.value = []
            value.content.forEach(async (element: string) => {
                const res = await base64ToPath(element)
                const p = plus.io.convertLocalFileSystemURL(res)
                imgSrcList.value.push(`file://${p}`)
            })
            // context.emit('getImgList', imgSrcList.value)
            props.imgValue.image = imgSrcList.value
            setTimeout(() => {
                uni.hideLoading()
            }, 500)
        }
        const showLoading = () => {
            uni.showLoading({
                title: '切图中'
            })
        }

        return {
            acceptDataFromRenderjs,
            imgSrcList,
            showLoading
        }
    }
}
</script>

<script module="test" lang="renderjs">
export default {
	data() {
		return{
            path:''
		}
	},
	methods: {
        onChange(newValue,oldValue){
            this.path = newValue;
        },
		getImages(event,ownerInstance){
            try {
                ownerInstance.callMethod('showLoading')
			let PathList = []
			const image = new Image()
			image.setAttribute('src', this.path + '?' + new Date().getTime())
			image.setAttribute('crossOrigin', '')
			image.onload = async() => {
			    PathList = await this.split(image.width, image.height, image)
				ownerInstance.callMethod('acceptDataFromRenderjs', { content: PathList })
           	}
            } catch (error) {
                console.log(error);
            }

		},

		split(width,height,image){
			const canvas = document.createElement('canvas')
			canvas.width = width
			canvas.height = height
			const ctx = canvas.getContext('2d')
			ctx.drawImage(image, 0, 0, width, height)
			return this.getList(ctx)
		},

		getList(ctx){
			const width = ctx.canvas.width / 2
        	const height = ctx.canvas.height / 2
			const list = []
			for (let i = 0; i < 2; i++) {
            	const listFirst = []
            	for (let k = 0; k < 2; k++) {
                	const path = this.getPart(ctx, k * width, i * height, width, height)
					// console.log(path);
                	listFirst.push(path)
            	}
            	list.push(...listFirst)
        	}
			// console.log(list);
			return list
		},

		getPart(ctx,x,y,w,h){
			const canvas = document.createElement('canvas')
			canvas.width = w
			canvas.height = h
			const data = ctx.getImageData(x, y, w, h)
			const context = canvas.getContext('2d')
			context.putImageData(data, 0, 0)
			return canvas.toDataURL('image/png', 1)
		}
	},
}
</script>

<style></style>
