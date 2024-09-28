<?php
// +----------------------------------------------------------------------
// | likeshop100%开源免费商用商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | 商业版本务必购买商业授权，以免引起法律纠纷
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshopTeam
// +----------------------------------------------------------------------
namespace app\adminapi\logic\setting;
use app\common\model\SensitiveWord;
use app\common\service\ConfigService;

/**
 * 敏感词逻辑列
 * Class SensitiveWordLogic
 * @package app\adminapi\logic\setting
 */
class SensitiveWordLogic
{

    /**
     * @notes 添加
     * @param array $post
     * @return bool
     * @author cjhao
     * @date 2023/5/26 15:11
     */
    public function add(array $post)
    {
        $post['word'] = implode('；',$post['word']);
        SensitiveWord::create($post);
        return true;
    }


    /**
     * @notes 编辑
     * @param array $post
     * @return bool
     * @author cjhao
     * @date 2023/5/26 15:11
     */
    public function edit(array $post)
    {
        $post['word'] = implode('；',$post['word']);
        SensitiveWord::update($post);
        return true;
    }

    /**
     * @notes 删除
     * @param int $id
     * @author cjhao
     * @date 2023/5/26 15:11
     */
    public function del(int $id)
    {
        SensitiveWord::destroy(['id'=>$id]);
    }


    /**
     * @notes 修改状态
     * @param int $id
     * @return bool
     * @author cjhao
     * @date 2023/5/26 15:11
     */
    public function status(int $id)
    {
        $sensitiveWord = SensitiveWord::findOrEmpty($id);
        if($sensitiveWord->isEmpty()){
            return true;
        }
        $sensitiveWord->status = $sensitiveWord->status ? 0 : 1;
        $sensitiveWord->save();
    }


    /**
     * @notes 获取配置接口
     * @return array
     * @author ljj
     * @date 2023/6/19 5:14 下午
     */
    public function getConfig()
    {
        $result = [
            'is_sensitive' => ConfigService::get('chat_config','is_sensitive',1),
        ];

        return $result;
    }

    /**
     * @notes 设置配置接口
     * @param array $params
     * @return bool
     * @author ljj
     * @date 2023/6/19 5:20 下午
     */
    public function setConfig(array $params)
    {
        ConfigService::set('chat_config','is_sensitive',$params['is_sensitive']);
        return true;
    }
}