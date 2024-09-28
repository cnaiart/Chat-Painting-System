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
                <template
                    v-if="
                        formData.ai_key?.includes('xinghuo') ||
                        formData.ai_key?.includes('kdxf') ||
                        formData.ai_key?.includes('hunyuan')
                    "
                >
                    <el-form-item label="APPID" prop="appid">
                        <el-input
                            class="w-[330px]"
                            v-model="formData.appid"
                            placeholder="请输入APPID"
                            clearable
                        />
                    </el-form-item>
                </template>

                <template v-if="formData.ai_key?.includes('minimax')">
                    <el-form-item label="groupId" prop="appid">
                        <el-input
                            class="w-[330px]"
                            v-model="formData.appid"
                            placeholder="请输入APPID"
                            clearable
                        />
                    </el-form-item>
                </template>
                <el-form-item :label="getFieldKey.key" prop="key">
                    <el-input
                        class="w-[330px]"
                        v-model="formData.key"
                        :placeholder="`请输入${getFieldKey.key}`"
                        :rows="4"
                        type="textarea"
                        clearable
                    />
                </el-form-item>
                <template
                    v-if="
                        getFieldKey.secret &&
                        (formData.ai_key?.includes('xinghuo') ||
                            formData.ai_key?.includes('wenxin') ||
                            formData.ai_key?.includes('kdxf') ||
                            formData.ai_key?.includes('yijian_sd') ||
                            formData.ai_key?.includes('hunyuan'))
                    "
                >
                    <el-form-item :label="getFieldKey.secret" prop="secret">
                        <el-input
                            class="w-[330px]"
                            v-model="formData.secret"
                            :placeholder="`请输入${getFieldKey.secret}`"
                            clearable
                        />
                    </el-form-item>
                </template>
                <el-form-item label="状态">
                    <el-switch v-model="formData.status" :active-value="1" :inactive-value="0" />
                </el-form-item>
            </el-form>
        </popup>
    </div>
</template>
<script lang="ts" setup>
import type { FormInstance } from 'element-plus'
import { addKeyPool, editKeyPool, getKeyPoolAiModel, getKeyPoolDetail } from '@/api/setting/ai_key'
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
    appid: '',
    secret: '',
    ai_key: '',
    is_clear: 0,
    key: '',
    status: 1
})
//表单校验规则
const getFieldKey = computed(() => {
    switch (formData.value.ai_key) {
        case 'hunyuan':
            return {
                key: 'SecretKey',
                secret: 'SecretId'
            }
        case 'minimax':
            return {
                key: '密钥',
                secret: 'SecretId'
            }
        case 'kdxf': {
            if ([5, 6].includes(formData.value.type)) {
                return {
                    key: 'SecretKey'
                }
            } else {
                return {
                    key: 'APIKey',
                    secret: 'APISecret'
                }
            }
        }
        default:
            return {
                key: 'APIKey',
                secret: 'APISecret'
            }
    }
})

const rules = ref({
    key: [
        {
            required: true,
            message() {
                return `请输入${getFieldKey.value.key}`
            },
            trigger: ['blur']
        }
    ],
    appid: [
        {
            required: true,
            message: '请输入APPID'
        }
    ],
    secret: [
        {
            required: true,
            message() {
                return `请输入${getFieldKey.value.secret}`
            }
        }
    ]
})

//提交表单
const handleSubmit = async () => {
    try {
        await formRef.value?.validate()
        if (formData.value.id == '') await addKeyPool(formData.value)
        else if (formData.value.id != '') await editKeyPool(formData.value)
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
            key: '',
            status: 1
        }
        popupTitle.value = '新增密钥'
    } else if (mode == 'edit') {
        const data = await getKeyPoolDetail({ id: value.id })
        Object.keys(data).map((item) => {
            formData.value[item] = data[item] ?? 0
        })
        formData.value.type = type
        if (value.change_btn) {
            formData.value.is_clear = 1
        }
        popupTitle.value = '编辑密钥'
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
