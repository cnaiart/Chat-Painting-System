<template>
    <!-- #ifndef MP-WEIXIN -->
    <view class="floating-menu" @touchstart.stop @touchend.stop @touchmove.stop>
    <!-- #endif -->
    <!-- #ifdef MP-WEIXIN -->
    <view class="floating-menu">
    <!-- #endif -->
        <movable-area class="movable-area" :scale-area="false">
            <movable-view
                class="movable-view"
                :class="!menuData.isRemove ? 'animation-info' : ''"
                style="pointer-events: auto"
                @touchstart="touchstart"
                @touchend="touchend"
                @change="onChange"
                direction="all"
                inertia="true"
                :x="menuData.x"
                :y="menuData.y"
                :disabled="disabled"
                :out-of-bounds="true"
                :damping="200"
                :friction="100"
            >
                <view class="item-main" @click="openMenu">
                    <u-icon :name="UnfoldIcon" size="36" />
                </view>
                <view
                    v-if="menuData.showBtn"
                    class="menu-box"
                    :class="menuData.isLeft ? 'leftOut1' : 'rightOut1'"
                >
                    <view
                        v-for="(item, index) in list"
                        :key="index"
                        @click="onJump(item)"
                        class="item-main"
                    >
                        <u-icon :name="item.icon" size="36" />
                    </view>
                </view>
            </movable-view>
        </movable-area>
    </view>
</template>

<script lang="ts" setup>
import { getCurrentInstance, nextTick, onMounted, reactive } from 'vue'
import HomeIcon from '@/static/images/floating_menu/home.png'
import UserIcon from '@/static/images/floating_menu/user.png'
import BackIcon from '@/static/images/floating_menu/back.png'
import UnfoldIcon from '@/static/images/floating_menu/unfold.png'
import router from '@/router'

const props = withDefaults(
    defineProps<{
        list?: any
        disabled?: boolean
        bottom?: number
        right?: number
    }>(),
    {
        list: [
            {
                icon: HomeIcon,
                pages: '/pages/index/index'
            },
            {
                icon: UserIcon,
                pages: '/pages/user/user'
            },
            {
                icon: BackIcon,
                pages: null
            }
        ],
        disabled: false,
        bottom: 160,
        right: 0
    }
)

const menuData = reactive({
    left: 0,
    top: 0,
    isRemove: true,
    windowWidth: 0,
    windowHeight: 0,
    btnWidth: 0,
    btnHeight: 0,
    x: 10000,
    y: 10000,
    old: {
        x: 0,
        y: 0
    },
    showBtn: false,
    isLeft: false
})
const ctx = getCurrentInstance()

onMounted(async () => {
    await nextTick()
    const sysInfo = uni.getSystemInfoSync()
    menuData.windowWidth = sysInfo.windowWidth
    menuData.windowHeight = sysInfo.windowHeight
    try {
        uni.createSelectorQuery()
            .in(ctx)
            .select('.movable-view')
            .boundingClientRect((rect: any) => {
                menuData.btnWidth = rect.width
                menuData.btnHeight = rect.height
                menuData.x = menuData.old.x
                menuData.y = menuData.old.y
                nextTick(() => {
                    menuData.x =
                        menuData.windowWidth - menuData.btnWidth - props.right
                    menuData.y =
                        menuData.windowHeight - menuData.btnHeight - props.bottom
                })
            })
            .exec()
    } catch (e) {
        console.log(e)
    }
})

//移动按钮
const onChange = (e: any) => {
    menuData.old.x = e.detail.x
    menuData.old.y = e.detail.y
}

//开始移动
const touchstart = (e: any) => {
    menuData.isRemove = true
}
//结束移动
const touchend = (e: any) => {
    if (!props.disabled && menuData.old.x) {
        menuData.x = menuData.old.x
        menuData.y = menuData.old.y
        const bWidth = (menuData.windowWidth - menuData.btnWidth) / 2
        if (menuData.x < 0 || (menuData.x > 0 && menuData.x <= bWidth)) {
            nextTick(() => {
                menuData.x = 0
                menuData.isLeft = true
            })
        } else {
            nextTick(() => {
                menuData.x = menuData.windowWidth - menuData.btnWidth
                menuData.isLeft = false
            })
        }
        menuData.isRemove = false
    }
}

const openMenu = () => {
    menuData.showBtn = !menuData.showBtn
}

//点击菜单
const onJump = (item: any) => {
    if (item.pages) {
        router.switchTab(item.pages)
    } else {
        uni.navigateBack({
            delta: 1,
            fail: () => {
                uni.$u.toast('已经是第一个页面了')
            }
        })
    }
}
</script>

<style scoped>
.movable-view {
    width: 90rpx;
    height: 90rpx;
    background: white;
    box-shadow: 0 4rpx 12rpx 0 rgba(0, 0, 0, 0.3);
    border-radius: 50rpx;
    align-items: center;
    justify-content: center;
    position: relative;
}

.menu-box {
    position: absolute;
    top: 0;
    display: flex;
    width: 272rpx;
    height: 90rpx;
    padding: 0 20rpx;
    background: white;
    box-shadow: 0 4rpx 12rpx 0 rgba(0, 0, 0, 0.3);
    border-radius: 50rpx;
}
.leftOut1 {
    left: 110rpx;
    animation: leftOut 0.4s;
}
.rightOut1 {
    right: 110rpx;
    animation: rightOut 0.4s;
}

.item-main {
    width: 90rpx;
    height: 90rpx;
    display: flex;
    align-items: center;
    justify-content: center;
}

@keyframes leftOut {
    0% {
        opacity: 0;
        left: 50rpx;
    }

    25% {
        opacity: 0;
        left: 50rpx;
    }

    50% {
        opacity: 0.5;
        left: 110rpx;
    }

    75% {
        opacity: 1;
        left: 100rpx;
    }

    100% {
        opacity: 1;
        left: 110rpx;
    }
}

@keyframes rightOut {
    0% {
        opacity: 0;
        right: 50rpx;
    }

    25% {
        opacity: 0;
        right: 50rpx;
    }

    50% {
        opacity: 0.5;
        right: 110rpx;
    }

    75% {
        opacity: 1;
        right: 100rpx;
    }

    100% {
        opacity: 1;
        right: 110rpx;
    }
}

.movable-area {
    width: 100%;
    height: 100%;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 999999 !important;
    pointer-events: none;
}
</style>
