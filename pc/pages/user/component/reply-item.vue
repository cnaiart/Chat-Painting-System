<template>
    <div class="bg-[#FBBC2D] text-white px-[3px] rounded-[3px]">答</div>
    <div class="ml-[10px] flex-1 min-w-0">
        <div
            ref="itemRef"
            class="overflow-hidden"
            :style="{
                height: isLongContent && !showMore ? showHeight + 'px' : 'auto'
            }"
        >
            <chat-content :content="text" :use-markdown="true" />
        </div>

        <div class="flex mt-[15px]">
            <div
                class="text-sm text-tx-secondary cursor-pointer"
                @click="copy(text)"
            >
                复制
            </div>
            <div class="flex-1 flex justify-center">
                <el-button
                    type="primary"
                    link
                    v-if="isLongContent"
                    @click="showMore = !showMore"
                >
                    {{ !showMore ? '点击查看全部' : '收起' }}
                    <Icon
                        :name="
                            showMore ? `el-icon-ArrowUp` : `el-icon-ArrowDown`
                        "
                    />
                </el-button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useElementSize } from '@vueuse/core'
const props = defineProps<{
    text: string
}>()

const { copy } = useCopy()
const itemRef = shallowRef()
const showHeight = ref(200)
const { height } = useElementSize(itemRef)
const showMore = ref(false)
const isLongContent = ref(false)
watchEffect(() => {
    if (height.value >= showHeight.value) {
        isLongContent.value = true
    } else {
        isLongContent.value = false
    }
})
</script>
