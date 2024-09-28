<?php
// +----------------------------------------------------------------------
// | likeadmin快速开发前后端分离管理后台（PHP版）
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | gitee下载：https://gitee.com/likeshop_gitee/likeadmin
// | github下载：https://github.com/likeshop-github/likeadmin
// | 访问官网：https://www.likeadmin.cn
// | likeadmin团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeadminTeam
// +----------------------------------------------------------------------

namespace app\api\logic;
use app\common\enum\{
    DrawEnum,
    YesNoEnum
};
use app\common\logic\BaseLogic;
use app\common\model\{
    ChatRecords,
    ChatCategory,
    article\Article,
    article\ArticleCate,
    decorate\DecoratePage,
    article\ArticleCollect,
    decorate\DecorateTabbar,
};
use app\common\service\{
    FileService,
    ConfigService,
};


/**
 * index
 * Class IndexLogic
 * @package app\api\logic
 */
class PcLogic extends BaseLogic
{

    /**
     * @notes 首页数据
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2022/9/21 19:15
     */
    public static function getIndexData()
    {
        // 装修配置
        $decoratePage = DecoratePage::findOrEmpty(4);
        // 最新资讯
        $newArticle = self::getLimitArticle('new', 7);
        // 全部资讯
        $allArticle = self::getLimitArticle('all', 5);
        // 热门资讯
        $hotArticle = self::getLimitArticle('hot', 8);

        return [
            'page' => $decoratePage,
            'all' => $allArticle,
            'new' => $newArticle,
            'hot' => $hotArticle
        ];
    }


    /**
     * @notes 获取文章
     * @param string $sortType
     * @param int $limit
     * @return mixed
     * @author 段誉
     * @date 2022/10/19 9:53
     */
    public static function getLimitArticle(string $sortType, int $limit = 0, int $cate = 0, int $excludeId = 0)
    {
        // 查询字段
        $field = [
            'id', 'cid', 'title', 'desc', 'abstract', 'image',
            'author', 'click_actual', 'click_virtual', 'create_time'
        ];

        // 排序条件
        $orderRaw = 'sort desc, id desc';
        if ($sortType == 'new') {
            $orderRaw = 'id desc';
        }
        if ($sortType == 'hot') {
            $orderRaw = 'click_actual + click_virtual desc, id desc';
        }

        // 查询条件
        $where[] = ['is_show', '=', YesNoEnum::YES];
        if (!empty($cate)) {
            $where[] = ['cid', '=', $cate];
        }
        if (!empty($excludeId)) {
            $where[] = ['id', '<>', $excludeId];
        }

        $article = Article::field($field)
            ->where($where)
            ->append(['click'])
            ->orderRaw($orderRaw)
            ->hidden(['click_actual', 'click_virtual']);

        if ($limit) {
            $article->limit($limit);
        }

        return $article->select()->toArray();
    }


