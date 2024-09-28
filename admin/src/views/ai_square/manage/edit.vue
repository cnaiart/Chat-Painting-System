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
                label-width="100px"
            >
                <el-form-item label="图片" prop="image">
                    <materialPicker
                        v-model="formData.image"
                        :disabled="isSourceFromUser"
                    ></materialPicker>
                </el-form-item>
                <el-form-item :label="isSourceFromUser ? '用户输入' : '中文提示词'">
                    <el-input
                        :disabled="isSourceFromUser"
                        class="w-[380px]"
                        v-model="formData.prompts_cn"
                        type="textarea"
                        :autosize="{ minRows: 4, maxRows: 20 }"
                        :placeholder="isSourceFromUser ? '' : '请输入中文提示词'"
                    />
                </el-form-item>
                <el-form-item label="英文提示词" prop="prompts">
                    <el-input
                        :disabled="isSourceFromUser"
                        class="w-[380px]"
                        v-model="formData.prompts"
                        type="textarea"
                        :autosize="{ minRows: 4, maxRows: 20 }"
                        placeholder="请输入英文提示词"
                    />
                </el-form-item>
                <el-form-item label="所属分类" prop="category_id">
                    <div>
                        <el-select
                            class="w-[380px]"
                            v-model="formData.category_id"
                            placeholder="全部"
                        >
                            <el-option
                                v-for="item in catePager.lists"
                                :key="item.id"
                                :label="item.name"
                                :value="item.id"
                            />
                        </el-select>
                        <div class="form-tips">如不选择分类，则默认显示在前台的【全部】中</div>
                    </div>
                </el-form-item>
                <el-form-item label="用户头像" prop="avatar">
                    <materialPicker
                        v-model="formData.avatar"
                        :disabled="isSourceFromUser"
                    ></materialPicker>
                </el-form-item>
                <el-form-item label="用户昵称">
                    <el-input
                        :disabled="isSourceFromUser"
                        class="w-[380px]"
                        v-model="formData.nickname"
                        placeholder="请输入用户昵称"
                    />
                </el-form-item>
                <el-form-item label="是否显示" prop="is_show">
                    <el-switch v-model="formData.is_show" :active-value="1" :inactive-value="0" />
                </el-form-item>
            </el-form>
        </popup>
    </div>
</template>
<script lang="ts" setup>
import type { FormInstance } from 'element-plus'
import { squareCategoryList } from '@/api/ai_square/category'
import { addDrawSquare, editDrawSquare, getDrawSquareDetail } from '@/api/ai_square/manage'
import Popup from '@/components/popup/index.vue'
const emit = defineEmits(['success', 'close'])
const formRef = shallowRef<FormInstance>()
const popupRef = shallowRef<InstanceType<typeof Popup>>()
const mode = ref('add')
//弹框标题
const popupTitle = computed(() => {
    return mode.value == 'edit' ? '编辑绘画' : '新增绘画'
})
//表单数据
const formData = reactive({
    id: '',
    prompts: '',
    prompts_cn: '',
    sort: 0,
    is_show: 1,
    category_id: '',
    avatar: '',
    nickname: '',
    image: '',
    source: 1
})
const catePager = reactive({
    loading: true,
    lists: []
})
//校验规则
const rules = {
    image: [
        {
            required: true,
            message: '请选择图片',
            trigger: ['blur']
        }
    ],
    prompts: [
        {
            required: true,
            message: '请输入提示词',
            trigger: ['blur']
        }
    ]
}

const isSourceFromUser = computed(() => {
    return formData.source == 2
})
const getData = async () => {
    catePager.loading = true
    try {
        const { lists } = await squareCategoryList()
        catePager.lists = lists
        catePager.loading = false
    } catch (error) {
        catePager.loading = false
        console.log('获取绘画分类失败=>', error)
    }
}

//提交
const handleSubmit = async () => {
    await formRef.value?.validate()
    mode.value == 'edit' ? await editDrawSquare(formData) : await addDrawSquare(formData)
    popupRef.value?.close()
    emit('success')
}

const handleClose = () => {
    emit('close')
}

const open = (type = 'add', id?: number) => {
    mode.value = type
    popupRef.value?.open()
    getData()
    if (id) getDetails(id)
}

const getDetails = async (id?: number) => {
    try {
        const data = await getDrawSquareDetail({ id })
        setFormData(data)
        formData.image = data.thumbnail || data.image
        formData.prompts = data.original_prompts?.prompt_en
        formData.prompts_cn = data.original_prompts?.prompt
    } catch (error) {
        console.log('获取详情失败=>', error)
    }
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
