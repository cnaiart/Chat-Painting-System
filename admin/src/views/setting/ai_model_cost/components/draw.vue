<template>
    <div class="pt-[10px]">
        <el-form label-width="120px" ref="formRef" :rules="rules" :model="formData">
            <el-form-item label="模型计费">
                <div>
                    <el-switch
                        v-model="formData.is_open"
                        :active-value="1"
                        :inactive-value="0"
                    ></el-switch>
                    <div class="form-tips !text-[14px]">
                        开启后用户可以在前端选择想要使用的模型，单独计费。
                    </div>
                </div>
            </el-form-item>

            <el-form-item label="当前默认接口" v-if="formData.is_open">
                <div>
                    <el-radio-group v-model="formData.default_model">
                        <el-radio :label="formData.default_model">
                            {{ formData.default_model }}
                        </el-radio>
                    </el-radio-group>
                    <div class="form-tips !text-[14px]">在「AI模型」选中的AI接口</div>
                </div>
            </el-form-item>
            <el-form-item label="模型设置" v-if="formData.is_open">
                <el-table ref="tableRef" size="large" row-key="key" :data="modelList">
                    <el-table-column width="50">
                        <template #default>
                            <div class="move-icon cursor-move">
                                <Icon name="el-icon-Rank" />
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column label="模型名称" prop="name" min-width="120" />
                    <el-table-column label="别名" prop="alias" min-width="100">
                        <template #default="{ row }">
                            <el-input v-model="row.alias" placeholder="为空时显示默认名字" />
                        </template>
                    </el-table-column>
                    <el-table-column label="消耗绘画条数" prop="balance" min-width="100">
                        <template #default="{ row }">
                            <el-input v-model="row.balance" placeholder="为空默认1条" />
                        </template>
                    </el-table-column>
                    <el-table-column label="状态" width="100">
                        <template #default="{ row }">
                            <el-switch v-model="row.status" :active-value="1" :inactive-value="0" />
                        </template>
                    </el-table-column>
                    <el-table-column label="状态" width="120">
                        <template #header>
                            <div class="flex items-center">
                                会员免费
                                <el-tooltip
                                    effect="dark"
                                    content="开启后，用户开通会员，使用该模型绘画不消耗条数"
                                    placement="top"
                                >
                                    <Icon name="local-icon-yiwen" />
                                </el-tooltip>
                            </div>
                        </template>
                        <template #default="{ row }">
                            <el-switch
                                v-model="row.member_free"
                                :active-value="1"
                                :inactive-value="0"
                            />
                        </template>
                    </el-table-column>
                </el-table>
            </el-form-item>
        </el-form>
    </div>
</template>

<script setup lang="ts">
import Sortable from 'sortablejs'
import { getDrawBillingConfig, setDrawBillingConfig } from '@/api/setting/ai_model_cost'

const props = defineProps({
    popRef: {
        type: Object,
        default: {} as any
    }
})

const tableRef = shallowRef()
//表单ref
const formRef = shallowRef()

//表单数据
const formData: any = ref({
    is_open: 0,
    default_model: '',
    billing_config: []
})
// 模型设置列表
const modelList = ref<any[]>([])

//表单校验规则
const rules = {
    api_key: [
        {
            required: true,
            message: '请输入秘钥',
            trigger: ['blur']
        }
    ],
    api_type: [
        {
            required: true,
            message: '请选择',
            trigger: ['blur']
        }
    ]
}

const initSortable = () => {
    const el = tableRef.value.$el.querySelector('.el-table__body tbody')
    Sortable.create(el, {
        animation: 150,
        handle: '.move-icon',
        onEnd: ({ newIndex, oldIndex }: any) => {
            console.log(newIndex, oldIndex)
            const arr = modelList.value
            const currRow = arr.splice(oldIndex, 1)[0]
            arr.splice(newIndex, 0, currRow)
            formData.form = []
            nextTick(() => {
                modelList.value = arr
            })
        }
    })
}

/**
 * 获取初始化数据
 */
const getData = async () => {
    const data = await getDrawBillingConfig()
    formData.value = data
    modelList.value = data.billing_config
}

const submit = async () => {
    await formRef.value.validate()
    formData.value.billing_config = modelList.value
    await setDrawBillingConfig(formData.value)
    getData()
}

onMounted(async () => {
    await getData()
    nextTick(() => {
        initSortable()
    })
})

defineExpose({ submit })
</script>

<style scoped lang="scss"></style>
