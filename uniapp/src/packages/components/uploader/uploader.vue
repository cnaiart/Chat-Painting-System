<template>
    <view class="flex uploader-container wrap">
        <!--    上传图片按钮    -->
        <view
            v-if="maxUpload > urls.length"
            class="upload"
            hover-class="slot-btn__hover"
            hover-stay-time="150"
            @click="onUploader"
            :style="uploadViewStyle"
        >
            <u-icon size="48" color="#dcdee0" name="camera" />
            <view class="xs m-t-10">
                {{ urls.length >= 1 ? urls.length + '/' + maxUpload : tips }}
            </view>
        </view>

        <!--    已上传的图片列表    -->
        <view
            class="list-item preview-wrap"
            v-for="(item, index) in urls"
            :key="index"
            :style="{
                width: addUnit(previewSize),
                height: addUnit(previewSize)
            }"
        >
            <!--      删除按钮     -->
            <view
                v-if="deletable"
                class="delete-icon"
                @tap.stop="deleteItem(index)"
                :style="{
                    background: '#fa3534'
                }"
            >
                <u-icon
                    class="u-icon"
                    name="close"
                    size="20"
                    color="#FFFFFF"
                ></u-icon>
            </view>
            <!--      图片     -->
            <image
                @tap.stop="doPreviewImage(item, index)"
                class="w-full h-full"
                :src="item"
                :mode="imageFit"
            ></image>
        </view>
    </view>
</template>

<script lang="ts" setup>
import { computed } from '@vue/reactivity'
import { uploadImage } from '@/api/app'
import { addUnit } from '@/utils/util'

const emit = defineEmits(['update:modelValue', 'change'])
const props = withDefaults(
    defineProps<{
        modelValue: any
        uploadViewStyle: any // upload上传视图样式
        maxUpload?: number // 限制上传文件数量
        previewSize?: string // upload显示的大小
        deletable?: boolean // 是否可删除
        tips?: string // 提示
        imageFit?: string // 图片填充
        maxSize?: number // 单个文件最大上传大小
    }>(),
    {
        modelValue: [],
        uploadViewStyle: {},
        maxUpload: 1,
        previewSize: '160',
        deletable: false,
        tips: '上传图片',
        imageFit: 'aspectFill',
        maxSize: 20
    }
)

const urls = computed({
    get: () => {
        return props.modelValue
    },
    set: (val) => {
        emit('update:modelValue', val)
    }
})

/**
 * @return { void }
 * @description 上传，不管成不成功都返回数据｜提示
 */
const onUploader = async () => {
    try {
        //@ts-ignore
        const { tempFiles } = await uni.chooseImage({
            count: props.maxUpload,
            sourceType: ['album', 'camera']
        })
        // 上传
        for (const { path, size } of tempFiles) {
            if (size >= props.maxSize * 1024 * 1024) {
                uni.$u.toast('选择的文件超出大小！')
                return new Error('文件超出大小～')
            }
            const res: any = await uploadImage(path)
            urls.value.push(res.uri)
            emit('change', urls.value)
        }
    } catch (error) {
        console.log(error)
        // uni.$u.toast('上传失败')
    }
}

/**
 * @param { number } index
 * @description 删除一个图片
 */
const deleteItem = (index: number | any) => {
    urls.value.splice(index, 1)
    emit('update:modelValue', urls.value)
    emit('change', urls.value)
}

/**
 * @param { string } url
 * @param { number } index
 * @description 查看图片
 */
const doPreviewImage = (url: string, index: number) => {
    uni.previewImage({
        urls: urls.value,
        current: index
    })
}
</script>

<style lang="scss" scoped>
.uploader-container {
    display: flex;
    flex-wrap: wrap;
    align-items: center;

    .upload {
        position: relative;
        width: 160rpx;
        height: 160rpx;
        padding-top: 30rpx;
        text-align: center;
        margin: 11rpx;
        border: 2px dashed #e5e5e5;
        background-color: #ffffff;

        > view {
            color: #bbb;
        }
    }

    .list-item {
        width: 200rpx;
        height: 200rpx;
        overflow: hidden;
        margin: 10rpx;
        background: rgb(244, 245, 246);
        position: relative;
        border-radius: 10rpx;
        /* #ifndef APP-NVUE */
        display: flex;
        /* #endif */
        align-items: center;
        justify-content: center;

        .delete-icon {
            position: absolute;
            top: 6rpx;
            right: 6rpx;
            z-index: 10;
            background-color: $u-type-error;
            border-radius: 100rpx;
            width: 36rpx;
            height: 36rpx;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    }

    .preview-wrap {
        border: 1px solid rgb(235, 236, 238);
    }
}
</style>
