<template>
    <popup
        ref="popupRef"
        title="上级分销商调整"
        width="500px"
        :async="true"
        @close="close"
        @confirm="handleConfirm"
    >
        <div class="pr-8">
            <el-form ref="formRef" :model="formData" label-width="120px" @submit.native.prevent>
                <el-form-item label="用户信息"> {{ userInfo.nickname }}({{ userInfo.sn }}) </el-form-item>

                <el-form-item label="当前邀请人">
                    {{ userInfo.inviter_name || '-' }}
                </el-form-item>
                <el-form-item label="调整方式" prop="adjust_type">
                    <el-radio-group v-model="formData.adjust_type">
                        <el-radio :label="1">指定邀请人</el-radio>
                        <el-radio :label="2">设置邀请人为系统</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label="选择邀请人">
                    <UserSelect
                        v-model="formData.leader_id"
                        v-model:select-data="selectData"
                        type="single"
                    >
                        <template #popup>
                            <div class="flex">
                                <span class="mr-2" v-if="selectData?.id">
                                    {{ selectData!.nickname || '' }}({{ selectData!.sn }})
                                </span>
                                <el-button type="primary" link>
                                    选择用户
                                </el-button>
                            </div>
                        </template>
                    </UserSelect>
                </el-form-item>
            </el-form>
        </div>
    </popup>
</template>
<script lang="ts" setup>
import type Popup from '@/components/popup/index.vue'
import type { FormInstance } from 'element-plus'
import { adjustLeader } from '@/api/consumer'

type AdjustLeaderType = {
    id: number | string
    adjust_type: number
    leader_id: number | string
}

const emit = defineEmits<{
    (event: 'success', value: void): void
    (event: 'close', value: void): void
}>()
const formRef = shallowRef<FormInstance>()
const props = defineProps({
    userInfo: {
        type: Object,
        default: {}
    },
    title: {
        type: String,
        required: true
    },
    show: {
        type: Boolean,
        required: true
    },
    value: {
        type: [Number, String],
        required: true
    }
})
const formData = reactive<AdjustLeaderType>({
    id: '',
    adjust_type: 1, //变动类型 1-指定 2-系统
    leader_id: ''
})
const selectData = ref([])
const popupRef = shallowRef<InstanceType<typeof Popup>>()

const handleConfirm = async () => {
    await adjustLeader(formData)
    emit('success')
    close()
}

const close = () => {
    emit('close')
    popupRef.value?.close()
}

const open = (id: number) => {
    formData.id = id
    popupRef.value?.open()
}

defineExpose({ open })
</script>