<template>
    <div class="h-full pb-[15px]">
        <div class="h-full flex flex-col">
            <div class="flex">
                <el-page-header @back="emit('history')">
                    <template #content>
                        <span class="text-xl font-medium"> 生成记录 </span>
                    </template>
                </el-page-header>
                <div
                    class="bg-[#f2f2f2] text-sm px-[15px] py-[2px] rounded-full cursor-pointer ml-[10px]"
                    @click="deleteChatLog()"
                >
                    清空记录
                </div>
            </div>
            <div class="flex-1 min-h-0 pt-[10px] mx-[-10px]">
                <ElScrollbar v-if="chatContentList.length">
                    <div class="flex flex-wrap items-stretch">
                        <div
                            class="2xl:w-1/4 xl:w-1/3 w-1/2 p-[8px]"
                            v-for="item in chatContentList"
                            :key="item.id"
                        >
                            <div
                                class="p-[15px] bg-white rounded-[10px] h-full record-item"
                            >
                                <div class="text-lg font-medium line-clamp-1">
                                    帮我生成：{{ item.ask }}
                                </div>
                                <div
                                    class="cursor-pointer"
                                    v-for="(text, index) in item.reply"
                                    :key="index"
                                    @click="emit('view', text)"
                                >
                                    <div
                                        class="whitespace-pre-line line-clamp-5 my-[10px] h-[105px]"
                                    >
                                        {{ text }}
                                    </div>
                                    <div class="flex items-center">
                                        <div
                                            class="mr-auto text-tx-secondary text-sm"
                                        >
                                            {{ item.create_time }}
                                        </div>
                                        <el-dropdown
                                            @command="
                                                handleCommand(
                                                    $event,
                                                    item.id,
                                                    text
                                                )
                                            "
                                        >
                                            <div
                                                class="cursor-pointer text-tx-secondary text-2xl"
                                                @click.stop
                                            >
                                                <!-- <icon name="el-icon-MoreFilled" /> -->
                                                ...
                                            </div>
                                            <template #dropdown>
                                                <el-dropdown-menu>
                                                    <el-dropdown-item
                                                        command="view"
                                                    >
                                                        查看
                                                    </el-dropdown-item>
                                                    <el-dropdown-item
                                                        command="delete"
                                                    >
                                                        删除
                                                    </el-dropdown-item>
                                                </el-dropdown-menu>
                                            </template>
                                        </el-dropdown>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </ElScrollbar>
                <div
                    v-else
                    class="h-full flex flex-col items-center justify-center"
                >
                    <el-empty :image="create_record_null" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { cleanChat, getchatLog } from '~/api/chat'
import create_record_null from '@/assets/images/create_record_null.png'
const emit = defineEmits<{
    (event: 'view', value: string): void
    (event: 'history'): void
}>()

const { data: chatContentList, refresh } = await useAsyncData(
    () => getchatLog({ type: 4, page_type: 0 }),
    {
        transform(data) {
            return data.lists
        },
        default() {
            return []
        }
    }
)

const handleCommand = (command: string, id: number, text: string) => {
    switch (command) {
        case 'view':
            emit('view', text)
            break
        case 'delete':
            deleteChatLog(id)
            break
    }
}

const deleteChatLog = async (id?: number) => {
    if (!chatContentList.value.length) return
    await feedback.confirm(`确定${id ? '删除' : '清空'}记录？`)
    await cleanChat({ type: 4, id })
    refresh()
}
</script>
<style lang="scss" scoped>
.record-item {
    &:hover {
        .view-btn {
            opacity: 1;
        }
    }
}
</style>
