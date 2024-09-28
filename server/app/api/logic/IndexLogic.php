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


use app\common\enum\DrawEnum;
use app\common\enum\user\UserTerminalEnum;
use app\common\logic\BaseLogic;
use app\common\model\article\Article;
use app\common\model\ChatCategory;
use app\common\model\ChatRecords;
use app\common\model\decorate\DecoratePage;
use app\common\model\decorate\DecorateTabbar;
use app\common\model\IndexVisit;
use app\common\model\user\User;
use app\common\service\ConfigService;
use app\common\service\FileService;
use app\common\service\wechat\WeChatOaService;
use think\facade\Log;


/**
 * index
 * Class IndexLogic
 * @package app\api\logic
 */
class IndexLogic extends BaseLogic
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
        $decoratePage = DecoratePage::findOrEmpty(1);

        // 首页文章
        $field = [
            'id', 'title', 'desc', 'abstract', 'image',
            'author', 'click_actual', 'click_virtual', 'create_time'
        ];

        $article = Article::field($field)
            ->where(['is_show' => 1])
            ->order(['id' => 'desc'])
            ->limit(20)->append(['click'])
            ->hidden(['click_actual', 'click_virtual'])
            ->select()->toArray();

        return [
            'page' => $decoratePage,
            'article' => $article
        ];
    }


    /**
     * @notes 获取政策协议
     * @param string $type
     * @return array
     * @author 段誉
     * @date 2022/9/20 20:00
     */
    public static function getPolicyByType(string $type)
    {
        return [
            'title' => ConfigService::get('agreement', $type . '_title', ''),
            'content' => get_file_domain(ConfigService::get('agreement', $type . '_content', '')),
        ];
    }


    /**
     * @notes 装修信息
     * @param $id
     * @return array
     * @author 段誉
     * @date 2022/9/21 18:37
     */
    public static function getDecorate($id)
    {
        return DecoratePage::field(['type', 'name', 'data'])
            ->findOrEmpty($id)->toArray();
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
    public static function getConfigData($code,$userId)
    {

        // 是否安装
        $install = file_exists(root_path() . '/config/install.lock');
        if (!$install) {
            return ['install'=>$install];
        }
        // 导航颜色
        $style = ConfigService::get('tabbar', 'style', config('project.decorate.tabbar_style'));
        // 登录配置
        $loginConfig = [
            // 登录方式
            'login_way' => ConfigService::get('login', 'login_way', config('project.login.login_way')),
            // 注册强制绑定手机
            'coerce_mobile' => ConfigService::get('login', 'coerce_mobile', config('project.login.coerce_mobile')),
            // 政策协议
            'login_agreement' => ConfigService::get('login', 'login_agreement', config('project.login.login_agreement')),
            // 第三方登录 开关
//            'third_auth' => ConfigService::get('login', 'third_auth', config('project.login.third_auth')),
            // 微信授权登录
//            'wechat_auth' => ConfigService::get('login', 'wechat_auth', config('project.login.wechat_auth')),
            // qq授权登录
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
        // 网址信息
        $website = [
            'shop_name' => ConfigService::get('website', 'shop_name'),
            'shop_logo' => FileService::getFileUrl(ConfigService::get('website', 'shop_logo')),
        ];
        // H5配置
        $webPage = [
            // 渠道状态 0-关闭 1-开启
            'status' => ConfigService::get('web_page', 'status', 1),
            // 关闭后渠道后访问页面 0-空页面 1-自定义链接
            'page_status' => ConfigService::get('web_page', 'page_status', 0),
            // 自定义链接
            'page_url' => ConfigService::get('web_page', 'page_url', ''),
            'url' => request()->domain() . '/mobile'
        ];
        //分享配置
        $share = [
            'share_page'        => ConfigService::get('share', 'share_page',2),
            'share_title'       => ConfigService::get('share', 'share_title',''),
            'share_content'     => ConfigService::get('share', 'share_content',''),
            'share_image'       => FileService::getFileUrl(ConfigService::get('share', 'share_image','')),
        ];
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
        //是否需要强制关注公众号
        $is_follow_official = ConfigService::get('login', 'is_follow_official', config('project.login.is_follow_official'));
        $subscribe = 0;
        if ($code && $is_follow_official) {
            try {
                $response = (new WeChatOaService())->getOaResByCode($code);
                $response = (new WeChatOaService())->userInfo($response['openid'] ?? '');
                $subscribe = $response['subscribe'] ?? 0;
            } catch (\Exception $e) {
                Log::write('获取用户基本信息异常：'.$e->getMessage());
            }
        }

        //公众号二维码
        $official_qr_code = ConfigService::get('oa_setting', 'qr_code', '');
        $official_qr_code = empty($official_qr_code) ? $official_qr_code : FileService::getFileUrl($official_qr_code);

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

        // 底部导航
        $tabbar = DecorateTabbar::where(['type'=>1])->value('data');
        //关闭绘画功能，移除绘画菜单
//        if(0 == $draw['is_open']){
//            foreach ($tabbar['list'] as $key => $nav){
//                //移除绘画菜单
//                if('/pages/drawing/drawing' == $nav['link']['path']){
//                    unset($tabbar['list'][$key]);
//                }
//                $tabbar['list'] = array_values($tabbar['list']);
//            }
//        }

        //绘画广场配置
        $drawSquareConfig = [
            // 允许用户分享：1-开启；0-关闭；
            'is_allow_share' => ConfigService::get('draw_square_config', 'is_allow_share', config('project.draw_square_config.is_allow_share')),
            // 自动通过审核：1-开启；0-关闭；
            'is_auto_pass' => ConfigService::get('draw_square_config', 'is_auto_pass', config('project.draw_square_config.is_auto_pass')),
            // 显示用户信息：1-开启；0-关闭；
            'is_show_user' => ConfigService::get('draw_square_config', 'is_show_user', config('project.draw_square_config.is_show_user')),
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
            'style' => $style,
            'login' => $loginConfig,
            'website' => $website,
            'webPage' => $webPage,
            'version'=> config('project.version'),
            'share' => $share,
            'member_package_status' => ConfigService::get('member', 'status', 1),
            'member_package_time_view' => ConfigService::get('member', 'member_time_view', [1]),
            'recharge_package_status' => ConfigService::get('recharge', 'status', 1),
            'chat' => $chat,
            'is_follow_official' => $is_follow_official,
            'subscribe' => $subscribe,
            'official_qr_code' => $official_qr_code,
            'draw' => $draw,
            'tabbar' =>  $tabbar,
            'bulletin_config' => $bulletinConfig,
            'card_code'  => $cardCode,
            'draw_square_config'  => $drawSquareConfig,
            'mindmap_config'  => $mindmap_config,
            'voice' => $voice,
            'current_domain' => request()->domain(true),
//            'is_bindmobile' => $isBindMobile,
        ];
    }



    /**
     * @notes 首页访客记录
     * @return bool
     * @author Tab
     * @date 2021/9/11 9:29
     */
    public static function visit()
    {
        try {
            $params = request()->post();
            if (!isset($params['terminal']) || !in_array($params['terminal'], UserTerminalEnum::ALL_TERMINAL)) {
                throw new \Exception('终端参数缺失或有误');
            }
            $ip =  request()->ip();
            // 一个ip一个终端一天只生成一条记录
            $record = IndexVisit::where([
                'ip' => $ip,
                'terminal' => $params['terminal']
            ])->whereDay('create_time')->findOrEmpty();
            if (!$record->isEmpty()) {
                // 增加访客在终端的浏览量
                $record->visit += 1;
                $record->save();
                return true;
            }
            // 生成访客记录
            IndexVisit::create([
                'ip' => $ip,
                'terminal' => $params['terminal'],
                'visit' => 1
            ]);

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }
}