    /**
     * @notes 获取配置
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2022/9/21 19:38
     */
    public static function getConfigData($userId)
    {
        // 是否安装
        $install = file_exists(root_path() . '/config/install.lock');
        if (!$install) {
            return ['install'=>$install];
        }
        // 登录配置
        $loginConfig = [
            // 登录方式
            'login_way' => ConfigService::get('login', 'login_way', config('project.login.login_way')),
            // 注册强制绑定手机
            'coerce_mobile' => ConfigService::get('login', 'coerce_mobile', config('project.login.coerce_mobile')),
            // 政策协议
            'login_agreement' => ConfigService::get('login', 'login_agreement', config('project.login.login_agreement')),
//            // 第三方登录 开关
//            'third_auth' => ConfigService::get('login', 'third_auth', config('project.login.third_auth')),
//            // 微信授权登录
//            'wechat_auth' => ConfigService::get('login', 'wechat_auth', config('project.login.wechat_auth')),
//            // qq授权登录
//            'qq_auth' => ConfigService::get('login', 'qq_auth', config('project.login.qq_auth')),

            // 短信验证码
            'sms_verify' => ConfigService::get('login', 'sms_verify', config('project.login.sms_verify')),
            // 关注文案
            'involved_text' => ConfigService::get('login', 'involved_text'),
            // 默认登录方式：1-微信登录/公众号授权登录；2-手机号登录；3-邮箱登录
            'default_login_way' => ConfigService::get('login', 'default_login_way'),
            // 登录图形验证码
            'is_captcha' => ConfigService::get('login', 'is_captcha', config('project.login.is_captcha')),

        ];
        if (empty($loginConfig['default_login_way'])) {
            $loginConfig['default_login_way'] = $loginConfig['login_way'][0];
        }

        // 网站信息
        $website = [
            'shop_name' => ConfigService::get('website', 'shop_name'),
            'shop_logo' => FileService::getFileUrl(ConfigService::get('website', 'shop_logo')),
            'pc_logo' => FileService::getFileUrl(ConfigService::get('website', 'pc_logo')),
            'pc_title' => ConfigService::get('website', 'pc_title'),
            'pc_ico' => FileService::getFileUrl(ConfigService::get('website', 'pc_ico')),
            'pc_desc' => ConfigService::get('website', 'pc_desc',''),
            'pc_keywords' => ConfigService::get('website', 'pc_key',''),
        ];

        // 备案信息
        $copyright = ConfigService::get('copyright', 'config', []);

        // 公众号二维码
        $oaQrCode = ConfigService::get('oa_setting', 'qr_code', '');
        $oaQrCode = empty($oaQrCode) ? $oaQrCode : FileService::getFileUrl($oaQrCode);
        // 小程序二维码
        $mnpQrCode = ConfigService::get('mnp_setting', 'qr_code', '');
        $mnpQrCode = empty($mnpQrCode) ? $mnpQrCode : FileService::getFileUrl($mnpQrCode);
        //对话配置
        $chat = [
            'is_sensitive' => ConfigService::get('chat_config','is_sensitive',1),
            'is_markdown' => ConfigService::get('chat_config','is_markdown',1),
            'chat_logo' => FileService::getFileUrl(ConfigService::get('chat_config','chat_logo',ConfigService::get('default_image','chat_logo'))),
//            'is_tip' => ConfigService::get('chat_config','is_tip',1),
            'chat_title' => ConfigService::get('chat_config','chat_title'),
            'is_reopen'     => ConfigService::get('chat_config','is_reopen',0),
            'network_is_open'   => ConfigService::get('chat_config','network_is_open'),
            'network_balance'   => ConfigService::get('chat_config','network_balance')
        ];
        //判断是否需要重开聊天窗口
        if ($chat['is_reopen'] == 1) {
            $ChatCategory = ChatCategory::where(['user_id'=>$userId])->order('id','desc')->findOrEmpty();
            if ($ChatCategory->isEmpty()) {
                $chat['is_reopen'] = 0;
            } else {
                $ChatRecords = ChatRecords::where(['user_id'=>$userId,'category_id'=>$ChatCategory->id,'is_show'=>1])->findOrEmpty();
                if ($ChatRecords->isEmpty()) {
                    $chat['is_reopen'] = 0;
                }
            }
        }

        // 绘画配置
        $drawApiType = ConfigService::get('draw_config', 'type', DrawEnum::API_ZHISHUYUN_FAST);
        $drawTranslateType = ConfigService::get('draw_config', $drawApiType, []);
        $drawTranslateType = array_merge(DrawEnum::getDrawDefaultConfig(DrawEnum::API_ZHISHUYUN_FAST),$drawTranslateType);
        $draw = [
            'is_open' => ConfigService::get('draw_config', 'is_open', 0),
            'num' => ConfigService::get('draw_config', 'num', 10),
            'translate_type' => (int)$drawTranslateType['translate_type'] ?? 1,
            'mj_version' => DrawEnum::getMjVersion(),
            'mj_style' => DrawEnum::getMjStyle(),
            'disclaimer_status' => ConfigService::get('draw_config', 'disclaimer_status', 0),
            'disclaimer_content' => ConfigService::get('draw_config', 'disclaimer_content', ""),
        ];
        $tabbar =  DecorateTabbar::where(['type'=>2])->value('data');
        //关闭绘画功能，移除绘画菜单
//        if(0 == $draw['is_open']){
//            foreach ($tabbar['nav'] as $key => $nav){
//                //移除绘画菜单
//                if('/drawing' == $nav['link']['path']){
//                    unset($tabbar['nav'][$key]);
//                }
//            }
//            $tabbar['nav'] = array_values($tabbar['nav']);
//        }
        // 公告配置
        $bulletinConfig = [
            // 公告弹窗
            'is_bulletin' => ConfigService::get('bulletin_config', 'is_bulletin', 0),
            // 公告内容
            'bulletin_content' => ConfigService::get('bulletin_config', 'bulletin_content'),
            // 公告标题
            'bulletin_title' => ConfigService::get('bulletin_config', 'bulletin_title'),
        ];
        //卡密配置
        $cardCode = [
            'is_show' =>  ConfigService::get('card_code','is_show'),
            'buy_site' =>  ConfigService::get('card_code','buy_site'),
            'is_open' =>  ConfigService::get('card_code','is_open',0),
        ];

        //绘画广场配置
        $drawSquareConfig = [
            // 允许用户分享：1-开启；0-关闭；
            'is_allow_share' => ConfigService::get('draw_square_config', 'is_allow_share', config('project.draw_square_config.is_allow_share')),
            // 自动通过审核：1-开启；0-关闭；
            'is_auto_pass' => ConfigService::get('draw_square_config', 'is_auto_pass', config('project.draw_square_config.is_auto_pass')),
            // 显示用户信息：1-开启；0-关闭；
            'is_show_user' => ConfigService::get('draw_square_config', 'is_show_user', config('project.draw_square_config.is_show_user')),
        ];

        // H5配置
        $pcPage = [
            // 渠道状态 0-关闭 1-开启
            'status' => ConfigService::get('pc_page', 'status', 1),
            // 关闭后渠道后访问页面 0-空页面 1-自定义链接
            'page_status' => ConfigService::get('pc_page', 'page_status', 0),
            // 自定义链接
            'page_url' => ConfigService::get('pc_page', 'page_url', ''),
            'url' => request()->domain() . '/pc',
        ];

        //导图设置
        $mindmap_config = [
            'is_example'       => ConfigService::get('mindmap_config', 'is_example',config('project.mindmap_config.is_example')),
            'example_content'     => ConfigService::get('mindmap_config', 'example_content'),
        ];
        $voice = [
            'voice_broadcast'   => [
                'is_open' => ConfigService::get('voice_config','is_open')
            ],
            'voice_input'   => [
                'is_open'       => ConfigService::get('voice_input_config','is_open'),
            ],
            'voice_chat'   => [
                'is_open'       => ConfigService::get('voice_chat_config','is_open'),
            ],
        ];


        //判断是否需要绑定手机号
//        $coerce_mobile = ConfigService::get('login', 'coerce_mobile', config('project.login.coerce_mobile'));
//        $isBindMobile = false;
//        if ($coerce_mobile == 1 && $userId) {
//            $userMobile = User::where(['id' => $userId])->value('mobile');
//            if (empty($userMobile)) {
//                $isBindMobile = true;
//            }
//        }


        return [
            'install'   =>  $install,
            'domain' => FileService::getFileUrl(),
            'login' => $loginConfig,
            'website' => $website,
            'version' => config('project.version'),
            'copyright' => $copyright,
            'admin_url' => request()->domain() . '/admin',
            'qrcode' => [
                'oa' => $oaQrCode,
                'mnp' => $mnpQrCode,
            ],
            'chat' => $chat,
            'member_package_status' => ConfigService::get('member', 'status', 1),
            'member_package_time_view' => ConfigService::get('member', 'member_time_view', [1]),
            'recharge_package_status' => ConfigService::get('recharge', 'status', 1),
            'pc_login_image' => empty(ConfigService::get('website', 'pc_login_image')) ? '' : FileService::getFileUrl(ConfigService::get('website', 'pc_login_image')),
            'draw' => $draw,
            'tabbar' => $tabbar,
            'bulletin_config' => $bulletinConfig,
            'card_code' => $cardCode,
            'draw_square_config'  => $drawSquareConfig,
            'pc_page'  => $pcPage,
            'mindmap_config'  => $mindmap_config,
            'voice' => $voice,
            'current_domain' => request()->domain(true),
//            'is_bindmobile' => $isBindMobile,
        ];
    }


