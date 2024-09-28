<template>
    <div class="h-full bg-white rounded-[20px] relative p-[15px]">
        <div class="w-full h-full flex flex-col">
            <div>
                <el-dropdown @command="handleExport">
                    <el-button text bg>
                        <template #icon>
                            <Icon name="el-icon-Download" />
                        </template>
                        导出文件
                        <Icon name="el-icon-ArrowDown" class="el-icon--right" />
                    </el-button>
                    <template #dropdown>
                        <el-dropdown-menu>
                            <el-dropdown-item command="html">
                                导出HTML
                            </el-dropdown-item>
                            <el-dropdown-item command="png">
                                导出PNG
                            </el-dropdown-item>
                            <el-dropdown-item command="jpg">
                                导出JPG
                            </el-dropdown-item>
                        </el-dropdown-menu>
                    </template>
                </el-dropdown>
            </div>
            <div class="flex-1 min-h-0" ref="svgWrapRef">
                <svg ref="svgRef" class="w-full h-full"></svg>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Transformer } from 'markmap-lib'
import { Markmap } from 'markmap-view'

const transformer = new Transformer()
const svgRef = shallowRef<SVGElement>()
const svgWrapRef = shallowRef<HTMLDivElement>()

let markmap: Markmap = null
const renderMarkMap = (value: string) => {
    const { root } = transformer.transform(value)
    markmap?.setData(root)
    markmap?.fit()
}
const handleExport = (command: string) => {
    switch (command) {
        case 'html':
            exportHtml()
            break
        case 'png':
            exportImg('png')
            break
        case 'jpg':
            exportImg('jpeg')
            break
    }
}

const exportHtml = () => {
    const html = `<style>*{margin: 0;padding:0} .markmap{width: 100vw;height:100vh}</style>
${svgWrapRef.value.innerHTML}`
    const blob = new Blob([html], { type: 'text/html' })
    const url = URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.download = 'markmap.html'
    link.click()
    URL.revokeObjectURL(url)
}

const exportImg = (type: 'png' | 'jpeg') => {
    downloadHtml2Image(
        svgWrapRef.value,
        { type, name: 'markmap' },
        {
            backgroundColor: '#fff',
            scale: window.devicePixelRatio * 1.5
        }
    )
}

onMounted(async () => {
    if (svgRef.value) {
        markmap = Markmap.create(svgRef.value)
    }
})

defineExpose({
    renderMarkMap
})
</script>
<style lang="scss" scoped></style>
