<template>
    <ElDialog v-model="showNotice" width="600" append-to-body>
        <template #header>
            <div class="text-lg text-center font-medium">
                {{ bulletinTitle }}
            </div>
        </template>
        <el-scrollbar height="60vh">
            <div class="richText" v-html="richTextContent"></div>
        </el-scrollbar>
        <div class="flex-1 flex justify-center items-center bg-white pt-[20px]">
            <el-button type="primary" size="large" @click="showNotice = false">
                我知道了
            </el-button>
        </div>
    </ElDialog>
</template>

<script setup lang="ts">
import { watch, computed } from 'vue'
import { ElDialog } from 'element-plus'
import { useAppStore } from '~/stores/app'
import { NOTICE_KEY } from '@/enums/cacheEnums'
import { isNewDay } from '~/utils/validate'

const appStore = useAppStore()
const showNotice = ref<boolean>(false)
const richTextContent = computed(
    () => appStore.getBulletinConfig.bulletin_content
)
const bulletinTitle = computed(() => appStore.getBulletinConfig.bulletin_title)
const isBulletin = computed(() => appStore.getBulletinConfig.is_bulletin)

watch(
    () => isBulletin.value,
    (val) => {
        if (val && isNewDay(val, NOTICE_KEY)) {
            showNotice.value = true
        }
    },
    {
        deep: true,
        immediate: true
    }
)
</script>

<style lang="scss" scoped>
::v-deep .richText {
    img {
        display: inline-block;
    }
}
</style>
