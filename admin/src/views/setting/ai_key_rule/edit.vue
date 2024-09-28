<template>
    <div class="edit-popup">
        <popup
            ref="popupRef"
            :title="popupTitle"
            :async="true"
            width="550px"
            @confirm="handleSubmit"
        >
            <el-form
                class="ls-form"
                ref="formRef"
                :rules="rules"
                :model="formData"
                label-width="90px"
            >
                <el-form-item label="接口类型">
                    <el-select class="w-[330px]" v-model="formData.ai_key">
                        <el-option
                            v-for="(item, key) in aiModelList"
                            :key="key"
                            :label="item"
                            :value="key"
                        />
                    </el-select>
                </el-form-item>
                <el-form-item label="停用规则" prop="rule">
                    <el-input
                        class="w-[330px]"
                        v-model="formData.rule"
                        placeholder="请输入停用规则"
                        type="textarea"
                        :autosize="{ minRows: 2, maxRows: 4 }"
                        clearable
                    />
                </el-form-item>

                <el-form-item label="停用提示" prop="prompt">
                    <el-input
                        class="w-[330px]"
                        v-model="formData.prompt"
                        placeholder="请输入停用提示"
                        :rows="4"
                        clearable
                    />
                </el-form-item>
                <el-form-item label="状态">
                    <el-switch v-model="formData.status" :active-value="1" :inactive-value="0" />
                </el-form-item>
            </el-form>
        </popup>
    </div>
</template>
<script lang="ts" setup>
import type { FormInstance } from 'element-plus'
import { getKeyPoolAiModel } from '@/api/setting/ai_key'
import { addKeyDownRule, editKeyDownRule, getKeyDownRuleDetail } from '@/api/setting/ai_key_rule'
import Popup from '@/components/popup/index.vue'
import feedback from '@/utils/feedback'

const emit = defineEmits(['success'])
//表单ref
const formRef = shallowRef<FormInstance>()
//弹框ref
const popupRef = shallowRef<InstanceType<typeof Popup>>()
//弹框标题
const popupTitle = ref('')
const aiModelList = ref<any>([])
//表单数据
const formData: any = ref({
    id: '',
    type: '',
    rule: '',
    ai_key: '',
    prompt: '',
    status: 1
})
//表单校验规则
const rules = {
    key: [
        {
            required: true,
            message: '请输入APIKey',
            trigger: ['blur']
        }
    ],
    rule: [
        {
            required: true,
            message: '请输入停用规则'
        }
    ],
    prompt: [
        {
            required: true,
            message: '请输入停用提示'
        }
    ]
}

//提交表单
const handleSubmit = async () => {
    try {
        await formRef.value?.validate()
        if (formData.value.id == '') await addKeyDownRule(formData.value)
        else if (formData.value.id != '') await editKeyDownRule(formData.value)
        feedback.msgSuccess('操作成功')
        emit('success')
        popupRef.value?.close()
    } catch (error) {
        return error
    }
}

//打开弹框
const open = async (type: number, mode: string, value: any) => {
    //初始化数据
    if (mode == 'add') {
        formData.value = {
            id: '',
            type,
            ai_key: '',
            rule: '',
            prompt: '',
            status: 1
        }
        popupTitle.value = '新增规则'
    } else if (mode == 'edit') {
        const data = await getKeyDownRuleDetail({ id: value.id })
        Object.keys(formData.value).map((item) => {
            formData.value[item] = data[item] ?? 0
        })
        formData.value.type = type
        popupTitle.value = '编辑规则'
    }
    popupRef.value?.open()
    getAiModelList(type)
}

const getAiModelList = async (type: number) => {
    try {
        const data = await getKeyPoolAiModel({
            type: type
        })
        aiModelList.value = data
    } catch (error) {
        console.log(error)
    }
}

defineExpose({
    open
})
</script>
