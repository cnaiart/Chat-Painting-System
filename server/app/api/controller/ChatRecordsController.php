<?php
// +----------------------------------------------------------------------
// | likeshop开源商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop系列产品在gitee、github等公开渠道开源版本可免费商用，未经许可不能去除前后端官方版权标识
// |  likeshop系列产品收费版本务必购买商业授权，购买去版权授权后，方可去除前后端官方版权标识
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | likeshop团队版权所有并拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshop.cn.team
// +----------------------------------------------------------------------

namespace app\api\controller;


use app\api\lists\ChatLogLists;
use app\api\lists\ChatRecordsCollectLists;
use app\api\logic\ChatRecordsLogic;
use app\api\validate\ChatRecordsValidate;
use app\common\service\zhipuai\ZhiPuChatService;
use Exception;
use Firebase\JWT\JWT;

class ChatRecordsController extends BaseApiController
{

    /**
     * @notes 聊天记录
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2023/4/25 2:39 下午
     */
    public function chatLog()
    {
        return $this->dataLists(new ChatLogLists());
    }

    /**
     * @notes 清除会话
     * @return \think\response\Json
     * @author ljj
     * @date 2023/4/25 11:37 上午
     */
    public function cleanChat()
    {
        $params = (new ChatRecordsValidate())->post()->goCheck('cleanChat', ['user_id' => $this->userId]);
        (new ChatRecordsLogic())->cleanChat($params);
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * @notes 收藏会话
     * @return \think\response\Json
     * @author ljj
     * @date 2023/4/25 11:44 上午
     */
    public function collect()
    {
        $params = (new ChatRecordsValidate())->post()->goCheck('collect', ['user_id' => $this->userId]);
        (new ChatRecordsLogic())->collect($params);
        return $this->success('收藏成功', [], 1, 1);
    }

    /**
     * @notes 取消收藏
     * @return \think\response\Json
     * @author ljj
     * @date 2023/4/25 11:47 上午
     */
    public function cancelCollect()
    {
        $params = (new ChatRecordsValidate())->post()->goCheck('cancelCollect', ['user_id' => $this->userId]);
        (new ChatRecordsLogic())->cancelCollect($params);
        return $this->success('取消收藏成功', [], 1, 1);
    }

    /**
     * @notes 收藏会话列表
     * @return \think\response\Json
     * @author ljj
     * @date 2023/4/25 11:58 上午
     */
    public function collectLists()
    {
        return $this->dataLists(new ChatRecordsCollectLists());
    }


    /**
     * @notes 加密敏感词文件
     * @author ljj
     * @date 2023/5/9 10:00 上午
     */
    public function encryption()
    {
        // 读取文件内容
        $filename = "../extend/sensitive_words.txt";
        $data = file_get_contents($filename);

        // 生成加密密钥
        $key = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $key = bin2hex($key);
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $iv = substr(bin2hex($iv), 0, 16);

        // 将密钥和初始化向量存储到文件中
        $file = fopen("../extend/sensitive_key.bin", "wb");
        fwrite($file, $key);
        fwrite($file, $iv);
        fclose($file);

        // 使用 CBC 模式加密数据
        $ciphertext = openssl_encrypt($data, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

        // 将加密数据存储到文件中
        $file = fopen("../extend/sensitive_data.bin", "wb");
        fwrite($file, $ciphertext);
        fclose($file);
    }

}