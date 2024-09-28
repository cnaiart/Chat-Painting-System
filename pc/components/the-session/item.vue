<template>
    <div
        class="px-[10px] h-[40px] flex items-center rounded-[8px] mb-[10px] cursor-pointer"
        :class="{
            'bg-page': isActive
        }"
        @click="activeId = itemId"
    >
        <Icon name="el-icon-ChatDotRound" />

        <div class="ml-[10px] flex-1 min-w-0">
            <ElInput v-if="isEdit" v-model="nameInput" size="small" />
            <div v-else class="line-clamp-1">{{ name }}</div>
        </div>
        <template v-if="isActive">
            <template v-if="isEdit">
                <div class="cursor-pointer mr-[6px] flex" @click="handleEdit">
                    <Icon name="el-icon-Select" />
                </div>
                <div class="cursor-pointer flex" @click="isEdit = false">
                    <Icon name="el-icon-CloseBold" />
                </div>
            </template>
            <template v-else>
                <div
                    class="cursor-pointer mr-[6px] flex"
                    @click="isEdit = true"
                >
                    <Icon name="el-icon-EditPen" />
                </div>
                <div
                    class="cursor-pointer flex"
                    @click="emit('delete', itemId)"
                >
                    <Icon name="el-icon-Delete" />
                </div>
            </template>
        </template>
    </div>
</template>
<script lang="ts" setup>
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
