<template>
    <div class="upload">
        <el-upload
            v-model:file-list="fileList"
            ref="uploadRefs"
            :action="action"
            :multiple="multiple"
            :limit="limit"
            :show-file-list="false"
            :headers="headers"
            :data="data"
            :on-progress="handleProgress"
            :on-success="handleSuccess"
            :on-exceed="handleExceed"
            :on-error="handleError"
            :accept="getAccept"
        >
            <slot />
        </el-upload>
        <el-dialog
            v-if="showProgress && fileList.length"
            v-model="visible"
            title="上传进度"
            :close-on-click-modal="false"
            width="500px"
            :modal="false"
            @close="handleClose"
        >
            <div class="file-list p-4">
                <template v-for="(item, index) in fileList" :key="index">
                    <div class="mb-5">
                        <div>{{ item.name }}</div>
                        <div class="flex-1">
                            <el-progress :percentage="parseInt(item.percentage)" />
                        </div>
                    </div>
                </template>
            </div>
        </el-dialog>
    </div>
</template>

<script lang="ts">
import { computed, defineComponent, ref, shallowRef } from 'vue'
import useUserStore from '@/stores/modules/user'
import config from '@/config'
import feedback from '@/utils/feedback'
import type { ElUpload } from 'element-plus'
import { RequestCodeEnum } from '@/enums/requestEnums'
export default defineComponent({
    components: {},
    props: {
        // 上传文件类型
        type: {
            type: String,
            default: 'image'
        },
        // 是否支持多选
        multiple: {
            type: Boolean,
            default: true
        },
        // 多选时最多选择几条
        limit: {
            type: Number,
            default: 10
        },
        // 上传时的额外参数
        data: {
            type: Object,
            default: () => ({})
        },
        // 是否显示上传进度
        showProgress: {
            type: Boolean,
            default: false
        }
    },
    emits: ['change', 'error', 'success'],
    setup(props, { emit }) {
        const userStore = useUserStore()
        const uploadRefs = shallowRef<InstanceType<typeof ElUpload>>()
        const action = ref(`${config.baseUrl}${config.urlPrefix}/upload/${props.type}`)
        const headers = computed(() => ({
            token: userStore.token,
            version: config.version
        }))
        const visible = ref(false)
        const fileList = ref<any[]>([])

        const handleProgress = (event: any, file: any, fileLists: any[]) => {
            visible.value = true
        }
        let uploadLen = 0
        const handleSuccess = (response: any, file: any, fileLists: any[]) => {
            uploadLen++
            if (uploadLen == fileList.value.length) {
                uploadLen = 0
                fileList.value = []
            }
            emit('change', file)
            if (response.code == RequestCodeEnum.SUCCESS) {
                emit('success', response)
            }
            if (response.code == RequestCodeEnum.FAIL && response.msg) {
                feedback.msgError(response.msg)
            }
        }
        const handleError = (event: any, file: any) => {
            uploadLen++
            if (uploadLen == fileList.value.length) {
                uploadLen = 0
                fileList.value = []
            }
            feedback.msgError(`${file.name}文件上传失败`)
            uploadRefs.value?.abort(file)
            visible.value = false
            emit('change', file)
            emit('error', file)
        }
        const handleExceed = () => {
            feedback.msgError(`超出上传上限${props.limit}，请重新上传`)
        }
        const handleClose = () => {
            fileList.value = []
            visible.value = false
        }

        const getAccept = computed(() => {
            switch (props.type) {
                case 'image':
                    return '.jpg,.png,.gif,.jpeg'
                case 'video':
                    return '.wmv,.avi,.mpg,.mpeg,.3gp,.mov,.mp4,.flv,.rmvb,.mkv'
                default:
                    return '*'
            }
        })
        return {
            uploadRefs,
            action,
            headers,
            visible,
            fileList,
            getAccept,
            handleProgress,
            handleSuccess,
            handleError,
            handleExceed,
            handleClose
        }
    }
})
</script>

<style lang="scss"></style>
