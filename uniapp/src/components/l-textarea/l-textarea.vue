<template>
    <view class="l-textarea" :data-error="error" :class="{ error: error }">
        <textarea
            class="l-textarea__inner"
            placeholder-class="l-textarea__placeholder"
            :placeholder="placeholder"
            :rows="rows"
            v-model="textAreaValue"
            :maxlength="maxlength"
            :class="{
                '!border-primary': isFocus
            }"
            :style="customClass"
            @input="getTextAreaValue"
            @focus="isFocus = true"
            @blur="isFocus = false"
        />
        <view v-if="showWordLimit" class="l-textarea-length-maxlength">
            <text>{{ lengthText }}</text> / <text>{{ maxlength }}</text>
        </view>
        <slot name="length-suffix"></slot>
    </view>
</template>

<script setup>
import { watch, ref, nextTick } from 'vue'

const props = defineProps({
    placeholder: '',
    maxlength: '',
    showWordLimit: false,
    customClass: {},
    error: '',
    rows: '',
    modelValue: ''
})

const emit = defineEmits(['update:modelValue'])
const textAreaValue = ref(props.modelValue)
const lengthText = ref(props.modelValue?.length || 0)
const isFocus = ref(false)

const getTextAreaValue = (e) => {
    const event = e || window.event
    const target = event.detail || event.taget
    emit('update:modelValue', target.value)
}

watch(
    () => props.modelValue,
    (val) => {
        textAreaValue.value = val
        lengthText.value = val.length
    }
)
</script>

<style lang="scss" scoped>
.l-textarea {
    width: 100%;
    position: relative;
}

.l-textarea-length-maxlength {
    position: absolute;
    z-index: 10;
    bottom: 10rpx;
    right: 20rpx;
    display: flex;
    font-size: 24rpx;
    color: #aeaeae;
    @apply bg-page;
}
.l-textarea__inner {
    width: 100%;
    padding: 8px;
    box-sizing: border-box;
    transition: border 0.3s;
    outline: none;
    border-radius: 8rpx;
    border: 1px solid transparent;
    /** 禁止textarea拉伸 */
    resize: none;
    @apply bg-page;
}
.l-textarea__placeholder {
    color: #888888;
    font-size: 28rpx;
}
</style>
