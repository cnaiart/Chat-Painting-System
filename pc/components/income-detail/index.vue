<template>
    <ElDialog
        v-model="popShow"
        width="1000px"
        title="收益明细"
        :close-on-click-modal="false"
        @close="closePop"
    >
        <el-form inline label-width="80px">
            <el-form-item label="变动类型">
                <el-select v-model="formData.change_type">
                    <el-option label="全部" value></el-option>
                    <el-option
                        v-for="(value, key, index) in changeType"
                        :key="index"
                        :label="value"
                        :value="key"
                    ></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="结算时间">
                <DaterangePicker
                    v-model:startTime="formData.start_time"
                    v-model:endTime="formData.end_time"
                >
                </DaterangePicker>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="resetPage">查询</el-button>
            </el-form-item>
        </el-form>
        <el-table :data="pager.lists">
            <el-table-column
                label="来源订单"
                prop="source_sn"
            ></el-table-column>
            <el-table-column label="金额变动（元）" prop="change_amount_desc">
                <template #default="{ row }">
                    <div :class="{ 'text-primary': row.action == 1 }">
                        {{ row.change_amount_desc }}
                    </div>
                </template>
            </el-table-column>
            <el-table-column
                label="剩余金额（元）"
                prop="left_amount"
            ></el-table-column>
            <el-table-column
                label="变动类型"
                prop="change_type_desc"
            ></el-table-column>
            <el-table-column
                label="结算时间"
                prop="create_time"
            ></el-table-column>
        </el-table>
        <div class="flex justify-end mt-4">
            <pagination v-model="pager" @change="getLists" />
        </div>
    </ElDialog>
</template>

<script setup lang="ts">
import { MoneyChangeType, MoneyList } from '@/api/promotion'
import { usePaging } from '@/utils/usePaging'
import {
    ElDialog,
    ElForm,
    ElFormItem,
    ElInput,
    ElCheckbox,
    ElButton,
    ElSelect,
    ElOption,
    ElDatePicker,
    ElTable,
    ElTableColumn
} from 'element-plus'

const emit = defineEmits(['closePop'])
const changeType = ref([])

//弹框显示/隐藏
const popShow = ref(false)

const formData = ref({
    type: 'money',
    change_type: '',
    start_time: '',
    end_time: ''
})

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: MoneyList,
    params: formData.value
})

//获取变动类型
const getChangeType = async () => {
    changeType.value = await MoneyChangeType()
}

//打开弹框
const open = () => {
    popShow.value = true
    getChangeType()
    getLists()
}

//关闭弹框
const closePop = () => {
    popShow.value = false
    emit('closePop')
}

defineExpose({ open })
</script>

<style scoped lang="scss"></style>
