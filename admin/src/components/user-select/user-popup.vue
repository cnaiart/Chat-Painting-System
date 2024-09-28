<template>
    <popup
        class="inline mr-3"
        :clickModalClose="false"
        title="选择用户"
        :center="true"
        @close="handleClose(productFormRef)"
        @confirm="handleConfirm"
        width="1100px"
    >
        <template #trigger>
            <slot></slot>
        </template>

        <el-form :model="productForm" ref="productFormRef" :inline="true" label-width="auto">
            <el-input
                class="mr-2 ls-input"
                v-model="productForm.keyword"
                placeholder="请输入名称/编号"
            />
            <el-button type="primary" @click="getLists">搜索</el-button>
            <el-button @click="resetParams">重置</el-button>
        </el-form>

        <div class="mt-4">
            <el-table
                ref="tableDataRef"
                :data="pager.lists"
                style="width: 100%"
                height="420px"
            >
                <el-table-column label="选择" width="60">
                    <template #header>
                        <span v-if="type == 'single'">选择</span>
                        <el-checkbox
                            size="large"
                            v-model="selectAll"
                            v-if="type == 'multiple'"
                            :disabled="disabled"
                        ></el-checkbox>
                    </template>
                    <template #default="{ row }">
                        <div class="flex row-center" @click.stop>
                            <el-checkbox
                                :model-value="selectItem(row)"
                                @change="handleSelect($event, row)"
                                size="large"
                                :disabled="disabled"
                            ></el-checkbox>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="用户ID" prop="sn" min-width="100"/>
                <el-table-column label="用户头像" min-width="100">
                    <template #default="{ row }">
                        <div class="flex items-center">
                            <el-image
                                fit="cover"
                                :src="row.avatar"
                                class="flex-none w-[58px] h-[58px]"
                            />
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="用户昵称" min-width="200">
                    <template #default="{ row }">
                        {{ row.nickname }}
                    </template>
                </el-table-column>
                <el-table-column label="累计消费" prop="total_amount" min-width="120"/>
                <el-table-column label="分销资格" min-width="140">
                    <template #default="{ row }">
                        <el-tag type="success" v-if="row?.is_distribution">已开通</el-tag>
                        <el-tag type="warning" v-else>未开通</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="注册时间" prop="create_time" min-width="180"/>
            </el-table>
        </div>

        <div class="flex justify-end mt-5">
            <pagination v-model="pager" @change="getLists" />
        </div>
    </popup>
</template>

<script lang="ts" setup>
import { getUserList } from '@/api/consumer'
import Popup from '@/components/popup/index.vue'
import Pagination from '@/components/pagination/index.vue'
import type { ElForm } from 'element-plus'
import { usePaging } from '@/hooks/usePaging'
import { cloneDeep } from 'lodash'
import { withDefaults, watchEffect, ref, reactive } from 'vue'

interface productFormObj {
    keyword?: string
    status?: number | string
}
type FormInstance = InstanceType<typeof ElForm>
const productFormRef = ref<FormInstance>()
const tableDataRef = ref()

const props = withDefaults(
    defineProps<{
        modelValue: any
        type?: string
        disabled: boolean
        maxNum?: number
    }>(),
    {
        modelValue: [],
        // 类型: 多选(multiple) ｜ 单选(single)
        type: 'single',
        // 禁用
        disabled: false,
        // 最大选择数量
        maxNum: 10
    }
)

const emit = defineEmits(['update:modelValue'])

const selectData = ref<Array<object> | any>(props.modelValue)
const productForm = reactive<productFormObj>({
    keyword: '',
    status: ''
})

const { pager, getLists, resetParams } = usePaging({
    size: 15,
    fetchFun: getUserList,
    params: productForm
})

// 弹窗关闭
const handleClose = (formEl: FormInstance | undefined): void => {
    if (!formEl) return
    formEl.resetFields()
}
const handleConfirm = (): void => {
    // 深度克隆防止数据串到父组件
    emit('update:modelValue', cloneDeep(selectData.value))
}

watch(
    () => props.modelValue,
    (val) => {
        selectData.value = cloneDeep(val)
    },
    { deep: true, immediate: true }
)

const selectAll = computed({
    get: () => {
        const { lists } = pager
        if (!selectData.value) return false
        const ids: any[] = selectData.value.map((item: any) => item.id)
        if (!lists.length) {
            return false
        }
        return lists.every(item => ids.includes(item.id))
    },
    set: (val) => {
        const { lists } = pager
        if (val) {
            for (let i = 0; i < lists.length; i++) {
                const item = lists[i]
                const ids: any[] = selectData.value.map((item: any) => item.id)
                if (!ids.includes(item.id) && selectData.value.length < props.maxNum) {
                    selectData.value.push(item)
                }
            }
        } else {
            lists.forEach(row => { deleteSelectedData(row) })
        }
    }
});

const selectItem = computed(() => {
    return (row: any) => {
        if (props.type == 'single') {
            return selectData.value.id == row.id
        }
        if(!selectData.value) return false
        return selectData.value.some((item: any) => item.id == row.id)
    }
})

const handleSelect = ($event: boolean, row: any) => {
    if (props.type == 'single') {
        if ($event) {
            selectData.value = row
        } else {
            selectData.value = {}
        }
    } else if ($event && selectData.value.length < props.maxNum) {
        selectData.value.push(row)
    } else {
        deleteSelectedData(row)
    }
}

const deleteSelectedData = (row: any) => {
    const index = selectData.value.findIndex((item: any) => item.id == row.id)
    if (index != -1) {
        selectData?.value.splice(index, 1)
    }
}

onMounted(() => {
    getLists()
    selectData.value = [...props.modelValue]
})
</script>

<style lang="scss" scoped>
.ls-input {
    width: 240px;
}
:deep(.el-table__inner-wrapper) {
    height: 365px;
    overflow-y: scroll;
    overflow-x: hidden;
}
</style>
