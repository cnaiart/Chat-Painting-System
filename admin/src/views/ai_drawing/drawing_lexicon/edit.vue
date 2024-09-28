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
            <el-form ref="formRef" :rules="rules" :model="formData" label-width="100px">
                <el-form-item label="关键词" prop="prompt" v-if="!formData.id">
                    <div class="w-full">
                        <el-input
                            v-model="formData.prompt"
                            type="textarea"
                            :autosize="{ minRows: 8, maxRows: 20 }"
                            placeholder="请输入关键词，如：Surrealism&超现实主义
Baroque&巴洛克
modern&现代"
                        />
                        <div class="form-tips">添加多个关键词，中英文以&隔开，按回车换行</div>
                    </div>
                </el-form-item>
                <template v-else>
                    <el-form-item label="英文关键词" prop="prompt_en">
                        <div class="w-full">
                            <el-input v-model="formData.prompt_en" placeholder="请输入关键词" />
                        </div>
                    </el-form-item>
                    <el-form-item label="中文关键词" prop="prompt">
                        <div class="w-full">
                            <el-input v-model="formData.prompt" placeholder="请输入关键词" />
                        </div>
                    </el-form-item>
                </template>
                <el-form-item label="所属类目" prop="category_id">
                    <el-cascader
                        class="w-full"
                        v-model="formData.category_id"
                        :options="categoryList"
                        :props="props"
                        :clearable="true"
                        :filterable="true"
                    />
                </el-form-item>
                <el-form-item label="排序" prop="sort">
                    <div class="w-full">
                        <el-input type="text" v-model="formData.sort" :min="0" :max="9999" />
                        <div class="form-tips">默认为0，数据越大越排前面</div>
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
import { addPrompt, editPrompt } from '@/api/ai_draw/draw_prompt'
import { getDrawCategoryList } from '@/api/ai_draw/draw_category'
import Popup from '@/components/popup/index.vue'

const emit = defineEmits(['success', 'close'])
//表单ref
const formRef = shallowRef<FormInstance>()
//弹框ref
const popupRef = shallowRef<InstanceType<typeof Popup>>()
//弹框标题
const popupTitle = ref('')
//分类列表
const categoryList: any = ref([])
const props = reactive<any>({
    multiple: false,
    checkStrictly: true,
    label: 'name',
    value: 'id',
    children: 'children',
    emitPath: false
})
//表单数据
const formData: any = ref({
    id: '',
    category_id: '', //分类
    prompt: '', //内容
    prompt_en: '',
    sort: 0, //排序
    status: '1' //状态 1-开启 0-关闭
})
//表单校验规则
const rules = {
    prompt: [
        {
            required: true,
            message: '请输入关键词',
            trigger: ['blur']
        }
    ],
    prompt_en: [
        {
            required: true,
            message: '请输入英文关键词',
            trigger: ['blur']
        }
    ],
    category_id: [
        {
            required: true,
            message: '请选择所属类目',
            trigger: ['blur']
        }
    ]
}

//获取分类列表
const getCategoryList = async () => {
    const lists = await getDrawCategoryList()
    categoryList.value = lists
}

//提交表单
const handleSubmit = async () => {
    try {
        await formRef.value?.validate()
        if (formData.value.id == '') {
            await addPrompt({
                ...formData.value,
                prompt: formData.value.prompt.split('\n')
            })
        } else if (formData.value.id != '') {
            await editPrompt(formData.value)
        }
        popupRef.value?.close()
        emit('success')
    } catch (error) {
        return error
    }
}

const handleClose = () => {
    emit('close')
}

const open = (type: string, value: any) => {
    getCategoryList()
    //初始化数据
    if (type == 'add') {
        formData.value = {
            id: '',
            category_id: '', //分类
            prompt: '', //内容
            prompt_en: '',
            sort: 0, //排序
            status: 1 //状态 1-开启 0-关闭
        }
        popupTitle.value = '新增关键词'
    } else if (type == 'edit') {
        Object.keys(formData.value).map((item) => {
            formData.value[item] = value[item]
        })
        console.log(formData.value, value)
        popupTitle.value = '编辑关键词'
    }
    popupRef.value?.open()
}

defineExpose({
    open
})
</script>
