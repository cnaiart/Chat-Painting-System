<template>
    <view class="user-info mb-[-70rpx]">
        <!-- #ifndef H5 -->
        <u-sticky
            h5-nav-height="0"
            bg-color="transparent"
            @fixed="isFixed = true"
            @unfixed="isFixed = false"
        >
            <u-navbar
                :is-back="false"
                :is-fixed="false"
                :title="navigationBarTitleStore.getTitle"
                :title-color="$theme.navColor"
                :background="{ backgroundColor: getNavBg }"
                :border-bottom="false"
                :title-bold="true"
            >
            </u-navbar>
        </u-sticky>
        <!-- #endif -->
        <view class="flex px-[50rpx] pb-[100rpx] justify-between pt-[40rpx]">
            <view
                v-if="isLogin"
                class="flex items-center"
                @click="navigateTo('/packages/pages/user_set/user_set')"
            >
                <u-avatar :src="user.avatar" :size="120"></u-avatar>
                <view class="text-btn-text ml-[20rpx]">
                    <view class="flex items-center">
                        <view class="text-2xl font-medium">
                            {{ user.nickname }}
                        </view>
                        <view
                            class="flex-none ml-[16rpx] text-xs text-white rounded-[6rpx] px-[10rpx] py-[6rpx]"
                            :class="{
                                'text-[#F8C596]': user.is_member
                            }"
                            :style="{
                                background: user.is_member
                                    ? 'linear-gradient(90.00deg, #484848 0%, #101010 100%)'
                                    : '#4073fa'
                            }"
                        >
                            {{
                                user.is_member
                                    ? user.member_package_name || 'VIP会员'
                                    : '普通用户'
                            }}
                        </view>
                    </view>
                    <view
                        class="text-xs mt-[18rpx]"
                        @click.stop="copy(user.sn)"
                    >
                        用户ID：{{ user.sn }}
                        <text class="underline">复制</text>
                    </view>
                </view>
            </view>
            <navigator
                v-else
                class="flex items-center"
                hover-class="none"
                url="/pages/login/login"
            >
                <u-avatar
                    src="/static/images/user/default_avatar.png"
                    :size="120"
                ></u-avatar>
                <view class="text-btn-text text-3xl ml-[20rpx]">未登录</view>
            </navigator>
        </view>
    </view>
</template>
<script lang="ts" setup>
import { useCopy } from '@/hooks/useCopy'
import { computed, ref } from 'vue'
import { useThemeStore } from '@/stores/theme'
import { useRouter } from 'uniapp-router-next'
// import { useRouter } from 'uniapp-router-next-zm'
import { useNavigationBarTitleStore } from '@/stores/navigationBarTitle'
const navigationBarTitleStore = useNavigationBarTitleStore()
const props = defineProps({
    content: {
        type: Object,
        default: () => ({})
    },
    styles: {
        type: Object,
        default: () => ({})
    },
    user: {
        type: Object,
        default: () => ({})
    },
    isLogin: {
        type: Boolean
    }
})
const router = useRouter()
const { copy } = useCopy()
const isFixed = ref(false)
const themeStore = useThemeStore()
const getNavBg = computed(() => {
    return isFixed.value ? themeStore.primaryColor : 'transparent'
})
const navigateTo = (url: string) => {
    router.navigateTo(url)
}
</script>

<style lang="scss" scoped>
.user-info {
    background: url('../../../static/images/user/user_bg.png'),
        linear-gradient(90deg, $u-type-primary, $u-minor-color);
    background-repeat: no-repeat;
    background-position: bottom;
    background-size: 100%;
}
</style>
