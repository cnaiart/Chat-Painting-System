<template>
    <ElForm
        ref="formRef"
        v-bind="props"
        :rules="formRules"
        :model="formData"
        @submit.prevent
    >
        <ElFormItem
            v-for="item in formLists"
            :key="item.id"
            :prop="item.id"
            class="designer"
            :label="item.props.title"
        >
            <component
                v-bind="item.props"
                :is="getWidgetByName(item.name)"
                v-model="formData[item.id]"
            />
        </ElFormItem>
    </ElForm>
</template>

<script setup lang="ts">
import type { FormProps } from 'element-plus'
import * as widgets from './widgets'

interface Props extends Partial<FormProps> {
    formLists: any[]
    modelValue: Record<string, any>
}
const props = withDefaults(defineProps<Props>(), {
    labelPosition: 'top',
    formLists: () => []
})
const emit = defineEmits<{
    (event: 'update:modelValue', value: any): void
}>()
const formRef = shallowRef()
const formData = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
})
const formRules = ref<any>({})
const getWidgetByName = (name: string) => {
    return (widgets as any)[name]
}
const validate = async () => {
    await formRef.value?.validate()
    console.log(1111)
}

watch(
    () => props.formLists,
    (value) => {
        formRules.value = value?.reduce((prev: any, item: any) => {
            formData.value[item.id] = undefined
            if (item.props.isRequired) {
                prev[item.id] = [
                    {
                        required: true,
                        message: '必填项不能为空',
                        trigger: 'blur'
                    }
                ]
            }
            return prev
        }, {})
    },
    {
        immediate: true
    }
)

defineExpose({
    validate
})
</script>

<style scoped>
.designer {
    :deep(.el-form-item__label) {
        color: #333333 !important;
    }
}
</style>