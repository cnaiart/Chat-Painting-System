import { isAndroid, isWeixinClient } from '@/utils/client'
//#ifdef H5
import { Howl, Howler } from 'howler'
//#endif
import { onBeforeUnmount, ref, shallowRef } from 'vue'

interface Options {
    api(...args: any): Promise<any>
    params: Record<string, any>
    dataTransform(data: any): string
    onstart?(): void
    onstop?(): void
}

const audioSet = new Set<UniApp.InnerAudioContext>()

export const useAudioPlay = (options?: Options) => {
    const { api, dataTransform, params, onstart, onstop } = options || {}
    const audio = shallowRef<UniApp.InnerAudioContext | null>(null)
    const audioLoading = ref(false)
    const audioPlaying = ref(false)

    const _paly = async () => {
        //#ifdef H5
        //@ts-ignore
        audio.value.load()
        //@ts-ignore
        audio.value.once('load', () => {
            if (isWeixinClient()) {
                window.WeixinJSBridge.invoke(
                    'getNetworkType',
                    {},
                    () => {
                        audio.value!.play()
                    },
                    false
                )
            } else {
                audio.value!.play()
            }
        })

        //#endif
        //#ifndef H5
        audio.value!.play()
        //#endif
    }
    const play = async () => {
        pauseAll()

        if (audio.value?.src || audio.value?._src) {
            await _paly()
            return
        }
        audioLoading.value = true
        try {
            const data = await api?.(params)
            const audioUrl = dataTransform?.(data)
            if (!audioUrl) {
                throw Error('获取的语音url错误')
            }
            if (!audio.value) {
                createAudio(audioUrl)
            }
            audio.value!.src = audioUrl
            await _paly()
        } catch (error) {
            console.error(error)
            uni.$u.toast('语音播报异常')
        } finally {
            audioLoading.value = false
        }
    }
    const pause = () => {
        audio.value?.stop()
        audioPlaying.value = false
    }

    const pauseAll = () => {
        audioSet.forEach((audio) => {
            audio?.stop()
            //@ts-ignore
            audio.audioPlaying.value = false
        })
    }

    const onPlay = () => {
        onstart?.()
        audioPlaying.value = true
    }
    const onStop = () => {
        if (audio.value?.src || audio.value?._src) {
            //有src才算正真的停止
            onstop?.()
        }
        audioPlaying.value = false
    }

    const onError = () => {
        // onerror()
        audioPlaying.value = false
    }

    const createAudio = (src: string) => {
        // if (isWeixinClient() ) {
        // #ifdef H5
        //@ts-ignore
        audio.value = new Howl({
            src,
            autoplay: false,
            preload: false
        })
        //@ts-ignore
        audio.value.audioPlaying = audioPlaying

        //@ts-ignore
        audio.value.on('play', onPlay)
        //@ts-ignore
        audio.value.on('end', onStop)
        //@ts-ignore
        audio.value.on('loaderror', onError)
        //@ts-ignore
        audio.value.on('playerror', onError)
        //@ts-ignore
        audio.value.on('stop', onStop)
        // #endif
        // } else {

        // #ifndef H5
        audio.value = uni.createInnerAudioContext()
        //@ts-ignore
        audio.value.audioPlaying = audioPlaying
        audio.value.onPlay(onPlay)
        audio.value.onEnded(onStop)
        audio.value.onError(onError)
        audio.value.onStop(onStop)
        // #endif
        // }
        //@ts-ignore
        audioSet.add(audio.value)
    }
    const destroy = () => {
        if (!audio.value) {
            return
        }
        // if (isWeixinClient() && audio.value) {
        // #ifdef H5
        //@ts-ignore
        audio.value.off('play', onPlay)
        //@ts-ignore
        audio.value.off('end', onStop)
        //@ts-ignore
        audio.value.off('loaderror', onError)
        //@ts-ignore
        audio.value.off('playerror', onError)
        //@ts-ignore
        audio.value.off('stop', onStop)
        // #endif
        // #ifndef H5
        //@ts-ignore
        audio.value?.offPlay(onPlay)
        //@ts-ignore
        audio.value?.offEnded(onStop)
        //@ts-ignore
        audio.value?.offError(onError)
        //@ts-ignore
        audio.value?.offStop(onStop)
        // #endif

        audioSet.delete(audio.value!)
        audio.value = null
    }

    onBeforeUnmount(() => {
        if (audioPlaying.value) {
            pause()
        }
        destroy()
    })

    return {
        play,
        audioLoading,
        audioPlaying,
        pause,
        pauseAll,
        destroy
    }
}
