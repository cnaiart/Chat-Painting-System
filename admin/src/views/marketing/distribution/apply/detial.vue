<template>
    <div class="edit-popup">
        <popup
            ref="popupRef"
            title="分销商申请详情"
            :async="true"
            width="550px"
            @close="handleClose"
            cancelButtonText=""
            confirmButtonText=""
        >
            <el-form ref="formRef" class="mb-[-16px]" :model="formData" label-width="90">
                <el-form-item label="审核状态">
                    <div class="text-success" v-if="formData.status == 2">
                        {{ formData.status_desc }}
                    </div>
                    <div class="text-warning" v-if="formData.status == 1">
                        {{ formData.status_desc }}
                    </div>
                    <div class="text-danger" v-if="formData.status == 3">
                        {{ formData.status_desc }}
                    </div>
                </el-form-item>
                <el-form-item label="用户信息"> {{ formData.nickname }}</el-form-item>
                <el-form-item label="姓名"> {{ formData.name }} </el-form-item>
                <el-form-item label="手机号"> {{ formData.mobile }} </el-form-item>
                <el-form-item label="邀请人"> {{ formData.leader_nickname || '-' }} </el-form-item>
                <el-form-item label="申请时间"> {{ formData.create_time }} </el-form-item>
                <el-form-item label="审核时间"> {{ formData.audit_time }} </el-form-item>
                <el-form-item label="拒绝原因" v-if="formData.status == 3">
                    {{ formData.audit_remark }}
                </el-form-item>
            </el-form>
        </popup>
    </div>
</template>
<script lang="ts" setup>
import Popup from '@/components/popup/index.vue'
import { applydetial } from '@/api/marketing/distribution'

const emit = defineEmits(['success', 'close'])
const popupRef = shallowRef<InstanceType<typeof Popup>>()
const formData = ref<any>({})
const open = async (val: any) => {
    popupRef.value?.open()
    formData.value = await applydetial({ id: val })
}
const handleClose = () => {
    emit('close')
}

defineExpose({
    open
})
</script>
