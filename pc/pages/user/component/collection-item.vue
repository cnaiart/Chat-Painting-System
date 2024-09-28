<template>
    <div class="bg-[#F8F8F8] p-[20px] flex items-center mb-[10px]">
        <div class="flex-1 min-w-0">
            <div class="flex items-center">
                <div class="bg-primary text-white px-[3px] rounded-[3px]">
                    问
                </div>
                <div class="ml-[10px]">
                    {{ item.ask }}
                </div>
            </div>
            <div
                class="flex items-start mt-[30px]"
                v-for="(text, index) in item.reply"
                :key="index"
            >
                <ReplyItem :text="text" />
            </div>
        </div>
        <div
            class="w-[40px] px-[8px] cursor-pointer"
            @click.stop="emit('collect')"
        >
            <div class="mx-auto">
                <Icon
                    name="el-icon-StarFilled"
                    size="24px"
                    color="#FBAE00"
                ></Icon>
            </div>

            <div class="form-tips cancelCollection">取消收藏</div>
        </div>
    </div>
</template>

<script setup lang="ts">
import ReplyItem from './reply-item.vue'
const props = defineProps<{
    item: any
}>()
const emit = defineEmits<{
    (event: 'collect'): void
}>()
const itemRef = shallowRef()
</script>
