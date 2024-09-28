//菜单主题类型
export enum ThemeEnum {
    LIGHT = 'light',
    DARK = 'dark'
}

// 菜单类型
export enum MenuEnum {
    CATALOGUE = 'M',
    MENU = 'C',
    BUTTON = 'A'
}

// 屏幕
export enum ScreenEnum {
    SM = 640,
    MD = 768,
    LG = 1024,
    XL = 1280,
    '2XL' = 1536
}

export enum SMSEnum {
    LOGIN = 'YZMDL',
    BIND_MOBILE = 'BDSJHM',
    CHANGE_MOBILE = 'BGSJHM',
    FIND_PASSWORD = 'ZHDLMM',
    REGISTER = 'YZMZC'
}

export enum PolicyAgreementEnum {
    SERVICE = 'service',
    PRIVACY = 'privacy',
    PAY = 'pay',
    DISTRIBUTION = 'distribution'
}

/* 消息类型 */
export const E_Msg = {
    TEXT: 1,
    IMAGE: 2,
    GOODS: 3
}

export enum LoginPopupTypeEnum {
    LOGIN,
    FORGOT_PWD_MOBILE,
    FORGOT_PWD_MAILBOX,
    REGISTER,
    BIND_MOBILE
}

export enum ClientEnum {
    PC = 'pc',
    MOBILE = 'mobile',
    QQ = 'qq',
    WECHAT = 'wechat',
    ALIPAY = 'alipay'
}