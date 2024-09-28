<template>
    <div class="edit-popup">
        <popup
            ref="popupRef"
            title="开通分销商"
            :async="true"
            width="950px"
            @confirm="handleSubmit"
            @close="handleClose"
            cancelButtonText="取消开通"
            confirmButtonText="确认开通"
        >
            <el-form
                ref="formRef"
                class="mb-[-16px]"
                :model="queryParams"
                :inline="true"
                @submit.native.prevent
            >
                <el-form-item label="用户信息">
                    <el-input
                        class="w-[280px]"
                        v-model="queryParams.keyword"
                        placeholder="请输入用户ID编号/用户昵称"
                        clearable
                    />
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                </el-form-item>
            </el-form>
            <el-table
                size="large"
                v-loading="pager.loading"
                :data="pager.lists"
                height="500px"
                class="mt-4"
                @selection-change="handleSelectionChange"
            >
                <el-table-column type="selection" width="55" />
                <el-table-column label="用户ID" prop="sn" width="120" />
                <el-table-column label="用户昵称" prop="nickname">
                    <template #default="{ row }">
                        <div class="flex items-center">
                            <el-avatar :src="row.avatar" :size="50" class="mr-2" />{{
                                row.nickname
                            }}
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="手机号码" prop="mobile" />
                <el-table-column label="是否分销商" width="120">
                    <template #default>否</template>
                </el-table-column>
                <el-table-column label="注册时间" prop="create_time" />
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </popup>
    </div>
</template>
<script lang="ts" setup>
import type { FormInstance } from 'element-plus'
import Popup from '@/components/popup/index.vue'
import { usePaging } from '@/hooks/usePaging'
import { getUserList } from '@/api/consumer'
import { adddistributor } from '@/api/marketing/distribution'

const queryParams = reactive({
    keyword: '',
    is_distribution: 0
})
const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getUserList,
    params: queryParams
})
const emit = defineEmits(['success', 'close'])
const popupRef = shallowRef<InstanceType<typeof Popup>>()

const open = () => {
    popupRef.value?.open()
    getLists()
}
const handleClose = () => {
    emit('close')
}
const handleSubmit = async () => {
    adddistributor({ user_ids: multipleSelection })
    emit('close')
}
let multipleSelection: any = []
const handleSelectionChange = (val: any) => {
    multipleSelection = val.map((item: any) => {
        return item.id
    })
    console.log(multipleSelection)
}
defineExpose({
    open
})
</script>
