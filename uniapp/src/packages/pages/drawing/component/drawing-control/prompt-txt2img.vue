<template>
    <!--    描述推荐    -->
    <view class="flex justify-between">
        <view class="text-base">
            <text class="font-medium"> 文本描述 </text>
            <text class="text-error">*</text>
        </view>
    </view>
    <!--    关键词输入    -->
    <view class="mt-[24rpx]">
        <l-textarea
            v-model="value"
            maxlength="1024"
            :rows="4"
            :show-word-limit="false"
            :custom-class="{
                paddingBottom: '60rpx'
            }"
            placeholder="输入英文描述词，生成效果会更好；描述词越详细，生成的图片效果更加真实哦～~"
        >
            <template #length-suffix>
                <view class="length-suffix justify-end text-sm">
                    <view class="text-primary" @click="keydownRef?.open()">
                        描述词推荐
                    </view>
                    <view
                        v-if="appStore?.getDrawConfig?.translate_type == 2"
                        class="ml-[40rpx] text-muted hover:text-primary"
                        :loading="isTranslate"
                        @click.stop="translatePrompt"
                    >
                        <u-loading
                            v-if="isTranslate"
                            mode="flower"
                            :color="$theme.primaryColor"
                            size="28"
                        ></u-loading>
                        <image
                            v-else
                            class="w-[26rpx] h-[26rpx]"
                            src="@/static/images/common/translate.png"
                        ></image>
                        <text class="ml-[10rpx]">翻译成英文</text>
                    </view>
                    <view
                        class="text-muted hover:text-primary ml-[40rpx]"
                        @click.stop="value = ''"
                    >
                        <u-icon name="trash"></u-icon>
                        <text>清空</text>
                    </view>
                </view>
            </template>
        </l-textarea>
    </view>
</template>

<script setup lang="ts">
import { useAppStore } from '@/stores/app'
import { useLockFn } from '@/hooks/useLockFn'
import { keywordPromptTranslate } from '@/api/drawing'
import { computed } from 'vue'

const appStore = useAppStore()
const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
}>()

const props = withDefaults(
    defineProps<{
        modelValue?: any
        keydownRef: any
    }>(),
    {
        modelValue: '',
        keydownRef: {}
    }
)

const value = computed({
    get: () => {
        return props.modelValue
    },
    set: (val) => {
        emit('update:modelValue', val)
    }
})

// 请求翻译
const { isLock: isTranslate, lockFn: translatePrompt } = useLockFn(async () => {
    try {
        if (value.value.trim() == '') {
            uni.$u.toast('请输入描述词')
            return
        }
        const { translate } = await keywordPromptTranslate({
            prompt: value.value
        })
        emit('update:modelValue', translate)
    } catch (error) {
        console.log('翻译失败=>', error)
    }
})
</script>

<style scoped>
.length-suffix {
    position: absolute;
    bottom: 0px;
    left: 10px;
    z-index: 10;
    font-size: 14px;
    display: flex;
    align-items: center;
    width: 90%;
    height: 60rpx;
}
</style>
