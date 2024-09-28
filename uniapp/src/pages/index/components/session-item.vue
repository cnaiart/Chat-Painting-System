<template>
    <view
        class="px-[20rpx] h-[80rpx] flex items-center rounded-[8rpx] mb-[20rpx] cursor-pointer"
        :class="{
            ' bg-page': isActive
        }"
        @click="activeId = itemId"
    >
        <u-icon name="chat" />

        <view class="ml-[20rpx] flex-1 min-w-0">
            <u-input
                height="50"
                v-if="isEdit"
                :custom-style="{
                    fontSize: '26rpx'
                }"
                v-model="nameInput"
                border
            />
            <view v-else class="line-clamp-1">{{ name }}</view>
        </view>
        <template v-if="isActive">
            <template v-if="isEdit">
                <view
                    class="cursor-pointer mx-[12rpx] flex h-full"
                    @click="handleEdit"
                >
                    <u-icon name="checkbox-mark" />
                </view>
                <view
                    class="cursor-pointer flex h-full"
                    @click="isEdit = false"
                >
                    <u-icon name="close" />
                </view>
            </template>
            <template v-else>
                <view
                    class="cursor-pointer mr-[12rpx] flex h-full"
                    @click="isEdit = true"
                >
                    <u-icon name="edit-pen" />
                </view>
                <view
                    class="cursor-pointer flex h-full"
                    @click="emit('delete', itemId)"
                >
                    <u-icon name="trash" />
                </view>
            </template>
        </template>
    </view>
</template>
<script lang="ts" setup>
import { computed, ref, watch } from 'vue'

const props = withDefaults(
    defineProps<{
        modelValue: number | string
        name: string
        itemId: number | string
    }>(),
    {}
)
const emit = defineEmits(['update:modelValue', 'edit', 'delete'])

const nameInput = ref('')
const isEdit = ref(false)

const activeId = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
})

const isActive = computed(() => activeId.value === props.itemId)

const handleEdit = () => {
    isEdit.value = false
    emit('edit', props.itemId, nameInput.value)
}
watch(
    () => props.modelValue,
    () => {
        isEdit.value = false
    }
)

watch(
    () => props.name,
    (value) => {
        nameInput.value = value
    },
    {
        immediate: true
    }
)
</script>
