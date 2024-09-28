<template>
    <popup width="700px" title="详情" ref="popRef">
        <el-form-item label="审核状态" label-width="120px">
            <div class="text-error">{{ replyRecord.censor_status_desc }}</div>
        </el-form-item>
        <el-form-item label="审核提示" label-width="120px">
            <span
                v-for="(item, index) in replyRecord.censor_result_desc"
                :key="index"
                class="mb-[20px]"
            >
                {{ item }}
            </span>
        </el-form-item>
    </popup>
</template>

<script setup lang="ts">
import popup from '@/components/popup/index.vue'

const popRef = shallowRef()
const replyRecord = ref({})

const open = (reply: any) => {
    popRef.value?.open()
    replyRecord.value = reply
}

defineExpose({ open })
</script>

<style scoped lang="scss"></style>
