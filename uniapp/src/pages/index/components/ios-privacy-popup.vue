<template>
    <!-- modal:隐私授权弹窗-->
    <view v-if="show" class="modal-box" @tap.stop @touchmove.stop.prevent>
        <view class="modal-mask"></view>
        <view class="modal-dialog" @tap.stop>
            <view class="title">服务协议及隐私政策</view>
            <view class="content">
                为了更好的保障您的合法权益，请点击
                <text
                    class="text-[#101010]"
                    hover-class="hover"
                    @click="
                        openAgreement(
                            '/packages/pages/agreement/agreement?type=service'
                        )
                    "
                >
                    《用户协议》
                </text>
                <text
                    class="text-[#101010]"
                    hover-class="hover"
                    @click="
                        openAgreement(
                            '/packages/pages/agreement/agreement?type=privacy'
                        )
                    "
                >
                    《隐私政策》
                </text>
                并仔细阅读，如您同意全部内容，请点击同意开始使用我们的服务。
            </view>
            <view class="btn-box">
                <button
                    class="btn disagree"
                    hover-class="hover"
                    @click="disagreePrivacy"
                >
                    不同意
                </button>
                <button
                    class="btn bg-primary text-white"
                    hover-class="hover"
                    @click="agreePrivacy"
                >
                    同意并继续
                </button>
            </view>
        </view>
    </view>
</template>

<script lang="ts" setup>
import { nextTick, ref } from 'vue'
import cache from '@/utils/cache'
import { HAS_READ_PRIVACY } from '@/enums/constantEnums'

const emit = defineEmits<{
    (event: 'refresh'): void
}>()

const show = ref<boolean>(false)

// 打开新页面
const openAgreement = (url: string) => {
    uni.navigateTo({ url })
}

// 不同意直接退出App
const disagreePrivacy = () => {
    uni.$u.toast('同意隐私政策后继续使用')
}

// 同意
const agreePrivacy = async () => {
    emit('refresh')
    await nextTick()
    show.value = false
    cache.set(HAS_READ_PRIVACY, true)
}

if (!cache.get(HAS_READ_PRIVACY)) show.value = true
</script>

<style scoped>
.modal-box {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    height: 100vh;
    width: 100vw;
    z-index: 9999;
    background-color: #ffffff;
}

.modal-box .modal-mask {
    position: absolute;
    z-index: 9999;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
}

.modal-box .modal-dialog {
    box-sizing: border-box;
    position: absolute;
    z-index: 99999;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 80%;
    padding: 40rpx 20rpx;
    background: #ffffff;
    border-radius: 20rpx;
}

.modal-box .title {
    text-align: center;
    color: #333;
    font-weight: bold;
    font-size: 34rpx;
}

.modal-box .content {
    display: block;
    font-size: 28rpx;
    color: #666;
    margin-top: 20rpx;
    text-align: justify;
    line-height: 1.6;
    padding: 10rpx 20rpx;
}

.modal-box .btn-box {
    margin-top: 30rpx;
    padding: 0 30rpx;
    padding-bottom: 10rpx;
    display: flex;
    text-align: center;
}

.modal-box .btn::after {
    border: none;
    display: none;
}

.modal-box .btn-box .btn {
    width: 50%;
    height: 76rpx;
    line-height: 76rpx;
    margin: 0 10rpx;
    padding: 0;
    align-items: center;
    justify-content: center;
    border-radius: 60px;
    font-size: 28rpx;
    font-weight: 500;
}

.modal-box .disagree {
    color: #0f0f0f;
    background: #f5f5f5;
}
</style>
