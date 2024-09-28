<template>
    <div class="balance-details">
        <el-card shadow="never" class="!border-none">
            <el-tabs v-model="activeTab" class="balance-details__tabs">
                <el-tab-pane
                    v-for="(item, index) in tabLists"
                    :label="`${item.name}`"
                    :name="item.label"
                    :key="index"
                    :lazy="true"
                >
                    <div v-if="item.label == 'session'">
                        <SesionDetails ref="sessionRef"></SesionDetails>
                    </div>
                    <div v-if="item.label == 'draw'">
                        <DrawDetails ref="drawRef"></DrawDetails>
                    </div>
                </el-tab-pane>
            </el-tabs>
        </el-card>
    </div>
</template>
<script setup lang="ts" name="BalanceDetails">
import DrawDetails from '@/views/finance/component/draw-details.vue'
import SesionDetails from '@/views/finance/component/sesion-details.vue'

const activeTab = ref('session')
const tabLists = [
    {
        name: '对话明细',
        label: 'session'
    },
    {
        name: '绘画明细',
        label: 'draw'
    }
]
</script>

<style lang="scss" scoped>
</style>