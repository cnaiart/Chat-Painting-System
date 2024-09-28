export function useCopy() {
    const copy = (text: string) => {
        try {
            uni.setClipboardData({
                data: String(text)
            })
        } catch (error) {
            uni.$u.toast(error)
        }
    }
    return {
        copy
    }
}
