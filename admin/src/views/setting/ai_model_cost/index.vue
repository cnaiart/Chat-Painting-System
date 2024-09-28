<template>
    <div>
        <el-card shadow="never" class="!border-none mt-4">
            <el-tabs v-model="activeTab">
                <el-tab-pane
                    v-for="(item, index) in tabLists"
                    :label="`${item.name}`"
                    :name="item.label"
                    :key="index"
                >
                </el-tab-pane>
            </el-tabs>
            <div v-if="activeTab == 'session'">
                <sessionView ref="sessionRef" :pop-ref="popRef"></sessionView>
            </div>
            <div v-if="activeTab == 'draw'">
                <drawView ref="drawRef" :pop-ref="popRef"></drawView>
            </div>
            <div v-if="activeTab == 'QRcode'">
                <QRcode ref="QRcodeRef" :pop-ref="popRef"></QRcode>
            </div>
        </el-card>
        <footer-btns>
            <el-button
                v-perms="[
                    'setting.chatSetting/setChatBillingConfig',
                    'setting.DrawSetting/setDrawBillingConfig'
                ]"
                type="primary"
                @click="submit"
            >
                保存
            </el-button>
        </footer-btns>
    </div>
</template>
<script setup lang="ts">
import sessionView from './components/session.vue'
import drawView from './components/draw.vue'
import QRcode from './components/QRcode.vue'

//弹框ref
const popRef = shallowRef()
//对话ref
const sessionRef = shallowRef()
//绘画ref
const drawRef = shallowRef()
//二维码ref
const QRcodeRef = shallowRef()

const activeTab = ref('session')
const tabLists = [
    {
        name: 'AI对话模型',
        label: 'session'
    },
    {
        name: 'AI绘画模型',
        label: 'draw'
    },
    {
        name: '艺术二维码',
        label: 'QRcode'
    }
]

//提交
const submit = () => {
    switch (activeTab.value) {
        case 'session':
            sessionRef.value.submit()
            break
        case 'draw':
            drawRef.value.submit()
            break
        case 'QRcode':
            QRcodeRef.value.submit()
            break
        default:
            break
    }
}
</script>
