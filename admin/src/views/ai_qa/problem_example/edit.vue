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
                <el-form-item label="示例类目" prop="category_id">
                    <el-select class="w-full" placeholder="请选择" v-model="formData.category_id">
                        <el-option
                            v-for="(item, index) in categoryList"
                            :key="index"
                            :value="item.id"
                            :label="item.name"
                        ></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="示例内容" prop="content">
                    <el-input
                        v-model="formData.content"
                        type="textarea"
                        :autosize="{ minRows: 4, maxRows: 6 }"
                        placeholder="请输入示例内容"
                        maxlength="200"
                        show-word-limit
                    />
                </el-form-item>
                <el-form-item label="排序" prop="sort">
                    <div>
                        <el-input-number v-model="formData.sort" :min="0" :max="9999" />
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
import { addQusetionSample, editQusetionSample } from '@/api/ai_qa/problem_example'
import { getQuestionCategoryList } from '@/api/ai_qa/problem_category'
import Popup from '@/components/popup/index.vue'
import feedback from '@/utils/feedback'

const emit = defineEmits(['success', 'close'])
//表单ref
const formRef = shallowRef<FormInstance>()
//弹框ref
const popupRef = shallowRef<InstanceType<typeof Popup>>()
//弹框标题
const popupTitle = ref('')
//分类列表
const categoryList: any = ref([])

//表单数据
const formData: any = ref({
    id: '',
    category_id: '', //分类
    content: '', //内容
    sort: 0, //排序
    status: '1' //状态 1-开启 0-关闭
})
//表单校验规则
const rules = {
    category_id: [
        {
            required: true,
            message: '请选择示例类目',
            trigger: ['blur']
        }
    ],
    content: [
        {
            required: true,
            message: '请输入示例内容',
            trigger: ['blur']
        }
    ]
}

//获取分类列表
const getCategoryList = async () => {
    const { lists } = await getQuestionCategoryList()
    categoryList.value = lists
}

//提交表单
const handleSubmit = async () => {
    try {
        await formRef.value?.validate()
        if (formData.value.id == '') await addQusetionSample(formData.value)
        else if (formData.value.id != '') await editQusetionSample(formData.value)
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
            content: '', //内容
            sort: 0, //排序
            status: 1 //状态 1-开启 0-关闭
        }
        popupTitle.value = '新增问题示例'
    } else if (type == 'edit') {
        Object.keys(formData.value).map((item) => {
            formData.value[item] = value[item]
        })
        popupTitle.value = '编辑问题示例'
    }
    popupRef.value?.open()
}

defineExpose({
    open
})
</script>