    /**
     * @notes 资讯中心
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2022/10/19 16:55
     */
    public static function getInfoCenter()
    {
        $data = ArticleCate::field(['id', 'name'])
            ->with(['article' => function ($query) {
                $query->hidden(['content', 'click_virtual', 'click_actual'])
                    ->order(['sort' => 'desc', 'id' => 'desc'])
                    ->append(['click'])
                    ->limit(10);
            }])
            ->where(['is_show' => YesNoEnum::YES])
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->select()
            ->toArray();

        return $data;
    }


    /**
     * @notes 获取文章详情
     * @param $userId
     * @param $articleId
     * @param string $source
     * @return array
     * @author 段誉
     * @date 2022/10/20 15:18
     */
    public static function getArticleDetail($userId, $articleId, $source = 'default')
    {
        // 文章详情
        $detail = Article::getArticleDetailArr($articleId);

        // 根据来源列表查找对应列表
        $nowIndex = 0;
        $lists = self::getLimitArticle($source, 0, $detail['cid']);
        foreach ($lists as $key => $item) {
            if ($item['id'] == $articleId) {
                $nowIndex = $key;
            }
        }
        // 上一篇
        $detail['last'] = $lists[$nowIndex - 1] ?? [];
        // 下一篇
        $detail['next'] = $lists[$nowIndex + 1] ?? [];

        // 最新资讯
        $detail['new'] = self::getLimitArticle('new', 8, $detail['cid'], $detail['id']);
        // 关注状态
        $detail['collect'] = ArticleCollect::isCollectArticle($userId, $articleId);
        // 分类名
        $detail['cate_name'] = ArticleCate::where('id', $detail['cid'])->value('name');

        return $detail;
    }

}