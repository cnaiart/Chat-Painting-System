<template>
    <div>
        <NuxtLayout name="default">
            <ElScrollbar class="bg-white rounded-[12px]">
                <div class="w-full p-[20px]">
                    <div class="text-[18px] font-medium">意见反馈</div>
                    <div class="mt-4">
                        <ElForm :model="formData" label-width="120px">
                            <ElFormItem label="反馈类型">
                                <ElRadioGroup v-model="formData.type">
                                    <ElRadio label="1">故障</ElRadio>
                                    <ElRadio label="2">建议</ElRadio>
                                    <ElRadio label="3">投诉</ElRadio>
                                </ElRadioGroup>
                            </ElFormItem>
                            <ElFormItem label="反馈内容" class="is-required">
                                <div class="w-[420px]">
                                    <ElInput
                                        v-model="formData.content"
                                        type="textarea"
                                        :rows="5"
                                        class="w-[420px]"
                                        placeholder="请输入反馈内容"
                                    ></ElInput>
                                </div>
                            </ElFormItem>
                            <ElFormItem label="联系方式" class="is-required">
                                <div class="w-[420px]">
                                    <ElInput
                                        v-model="formData.mobile"
                                        class="w-[420px]"
                                        placeholder="请输入联系方式"
                                    ></ElInput>
                                </div>
                            </ElFormItem>
                            <ElFormItem label="">
                                <ElButton type="primary" @click="handleSubmit">
                                    <template #default> 立即提交 </template>
                                </ElButton>
                            </ElFormItem>
                        </ElForm>
                    </div>
                </div>
            </ElScrollbar>
        </NuxtLayout>
    </div>
</template>
<script setup lang="ts">
import { addopinion } from '@/api/my'

const formData = ref({
    type: '1',
    content: '',
    mobile: ''
})
const handleSubmit = async () => {
    await addopinion(formData.value)
    formData.value = {
        type: '1',
        content: '',
        mobile: ''
    }
}
definePageMeta({
    auth: true,
    layout: false
})
</script>
