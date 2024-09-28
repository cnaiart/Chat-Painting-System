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
                    <el-radio-group v-model="formData.member_model">
                        <el-radio :label="formData.member_model">
                            {{ formData.member_model }}
                        </el-radio>
                    </el-radio-group>
                    <div class="form-tips !text-[14px]">在「AI模型」选中的默认AI接口</div>
                </div>
            </el-form-item>
            <el-form-item label="模型设置" v-if="formData.is_open">
                <el-table ref="tableRef" size="large" row-key="key" :data="formData.billing_config">
                    <el-table-column width="50">
                        <template #default>
                            <div class="move-icon cursor-move">
                                <Icon name="el-icon-Rank" />
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column label="序号" width="60">
                        <template #default="{ $index }">
                            {{ $index + 1 }}
                        </template>
                    </el-table-column>
                    <el-table-column label="模型名称" prop="name" min-width="150">
                        <template #default="{ row }">
                            <el-select
                                class="w-full"
                                :model-value="row.key"
                                filterable
                                @change="modelChange($event, row)"
                            >
                                <el-option
                                    v-for="(item, key) in chatModelOptions"
                                    :value="item.key"
                                    :label="item.name"
                                    :key="item.name"
                                    :disabled="item.disabled && item.key !== row.key"
                                ></el-option>
                            </el-select>
                        </template>
                    </el-table-column>
                    <el-table-column label="别名" prop="alias" min-width="160">
                        <template #default="{ row }">
                            <el-input v-model="row.alias" placeholder="为空时显示默认名字" />
                        </template>
                    </el-table-column>
                    <el-table-column label="消耗对话条数" prop="balance" min-width="80">
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
                                    content="开启后，用户开通会员，使用该模型对话不消耗条数"
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
                    <el-table-column label="操作" width="100">
                        <template #default="{ row, $index }">
                            <ElButton type="danger" link @click="modelDelete($index)"
                                >删除</ElButton
                            >
                        </template>
                    </el-table-column>
                </el-table>
            </el-form-item>
            <el-form-item
                v-if="
                    chatModelOptions.length !== formData.billing_config.length && formData.is_open
                "
            >
                <ElButton type="primary" link @click="modelAdd">+添加模型</ElButton>
            </el-form-item>
        </el-form>
    </div>
</template>

<script setup lang="ts">
import { getChatBillingConfig, setChatBillingConfig } from '@/api/setting/ai_model_cost'
import { cloneDeep } from 'lodash-es'
import Sortable from 'sortablejs'

const props = defineProps({
    popRef: {
        type: Object,
        default: {} as any
    }
})

//表单ref
const formRef = shallowRef()
const tableRef = shallowRef()
const initSortable = () => {
    const el = tableRef.value.$el.querySelector('.el-table__body tbody')
    Sortable.create(el, {
        animation: 150,
        handle: '.move-icon',
        onEnd: ({ newIndex, oldIndex }: any) => {
            console.log(newIndex, oldIndex)
            const arr = formData.value.billing_config
            const currRow = arr.splice(oldIndex, 1)[0]
            arr.splice(newIndex, 0, currRow)
            formData.value.billing_config = arr
        }
    })
}

//表单数据
const formData = ref({
    is_open: 0,
    member_model: '',
    billing_config: [],
    chat_model_lists: []
})

const modelAdd = () => {
    const item = chatModelOptions.value.find((item) => !item.disabled)

    if (item) {
        ;(formData.value.billing_config as any[]).push(cloneDeep(item))
    }
}

const modelDelete = (index: number) => {
    formData.value.billing_config.splice(index, 1)
}

const chatModelOptions = computed<any[]>(() => {
    const chatModel = Object.values(cloneDeep(formData.value.chat_model_lists)) || []
    chatModel.forEach((model: any) => {
        const index = formData.value.billing_config.findIndex((item: any) => item.key === model.key)
        if (index !== -1) {
            model.disabled = true
        }
    })
    return chatModel
})
const modelChange = (key: any, row: any) => {
    const model = chatModelOptions.value.find((item) => item.key === key)
    row.name = model.name
    row.key = model.key
}
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

/**
 * 获取初始化数据
 */
const getData = async () => {
    const data = await getChatBillingConfig()
    formData.value = data
}
// 数组转对象

const submit = async () => {
    await formRef.value.validate()
    await setChatBillingConfig(formData.value)
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
