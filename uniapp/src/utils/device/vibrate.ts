// 封装震动函数
export function vibrate(duration: number) {
    //#ifdef H5
    if (window.navigator.vibrate) {
        window.navigator.vibrate(duration)
    }
    //#endif
    //#ifndef H5
    if (duration > 400) {
        uni.vibrateLong()
    } else {
        uni.vibrateShort()
    }
    //#endif
}
