<template>
    <div class="chat-message flex">
        <!-- My message -->
        <div v-if="type == 1" class="ml-auto">
            <div class="message-contain message-contain--my">
                <slot name="my"></slot>
            </div>
            <div
                class="flex items-center justify-end text-[#999999] pr-4 pt-2 cursor-pointer"
                @click="copyMyContent"
            >
                <el-icon><CopyDocument /></el-icon>
                <span class="text-xs ml-[4px]">复制</span>
            </div>
        </div>

        <!-- Avatar -->
        <el-avatar class="message-avatar" :size="40" :src="avatar"></el-avatar>

        <!-- His message -->
        <div class="flex-1 min-w-0 max-w-[750px]" v-if="type == 2">
            <div v-if="time" class="h-[20px] ml-[10px] mb-[10px] text-tx-secondary">
                {{ time }}
            </div>
            <div class="message-contain message-contain--his flex flex-col">
                <div>
                    <slot name="rob"></slot>
                </div>
                <div
                    class="flex items-center text-tx-secondary text-sm mt-[8px]"
                    v-if="loading"
                >
                    <div
                        class="flex items-center"
                        style="animation: loading-rotate 2s linear infinite"
                    >
                        <Icon name="el-icon-Loading" class="circular" />
                    </div>
                    <div class="ml-[5px]">加载中，请稍等</div>
                    <!-- <ElButton
                        type="primary"
                        link
                        :disabled="!stopping"
                        @click="emit('close')"
                    >
                        <div class="text-[#FFA621]">停止回答</div>
                    </ElButton> -->
                </div>
                <div class="action-btns" v-if="!loading">
                    <el-tooltip
                        v-if="showRewriteBtn"
                        effect="dark"
                        content="重写"
                        placement="top"
                    >
                        <ElButton class="mt-2" circle @click="emit('rewrite')">
                            <template #icon>
                                <Icon name="el-icon-RefreshRight" />
                            </template>
                        </ElButton>
                    </el-tooltip>
                    <el-tooltip
                        v-if="showCollectBtn"
                        effect="dark"
                        placement="top"
                        :content="isCollet ? '取消收藏' : '收藏'"
                    >
                        <ElButton class="mt-2" circle @click="collect">
                            <template #icon>
                                <Icon
                                    v-if="isCollet"
                                    color="#FBAE00"
                                    size="18"
                                    name="el-icon-StarFilled"
                                />
                                <Icon v-else name="el-icon-Star" />
                            </template>
                        </ElButton>
                    </el-tooltip>
                    <el-tooltip
                        v-if="showPoster"
                        effect="dark"
                        content="生成海报"
                        placement="top"
                    >
                        <ElButton class="mt-2" circle @click="emit('poster')">
                            <template #icon>
                                <Icon name="el-icon-Picture" />
                            </template>
                        </ElButton>
                    </el-tooltip>
                </div>
            </div>

            <div class="ml-[10px]">
                <slot name="footer" />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ElAvatar, ElButton } from 'element-plus'
import { CopyDocument } from '@element-plus/icons-vue'
const emit = defineEmits([
    'collect',
    'close',
    'copyContent',
    'copyMyContent',
    'poster',
    'rewrite'
])
const props = defineProps({
    loading: {
        type: Boolean,
        default: false
    },
    stopping: {
        type: Boolean,
        default: false
    },
    avatar: {
        type: String,
        default: ''
    },
    time: {
        type: String,
        default: ''
    },
    type: {
        type: Number,
        default: null
    },
    showCopyBtn: {
        type: Boolean,
        default: true
    },
    showCollectBtn: {
        type: Boolean,
        default: true
    },
    showRewriteBtn: {
        type: Boolean,
        default: false
    },
    isCollet: {
        type: Number
    },
    showPoster: {
        type: Boolean,
        default: true
    }
})
//收藏
const collect = () => {
    emit('collect')
}
//复制我的
const copyMyContent = () => {
    emit('copyMyContent')
}
//复制
const copyContent = () => {
    emit('copyContent')
}
</script>

<style lang="scss" scoped>
.chat-message {
    // display: flex;
    flex: 1;
    min-width: 0;
    .message-avatar {
        min-width: 40px;
    }

    .message-contain {
        padding: 10px 12px;
        border-radius: 8px;
        max-width: 750px;
    }

    .message-contain--my {
        margin-right: 10px;
        background-color: var(--el-color-primary);
        color: var(--color-btn-text);
        margin-left: auto;
    }

    .message-contain--his {
        margin-left: 10px;
        background-color: var(--el-bg-color-page);
        flex: 1;
        min-width: 0;
        position: relative;
        &:hover {
            .action-btns {
                display: flex;
            }
        }
        .action-btns {
            display: none;
            position: absolute;
            justify-content: flex-end;
            right: 0;
            bottom: 0;
            width: 100%;
            transform: translateY(100%);
        }
    }
}
</style>
