import html2Canvas, { Html2CanvasOptions } from './html2canvas'

/**
 * @description 下载图片文件
 * @param {String} path 图片资源路径
 * @param {String} name 图片下载名称（不传则是当前时间戳
 */
export const downloadImgFile = (path: string, name?: string): void => {
    // 创建一个img标签
    const image: HTMLImageElement = new Image()
    // 解决跨域 Canvas 污染问题
    image.setAttribute('crossOrigin', 'anonymous')
    image.onload = function () {
        const canvas: HTMLCanvasElement = document.createElement('canvas')
        canvas.width = image.width
        canvas.height = image.height
        const context: CanvasRenderingContext2D = canvas.getContext('2d')
        context.drawImage(image, 0, 0, image.width, image.height)
        const url = canvas.toDataURL('image/png')
        // 生成一个a元素
        const a: HTMLAnchorElement = document.createElement('a')
        // 创建一个单击事件
        const event: MouseEvent = new MouseEvent('click')
        // 将a的download属性设置为我们想要下载的图片名称，若name不存在则使用‘当前时间戳’作为默认名称
        a.download = name || new Date().getTime() + ''
        // 将生成的URL设置为a.href属性
        a.href = url
        // 触发a的单击事件
        a.dispatchEvent(event)
    }
    image.src = path
}

/**
 * @description 下载HTML为图片
 * @param {HTMLElement} el dom元素
 * @param {String} params 参数配置
 * @param {String} canvasOptions html2Canvas参数配置
 */
export const downloadHtml2Image = async (
    el: HTMLElement,
    params?: {
        type?: 'png' | 'jpeg'
        name?: string
    },
    canvasOptions?: Html2CanvasOptions
) => {
    try {
        const { type = 'png', name = 'file' } = params || {}
        const canvas = await html2Canvas(el, {
            useCORS: true,
            backgroundColor: 'transparent',
            ...canvasOptions
        })
        const dataURL = canvas.toDataURL(`image/${type}`)
        download(dataURL, name)
    } catch (error) {
        feedback.msgError('下载失败，请重试')
        throw new Error(error)
    }
}
/**
 * @description 下载文件
 * @param {String} url  文件url
 * @param {String} name 下载的文件名称
 */
export const download = (url: string, name: string) => {
    const aTag = document.createElement('a')
    document.body.appendChild(aTag)
    aTag.href = url
    aTag.download = name
    aTag.click()
    aTag.remove()
}
