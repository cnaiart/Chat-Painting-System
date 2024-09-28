<template>
    <Popup ref="popRef" title="1" width="800px">
        <template #header>
            <span class="font-medium text-xl">会员开通记录</span>
            <span class="text-[#999] text-xs ml-4">优先显示排序值最大的会员套餐，如果没有排序值就显示到期时间最长的那个套餐</span>
        </template>
        <el-table :data="pager.lists">
            <el-table-column label="套餐名称" prop="package_name"></el-table-column>
            <el-table-column label="到期时间" prop="member_end_time_desc"></el-table-column>
            <el-table-column label="购买来源" prop="channel_desc"></el-table-column>
            <el-table-column label="退款状态" prop="refund_status_desc"></el-table-column>
            <el-table-column label="排序" prop="refund_status_desc">
                <template #default="{ row }">
                    <div class="flex">
                        {{ row.sort || '0' }}
                        <popover-input
                            class="ml-[10px]"
                            type="number"
                            @confirm="handleSort($event, row.id)"
                            v-perms="['user.user/userMemberSort']"
                        >
                            <el-button type="primary" link>
                                <icon name="el-icon-EditPen" />
                            </el-button>
                        </popover-input>
                    </div>
                </template>
            </el-table-column>
            <el-table-column label="操作人" prop="operate_desc"></el-table-column>
            <el-table-column label="记录时间" prop="create_time"></el-table-column>
        </el-table>
        <div class="flex justify-end mt-4">
            <pagination v-model="pager" @change="getLists" />
        </div>
    </Popup>
</template>

<script lang="ts" setup>
import Popup from '@/components/popup/index.vue'
import { getOpenVipRecord, userMemberSort } from '@/api/consumer'
import { usePaging } from '@/hooks/usePaging'

const popRef = shallowRef()
const userId = ref({
    id: ''
})

const open = async (id: any) => {
    await nextTick()
    popRef.value?.open()
    userId.value.id = id
    getLists()
}

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getOpenVipRecord,
    params: userId.value
})

//获取数据
const handleSort = async (sort: number, id: number) => {
    try {
        await userMemberSort({
            user_member_id: id,
            sort: sort
        })
        getLists()
    } catch (e) {
        console.log('修改排序失败', e)
    }
}

defineExpose({ open })
</script>
