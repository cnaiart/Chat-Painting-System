<template>
    <u-popup
        v-model="show"
        mode="center"
        border-radius="14"
        duration="16"
        :customStyle="{
            background: 'none'
        }"
        @close="close"
    >
        <view
            class="p-[20rpx] w-[650rpx] flex flex-col justify-center items-center relative"
            @click="close"
        >
            <view
                class="w-full bg-white"
                style="border-radius: 12rpx 12rpx 0 0"
            >
                <image
                    v-show="!loading"
                    :src="content.thumbnail || content.image"
                    class="w-full rounded-[12rpx]"
                    mode="widthFix"
                    @load="loading = false"
                    @error="error = true"
                    @click.stop="previewImage"
                />
                <view
                    class="flex justify-center items-center h-[500rpx]"
                    v-if="loading && !error"
                >
                    <u-loading
                        mode="circle"
                        :color="$theme.primaryColor"
                        size="80"
                    ></u-loading>
                </view>
                <image
                    v-if="error"
                    class="w-full"
                    :src="ErrorIcon"
                    mode="widthFix"
                ></image>
            </view>
            <view
                class="bg-white w-full p-[20rpx]"
                style="border-radius: 0 0 12rpx 12rpx"
            >
                <view class="text-sm text-black">
                    {{ content?.original_prompts?.prompt }}
                </view>
                <view
                    class="flex items-center mt-[20rpx]"
                    v-if="appStore.getDrawSquareConfig.is_show_user"
                >
                    <u-avatar
                        :src="content.user_info.image"
                        size="40"
                    ></u-avatar>
                    <text class="text-muted text-xs ml-[20rpx]">
                        {{ content.user_info.name }}
                    </text>
                </view>
                <view
                    class="footer_btn flex justify-center mt-[30rpx] pb-[20rpx]"
                >
                    <view
                        class="mr-[30rpx]"
                        @click.stop="saveImage"
                    >
                        <image
                            class="w-[32rpx] h-[32rpx]"
                            src="@/packages/static/images/square/icon_save.png"
                        >
                        </image>
                        <text class="ml-[12rpx]">保存</text>
                    </view>
                    <view @click.stop="copy(content.prompts)">
                        <image
                            class="w-[32rpx] h-[32rpx]"
                            src="@/packages/static/images/square/icon_copy.png"
                        ></image>
                        <text class="ml-[12rpx]">复制提示词</text>
                    </view>
                </view>
            </view>
            <view class="absolute top-[40rpx] right-[40rpx]" @click="close">
                <u-icon name="close" size="30" color="#999999"></u-icon>
            </view>
        </view>
    </u-popup>
</template>

<script lang="ts" setup>
import { ref } from 'vue'
import ErrorIcon from '@/packages/static/images/square/error.png'
import { saveImageToPhotosAlbum } from '@/utils/file'
import { useAppStore } from '@/stores/app'
import { useCopy } from '@/hooks/useCopy'
const { copy } = useCopy()
const appStore = useAppStore()

const show = ref<boolean>(false)
const error = ref<boolean>(false)
const loading = ref<boolean>(true)
const content = ref({
    image: '',
    prompts: '',
    category_name: '',
    user_info: {
        image: '',
        name: ''
    }
})

const open = (row: any) => {
    show.value = true
    content.value = row
}

const close = () => {
    show.value = false
    loading.value = true
}

const saveImage = () => {
    //#ifdef H5
    uni.$u.toast('请长按图片保存')
    previewImage()
    //#endif
    //#ifndef H5
    saveImageToPhotosAlbum(content.value.image)
    //#endif
}

const previewImage = () => {
    uni.previewImage({
        urls: [content.value.image],
        current: 0,
        success: function () { close() },
        fail: function () {}
    })
}

defineExpose({ open })
</script>

<style lang="scss" scoped>
.footer_btn {
    view {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 0 30rpx;
        height: 64rpx;
        border-radius: 32px;
        border: 1px solid #d7dae2;
        font-size: 28rpx;
        color: #333333;
    }
}
</style>
