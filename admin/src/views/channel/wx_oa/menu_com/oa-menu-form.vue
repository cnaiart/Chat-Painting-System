<template>
    <el-form ref="menuFormRef" :rules="rules" :model="menuForm" label-width="100px">
        <!-- 菜单名称 -->
        <el-form-item :label="modular === 'master' ? '主菜单名称' : '子菜单名称'" prop="name">
            <div>
                <el-input v-model="menuForm.name" />
                <div v-if="modular === 'master'" class="form-tips">
                    中文最多5个字符，英文字符最多16个字符
                </div>
                <div v-else class="form-tips">中文最多20个字符，英文字符最多60个字符</div>
            </div>
        </el-form-item>

        <!-- 菜单类型 -->
        <el-form-item label="主菜单类型" prop="menuType" v-if="modular === 'master'">
            <el-radio-group v-model="menuForm.menuType">
                <el-radio :label="false">不配置子菜单</el-radio>
                <el-radio :label="true">配置子菜单</el-radio>
            </el-radio-group>
        </el-form-item>
        <el-form-item label="" v-if="menuForm.menuType && modular === 'master'">
            <slot></slot>
        </el-form-item>

        <template v-if="!menuForm.menuType">
            <!-- 跳转链接 -->
            <el-form-item label="跳转链接" prop="visitType">
                <el-radio-group v-model="menuForm.visitType">
                    <el-radio label="view">网页</el-radio>
                    <el-radio label="miniprogram">小程序</el-radio>
                </el-radio-group>
            </el-form-item>

            <!-- 网址 -->
            <el-form-item label="网址" prop="url">
                <el-input v-model="menuForm.url" />
            </el-form-item>

            <template v-if="menuForm.visitType == 'miniprogram'">
                <!-- AppId -->
                <el-form-item label="AppId" prop="appId">
                    <el-input v-model="menuForm.appId" />
                </el-form-item>

                <!-- 路径 -->
                <el-form-item label="路径" prop="pagePath">
                    <el-input v-model="menuForm.pagePath" placeholder="例如小程序首页：pages/index/index" />
                </el-form-item>
            </template>
        </template>
    </el-form>
</template>

<script lang="ts" setup>
import { generateRules } from './useMenuOa'
import type { FormInstance } from 'element-plus'

const emit = defineEmits([
    'update:name',
    'update:menuType',
    'update:visitType',
    'update:url',
    'update:appId',
    'update:pagePath'
])

const props = withDefaults(
    defineProps<{
        modular?: string
        name?: string
        menuType?: boolean
        visitType?: string
        url?: string
        appId?: string
        pagePath?: string
    }>(),
    {
        modular: 'master',
        name: '',
        menuType: false,
        visitType: 'view',
        url: '',
        appId: '',
        pagePath: ''
    }
)

const rules = generateRules({
    menuLength: props.modular === 'master' ? 16 : 60
})

const menuFormRef = shallowRef<FormInstance>()
// 表单数据
const menuForm = ref({ ...props })

watch(
    () => props,
    (value) => {
        menuForm.value = value
    },
    { immediate: true }
)

watchEffect(() => {
    if (props.modular === 'master') {
        emit('update:menuType', menuForm.value.menuType)
    }
    emit('update:name', menuForm.value.name)
    emit('update:visitType', menuForm.value.visitType)
    emit('update:url', menuForm.value.url)
    emit('update:appId', menuForm.value.appId)
    emit('update:pagePath', menuForm.value.pagePath)
})

defineExpose({
    menuFormRef
})
</script>
