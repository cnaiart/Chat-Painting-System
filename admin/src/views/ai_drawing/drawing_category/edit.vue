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
                <el-form-item label="分类名称" prop="name">
                    <el-input
                        class="ls-input"
                        v-model="formData.name"
                        placeholder="请输入分类名称"
                        clearable
                    />
                </el-form-item>
                <el-form-item label="父级分类" prop="has_parent">
                    <el-radio-group v-model="formData.has_parent">
                        <el-radio :label="0">无父级分类</el-radio>
                        <el-radio :label="1">有父级分类</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item prop="pid" v-if="formData.has_parent">
                    <el-cascader
                        class="w-80"
                        v-model="formData.pid"
                        :options="drawCategoryList"
                        :props="props"
                        :clearable="true"
                        :filterable="true"
                    />
                </el-form-item>
                <el-form-item label="排序">
                    <div>
                        <el-input class="ls-input" v-model="formData.sort" />
                        <div class="form-tips">默认为0，数值越大排越前面</div>
                    </div>
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
import { addDrawCategory, getDrawCategoryList, editDrawCategory } from '@/api/ai_draw/draw_category'
import Popup from '@/components/popup/index.vue'
import feedback from '@/utils/feedback'

const emit = defineEmits(['success'])
//表单ref
const formRef = shallowRef<FormInstance>()
//弹框ref
const popupRef = shallowRef<InstanceType<typeof Popup>>()
//弹框标题
const popupTitle = ref('')
const drawCategoryList = ref<any>([])
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
    name: '',
    pid: '',
    has_parent: 0,
    sort: 0,
    status: '',
    image: ''
})
//表单校验规则
const rules = {
    name: [
        {
            required: true,
            message: '请输入名称',
            trigger: ['blur']
        }
    ],
    pid: [{ required: true, message: '请选择父级分类', trigger: ['blur'] }],
    has_parent: [{ required: true, message: '请选择父级分类', trigger: ['blur'] }]
}

//提交表单
const handleSubmit = async () => {
    try {
        await formRef.value?.validate()
        if (formData.value.id == '') await addDrawCategory(formData.value)
        else if (formData.value.id != '') await editDrawCategory(formData.value)
        popupRef.value?.close()
        emit('success')
    } catch (error) {
        return error
    }
}

//打开弹框
const open = (type: string, value: any) => {
    //初始化数据
    if (type == 'add') {
        formData.value = {
            id: '',
            name: '',
            pid: 0,
            has_parent: 0,
            sort: '0',
            status: 1
        }
        popupTitle.value = '新增分类'
    } else if (type == 'edit') {
        Object.keys(formData.value).map((item) => {
            formData.value[item] = value[item] ?? 0
        })
        if (formData.value.pid) {
            formData.value.has_parent = 1
        }
        popupTitle.value = '编辑分类'
    }
    popupRef.value?.open()
    getCategoryList()
}

const getCategoryList = async () => {
    try {
        const data = await getDrawCategoryList()
        // 禁止选择自己以及自己以下的
        data.forEach((item: any) => {
            if (formData.value.id == item.id) {
                item.disabled = true
            }
        })
        drawCategoryList.value = data
    } catch (error) {
        console.log(error)
    }
}



defineExpose({
    open
})
</script>
