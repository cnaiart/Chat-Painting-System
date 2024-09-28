const weekArr = [
    '星期日',
    '星期一',
    '星期二',
    '星期三',
    '星期四',
    '星期五',
    '星期六'
]

/**
 * @description 时间格式化
 * @param dateTime { number } 时间错
 * @param fmt { string } 时间格式
 * @return { string }
 */
// yyyy:mm:dd|yyyy:mm|yyyy年mm月dd日|yyyy年mm月dd日 hh时MM分等,可自定义组合
export const timeFormat = (dateTime, fmt = 'yyyy-mm-dd') => {
    // 如果为null,则格式化当前时间
    if (!dateTime) dateTime = Number(new Date())
    // 如果dateTime长度为10或者13，则为秒和毫秒的时间戳，如果超过13位，则为其他的时间格式
    if (dateTime.toString().length == 10) dateTime *= 1000
    const date = new Date(dateTime)
    let ret
    const opt = {
        'y+': date.getFullYear().toString(), // 年
        'm+': (date.getMonth() + 1).toString(), // 月
        'd+': date.getDate().toString(), // 日
        'h+': date.getHours().toString(), // 时
        'M+': date.getMinutes().toString(), // 分
        's+': date.getSeconds().toString() // 秒
    }
    for (const k in opt) {
        ret = new RegExp(`(${k})`).exec(fmt)
        if (ret) {
            fmt = fmt.replace(
                ret[1],
                ret[1].length == 1
                    ? opt[k]
                    : opt[k].padStart(ret[1].length, '0')
            )
        }
    }
    return fmt
}

/**
 * @description 聊天记录专用时间格式化
 * @param dateTime { number } 时间错
 * @return { string }
 */

export const timeFormatChat = (dateTime: any) => {
    if (dateTime.toString().length == 10) dateTime *= 1000
    const date = new Date(dateTime)
    let fmt = timeFormat(dateTime, 'yyyy年mm月dd日 hh:MM')

    if (isToday(date)) {
        fmt = timeFormat(dateTime, 'hh:MM')
    } else if (isThisWeak(date)) {
        fmt = weekArr[date.getDay()] + timeFormat(dateTime, ' hh:MM')
    } else if (isThisYear(date)) {
        fmt = timeFormat(dateTime, 'mm月dd日 hh:MM')
    }
    return fmt
}

// 是否是今年
const isThisYear = (date) => {
    const now: any = new Date()
    return date.getYear() == now.getYear()
}

// 是否是今月
const isThisMonth = (date) => {
    const now = new Date()
    return isThisYear(date) && date.getMonth() == now.getMonth()
}

// 是否是今天
const isToday = (date) => {
    const now = new Date()
    return isThisMonth(date) && date.getDate() == now.getDate()
}
// 是否本周
// 只考虑同月份的情况
const isThisWeak = (date) => {
    const now = new Date()
    if (isThisMonth(date)) {
        if (
            now.getDay() - date.getDay() > 0 &&
            now.getDate() - date.getDate() < 7
        ) {
            return true
        }
    } else {
        return false
    }
}
