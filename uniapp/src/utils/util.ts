import { useNavigationBarTitleStore } from '@/stores/navigationBarTitle'
import { isObject } from '@vue/shared'
import { cloneDeep } from 'lodash-es'

/**
 * @description 获取元素节点信息（在组件中的元素必须要传ctx）
 * @param  { String } selector 选择器 '.app' | '#app'
 * @param  { Boolean } all 是否多选
 * @param  { ctx } context 当前组件实例
 */
export const getRect = (selector: string, all = false, context?: any) => {
    return new Promise((resolve, reject) => {
        let qurey = uni.createSelectorQuery()
        if (context) {
            qurey = uni.createSelectorQuery().in(context)
        }
        qurey[all ? 'selectAll' : 'select'](selector)
            .boundingClientRect(function (rect) {
                if (all && Array.isArray(rect) && rect.length) {
                    return resolve(rect)
                }
                if (!all && rect) {
                    return resolve(rect)
                }
                reject('找不到元素')
            })
            .exec()
    })
}

/**
 * @description 获取当前页面实例
 */
export function getCurrentPage() {
    const pages = getCurrentPages()
    const currentPage = pages[pages.length - 1]
    return currentPage || {}
}

/**
 * @description 后台选择链接专用跳转
 */
interface Link {
    path: string
    name?: string
    type: string
    isTab: boolean
    query?: Record<string, any>
}

export enum LinkTypeEnum {
    'SHOP_PAGES' = 'shop',
    'CUSTOM_LINK' = 'custom'
}

export function navigateTo(
    link: Link,
    takeName = false,
    navigateType: 'switchTab' | 'navigateTo' | 'reLaunch' = 'navigateTo'
) {
    const url = link.query
        ? `${link.path}?${objectToQuery(link.query)}`
        : link.path

    const navigationBarTitleStore = useNavigationBarTitleStore()
    navigationBarTitleStore.add({
        path: link.path,
        title: link.name as string
    })
    navigateType == 'navigateTo' &&
        uni.navigateTo({
            url
        })
    navigateType == 'reLaunch' &&
        uni.reLaunch({
            url
        })
    navigateType == 'switchTab' &&
        uni.switchTab({
            url
        })
}

/**
 * @description 是否为空
 * @param {unknown} value
 * @return {Boolean}
 */
export const isEmpty = (value: unknown) => {
    return value == null || typeof value == 'undefined'
}

/**
 * @description 对象格式化为Query语法
 * @param { Object } params
 * @return {string} Query语法
 */
export function objectToQuery(params: Record<string, any>): string {
    let query = ''
    for (const props of Object.keys(params)) {
        const value = params[props]
        if (!isEmpty(value)) {
            query += props + '=' + value + '&'
        }
    }
    return query.slice(0, -1)
}

/**
 * @description 添加单位
 * @param {String | Number} value 值 100
 * @param {String} unit 单位 px em rem
 */
export const addUnit = (value: string | number, unit = 'rpx') => {
    return !Object.is(Number(value), NaN) ? `${value}${unit}` : value
}

/**
 * @description 格式化输出价格
 * @param  { string } price 价格
 * @param  { string } take 小数点操作
 * @param  { string } prec 小数位补
 */
export function formatPrice({ price, take = 'all', prec = undefined }: any) {
    let [integer, decimals = ''] = (price + '').split('.')

    // 小数位补
    if (prec !== undefined) {
        const LEN = decimals.length
        for (let i = prec - LEN; i > 0; --i) decimals += '0'
        decimals = decimals.substr(0, prec)
    }

    switch (take) {
        case 'int':
            return integer
        case 'dec':
            return decimals
        case 'all':
            return integer + '.' + decimals
    }
}

/**
 * @description 组合异步任务
 * @param  { string } task 异步任务
 */

export function series(...task: Array<(_arg: any) => any>) {
    return function (_arg?: any): Promise<any> {
        return new Promise((resolve, reject) => {
            const iteratorTask = task.values()
            const next = (res?: any) => {
                const nextTask = iteratorTask.next()
                if (nextTask.done) {
                    resolve(res)
                } else {
                    Promise.resolve(nextTask.value(res))
                        .then(next)
                        .catch(reject)
                }
            }
            next(_arg)
        })
    }
}

export function strToParams(str: string) {
    if (str === '' || str === '?') return {}
    const newparams: any = {}
    for (const item of str.split('&')) {
        newparams[item.split('=')[0]] = item.split('=')[1]
    }
    return newparams
}

/**
 * @description 对象参数转为以？&拼接的字符
 * @param params
 * @returns
 */
export function paramsToStr(params: Record<string, string>) {
    let p = ''
    if (isObject(params)) {
        p = '?'
        for (const props in params) {
            p += `${props}=${params[props]}&`
        }
        p = p.slice(0, -1)
    }
    return p
}

/**
 * @description 数组转
 * @param {Array} data  数据
 * @param {Object} props `{ parent: 'pid', children: 'children' }`
 */

export const arrayToTree = (
    data: any[],
    props = { id: 'id', parentId: 'pid', children: 'children' }
) => {
    data = cloneDeep(data)
    const { id, parentId, children } = props
    const result: any[] = []
    const map = new Map()
    data.forEach((item) => {
        map.set(item[id], item)
        const parent = map.get(item[parentId])
        if (parent) {
            parent[children] = parent[children] || []
            parent[children].push(item)
        } else {
            result.push(item)
        }
    })
    return result
}

/**
 * @description 树转数组，队列实现广度优先遍历
 * @param {Array} data  数据
 * @param {Object} props `{ children: 'children' }`
 */

export const treeToArray = (data: any[], props = { children: 'children' }) => {
    data = cloneDeep(data)
    const { children } = props
    const newData = []
    const queue: any[] = []
    data.forEach((child: any) => queue.push(child))
    while (queue.length) {
        const item: any = queue.shift()
        if (item[children]) {
            item[children].forEach((child: any) => queue.push(child))
            delete item[children]
        }
        newData.push(item)
    }
    return newData
}

/**
 * @description 域名删除http/https
 */
export const splitDomain = (domain: string) => {
    if (domain.includes('https://')) {
        return domain.replace(/^https?:\/\//, '')
    }
    if (domain.includes('http://')) {
        return domain.replace(/^http?:\/\//, '')
    }
    return domain
}
