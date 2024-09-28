import { onBeforeUnmount, ref, shallowRef } from 'vue'

const audioCtxs = new Set<UniApp.InnerAudioContext>()
export const useAudio = () => {
    const audioCtx = shallowRef<UniApp.InnerAudioContext | null>(null)
    const isPlaying = ref(false)
    const duration = ref(0)
    const onPlay = () => {
        isPlaying.value = true
    }
    const onStop = () => {
        isPlaying.value = false
    }

    const onError = (e: any) => {
        console.error(e)
        isPlaying.value = false
    }
    const onCanplay = () => {
        duration.value = audioCtx.value?.duration || 0
        if (duration.value == 0) {
            //处理微信小程序获取不到时长的bug
            setTimeout(() => {
                duration.value = audioCtx.value?.duration || 0
            }, 100)
        }
        // console.log(audioCtx.value?.buffered)
    }
    const createAudio = () => {
        audioCtx.value = uni.createInnerAudioContext()
        audioCtxs.add(audioCtx.value)
        audioCtx.value.onCanplay(onCanplay)
        audioCtx.value.onPlay(onPlay)
        audioCtx.value.onEnded(onStop)
        audioCtx.value.onError(onError)
        audioCtx.value.onStop(onStop)
    }

    const destroy = () => {
        if (audioCtx.value) {
            audioCtx.value.destroy()
            audioCtxs.delete(audioCtx.value)
            audioCtx.value = null
        }
    }
    const setUrl = (src: string) => {
        if (!audioCtx.value) {
            createAudio()
        }
        audioCtx.value!.src = src
    }
    const play = async (src?: string) => {
        pauseAll()
        if (!audioCtx.value) {
            createAudio()
        }
        if (src) {
            setUrl(src)
        }
        audioCtx.value!.play()
    }
    const pause = () => {
        audioCtx.value?.stop()
    }

    const pauseAll = () => {
        audioCtxs.forEach((audio) => {
            if (!audio.paused) {
                audio.stop()
            }
        })
    }
    onBeforeUnmount(() => {
        if (isPlaying.value) {
            pause()
        }
        destroy()
    })
    return {
        pause,
        pauseAll,
        play,
        duration,
        isPlaying,
        setUrl
    }
}
