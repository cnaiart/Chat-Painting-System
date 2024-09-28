import {
    FetchOptions,
    $fetch,
    $Fetch,
    FetchResponse,
    RequestOptions,
    FileParams,
    RequestEventStreamOptions
} from 'ofetch'
import { merge } from 'lodash-es'
import { isFunction } from '../validate'
import { RequestMethodsEnum } from '@/enums/requestEnums'
import { objectToQuery } from '../util'

export class Request {
    private requestOptions: RequestOptions
    private fetchInstance: $Fetch
    constructor(private fetchOptions: FetchOptions) {
        this.fetchInstance = $fetch.create(fetchOptions)
        this.requestOptions = fetchOptions.requestOptions
    }

    getInstance() {
        return this.fetchInstance
    }
    /**
     * @description get请求
     */
    get(fetchOptions: FetchOptions, requestOptions?: Partial<RequestOptions>) {
        return this.request(
            { ...fetchOptions, method: RequestMethodsEnum.GET },
            requestOptions
        )
    }

    /**
     * @description eventStream请求，无法使用$fetch
     */
    async eventStream(
        fetchOptions: FetchOptions,
        requestOptions?: Partial<RequestEventStreamOptions>
    ) {
        let mergeOptions = merge({}, this.fetchOptions, fetchOptions)

        mergeOptions.requestOptions = merge(
            {},
            this.requestOptions,
            requestOptions
        )
        const { requestInterceptorsHook, responseInterceptorsHook } =
            this.requestOptions
        if (requestInterceptorsHook && isFunction(requestInterceptorsHook)) {
            mergeOptions = requestInterceptorsHook(mergeOptions)
        }
        const { onmessage, onclose, onstart } = requestOptions
        return new Promise((resolve, reject) => {
            const push = async (controller, reader) => {
                try {
                    const { value, done } = await reader.read()
                    if (done) {
                        controller.close()
                        onclose?.()
                    } else {
                        // console.log(value)
                        onmessage?.(new TextDecoder().decode(value))
                        controller.enqueue(value)
                        push(controller, reader)
                    }
                } catch (error) {
                    onclose?.()
                }
            }
            let body = undefined
            let url = `${mergeOptions.baseURL}${mergeOptions.url}`
            if (mergeOptions.method.toUpperCase() == RequestMethodsEnum.GET) {
                url = `${url}?${objectToQuery(mergeOptions.params)}`
            }
            if (mergeOptions.method.toUpperCase() == RequestMethodsEnum.POST) {
                body = JSON.stringify(mergeOptions.body)
            }
            fetch(url, {
                ...mergeOptions,
                body,
                headers: {
                    accept: 'text/event-stream',
                    'Content-Type': 'application/json',
                    ...mergeOptions.headers
                }
            })
                .then(async (response) => {
                    if (response.status == 200) {
                        if (
                            response.headers
                                .get('content-type')
                                .includes('text/event-stream')
                        ) {
                            const reader = response.body!.getReader()
                            onstart?.(reader)
                            new ReadableStream({
                                start(controller) {
                                    push(controller, reader)
                                }
                            })
                        } else {
                            //@ts-ignore
                            response._data = await response.json()
                            return response
                        }
                    } else {
                        reject(response.statusText)
                    }
                })
                .then(async (response) => {
                    if (!response) {
                        resolve(response)
                        return
                    }
                    if (
                        responseInterceptorsHook &&
                        isFunction(responseInterceptorsHook)
                    ) {
                        try {
                            response = await responseInterceptorsHook(
                                response,
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
                .catch(() => {
                    reject()
                })
        })
    }

    /**
     * @description post请求
     */
    post(fetchOptions: FetchOptions, requestOptions?: Partial<RequestOptions>) {
        return this.request(
            { ...fetchOptions, method: RequestMethodsEnum.POST },
            requestOptions
        )
    }
    /**
     * @description: 文件上传
     */
    uploadFile(options: FetchOptions, params: FileParams) {
        const formData = new FormData()
        const customFilename = params.name || 'file'
        formData.append(customFilename, params.file)
        if (params.data) {
            Object.keys(params.data).forEach((key) => {
                const value = params.data![key]
                if (Array.isArray(value)) {
                    value.forEach((item) => {
                        formData.append(`${key}[]`, item)
                    })
                    return
                }

                formData.append(key, params.data![key])
            })
        }
        return this.request({
            ...options,
            method: RequestMethodsEnum.POST,
            body: formData
        })
    }
    /**
     * @description 请求函数
     */
    request(
        fetchOptions: FetchOptions,
        requestOptions?: Partial<RequestOptions>
    ): Promise<any> {
        let mergeOptions = merge({}, this.fetchOptions, fetchOptions)
        mergeOptions.requestOptions = merge(
            {},
            this.requestOptions,
            requestOptions
        )
        const {
            requestInterceptorsHook,
            responseInterceptorsHook,
            responseInterceptorsCatchHook
        } = this.requestOptions
        if (requestInterceptorsHook && isFunction(requestInterceptorsHook)) {
            mergeOptions = requestInterceptorsHook(mergeOptions)
        }
        return new Promise((resolve, reject) => {
            return this.fetchInstance
                .raw(mergeOptions.url, mergeOptions)
                .then(async (response: FetchResponse<any>) => {
                    if (
                        responseInterceptorsHook &&
                        isFunction(responseInterceptorsHook)
                    ) {
                        try {
                            response = await responseInterceptorsHook(
                                response,
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
                .catch((err) => {
                    if (
                        responseInterceptorsCatchHook &&
                        isFunction(responseInterceptorsCatchHook)
                    ) {
                        reject(responseInterceptorsCatchHook(err))
                        return
                    }
                    reject(err)
                })
        })
    }
}
