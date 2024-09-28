import { ref } from 'vue'

const downloading = ref<boolean>(false)
export async function saveImageToPhotosAlbum(url: string) {
    if (!url) return uni.$u.toast('图片错误')

    //#ifdef H5
    uni.$u.toast('请长按图片保存')
    //#endif
    //#ifndef H5
    if (downloading.value) {
        return
    }
    uni.showLoading({ title: '下载中' })
    downloading.value = true
    try {
        const res: any = await uni.downloadFile({ url, timeout: 10000 })
        await uni.saveImageToPhotosAlbum({
            filePath: res.tempFilePath
        })
        uni.hideLoading()
        uni.showToast({
            title: '保存成功',
            icon: 'success'
        })
        downloading.value = false
    } catch (error: any) {
        uni.hideLoading()
        console.log(error)
        downloading.value = false
        if (error.errMsg == 'saveImageToPhotosAlbum:fail cancel') {
            uni.$u.toast('取消保存')
            return
        }
        if (error.errMsg == 'downloadFile:fail fail:timeout') {
            uni.$u.toast('下载图片超时，请重新下载')
            return
        }
        if (error.errMsg == 'saveImageToPhotosAlbum:fail auth deny') {
            const res: UniApp.ShowModalRes = await uni.showModal({
                title: '提示',
                content: '您关闭了权限，请前往设置打开权限'
            })
            if (res.confirm) {
                const setting = await uni.openSetting()
                if (setting.authSetting['scope.writePhotosAlbum']) {
                    uni.showModal({
                        title: '提示',
                        content: '获取权限成功,再次保存图片即可成功',
                        showCancel: false
                    })
                } else {
                    uni.showModal({
                        title: '提示',
                        content: '获取权限失败，无法保存到相册',
                        showCancel: false
                    })
                }
            }
            return
        }
        uni.$u.toast(error.errMsg || '保存失败')
    }
    //#endif
}
