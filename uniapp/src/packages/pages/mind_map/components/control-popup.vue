<template>
    <u-popup v-model="showModel" mode="left" width="80%" closeable>
        <view class="h-screen flex flex-col">
            <view
                class="text-xl font-bold border-b border-light border-0 border-solid px-[20rpx] py-[30rpx]"
            >
                思维导图
            </view>
            <view class="flex-1 min-h-0 overflow-hidden">
                <view class="p-[20rpx] h-full flex flex-col">
                    <view>
                        <view class="text-xl font-medium">
                            <text>帮我生成</text>
                            <text class="text-error">*</text>
                        </view>
                        <view class="mt-[20rpx]" @touchmove.stop>
                            <u-input
                                class="bg-page rounded"
                                v-model="userInput"
                                type="textarea"
                                :custom-style="{
                                    height: '250rpx',
                                    padding: '10rpx',
                                    'font-size': '28rpx',
                                    'border-radius': '8rpx'
                                }"
                                maxlength="-1"
                                :auto-height="false"
                                placeholder="请输入简单描述，AI将智能输出markdown内容"
                            />
                        </view>
                    </view>
                    <view class="mt-[40rpx] flex-1 min-h-0 flex flex-col">
                        <view class="flex items-center">
                            <text class="text-xl font-medium"> 需求描述 </text>
                            <text
                                class="ml-auto text-primary"
                                v-if="appStore.getMindMapConfig.is_example"
                                @click="
                                    descModel =
                                        appStore.getMindMapConfig
                                            .example_content
                                "
                            >
                                试试示例
                            </text>
                        </view>
                        <view
                            class="mt-[20rpx] flex-1 min-h-0 bg-page rounded"
                            @touchmove.stop
                        >
                            <textarea
                                class="w-full h-full p-[10rpx] text-base box-border"
                                v-model="descModel"
                                maxlength="-1"
                                type="textarea"
                            />
                        </view>
                    </view>
                </view>
            </view>
            <view class="p-[20rpx]">
                <model-picker v-model="modelKey" />
                <u-button
                    type="primary"
                    :loading="isReceiving"
                    :custom-style="{ width: '100%' }"
                    shape="circle"
                    @click="handleCreate"
                >
                    生成思维导图描述
                </u-button>
            </view>
        </view>
    </u-popup>
    <guided-popup ref="guidedPopupRef" />
</template>

<script setup lang="ts">
import { useVModel } from '@vueuse/core'
import { ref, shallowRef, watch } from 'vue'
import modelPicker from '@/packages/components/model-picker/model-picker.vue'
import { useUserStore } from '@/stores/user'
import { chatSendText } from '@/api/chat'
// import { useRouter } from 'uniapp-router-next-zm'
import { useRouter } from 'uniapp-router-next'

//#ifdef H5
import { isMiniProgram } from '@/utils/env'
import wechat from '@/utils/wechat'
//#endif

import { useAppStore } from '@/stores/app'
const appStore = useAppStore()

const props = defineProps<{
    show: boolean
}>()

const emit = defineEmits<{
    (event: 'update:show', show: boolean): void
    (event: 'update', value: string): void
}>()
const router = useRouter()
const guidedPopupRef = shallowRef()
const showModel = useVModel(props, 'show', emit)
const descModel = ref('')
watch(descModel, (value) => {
    emit('update', value)
})
const modelKey = ref('')

const userInput = ref('')
const userStore = useUserStore()
const isReceiving = ref(false)
const handleCreate = async () => {
    if (!userStore.isLogin) {
        return toLogin()
    }
    if (isReceiving.value) return
    if (!userInput.value) return uni.$u.toast('请输入内容')

    try {
        isReceiving.value = true
        await chatSendText(
            {
                model: modelKey.value,
                question: userInput.value,
                type: 4
            },
            {
                onstart(reader) {
                    descModel.value = ''
                },
                onmessage(value) {
                    value
                        .trim()
                        .split('data:')
                        .forEach(async (text) => {
                            if (text !== '') {
                                try {
                                    const dataJson = JSON.parse(text)
                                    const { event, data, code } = dataJson

                                    if (event == 'error' && code === 101) {
                                        guidedPopupRef.value?.open()
                                    } else if (event == 'error') {
                                        uni.$u.toast(data)
                                    }

                                    if (event == 'chat') {
                                        if (data) {
                                            descModel.value += data
                                        }
                                    }

                                    if (event === 'finish') {
                                        return
                                    }
                                } catch (error) {
                                    isReceiving.value = false
                                }
                            }
                        })
                },
                onclose() {
                    isReceiving.value = false
                }
            }
        )
    } catch (error: any) {
        isReceiving.value = false
    }
}

const toLogin = () => {
    if (isMiniProgram) {
        return wechat.miniProgram.navigateTo({ url: '/pages/login/login' })
    } else {
        return router.navigateTo({ path: '/pages/login/login' })
    }
}

watch(
    () => appStore.getMindMapConfig,
    (value) => {
        if (value?.is_example) {
            descModel.value = value?.example_content
        }
    },
    {
        immediate: true
    }
)
defineExpose({
    changDescInput(value: string) {
        descModel.value = value
    }
})
</script>
