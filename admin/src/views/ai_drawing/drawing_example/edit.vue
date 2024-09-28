<template>
    <div class="edit-popup">
        <popup
            ref="popupRef"
            :title="popupTitle"
            :async="true"
            width="520px"
            @confirm="handleSubmit"
            @close="handleClose"
        >
            <el-form ref="formRef" class="mt-4" :model="formData" :rules="rules" :inline="false" label-width="80px">
                <el-form-item label="示例标题" prop="prompt">
                    <div class="w-full">
                        <el-input v-model="formData.prompt" placeholder="例如：可爱的美少女" />
                    </div>
                </el-form-item>
                <el-form-item label="示例内容" prop="prompt_en">
                    <el-input
                        v-model="formData.prompt_en"
                        type="textarea"
                        :autosize="{ minRows: 8, maxRows: 20 }"
                        placeholder="请输入示例内容，将会被填充进输入框的内容"
                    />
                </el-form-item>
                <el-form-item label="排序" prop="sort">
                    <div class="w-full">
                        <el-input v-model.number="formData.sort" placeholder="请输入" />
                        <div class="form-tips">默认为0，数值越大越排前面</div>
                    </div>
                </el-form-item>
                <el-form-item label="状态" prop="status">
                    <el-switch v-model="formData.status" :active-value="1" :inactive-value="0" />
                </el-form-item>
            </el-form>
        </popup>
    </div>
</template>
<script lang="ts" setup>
import type { FormInstance, FormRules } from 'element-plus'
import Popup from '@/components/popup/index.vue'
import {
    getPromptExampleDetail,
    addPromptExample,
    editPromptExample
} from '@/api/ai_draw/draw_example'

const emit = defineEmits(['success', 'close'])
//表单ref
const formRef = shallowRef<FormInstance>()
//弹框ref
const popupRef = shallowRef<InstanceType<typeof Popup>>()
// 弹窗标题
const popupTitle = ref<string>('')

const formData = reactive({
    id: '',
    prompt: '',
    prompt_en: '',
    sort: '',
    status: 1
})

const rules = reactive<FormRules>({
    prompt: [
        {
            required: true,
            message: '请输入中文示例',
            trigger: ['change', 'blur']
        }
    ],
    prompt_en: [
        {
            required: true,
            message: '请输入英文示例',
            trigger: ['change', 'blur']
        }
    ]
})

//获取详情
const getDetails = async (id: number) => {
    try {
        const data = await getPromptExampleDetail({ id })
        for (const key in formData) {
            if (data[key] != null && data[key] != undefined) {
                //@ts-ignore
                formData[key] = data[key]
            }
        }
    } catch (error) {
        console.log('获取详情=>', error)
    }
}

//提交表单
const handleSubmit = async () => {
    try {
        await formRef.value?.validate()
        formData.id ? await editPromptExample(formData) : await addPromptExample(formData)
        emit('success')
        popupRef.value?.close()
    } catch (error) {
        return error
    }
}

const handleClose = () => {
    emit('close')
}

const open = (type: string, id: number) => {
    if (id) {
        popupTitle.value = '编辑示例'
        getDetails(id)
    } else {
        popupTitle.value = '新增示例'
    }
    popupRef.value?.open()
}

defineExpose({ open })
</script>
