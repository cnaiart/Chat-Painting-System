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
            <el-form ref="formRef" :model="formData" label-width="84px" :rules="rules">
                <!-- 技能名称 -->
                <el-form-item label="类别名称" prop="name">
                    <el-input placeholder="请输入类别名称" v-model="formData.name"></el-input>
                </el-form-item>

                <!-- 排序 -->
                <el-form-item label="排序">
                    <div>
                        <el-input class="ls-input" v-model="formData.sort" />
                        <div class="form-tips">默认为0，数值越大排越前面</div>
                    </div>
                </el-form-item>
                <!-- 状态 -->
                <el-form-item label="状态">
                    <template #default="{ row }">
                        <el-switch
                            v-model="formData.status"
                            :active-value="1"
                            :inactive-value="0"
                        />
                    </template>
                </el-form-item>
            </el-form>
        </popup>
    </div>
</template>
<script lang="ts" setup>
import type { FormInstance } from 'element-plus'
import Popup from '@/components/popup/index.vue'
import { addSkillCategory, editkillCategory } from '@/api/skill/type'
const emit = defineEmits(['success', 'close'])
const formRef = shallowRef<FormInstance>()
const popupRef = shallowRef<InstanceType<typeof Popup>>()
const mode = ref('add')
const popupTitle = computed(() => {
    return mode.value == 'add' ? '新增类别' : '编辑类别'
})
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

const handleSubmit = async () => {
    await formRef.value?.validate()
    mode.value == 'edit' ? await editkillCategory(formData) : await addSkillCategory(formData)
    popupRef.value?.close()
    emit('success')
}

const open = (type = 'add') => {
    mode.value = type
    popupRef.value?.open()
}

const setFormData = async (row: any) => {
    for (const key in formData) {
        if (row[key] != null && row[key] != undefined) {
            //@ts-ignore
            formData[key] = row[key]
        }
    }
}

const handleClose = () => {
    emit('close')
}

defineExpose({
    open,
    setFormData
})
</script>
