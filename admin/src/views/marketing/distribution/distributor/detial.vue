<template>
    <div>
        <!-- Header Start -->
        <el-card shadow="never" class="!border-none">
            <el-page-header content="分销商详情" @back="$router.back()" />
        </el-card>
        <!-- Header End -->
        <!-- body -->
        <el-card shadow="never" class="!border-none mt-[10px]">
            <div class="text-xl font-medium">基本资料</div>
            <el-form ref="formRef" class="ls-form mt-4" :model="formData" label-width="120px">
                <div class="bg-page flex py-5 mb-10 items-center">
                    <div class="basis-40 flex flex-col justify-center items-center">
                        <div class="mb-2 text-tx-regular">用户头像</div>
                        <el-avatar :src="formData.avatar" :size="58" />
                    </div>
                    <div class="basis-40 flex flex-col justify-center items-center">
                        <div class="text-tx-regular">可提现佣金</div>
                        <div class="mt-2 flex items-center">{{ formData.user_money }}</div>
                    </div>
                    <div class="basis-40 flex flex-col justify-center items-center">
                        <div class="text-tx-regular">累计获得佣金</div>
                        <div class="mt-2 flex items-center">{{ formData.total_user_money }}</div>
                    </div>
                    <div class="basis-40 flex flex-col justify-center items-center">
                        <div class="text-tx-regular">下级人数</div>
                        <div class="mt-2 flex items-center">
                            {{ formData.below_num }}
                            <router-link
                                v-perms="['distribution.distributor/status']"
                                :to="{
                                    path: getRoutePath('distribution.distributor/belowLists'),
                                    query: {
                                        id: route.query.id
                                    }
                                }"
                            >
                                <el-button type="primary" link> 查看下级列表</el-button>
                            </router-link>
                        </div>
                    </div>
                    <div class="basis-40 flex flex-col justify-center items-center">
                        <div class="text-tx-regular">下级分销商人数</div>
                        <div class="mt-2 flex items-center">
                            {{ formData.below_distribution_num }}
                        </div>
                    </div>
                    <div class="basis-40 flex flex-col justify-center items-center">
                        <div class="text-tx-regular">分销订单</div>
                        <div class="mt-2 flex items-center">
                            {{ formData.distribution_order_num }}
                        </div>
                    </div>
                </div>
                <el-form-item label="用户信息："> {{ formData.nickname }}</el-form-item>
                <el-form-item label="真实姓名："> {{ formData.real_name || '-' }} </el-form-item>

                <el-form-item label="上级分销商："
                    >{{
                        `${formData.leader_nickname || '系统'}${
                            formData.leader_is_distribution == 1 ? '(分销商)' : ''
                        }`
                    }}
                </el-form-item>
                <el-form-item label="分销状态："
                    ><span
                        :class="formData.distribution_status == 1 ? 'text-success' : 'text-warning'"
                        >{{ formData.distribution_status_desc }}</span
                    >
                </el-form-item>
                <el-form-item label="成为分销时间：">
                    {{ formData.distribution_time }}
                </el-form-item>
            </el-form>

            <el-button
                v-perms="['distribution.distributor/status']"
                @click="handleclick(formData.distribution_status)"
                v-if="formData.distribution_status == 1"
            >
                冻结资格
            </el-button>
            <el-button
                v-perms="['distribution.distributor/status']"
                type="primary"
                @click="handleclick(formData.distribution_status)"
                v-if="formData.distribution_status == 0"
            >
                恢复资格
            </el-button>
        </el-card>
    </div>
</template>
<script setup lang="ts">
import { getRoutePath } from '@/router'
import { getdistributordetail, changedistributor } from '@/api/marketing/distribution'
import feedback from '@/utils/feedback'
const formData = ref<any>({})
const route = useRoute()

const getDetails = async () => {
    const data = await getdistributordetail({
        id: route.query.id
    })
    formData.value = data
}
getDetails()
const handleclick = async (type: number) => {
    await feedback.confirm(
        `确定${type == 1 ? '冻结' : '恢复'}该用户分销资格？${
            type == 1 ? '冻结' : '恢复'
        }后，下级消费${type == 1 ? '不' : ''}会产生奖励`
    )
    await changedistributor({ id: route.query.id })
    getDetails()
}
</script>
