<template>
    <page-meta :page-style="$theme.pageStyle"> </page-meta>
    <page-status :status="status">
        <view class="promotion-center">
            <!-- #ifndef H5 -->
            <!-- <u-sticky
                h5-nav-height="0"
                bg-color="transparent"
                @fixed="isFixed = true"
                @unfixed="isFixed = false"
            >
                <u-navbar
                    :is-fixed="false"
                    title="推广中心"
                    :title-color="$theme.navColor"
                    :background="{ backgroundColor: getNavBg }"
                    :border-bottom="false"
                    :title-bold="true"
                >
                </u-navbar>
            </u-sticky> -->
            <!-- #endif -->
            <!--  已通过  -->
            <content
                v-if="isDistributor && isOpen"
                :user-info="userInfo"
                :account-log="accountLog"
                :nav-lists="navLists"
                :withdraw-config="withdrawConfig"
            />
            <!--  申请表单  -->
            <apply
                v-else-if="!isDistributor && isOpen"
                :apply-detail="applyDetail"
                @reload="getDistributionData"
            />
            <!--  功能未开启  -->
            <view class="mt-[45%]" v-if="!isOpen">
                <u-empty
                    font-size="28"
                    icon-size="300"
                    text="该功能还未开启～"
                    :src="EmptyDistributeCenterImage"
                ></u-empty>
            </view>
        </view>
        <!-- #ifdef H5 -->
        <!--    悬浮菜单    -->
        <floating-menu></floating-menu>
        <!-- #endif -->
    </page-status>
</template>
<script setup lang="ts">
import { getDistributionIndex } from '@/api/promotion'
import { useThemeStore } from '@/stores/theme'
import { onLoad, onPullDownRefresh, onShow } from '@dcloudio/uni-app'
import { computed, ref, shallowRef } from 'vue'
import distribution_orders from '@/packages/static/images/distribution_orders.png'
import invite_friends from '@/packages/static/images/invite_friends.png'
import commission_details from '@/packages/static/images/commission_details.png'
import team_fans from '@/packages/static/images/team_fans.png'
import { PageStatusEnum } from '@/enums/appEnums'
import Content from './components/content.vue'
import Apply from './components/apply.vue'
import EmptyDistributeCenterImage from '@/packages/static/empty/distribute_center.png'
import FloatingMenu from '@/components/floating-menu/floating-menu.vue'

const status = ref(PageStatusEnum.LOADING)
const userInfo = ref<any>({})
const accountLog = ref<any[]>([])
const applyDetail = ref<any>({})
const withdrawConfig = ref<any>({})
const isOpen = ref(false)
const isDistributor = ref(false)
const getDistributionData = async () => {
    const data = await getDistributionIndex()
    userInfo.value = data.user
    accountLog.value = data.account_log
    applyDetail.value = data.apply_detail
    isOpen.value = data.is_open
    withdrawConfig.value = data.withdraw_config
    isDistributor.value = !!userInfo.value.is_distribution
}

const isFixed = ref(false)
const navLists = shallowRef([
    {
        icon: distribution_orders,
        name: '分销订单',
        link: '/packages/pages/distribution_order/distribution_order'
    },
    {
        icon: commission_details,
        name: '佣金明细',
        link: '/packages/pages/earnings_detail/earnings_detail'
    },
    {
        icon: team_fans,
        name: '团队粉丝',
        link: '/packages/pages/team_fans/team_fans'
    },
    {
        icon: invite_friends,
        name: '邀请好友',
        link: '/packages/pages/invite_poster/invite_poster'
    }
])
const themeStore = useThemeStore()
const getNavBg = computed(() => {
    return isFixed.value ? themeStore.primaryColor : 'transparent'
})

onShow(async () => {
    try {
        await getDistributionData()
        status.value = PageStatusEnum.NORMAL
    } catch (error) {
        status.value = PageStatusEnum.ERROR
    }
})

onPullDownRefresh(async () => {
    try {
        await getDistributionData()
        status.value = PageStatusEnum.NORMAL
    } catch (error) {
        status.value = PageStatusEnum.ERROR
    }
    uni.stopPullDownRefresh()
})
</script>
