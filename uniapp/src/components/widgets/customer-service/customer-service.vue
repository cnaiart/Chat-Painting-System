<template>
    <view
        class="customer-service bg-white flex flex-col justify-center items-center mx-[36rpx] mt-[20rpx] rounded-lg px-[110rpx] pt-[100rpx] pb-[160rpx]"
    >
        <u-image width="280" height="280" :src="getImageUrl(content.qrcode)" />
        <view
            v-if="content.title && +content.title_status"
            class="text-lg mt-[14rpx] font-medium"
        >
            {{ content.title }}
        </view>
        <view
            v-if="content.time && +content.time_status"
            class="text-content mt-[40rpx]"
        >
            服务时间：{{ content.time }}
        </view>
        <view
            v-if="content.mobile && +content.mobile_status"
            class="text-content mt-[14rpx] flex flex-wrap"
        >
            客服电话：{{ content.mobile }}
            <!-- #ifdef H5 -->
            <a
                class="ml-[10rpx] phone text-muted underline"
                :href="'tel:' + content.mobile"
            >
                拨打
            </a>
            <!-- #endif -->
            <!-- #ifndef H5 -->
            <view
                class="ml-[10rpx] phone text-muted underline"
                @click="handleCall"
            >
                拨打
            </view>
            <!-- #endif -->
        </view>
        <view class="mt-[100rpx] w-full">
            <u-button
                type="primary"
                shape="circle"
                @click="onSaveQrcode(getImageUrl(content.qrcode))"
            >
                保存二维码图片
            </u-button>
        </view>
    </view>
</template>
<script lang="ts" setup>
import { useAppStore } from '@/stores/app'
import { saveImageToPhotosAlbum } from '@/utils/file'
import { client } from '@/utils/client'
import cache from '@/utils/cache'
import { usePermissionsStore } from '@/stores/androidPermissions'

const props = defineProps({
    content: {
        type: Object,
        default: () => ({})
    },
    styles: {
        type: Object,
        default: () => ({})
    }
})

const { getImageUrl } = useAppStore()

const handleCall = () => {
    uni.makePhoneCall({
        phoneNumber: String(props.content.mobile)
    })
}

const onSaveQrcode = async (qrcode: string) => {
    // #ifdef APP-PLUS
    if (client == 6) {
        // const res = await uni.showModal({
        //     title: '温馨提示',
        //     content:
        //         '是否允许应用使用(存储权限)，以便我们将联系方式二维码保存到您的手机上'
        // })
        // if (res.cancel) return
        // else cache.set('customer-service', 1)
        const { requestPermissions } = usePermissionsStore()
        const result = await requestPermissions('WRITE_EXTERNAL_STORAGE')
        if (result !== 1) return
    }
    // #endif
    saveImageToPhotosAlbum(qrcode)
}
</script>

<style lang="scss"></style>
