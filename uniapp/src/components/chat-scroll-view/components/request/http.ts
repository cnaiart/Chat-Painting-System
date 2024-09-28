import { merge } from 'lodash-es'
import { useUserStore } from '@/stores/user'
import type {
    HttpRequestOptions,
    RequestConfig,
    RequestEventStreamConfig,
    RequestHooks,
    RequestOptions
} from './type'
import { RequestCodeEnum, RequestMethodsEnum } from '@/enums/requestEnums'
interface IConfig {
    onstart: Function
    onmessage: Function
    onclose: Function
}

// const { token: itoken } = useUserStore()

function isStreamResponse(contentType?: string) {
    if (typeof contentType !== 'string') return false
    return contentType.includes('text/event-stream')
}

const requestHooks: RequestHooks = {
    requestInterceptorsHook(options, config) {
        const { urlPrefix, baseUrl, withToken } = config
        options.header = options.header || {}
        if (urlPrefix) {
            options.url = `${urlPrefix}${options.url}`
        }
        if (baseUrl) {
            options.url = `${baseUrl}${options.url}`
        }
        // const token = useUserStore().token || null
        const token = config.token
        // 添加token
        if (withToken && !options.header.token) {
            options.header.token = token
        }
        return options
    },
    async responseInterceptorsHook(response, config, options) {
        const { isTransformResponse, isReturnDefaultResponse, isAuth } = config

        //返回默认响应，当需要获取响应头及其他数据时可使用
        if (isReturnDefaultResponse) {
            return response
        }
        // 是否需要对数据进行处理
        if (!isTransformResponse) {
            return response.data
        }
        // const { logout } = useUserStore()
        const { code, data, msg, show } = response.data as any
        switch (code) {
            case RequestCodeEnum.SUCCESS:
                // msg && show && uni.$u.toast(msg)
                return data
            case RequestCodeEnum.FAILED:
                // msg && uni.$u.toast(msg)
                return Promise.reject(msg)

            case RequestCodeEnum.TOKEN_INVALID:
                // logout()
                if (isAuth && options.method?.toUpperCase() !== 'GET') {
                    // router.navigateTo({ path: '/pages/login/login' })
                }
                return Promise.reject(msg)

            default:
                return data
        }
    },
    async responseInterceptorsCatchHook(options, error) {
        if (options.method?.toUpperCase() == RequestMethodsEnum.POST) {
            // uni.$u.toast('请求失败，请重试')
        }
        return error
    }
}

export const defaultOptions: HttpRequestOptions = {
    requestOptions: {
        timeout: 30 * 1000
    },
    // baseUrl: `${import.meta.env.VITE_APP_BASE_URL || ''}/`,
    baseUrl: '',

    //是否返回默认的响应
    isReturnDefaultResponse: false,
    // 需要对返回数据进行处理
    isTransformResponse: true,
    // 接口拼接地址
    urlPrefix: 'api',
    // 忽略重复请求
    ignoreCancel: false,
    // 是否携带token
    withToken: true,
    isAuth: false,
    retryCount: 2,
    retryTimeout: 1000,
    requestHooks: requestHooks,
    token: ''
}

export const eventStream = (
    options: RequestOptions,
    config: RequestEventStreamConfig
) => {
    let mergeOptions = merge({}, defaultOptions.requestOptions, options)

    const mergeConfig: RequestEventStreamConfig = merge(
        {},
        defaultOptions,
        config
    )
    const { requestInterceptorsHook, responseInterceptorsHook } =
        mergeConfig.requestHooks || {}

    // if (requestInterceptorsHook && isFunction(requestInterceptorsHook)) {
    mergeOptions = requestInterceptorsHook(
        mergeOptions,
        mergeConfig as RequestConfig
    )
    // }

    let body: any = undefined
    body = JSON.stringify(mergeOptions.data)

    const { onmessage, onclose, onstart } = config

    const decoder = new TextDecoder()
    const push = async (controller: any, reader: any) => {
        try {
            const { value, done } = await reader.read()
            if (done) {
                controller.close()
                onclose?.()
            } else {
                onmessage?.(decoder.decode(value))
                controller.enqueue(value)
                push(controller, reader)
            }
        } catch (error) {
            onclose?.()
        }
    }

    return new Promise((resolve, reject) => {
        fetch(mergeOptions.url, {
            ...mergeOptions,
            body,
            headers: {
                'content-type': 'application/json; charset=utf-8',
                Accept: 'text/event-stream',
                ...mergeOptions.header
            }
        })
            .then(async (response) => {
                if (response.status == 200) {
                    if (
                        isStreamResponse(response.headers?.get('content-type')!)
                    ) {
                        const reader = response.body!.getReader()
                        // console.log(reader)
                        onstart?.(reader)
                        new ReadableStream({
                            start(controller) {
                                push(controller, reader)
                            }
                        })
                    } else {
                        //@ts-ignore
                        response.data = await response.json()
                        return response
                    }
                } else {
                    reject(response.statusText)
                }
            })
            .then(async (response: any) => {
                if (!response) {
                    resolve(response)
                    return
                }
                if (responseInterceptorsHook) {
                    try {
                        response = await responseInterceptorsHook(
                            response,
                            mergeConfig as RequestConfig,
                            mergeOptions
                        )
                        resolve(response)
                    } catch (error) {
                        reject(error)
                    }
                    return
                }
                resolve(response)
            })
            .catch((error) => {
                reject(error)
            })
    })
}
