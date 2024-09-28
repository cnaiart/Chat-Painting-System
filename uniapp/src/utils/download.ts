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
