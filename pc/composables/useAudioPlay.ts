interface Options {
    api(...args: any): Promise<any>
    params: Record<string, any>
    dataTransform(data: any): string
}

const audioSet = new Set<HTMLAudioElement>()

export const useAudioPlay = (options?: Options) => {
    const { api, dataTransform, params } = options || {}
    const audio = shallowRef<HTMLAudioElement>(null)
    const audioLoading = ref(false)
    const audioPlaying = ref(false)

    const play = async () => {
        pauseAll()
        if (!audio.value) {
            createAudio()
        }
        if (audio.value.src) {
            audio.value.play()
            return
        }
        audioLoading.value = true
        try {
            const data = await api(params)
            const audioUrl = dataTransform(data)
            audio.value.src = audioUrl
            audio.value.play()
        } catch (error) {
            console.error(error)
            feedback.msgError('语音播报异常')
        } finally {
            audioLoading.value = false
        }
    }
    const pause = () => {
        audio.value.pause()
        audioPlaying.value = false
    }

    const pauseAll = () => {
        audioSet.forEach((audio) => {
            audio.pause()
            audio.currentTime = 0
            //@ts-ignore
            audio.audioPlaying.value = false
        })
    }
    const createAudio = () => {
        audio.value = new Audio()
        //@ts-ignore
        audio.value.audioPlaying = audioPlaying
        audioSet.add(audio.value)
        audio.value.onplay = () => {
            audioPlaying.value = true
        }
        audio.value.onended = () => {
            audioPlaying.value = false
        }
        audio.value.onerror = () => {
            audioPlaying.value = false
        }
    }

    onBeforeUnmount(() => {
        if (audio.value?.src) {
            pauseAll()
        }
        if (audio.value) {
            audioSet.delete(audio.value)
            audio.value = null
        }
    })

    return {
        play,
        audioLoading,
        audioPlaying,
        pause,
        pauseAll
    }
}
