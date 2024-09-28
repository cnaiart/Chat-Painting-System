export type SplitOptionsType = {
    columns: number // 横向分割几块
    rows: number // 纵向分割几块
}

/**
 * @description 切割图片
 * @param {String} url  图片地址
 * @param {SplitOptionsType} options `{ columns: 2, rows: 2 }`
 */
export const useSplit = (url: string, options: SplitOptionsType) => {
    const { columns, rows } = options
    if (!url) {
        throw Error('请传入图片地址')
    }
    if (!columns && !rows) {
        throw Error('请传入columns和rows')
    }

    // 获取图片
    const getImages = () => {
        return new Promise((resolve, reject) => {
            const image: HTMLImageElement = new Image()
            // 图片加上时间错（防止canvas污染跨域
            image.setAttribute('src', url)
            // 图片设置跨域属性（防止canvas污染跨域
            image.setAttribute('crossOrigin', '')
            image.onload = () => {
                console.log('loading??')
                resolve(split(image.width, image.height, image))
            }
            image.onerror = (err) => {
                reject(err)
            }
        })
    }

    // 拆分图片
    const split = (width: number, height: number, ref: HTMLImageElement) => {
        const canvas: HTMLCanvasElement = document.createElement('canvas')
        canvas.width = width
        canvas.height = height
        const ctx: CanvasRenderingContext2D = canvas.getContext('2d')
        ctx.drawImage(ref, 0, 0, width, height)
        return getList(ctx)
    }

    // 得到切割后的图片列表
    const getList = (ctx: CanvasRenderingContext2D) => {
        const width: number = ctx.canvas.width / columns
        const height: number = ctx.canvas.height / rows
        const list: any[] = []
        for (let i = 0; i < rows; i++) {
            const listFirst = []
            for (let k = 0; k < columns; k++) {
                console.log('i:' + i + ';k:' + k)
                const url = getPart(
                    ctx,
                    k * width,
                    i * height,
                    width,
                    height
                )
                listFirst.push(url)
            }
            list.push(...listFirst)
        }
        return list
    }

    // 得到canvas的指定部分
    const getPart = (
        ctx: CanvasRenderingContext2D,
        x: number,
        y: number,
        w: number,
        h: number
    ) => {
        const canvas: HTMLCanvasElement = document.createElement('canvas')
        canvas.width = w
        canvas.height = h
        const data: ImageData = ctx.getImageData(x, y, w, h)
        const context: CanvasRenderingContext2D = canvas.getContext('2d')
        context.putImageData(data, 0, 0)
        return canvas.toDataURL('image/png', 1)
    }

    return {
        getImages
    }
}
