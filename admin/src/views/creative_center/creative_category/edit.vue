<template>
    <div class="edit-popup">
        <popup
            ref="popupRef"
            :title="popupTitle"
            :async="true"
            width="550px"
            @confirm="handleSubmit"
            @close="handleClose"
        >
            <el-form
                class="ls-form"
                ref="formRef"
                :rules="rules"
                :model="formData"
                label-width="90px"
            >
                <el-form-item label="分类名称" prop="name">
                    <el-input
                        class="ls-input"
                        v-model="formData.name"
                        placeholder="请输入分类名称"
                        clearable
                    />
                </el-form-item>
                <el-form-item label="排序" prop="sort">
                    <div>
                        <el-input class="ls-input" v-model="formData.sort" />
                        <div class="form-tips">默认为0，数值越大排越前面</div>
                    </div>
                </el-form-item>
                <el-form-item label="状态" prop="sort">
                    <el-switch v-model="formData.status" :active-value="1" :inactive-value="0" />
                </el-form-item>
            </el-form>
        </popup>
    </div>
</template>
<script lang="ts" setup>
import type { FormInstance } from 'element-plus'
import { addCreationCategory, editCreationCategory } from '@/api/creative_center/creative_category'
import Popup from '@/components/popup/index.vue'
const emit = defineEmits(['success', 'close'])
const formRef = shallowRef<FormInstance>()
const popupRef = shallowRef<InstanceType<typeof Popup>>()
const mode = ref('add')
//弹框标题
const popupTitle = computed(() => {
    return mode.value == 'edit' ? '编辑类别' : '新增类别'
})
//表单数据
const formData = reactive({
    id: '',
    name: '',
    sort: 0,
    status: 1
})
//校验规则
const rules = {
    name: [
        {
            required: true,
            message: '请输入名称',
            trigger: ['blur']
        }
    ]
}
//提交
const handleSubmit = async () => {
    await formRef.value?.validate()
    mode.value == 'edit'
        ? await editCreationCategory(formData)
        : await addCreationCategory(formData)
    popupRef.value?.close()
    emit('success')
}

const handleClose = () => {
    emit('close')
}

const open = (type = 'add') => {
    mode.value = type
    popupRef.value?.open()
}

const setFormData = async (data: Record<any, any>) => {
    for (const key in formData) {
        if (data[key] != null && data[key] != undefined) {
            //@ts-ignore
            formData[key] = data[key]
        }
    }
}

defineExpose({
    open,
    setFormData
})
</script>
