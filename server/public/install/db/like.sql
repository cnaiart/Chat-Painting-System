SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ai_admin
-- ----------------------------
DROP TABLE IF EXISTS `ai_admin`;
CREATE TABLE `ai_admin`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `root` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否超级管理员 0-否 1-是',
  `name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户头像',
  `account` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '账号',
  `password` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '密码',
  `login_time` int(10) NULL DEFAULT NULL COMMENT '最后登录时间',
  `login_ip` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '最后登录ip',
  `multipoint_login` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '是否支持多处登录：1-是；0-否；',
  `disable` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '是否禁用：0-否；1-是；',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '管理员表';

-- ----------------------------
-- Records of ai_admin
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_admin_dept
-- ----------------------------
DROP TABLE IF EXISTS `ai_admin_dept`;
CREATE TABLE `ai_admin_dept`  (
  `admin_id` int(10) NOT NULL DEFAULT 0 COMMENT '管理员id',
  `dept_id` int(10) NOT NULL DEFAULT 0 COMMENT '部门id',
  PRIMARY KEY (`admin_id`, `dept_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '部门关联表';

-- ----------------------------
-- Records of ai_admin_dept
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_admin_jobs
-- ----------------------------
DROP TABLE IF EXISTS `ai_admin_jobs`;
CREATE TABLE `ai_admin_jobs`  (
  `admin_id` int(10) NOT NULL COMMENT '管理员id',
  `jobs_id` int(10) NOT NULL COMMENT '岗位id',
  PRIMARY KEY (`admin_id`, `jobs_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '岗位关联表';

-- ----------------------------
-- Records of ai_admin_jobs
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_admin_role
-- ----------------------------
DROP TABLE IF EXISTS `ai_admin_role`;
CREATE TABLE `ai_admin_role`  (
  `admin_id` int(10) NOT NULL COMMENT '管理员id',
  `role_id` int(10) NOT NULL COMMENT '角色id',
  PRIMARY KEY (`admin_id`, `role_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '角色关联表';

-- ----------------------------
-- Records of ai_admin_role
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_admin_session
-- ----------------------------
DROP TABLE IF EXISTS `ai_admin_session`;
CREATE TABLE `ai_admin_session`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) UNSIGNED NOT NULL COMMENT '用户id',
  `terminal` tinyint(1) NOT NULL DEFAULT 1 COMMENT '客户端类型：1-pc管理后台 2-mobile手机管理后台',
  `token` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '令牌',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '更新时间',
  `expire_time` int(10) NOT NULL COMMENT '到期时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `admin_id_client`(`admin_id`, `terminal`) USING BTREE COMMENT '一个用户在一个终端只有一个token',
  UNIQUE INDEX `token`(`token`) USING BTREE COMMENT 'token是唯一的'
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '管理员会话表';

-- ----------------------------
-- Records of ai_admin_session
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_article
-- ----------------------------
DROP TABLE IF EXISTS `ai_article`;
CREATE TABLE `ai_article`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '文章id',
  `cid` int(11) NOT NULL COMMENT '文章分类',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '文章标题',
  `desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '简介',
  `abstract` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '文章摘要',
  `image` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '文章图片',
  `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '作者',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '文章内容',
  `click_virtual` int(10) NULL DEFAULT 0 COMMENT '虚拟浏览量',
  `click_actual` int(11) NULL DEFAULT 0 COMMENT '实际浏览量',
  `is_show` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否显示:1-是.0-否',
  `sort` int(5) NULL DEFAULT 0 COMMENT '排序',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(11) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '文章表';

-- ----------------------------
-- Records of ai_article
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_article_cate
-- ----------------------------
DROP TABLE IF EXISTS `ai_article_cate`;
CREATE TABLE `ai_article_cate`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '文章分类id',
  `name` varchar(90) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '分类名称',
  `sort` int(11) NULL DEFAULT 0 COMMENT '排序',
  `is_show` tinyint(1) NULL DEFAULT 1 COMMENT '是否显示:1-是;0-否',
  `create_time` int(10) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '文章分类表';

-- ----------------------------
-- Records of ai_article_cate
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_article_collect
-- ----------------------------
DROP TABLE IF EXISTS `ai_article_collect`;
CREATE TABLE `ai_article_collect`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `article_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '文章ID',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '收藏状态 0-未收藏 1-已收藏',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '文章收藏表';

-- ----------------------------
-- Records of ai_article_collect
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_card_code
-- ----------------------------
DROP TABLE IF EXISTS `ai_card_code`;
CREATE TABLE `ai_card_code`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sn` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '卡密编号',
  `type` tinyint(1) NOT NULL COMMENT '类型：1-会员套餐；2-充值套餐；3-对话次数；4-绘画次数',
  `relation_id` int(11) NOT NULL DEFAULT 0 COMMENT '关联套餐（充值、会员套餐）',
  `chat_num` int(11) NOT NULL DEFAULT 0 COMMENT '对话次数',
  `draw_num` int(11) NOT NULL DEFAULT 0 COMMENT '绘画次数',
  `card_num` int(11) NOT NULL COMMENT '卡密数量',
  `valid_start_time` int(11) NOT NULL COMMENT '有效开始时间',
  `valid_end_time` int(11) NOT NULL COMMENT '有效结束时间',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `sn`(`sn`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '卡密';

-- ----------------------------
-- Records of ai_card_code
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_card_code_record
-- ----------------------------
DROP TABLE IF EXISTS `ai_card_code_record`;
CREATE TABLE `ai_card_code_record`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sn` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '卡密编号',
  `card_id` int(11) NOT NULL COMMENT '卡密id',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态：0-未使用，1-已使用',
  `user_id` int(11) NULL DEFAULT NULL COMMENT '使用的用户id',
  `use_time` int(11) NULL DEFAULT NULL COMMENT '使用时间',
  `package_snapshot` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '套餐快照',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `sn`(`sn`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '卡密兑换记录';

-- ----------------------------
-- Records of ai_card_code_record
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_chat_category
-- ----------------------------
DROP TABLE IF EXISTS `ai_chat_category`;
CREATE TABLE `ai_chat_category`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `name` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '对话分类名称',
  `create_time` int(10) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '聊天对话分类';

-- ----------------------------
-- Records of ai_chat_category
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_chat_records
-- ----------------------------
DROP TABLE IF EXISTS `ai_chat_records`;
CREATE TABLE `ai_chat_records`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `category_id` int(11) NOT NULL DEFAULT 0 COMMENT '对话分类',
  `other_id` int(11) NOT NULL DEFAULT 0 COMMENT '创作模型ID、技能管理ID',
  `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '类型：1-AI问答；2-创作记录；3-技能对话；4-思维导图；',
  `ask` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '提问',
  `reply` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '回复',
  `reply_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '回复类型：1-模型回复；2-默认回复',
  `use_tokens` int(10) NULL DEFAULT NULL COMMENT '消耗tokens',
  `chat_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT 'ai返回信息',
  `is_show` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否显示：1-是；0-否；',
  `key` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '模型来源公司',
  `model` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '对话模型',
  `censor_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '审核状态：0-未审核；1-合规；2-不合规；3-疑似；4-审核失败；',
  `censor_result` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '审核结果',
  `censor_num` int(2) NULL DEFAULT 0 COMMENT '审核次数',
  `extra` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '预留扩展字段',
  `network_plugin` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '联网插件',
  `voice_plugin` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '语音插件',
  `create_time` int(10) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `type`(`type`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'AI对话记录表';

-- ----------------------------
-- Records of ai_chat_records
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_chat_records_collect
-- ----------------------------
DROP TABLE IF EXISTS `ai_chat_records_collect`;
CREATE TABLE `ai_chat_records_collect`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `records_id` int(11) NOT NULL COMMENT '对话记录ID',
  `create_time` int(10) NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '对话记录收藏表';

-- ----------------------------
-- Records of ai_chat_records_collect
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_config
-- ----------------------------
DROP TABLE IF EXISTS `ai_config`;
CREATE TABLE `ai_config`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '类型',
  `name` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '值',
  `create_time` int(10) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '配置表';

-- ----------------------------
-- Records of ai_config
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_creation_category
-- ----------------------------
DROP TABLE IF EXISTS `ai_creation_category`;
CREATE TABLE `ai_creation_category`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '类目名称',
  `sort` int(11) NULL DEFAULT NULL COMMENT '排序',
  `status` int(11) NOT NULL COMMENT '状态：1-开启，0-关闭',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '创作类别';

-- ----------------------------
-- Records of ai_creation_category
-- ----------------------------
BEGIN;
INSERT INTO `ai_creation_category` VALUES (1, 'AI工作', 2, 1, 1681805246, 1682586470, NULL), (4, '文字写作', 1, 1, 1682423695, 1688029399, NULL), (5, '新媒体运营', 1, 1, 1682504674, 1688029417, NULL), (6, '生活娱乐', 1, 1, 1682504680, 1688029425, NULL);
COMMIT;

-- ----------------------------
-- Table structure for ai_creation_model
-- ----------------------------
DROP TABLE IF EXISTS `ai_creation_model`;
CREATE TABLE `ai_creation_model`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '名称',
  `image` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '图标',
  `sort` int(11) NULL DEFAULT NULL COMMENT '排序',
  `category_id` int(11) NOT NULL COMMENT '类别id',
  `status` int(11) NOT NULL COMMENT '状态：1-开启，0-关闭',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '主题内容',
  `tips` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '提示文字',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  `context_num` int(5) UNSIGNED NOT NULL DEFAULT 2 COMMENT '上下文总数',
  `top_p` decimal(2, 1) UNSIGNED NOT NULL DEFAULT 0.9 COMMENT '随机属性',
  `presence_penalty` decimal(2, 1) UNSIGNED NOT NULL DEFAULT 0.5 COMMENT '话题属性',
  `frequency_penalty` decimal(2, 1) UNSIGNED NOT NULL DEFAULT 0.5 COMMENT '重复属性',
  `n` int(5) UNSIGNED NOT NULL DEFAULT 1 COMMENT '最大回复',
  `max_tokens` int(5) UNSIGNED NOT NULL DEFAULT 150 COMMENT '回复最大长度',
  `temperature` decimal(2, 1) UNSIGNED NOT NULL DEFAULT 0.6 COMMENT '词汇属性',
  `form` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '表单',
  `virtual_use_num` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '虚拟使用量',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 50 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '创作模型';
-- ----------------------------
-- Records of ai_creation_model
-- ----------------------------
BEGIN;
INSERT INTO `ai_creation_model` VALUES (1, '日报生成器', 'uploads/images/20230428/2023042815012318d626421.png', 100, 1, 1, '使用下面提供的文本作为${lji29mvj}的基础，请以工作内容开头，生成一个简要的${lji29mvj}，突出最重要的要点。${lji29mvj}应以中文书写，并采用降价格式，且应易于阅读和理解。请首先编辑以下文本：${ljgudfyw}', '输入工作内容，帮你快速完成日报。', 1681807727, 1688119884, NULL, 2, 0.9, 0.0, 0.0, 0, 150, 1.0, '[{\"name\":\"WidgetTextarea\",\"title\":\"多行文本\",\"id\":\"ljgudfyw\",\"props\":{\"title\":\"工作内容\",\"placeholder\":\"1.剪辑抖音视频8个 2.拍摄试镜2个 3.拍摄产品细节视频\",\"rows\":4,\"maxlength\":500,\"autosize\":false,\"isRequired\":true}},{\"name\":\"WidgetRadio\",\"title\":\"单选\",\"id\":\"lji29mvj\",\"props\":{\"title\":\"生成类型\",\"options\":[\"日报\",\"周报\",\"月报\"],\"defaultValue\":\"日报\",\"isRequired\":false},\"key\":\"field2\"}]',10), (3, '总结概括小助手', 'uploads/images/20230629/20230629154752b84795083.png', 1, 1, 1, '我希望你能根据我输入的内容描述：${ljgudg06}，概括为 ${ljicllar}个字，使其易于阅读和理解。避免使用复杂的句子结构或技术术语，不要偏离主题', '输入需要概括的内容，我们帮你精简总结。', 1681808367, 1688118173, NULL, 2, 0.9, 0.5, 0.5, 1, 150, 0.6, '[{\"name\":\"WidgetTextarea\",\"title\":\"多行文本\",\"id\":\"ljgudg06\",\"props\":{\"title\":\"内容描述\",\"placeholder\":\"笔记本电脑可以一直插着电源吗？会弄坏电池吗？ 在使用笔记本电脑时，你担心过电池的使用寿命吗？ 是否可以一直插着电源使用？ 是否需要用完电池再充电？ 究竟如何使用电池才能保证其寿命呢？ 可以把电脑用到没电关机吗？ 现在的笔记本电脑大多使用锂电池，有些朋友会担心深度放电、甚至是过度放电会无法挽回地损伤电池。其实这个担心有一定道理，不过也不用太小心不敢用电池了，厂家通常会设计电池管理系统，让你电脑没电的时候其实也没到深度放电的程度。总而言之，方便充电的时候就充着吧。 可以长期存放电脑吗？ 有些朋友可能不常用笔记本电脑，或者拥有一台备用电脑。这些笔记本电脑在长期存放的时候，为了保护电池，最好在关机之前保持电量在50%左右，不要太满，也别太少。 就算你关机了，电池还是会“偷偷地”自放电。所以如果你在电池电量完全耗尽的状态下长时间存放设备，电池可能深度放电，从而导致结构损坏。到这时候再充电，可能就充不进去了！ 相反，如果长期存放充满电的设备，可能会损害电池，使电池容量降低。 如果你打算让电脑吃灰6个月以上，最好每隔6个月拿出来擦擦灰给它充电到50%。 可以一直给笔记本电脑插着电吗？ 有些朋友常年在办公室或家中使用笔记本电脑，电脑一直插着电从未间断，电量始终维持在100%。这样做有一点点需要担心的是，长期使锂电池满电状态可能会让电池钝化，影响电池容量。 所以对于常年给笔记本电脑插着电源的朋友，我们的建议是，随心所欲偶尔想起来了，就拔下电源用用电池。毕竟你也不能保证一直都有电源可用，稍微维护一下电池寿命保险一些。哦记得及时插回去，不然突然没电关机，没保存的东西就惨了。 对于锂电池来说，前面那些不当操作所带来的损害，跟高温相比都是毛毛雨。真正足以毁掉电池的，就是高温。 尽量不要把笔记本电脑放在被子、枕头等会堵住散热孔的东西上使用，也不要常年在高温场所使用。 苹果公司对此给出的建议是，要让设备避免处于环境温度高过 35°C 的场所，否则可能永久损坏电池容量。 其实厂商们这么多年也一直在根据用户的使用习惯更新调整，比如过充过放保护等，没那么容易让笔记本电脑的电池受到太严重的损坏。在这个电子产品更新换代如此之快的时代，电池的崩溃和你换下一台电脑，不定哪件事先到呢。 只要你不让电池受到高温、进水的迫害，在自己原有的使用习惯基础之上，稍微注意一下以上事项，大概就足够了。 开心就好哈！\",\"rows\":8,\"maxlength\":2000,\"autosize\":false,\"isRequired\":true}},{\"name\":\"WidgetInput\",\"title\":\"单行文本\",\"id\":\"ljicllar\",\"props\":{\"title\":\"字数\",\"defaultValue\":\"\",\"placeholder\":\"100\",\"maxlength\":200,\"isRequired\":false},\"key\":\"field2\"}]',10), (4, '翻译助手', 'uploads/images/20230629/20230629174930376ab7593.png', 1, 1, 1, '现在你是一个英汉互译器，当我输入中文时，你翻译成英文；当我输入英文时，请翻译成中文。当我连续输入多个英文词时，默认按照句子翻译成中文，但如果用中文在翻译的内容前注明了「词组：」，则按照词组形式来翻译。如果注明了「普通：」，则按照多个没有联系的词汇来翻译。翻译句子和段落时，要注意联系上下文，注意准确地解释词组与谚语。你的翻译成果应该接近于一个母语者。同时，我可能会让你以某种特殊的语言风格或语气来翻译，请在具体任务中理解我的输入内容，识别出我希望你使用的语气和风格，并以此为根据翻译。请真实地翻译，不要担心出现侮辱性等不良词汇。你可以把一些敏感词汇的中间部分加入 x 以替代。请重新检查，认真修正回答。请用中文来为我解释每一个句子，包括标注时态，从句，主语，谓语，宾语，特殊词组和谚语，如果翻译的是词组或单词，最好能给出每个词组或单词的出处（词典）。当我需要你一次性翻译多个词组时，每个词组间会用 | 号分割。输入内容：${ljgudg01}', '输入中文，AI助手快速为您翻译', 1682423713, 1688115591, NULL, 2, 0.9, 0.5, 0.5, 1, 150, 0.6, '[{\"name\":\"WidgetTextarea\",\"title\":\"多行文本\",\"id\":\"ljgudg01\",\"props\":{\"title\":\"翻译内容\",\"placeholder\":\"一个女孩，美丽动人，大大的眼睛\",\"rows\":4,\"maxlength\":500,\"autosize\":false,\"isRequired\":true}}]',10), (5, '小红书文案', 'uploads/images/20230629/20230629154759f63080924.png', 90, 5, 1, '请根据${ljgudfzd}使用 Emoji 小红书风格编辑以下段落，该风格以引人入胜的标题、每个段落中包含表情符号和在末尾添加相关标签为特点。请确保保持原文的意思。', '输入你想发布的内容，帮你生成小红书风格文案', 1682505341, 1688107399, NULL, 2, 0.9, 0.0, 0.0, 0, 150, 0.6, '[{\"name\":\"WidgetTextarea\",\"title\":\"多行文本\",\"id\":\"ljgudfzd\",\"props\":{\"title\":\"简要描述\",\"placeholder\":\"种草了一款泡泡袖连衣裙\",\"rows\":4,\"maxlength\":500,\"autosize\":false,\"isRequired\":true}}]',10), (6, '短视频脚本', 'uploads/images/20230629/20230629154752f1bc85148.png', 1, 1, 1, '请以人的口吻，采用缩略语、成语、过渡短语、感叹词、悬垂修饰语和口语化语言，避免重复短语和不自然的句子结构，撰写一篇关于${ljgudfzw}的脚本。', '输入想要拍摄的主题，小助手为你生成一个短视频脚本', 1682505398, 1688121061, NULL, 2, 0.9, 0.5, 0.5, 1, 150, 0.6, '[{\"name\":\"WidgetTextarea\",\"title\":\"多行文本\",\"id\":\"ljgudfzw\",\"props\":{\"title\":\"视频主题\",\"placeholder\":\"餐厅探店短视频脚本\",\"rows\":4,\"maxlength\":500,\"autosize\":false,\"isRequired\":true}}]',8), (8, '商品好评', 'uploads/images/20230629/202306291547590da793048.png', 1, 5, 1, '根据我输入的商品名称${ljgudfyy}，结合商品描述${ljgudfz1}，生成一段很长的好评文案', '帮您生成超真实的商品好评文案', 1682505579, 1688108239, NULL, 2, 0.9, 0.5, 0.5, 1, 150, 0.6, '[{\"name\":\"WidgetInput\",\"title\":\"单行文本\",\"id\":\"ljgudfyy\",\"props\":{\"title\":\"商品名称\",\"placeholder\":\"连衣裙\",\"maxlength\":200,\"isRequired\":true}},{\"name\":\"WidgetTextarea\",\"title\":\"多行文本\",\"id\":\"ljgudfz1\",\"props\":{\"title\":\"商品描述\",\"placeholder\":\"布料透软舒适，上身效果很好\",\"rows\":4,\"maxlength\":500,\"autosize\":false,\"isRequired\":true}}]',20), (12, '写故事', 'uploads/images/20230629/20230629154759261a17445.png', 95, 4, 1, '我希望你扮演一个讲故事的人，用中文回应。你会想出引人入胜、富有想象力、引人入胜的有趣故事。它可以是童话、教育故事或任何其他类型的故事，有可能吸引人们的注意力和想象力。根据目标受众的不同，你可以选择讲故事的特定主题或主题，例如，如果是孩子，那么你可以谈论动物；如果是成年人，那么基于历史的故事可能会更好地吸引他们。我的第一个要求是${ljgudfza}', '根据关键词生成原创文章和对应的标题', 1682505819, 1688117611, NULL, 2, 0.9, 0.5, 0.5, 1, 150, 0.6, '[{\"name\":\"WidgetTextarea\",\"title\":\"多行文本\",\"id\":\"ljgudfza\",\"props\":{\"title\":\"文本内容\",\"placeholder\":\"农夫与蛇\",\"rows\":4,\"maxlength\":200,\"autosize\":false,\"isRequired\":true}}]',2), (15, '旅游计划', 'uploads/images/20230629/20230629174507177ea4954.png', 1, 6, 1, '根据我输入的文字内容或者[地名]，帮我制定一份关于[地名]的旅游的计划', '不知道去哪里旅游?来问问AI', 1682506001, 1688032004, NULL, 2, 0.9, 0.5, 0.5, 1, 150, 0.6, '[{\"name\":\"WidgetInput\",\"title\":\"单行文本\",\"id\":\"ljgudfzy\",\"props\":{\"title\":\"旅游地点\",\"placeholder\":\"云南\",\"maxlength\":200,\"isRequired\":true}}]',8), (16, '论文式回答', 'uploads/images/20230629/20230629154759db0d64124.png', 1, 4, 1, '写一篇高度详细的文章，包括引言、主体和结论段落，以回应以下内容：${ljgudfzt}', '以论文形式讨论问题，能够获得连贯的、结构化的和更高质量的回答。', 1682506028, 1688119548, NULL, 2, 0.9, 0.5, 0.5, 1, 150, 0.6, '[{\"name\":\"WidgetTextarea\",\"title\":\"多行文本\",\"id\":\"ljgudfzt\",\"props\":{\"title\":\"论文内容\",\"placeholder\":\"AI对自媒体工作者的影响\",\"rows\":4,\"maxlength\":500,\"autosize\":false,\"isRequired\":true}}]',32), (19, '英文写作助手', 'uploads/images/20230629/20230629154752d07ca9305.png', 1, 4, 1, '根据我输入的要求，帮我润色成优美的英文作文，我的第一个要求是：${ljgudg07}', '输入英文或中文，帮你润色成优美的英文', 1682506123, 1688119010, NULL, 2, 0.9, 0.5, 0.5, 1, 150, 0.6, '[{\"name\":\"WidgetTextarea\",\"title\":\"多行文本\",\"id\":\"ljgudg07\",\"props\":{\"title\":\"写作内容\",\"placeholder\":\"写一封邀请信，邀请英国大学的一位教授组队来中国参加国际创新大赛\",\"rows\":4,\"maxlength\":500,\"autosize\":false,\"isRequired\":true}}]',10), (20, '阅读助手', 'uploads/images/20230629/20230629154752b7e209843.png', 1, 4, 1, '在以下这个场景中，有人对我说了一句话，请帮我分析对方可能想表达什么意思，并提供一个合适的回应。场景：${lji29mvg}。说话人说：${lji29mvf}。对方的意图可能是什么？我应该如何回应？', '对于一些无法理解的对话，提供对话背景让 AI 来进行解读并制定出适当的回应。', 1682506139, 1688119344, NULL, 2, 0.9, 0.5, 0.5, 1, 150, 0.6, '[{\"name\":\"WidgetInput\",\"title\":\"单行文本\",\"id\":\"lji29mvg\",\"props\":{\"title\":\"描述情境\",\"defaultValue\":\"\",\"placeholder\":\"吃饭的时候\",\"maxlength\":200,\"isRequired\":true}},{\"name\":\"WidgetTextarea\",\"title\":\"多行文本\",\"id\":\"lji29mvf\",\"props\":{\"title\":\"具体的话语\",\"placeholder\":\"你好厉害！\",\"rows\":4,\"defaultValue\":\"\",\"maxlength\":500,\"autosize\":false,\"isRequired\":true},\"key\":\"field2\"}]',10), (29, '朋友圈文案', 'uploads/images/20230629/2023062915475966e285838.png', 100, 5, 1, '根据输入的关键词${ljgudfyp}，帮我生成吸引人的朋友圈文案。', '输入关键词，生成吸引人的朋友圈文案。', 1682506801, 1688117956, NULL, 2, 0.9, 0.5, 0.5, 1, 150, 0.6, '[{\"name\":\"WidgetInput\",\"title\":\"单行文本\",\"id\":\"ljgudfyp\",\"props\":{\"title\":\"关键词\",\"placeholder\":\"周末去喝了咖啡，和朋友聚了聚\",\"maxlength\":200,\"isRequired\":true}}]',22), (30, '广告创意', 'uploads/images/20230428/20230428154146d05d30456.png', 1, 5, 1, '我希望你能根据我输入的内容${ljgudfzk}，帮我生成几个有意思的广告创意', '输入想写的创意故事主题，小助手为你生成一篇妙趣横生的小故事', 1682506856, 1688107540, NULL, 2, 0.9, 0.0, 0.0, 0, 150, 0.6, '[{\"name\":\"WidgetInput\",\"title\":\"单行文本\",\"id\":\"ljgudfzk\",\"props\":{\"title\":\"内容描述\",\"placeholder\":\"三只小猪造房子\",\"maxlength\":500,\"isRequired\":true}}]',36), (33, '节日祝福', 'uploads/images/20230629/20230629170837644e78809.png', 1, 6, 1, '根据我输入的节日名称或关键词，帮我写一封节日祝福文案', '输入节日名称和关键词，帮您写一封节日祝福', 1682506924, 1688029846, NULL, 2, 0.9, 0.5, 0.5, 1, 150, 0.6, '[{\"name\":\"WidgetInput\",\"title\":\"单行文本\",\"id\":\"ljgudfzf\",\"props\":{\"title\":\"节日名称\",\"placeholder\":\"母亲节\",\"maxlength\":200,\"isRequired\":true}}]',10);
COMMIT;
-- ----------------------------
-- Table structure for ai_creation_model_collect
-- ----------------------------
DROP TABLE IF EXISTS `ai_creation_model_collect`;
CREATE TABLE `ai_creation_model_collect`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `creation_id` int(11) NOT NULL COMMENT '创作id',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '创作模型收藏' ROW_FORMAT = DYNAMIC;
-- ----------------------------
-- Records of ai_creation_model_collect
-- ----------------------------

-- ----------------------------
-- Table structure for ai_decorate_page
-- ----------------------------
DROP TABLE IF EXISTS `ai_decorate_page`;
CREATE TABLE `ai_decorate_page`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `type` tinyint(2) UNSIGNED NOT NULL DEFAULT 10 COMMENT '页面类型 1=商城首页, 2=个人中心, 3=客服设置 4-PC首页',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '页面名称',
  `data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '页面数据',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '装修页面配置表';

-- ----------------------------
-- Records of ai_decorate_page
-- ----------------------------
BEGIN;
INSERT INTO `ai_decorate_page` (`id`, `type`, `name`, `data`, `create_time`, `update_time`) VALUES (1, 1, '个人中心', '[{\"id\":\"lhzm5mrxdh6lb\",\"title\":\"用户信息\",\"name\":\"user-info\",\"disabled\":1,\"content\":{},\"styles\":{}},{\"id\":\"lhzm5mrxaf31p\",\"title\":\"vip会员\",\"name\":\"open-vip\",\"content\":{\"icon\":\"uploads/images/20240202/20240202164640de66c1217.png\",\"title\":\"充值会员\",\"sub_title\":\"畅享无限次数对话，享更多乐趣\",\"btn\":\"去开通\"},\"styles\":{}},{\"id\":\"lhzm5mrxdi2je\",\"title\":\"我的服务\",\"name\":\"my-service\",\"content\":{\"style\":1,\"title\":\"我的服务\",\"data\":[{\"image\":\"uploads/images/20240202/20240202164631352117494.png\",\"name\":\"任务奖励\",\"link\":{\"path\":\"/packages/pages/task_center/task_center\",\"name\":\"任务中心\",\"type\":\"shop\",\"canTab\":true},\"is_show\":\"1\"},{\"image\":\"uploads/images/20240202/20240202164636dd5206879.png\",\"name\":\"充值中心\",\"link\":{\"path\":\"/packages/pages/recharge/recharge\",\"name\":\"充值中心\",\"type\":\"shop\"},\"is_show\":\"1\"},{\"image\":\"resource/image/adminapi/default/distribution_promotion.png\",\"name\":\"分销推广\",\"link\":{\"path\":\"/packages/pages/promotion_center/promotion_center\",\"name\":\"分销中心\",\"type\":\"shop\"},\"is_show\":\"1\"},{\"image\":\"resource/image/adminapi/default/card_code.png\",\"name\":\"卡密兑换\",\"link\":{\"path\":\"/packages/pages/redeem_code/redeem_code\",\"name\":\"卡密兑换\",\"type\":\"shop\",\"canTab\":true},\"is_show\":\"1\"},{\"image\":\"resource/image/adminapi/default/invitation_poster.png\",\"name\":\"邀请海报\",\"link\":{\"path\":\"/packages/pages/invite_poster/invite_poster\",\"name\":\"邀请海报\",\"type\":\"shop\"},\"is_show\":\"1\"},{\"image\":\"resource/image/adminapi/default/center_collect.png\",\"name\":\"我的收藏\",\"link\":{\"path\":\"/packages/pages/collection/collection\",\"name\":\"我的收藏\",\"type\":\"shop\"},\"is_show\":\"1\"},{\"image\":\"resource/image/adminapi/default/article.png\",\"name\":\"文章资讯\",\"link\":{\"path\":\"/packages/pages/article_list/article_list\",\"name\":\"文章资讯\",\"type\":\"shop\"},\"is_show\":\"1\"},{\"image\":\"resource/image/adminapi/default/feedback.png\",\"name\":\"意见反馈\",\"link\":{\"path\":\"/packages/pages/feedback/feedback\",\"name\":\"意见反馈\",\"type\":\"shop\"},\"is_show\":\"1\"},{\"image\":\"resource/image/adminapi/default/contact_service.png\",\"name\":\"联系客服\",\"link\":{\"path\":\"/packages/pages/customer_service/customer_service\",\"name\":\"联系客服\",\"type\":\"shop\",\"canTab\":true},\"is_show\":\"1\"},{\"image\":\"resource/image/adminapi/default/about_us.png\",\"name\":\"关于我们\",\"link\":{\"path\":\"/packages/pages/as_us/as_us\",\"name\":\"关于我们\",\"type\":\"shop\"},\"is_show\":\"1\"}]},\"styles\":{}},{\"id\":\"lhzm5mrxo47fo\",\"title\":\"个人中心广告图\",\"name\":\"user-banner\",\"content\":{\"enabled\":1,\"data\":[{\"image\":\"uploads/images/20231108/20231108175150cd3080773.png\",\"name\":\"\",\"link\":{\"path\":\"/pages/index/index\",\"name\":\"商城首页\",\"type\":\"shop\"}}]},\"styles\":{}},{\"title\":\"底部信息\",\"name\":\"user-bottom\",\"content\":{\"title\":\"网页版：\",\"content\":\"www.baidu.com/\",\"canCopy\":1},\"styles\":{}}]', 1661757188, 1706863637), (2, 2, '会员中心', '[{\"title\":\"走马灯\",\"name\":\"vip-top\",\"disabled\":0,\"content\":{\"enable\":2,\"enabled\":1},\"styles\":{}},{\"title\":\"\",\"name\":\"vip-body\",\"disabled\":1,\"content\":{\"style\":1},\"styles\":{}},{\"title\":\"会员权益\",\"name\":\"vip-advantage\",\"content\":{\"enabled\":1,\"name\":\"会员权益\",\"data\":[{\"image\":\"uploads/images/20230426/20230426161049b7b921148.png\",\"name\":\"与AI畅聊\"},{\"image\":\"uploads/images/20230426/20230426161049ba0468649.png\",\"name\":\"人工智能\"},{\"image\":\"uploads/images/20230426/20230426161049cecb06682.png\",\"name\":\"智能对话\"},{\"image\":\"uploads/images/20230426/20230426161049e2c763351.png\",\"name\":\"超低延迟\"},{\"image\":\"uploads/images/20230426/20230426161049011657391.png\",\"name\":\"独享接口\"},{\"image\":\"uploads/images/20230426/202304261610492c57b4038.png\",\"name\":\"专属客服\"}],\"style\":2,\"enable\":1},\"styles\":{}},{\"title\":\"会员说明\",\"name\":\"vip-notice\",\"content\":{\"enabled\":1,\"name\":\"开通会员须知\",\"data\":\"1.会员权益为虚拟服务，套餐开通成功后不可退换，敬请谅解；\\n2.扣款成功后会员权益即刻生效若未即刻生效请等待30分钟或联系客服；\\n3.由于AI需要大量计算资源，高峰期可能会服务波动，系统可能会降级服务；\\n4.开发不易,您的支持将保证我们持续运营努力变得更好；\\n5.在法律允许范围内，本公司对本产品享有最终解释权。\"},\"styles\":{}},{\"title\":\"用户评价\",\"name\":\"vip-evaluate\",\"content\":{\"enabled\":1,\"data\":5},\"styles\":{}}]', 1661757188, 1699501965), (3, 3, '客服设置', '[{\"title\":\"客服设置\",\"name\":\"customer-service\",\"content\":{\"title\":\"添加客服二维码\",\"time\":\"早上 9:00 - 22:00\",\"mobile\":\"13800138000\",\"qrcode\":\"uploads/images/20230926/202309261029094f5115385.png\",\"title_status\":\"1\",\"mobile_status\":\"1\",\"time_status\":\"1\"},\"styles\":{}}]', 1661757188, 1695695357), (4, 4, '系统风格', '{\"themeColorId\":5,\"topTextColor\":\"black\",\"navigationBarColor\":\"#FFFFFF\",\"themeColor1\":\"#FFC94D\",\"themeColor2\":\"#FFB814\",\"buttonColor\":\"white\"}', 1661757188, 1706497146), (5, 5, '邀请海报', '[{\"id\":\"lhzliq33nbu2y\",\"title\":\"邀请海报\",\"name\":\"invite-poster\",\"content\":{\"default\":2,\"defaultUrl1\":\"/resource/image/adminapi/default/invite_backdend01.png\",\"defaultUrl2\":\"/resource/image/adminapi/default/invite_backdend02.png\",\"defaultQrcode\":\"/resource/image/adminapi/default/qrcode.png\",\"poster\":1,\"posterUrl\":\"uploads/images/20231108/20231108160004a782a1618.jpg\",\"data\":{\"content\":\"扫码体验\",\"x\":112,\"y\":311},\"showData\":1,\"code\":{\"x\":88,\"y\":336}},\"styles\":{}},{\"id\":\"lhzliq33onwgd\",\"title\":\"规则说明\",\"name\":\"invite-rule\",\"disabled\":0,\"content\":{\"enabled\":1,\"name\":\"邀请说明\",\"data\":\"好友通过您分享的邀请分销海报注册登录后，Ta将永久成为您的下级分销商，未来Ta产生的订单佣金您都有奖励，会自动计入您的账号中！!\"},\"styles\":{}}]', 1661757188, 1699430428), (6, 6, '对话海报', '[{\"id\":\"li34ldqby4fkb\",\"title\":\"对话海报\",\"name\":\"dialogue-poster\",\"content\":{\"default\":1,\"defaultUrl1\":\"/resource/image/adminapi/default/dialogue_backdend01.png\",\"defaultUrl2\":\"/resource/image/adminapi/default/dialogue_backdend02.png\",\"defaultAvatar\":\"/resource/image/adminapi/default/user_avatar.jpeg\",\"defaultQrcode\":\"/resource/image/adminapi/default/qrcode.png\",\"poster\":1,\"posterUrl\":\"uploads/images/20231108/202311081701220c5445131.png\",\"data\":\"邀请您前来体验\",\"showData\":\"1\",\"bgType\":1,\"bgColor1\":\"#4A5DFF\",\"bgColor2\":\"#FF9F43\",\"showContentType\":2,\"contentNum\":13,\"textColor\":\"#ffffff\",\"bgColor\":\"#409EFF\"},\"styles\":{}}]', 1661757188, 1706695489), (7, 7, '绘画广场', '[{\"title\":\"AI应用\",\"name\":\"ai-app\",\"content\":{\"data\":[{\"image\":\"resource/image/adminapi/default/ai_app.png\",\"link\":{\"path\":\"/packages/pages/drawing/drawing\",\"name\":\"AI绘画\",\"type\":\"shop\",\"canTab\":true},\"is_show\":\"1\",\"title\":\"AI绘画\",\"desc\":\"快速生成大师级绘画作品\",\"pcLink\":{\"path\":\"/app/drawing\",\"name\":\"AI绘画\",\"type\":\"shop\"}},{\"image\":\"resource/image/adminapi/default/ai_app.png\",\"link\":{\"path\":\"/packages/pages/square/square\",\"name\":\"绘画广场\",\"type\":\"shop\",\"canTab\":true},\"is_show\":\"1\",\"title\":\"绘画广场\",\"desc\":\"提供各类精美作品给用户欣赏和使用\",\"pcLink\":{\"path\":\"/app/square\",\"name\":\"绘画广场\",\"type\":\"shop\"}},{\"image\":\"resource/image/adminapi/default/ai_app.png\",\"pcLink\":{\"path\":\"/app/mind_map\",\"name\":\"思维导图\",\"type\":\"shop\"},\"link\":{\"path\":\"/packages/pages/mind_map/mind_map\",\"name\":\"思维导图\",\"type\":\"shop\",\"canTab\":true},\"is_show\":\"1\",\"title\":\"思维导图\",\"desc\":\"输入简单描述，AI将智能输出markdown内容\"},{\"image\":\"resource/image/adminapi/default/ai_app.png\",\"pcLink\":{\"path\":\"/app/qrcode\",\"name\":\"艺术二维码\",\"type\":\"shop\"},\"link\":{\"path\":\"/packages/pages/qrcode/qrcode\",\"name\":\"艺术二维码\",\"type\":\"shop\",\"canTab\":true},\"is_show\":\"1\",\"title\":\"艺术二维码\",\"desc\":\"快速生成艺术二维码\"},{\"image\":\"uploads/images/20231020/20231020155759c5bd76003.png\",\"pcLink\":{\"path\":\"/create\",\"name\":\"AI创作\",\"type\":\"shop\"},\"link\":{\"path\":\"/pages/ai_creation/ai_creation\",\"name\":\"AI创作\",\"type\":\"shop\",\"canTab\":true},\"is_show\":\"0\",\"title\":\"创作\"},{\"image\":\"uploads/images/20231130/202311301114420ce0a3849.png\",\"pcLink\":{\"path\":\"/pages/webview/webview\",\"query\":{\"url\":\"https://www.baidu.com/\"},\"type\":\"custom\"},\"link\":{\"path\":\"/pages/ai_creation/ai_creation\",\"name\":\"AI创作\",\"type\":\"shop\",\"canTab\":true},\"is_show\":\"1\",\"title\":\"第三方链接\"}]},\"styles\":{}}]', 1661757188, 1703583144), (8, 8, '首页装修', '[{\"title\":\"对话示例\",\"name\":\"index-example\",\"disabled\":1,\"content\":{},\"styles\":{}},{\"title\":\"提示\",\"name\":\"index-tips\",\"content\":{\"show\":1,\"title\":\"局限性\",\"sub_title\":\"禁止利用AI进行非法活动\"},\"styles\":{}},{\"title\":\"输入\",\"name\":\"index-input\",\"disabled\":1,\"content\":{},\"styles\":{}}]', 1694748160, 1703819042), (9, 9, 'pc端-系统风格', '{\"themeId\":3,\"primaryColor\":\"#A33AFE\",\"minorColor\":\"#762AFF\",\"buttonColor\":\"white\"}', 1661757188, 1706500347), (10, 10, '任务中心', '[{\"id\":\"lps6z7k4z9x0e\",\"title\":\"任务中心\",\"name\":\"task-center\",\"content\":{\"title\":\"每日任务\",\"subTitle\":\"免费获得条数\",\"data\":[{\"image\":\"/resource/image/api/default/share.png\",\"name\":\"分享给好友\",\"customName\":\"\",\"show\":1,\"type\":3},{\"image\":\"uploads/images/20231201/20231201145138ea3356730.png\",\"name\":\"分享绘画至广场\",\"customName\":\"绘画分享\",\"show\":1,\"type\":4},{\"image\":\"/resource/image/api/default/invite.png\",\"name\":\"邀请新用户\",\"customName\":\"邀请好友\",\"show\":1,\"type\":2},{\"image\":\"/resource/image/api/default/sign.png\",\"name\":\"每日签到\",\"customName\":\"签到奖励\",\"show\":1,\"type\":1}]},\"styles\":{}}]', 1701766819, 1702979826), (11, 11, '首页介绍宣传页', '[{"id":"lrycdv3f5shdn","title":"标题设置","name":"title","isShow":true,"prop":{"isShowBtn":true,"title":"AI聊天+AI绘画","desc":"ChatAI聚合对接国内外知名主流大语言模型接口及中转平台，如ChatGLM、讯飞星火、文心一言、GPT3.5、GPT4.0、API2D、知数云。并且聚合对接MJ绘画、SD绘画、意间AI等多种绘画通道，实现了AI对话+AI绘画的融合使用。系统功能包括：AI智能对话、AI创作模型、AI绘画、分销推广、会员套餐、充值套餐、卡密兑换、模型计费、会员回流优惠券等丰富的营销功能。","btnText":"立即前往体验","link":{"path":"/chat","name":"AI对话","type":"shop"}}},{"id":"lrycdv3fi5dg1","title":"功能介绍","name":"intro","isShow":true,"prop":{"data":[{"image":"resource/image/adminapi/default/ai_app.png","title":"这是标题","subtitle":"这是一段标题简介","functionPoint":[],"isShow":true,"link":{"path":"/app/mind_map","name":"思维导图","type":"shop"}},{"image":"resource/image/adminapi/default/ai_app.png","title":"这是标题","subtitle":"这是一段标题简介","functionPoint":[],"isShow":true,"link":{"path":"/skill","name":"AI技能","type":"shop"}},{"image":"resource/image/adminapi/default/ai_app.png","title":"这是标题","subtitle":"这是一段标题简介","functionPoint":[],"isShow":true,"link":{"path":"/create","name":"AI创作","type":"shop"}},{"image":"resource/image/adminapi/default/ai_app.png","title":"这是标题","subtitle":"这是一段标题简介","functionPoint":[],"isShow":true,"link":{"path":"/app","name":"AI应用","type":"shop"}}]}},{"id":"lrycdv3fcsrij","title":"引导设置","name":"guide","isShow":true,"prop":{"bgImage":"resource/image/adminapi/default/guidance_backdrop.png","content":"联系客服获取同款系统","link":{"path":"/chat","name":"AI对话","type":"shop"},"isShowBtn":false,"btnText":"立即体验"}}]', 1701766819, 1706867592);
COMMIT;

-- ----------------------------
-- Table structure for ai_decorate_tabbar
-- ----------------------------
DROP TABLE IF EXISTS `ai_decorate_tabbar`;
CREATE TABLE `ai_decorate_tabbar`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '页面类型:1-移动端导航栏；2-PC端导航栏',
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '导航名称',
  `data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '已选图标',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '装修底部导航表';

-- ----------------------------
-- Records of ai_decorate_tabbar
-- ----------------------------
BEGIN;
INSERT INTO `ai_decorate_tabbar` VALUES (1, 1, '移动端导航栏', '{\"style\":{\"default_color\":\"#999999\",\"selected_color\":\"#FFBA3C\"},\"list\":[{\"name\":\"首页\",\"selected\":\"resource/image/adminapi/default/question.png\",\"unselected\":\"resource/image/adminapi/default/select-question.png\",\"link\":{\"path\":\"/pages/index/index\",\"name\":\"首页\",\"type\":\"shop\"},\"is_show\":\"1\"},{\"name\":\"创作\",\"selected\":\"resource/image/adminapi/default/creation.png\",\"unselected\":\"resource/image/adminapi/default/select-creation.png\",\"link\":{\"path\":\"/pages/ai_creation/ai_creation\",\"name\":\"创作中心\",\"type\":\"shop\"},\"is_show\":\"1\"},{\"name\":\"技能\",\"selected\":\"resource/image/adminapi/default/skill.png\",\"unselected\":\"resource/image/adminapi/default/select-skill.png\",\"link\":{\"path\":\"/pages/skills/skills\",\"name\":\"技能大全\",\"type\":\"shop\"},\"is_show\":\"1\"},{\"name\":\"应用\",\"selected\":\"resource/image/adminapi/default/app.png\",\"unselected\":\"resource/image/adminapi/default/select-app.png\",\"link\":{\"path\":\"/pages/app/app\",\"name\":\"应用\",\"type\":\"shop\",\"canTab\":\"1\"},\"is_show\":\"1\"},{\"name\":\"我的\",\"selected\":\"resource/image/adminapi/default/my.png\",\"unselected\":\"resource/image/adminapi/default/select-my.png\",\"link\":{\"path\":\"/pages/user/user\",\"name\":\"个人中心\",\"type\":\"shop\"},\"is_show\":\"1\"}]}', 1688457444, 1688457444), (2, 2, 'PC端导航栏', '{\"nav\":[{\"name\":\"对话\",\"selected\":\"resource/image/adminapi/default/pc-chat-select.png\",\"unselected\":\"resource/image/adminapi/default/pc-chat.png\",\"is_show\":\"1\",\"link\":{\"path\":\"/chat\",\"name\":\"首页对话\",\"type\":\"shop\"}},{\"name\":\"创作\",\"selected\":\"resource/image/adminapi/default/pc-creation-select.png\",\"unselected\":\"resource/image/adminapi/default/pc-creation.png\",\"is_show\":\"1\",\"link\":{\"path\":\"/create\",\"name\":\"AI创作\",\"type\":\"shop\"}},{\"name\":\"技能\",\"selected\":\"resource/image/adminapi/default/pc-skill-select.png\",\"unselected\":\"resource/image/adminapi/default/pc-skill.png\",\"is_show\":\"1\",\"link\":{\"path\":\"/skill\",\"name\":\"AI技能\",\"type\":\"shop\"}},{\"name\":\"绘画\",\"selected\":\"resource/image/adminapi/default/pc-draw-select.png\",\"unselected\":\"resource/image/adminapi/default/pc-draw.png\",\"is_show\":\"1\",\"link\":{\"path\":\"/app/drawing\",\"name\":\"AI绘画\",\"type\":\"shop\"}},{\"name\":\"广场\",\"selected\":\"resource/image/adminapi/default/pc-square-select.png\",\"unselected\":\"resource/image/adminapi/default/pc-square.png\",\"is_show\":\"1\",\"link\":{\"path\":\"/app/square\",\"name\":\"绘画广场\",\"type\":\"shop\"}},{\"name\":\"应用\",\"selected\":\"resource/image/adminapi/default/pc-app-select.png\",\"unselected\":\"resource/image/adminapi/default/pc-app.png\",\"is_show\":\"1\",\"link\":{\"path\":\"/app\",\"name\":\"AI应用\",\"type\":\"shop\"}}],\"menu\":[{\"name\":\"开通会员\",\"selected\":\"resource/image/adminapi/default/pc-member.png\",\"unselected\":\"resource/image/adminapi/default/pc-select-member.png\",\"is_show\":\"1\",\"link\":{\"path\":\"/openvip\",\"name\":\"开通会员\",\"type\":\"shop\"}},{\"name\":\"充值/奖励\",\"selected\":\"resource/image/adminapi/default/pc-recharge.png\",\"unselected\":\"resource/image/adminapi/default/pc-select-recharge.png\",\"is_show\":\"1\",\"link\":{\"path\":\"/recharge\",\"name\":\"充值奖励\",\"type\":\"shop\"}},{\"name\":\"卡密兑换\",\"selected\":\"resource/image/adminapi/default/pc-card-code.png\",\"unselected\":\"resource/image/adminapi/default/pc-select-card-code.png\",\"is_show\":\"1\",\"link\":{\"path\":\"/redeem_code\",\"name\":\"卡密兑换\",\"type\":\"shop\"}},{\"name\":\"分销推广\",\"selected\":\"resource/image/adminapi/default/pc-distribution.png\",\"unselected\":\"resource/image/adminapi/default/pc-select-distribution.png\",\"is_show\":\"1\",\"link\":{\"path\":\"/promotion/distribution\",\"name\":\"分销推广\",\"type\":\"shop\"}},{\"name\":\"资讯中心\",\"selected\":\"resource/image/adminapi/default/pc-article.png\",\"unselected\":\"resource/image/adminapi/default/pc-select-article.png\",\"is_show\":\"1\",\"link\":{\"path\":\"/news\",\"name\":\"资讯中心\",\"type\":\"shop\"}},{\"name\":\"我的收藏\",\"selected\":\"resource/image/adminapi/default/pc-collection.png\",\"unselected\":\"resource/image/adminapi/default/pc-select-collection.png\",\"is_show\":\"1\",\"link\":{\"path\":\"/user/collection\",\"name\":\"我的收藏\",\"type\":\"shop\"}},{\"name\":\"建议反馈\",\"selected\":\"resource/image/adminapi/default/pc-opinion.png\",\"unselected\":\"resource/image/adminapi/default/pc-select-opinion.png\",\"is_show\":\"1\",\"link\":{\"path\":\"/user/opinion\",\"name\":\"意见反馈\",\"type\":\"shop\"}}]}', 1688457444, 1688457444);
COMMIT;

-- ----------------------------
-- Table structure for ai_dept
-- ----------------------------
DROP TABLE IF EXISTS `ai_dept`;
CREATE TABLE `ai_dept`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '部门名称',
  `pid` bigint(20) NOT NULL DEFAULT 0 COMMENT '上级部门id',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `leader` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '负责人',
  `mobile` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '联系电话',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '部门状态（0停用 1正常）',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '部门表';

-- ----------------------------
-- Records of ai_dept
-- ----------------------------
BEGIN;
INSERT INTO `ai_dept` VALUES (1, '公司', 0, 0, 'boss', '12345698745', 1, 1650592684, 1653640368, NULL);
COMMIT;

-- ----------------------------
-- Table structure for ai_dev_crontab
-- ----------------------------
DROP TABLE IF EXISTS `ai_dev_crontab`;
CREATE TABLE `ai_dev_crontab`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '定时任务名称',
  `type` tinyint(1) NOT NULL COMMENT '类型 1-定时任务',
  `system` tinyint(4) NULL DEFAULT 0 COMMENT '是否系统任务 0-否 1-是',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '备注',
  `command` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '命令内容',
  `params` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '参数',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态 1-运行 2-停止 3-错误',
  `expression` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '运行规则',
  `error` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '运行失败原因',
  `last_time` int(11) NULL DEFAULT NULL COMMENT '最后执行时间',
  `time` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '0' COMMENT '实时执行时长',
  `max_time` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '0' COMMENT '最大执行时长',
  `create_time` int(10) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '计划任务表';

-- ----------------------------
-- Records of ai_dev_crontab
-- ----------------------------
BEGIN;
INSERT INTO `ai_dev_crontab` VALUES (1, '订单退款状态处理', 1, 0, '', 'query_refund', '', 1, '* * * * *', '', 1691118066, '0.54', '30.03', 1682584872, 1683181616, NULL), (2, '内容审核', 1, 0, '', 'content_censor', '', 1, '*/10 * * * *', '', 1691118006, '0.01', '336.27', 1687164568, 1687246140, NULL), (3, '商家转账到零钱查询', 1, 0, '', 'wechat_merchant_transfer', '', 1, '* * * * *', '', 1691118066, '0.25', '7.3', 1687253388, 1690422014, NULL), (4, '处理没回调的绘画记录', 1, 0, '', 'draw_fail', '', 1, '*/1 * * * *', '', 1691118066, '0', '0.13', 1690282204, 1691048749, NULL),(5, '处理没回调的艺术二维码记录', 1, 0, '', 'qrcode_fail', '', 1, '* * * * *', '', 1698375426, '0', '0.1', 1697626807, 1698371183, NULL);
COMMIT;

-- ----------------------------
-- Table structure for ai_dev_pay_config
-- ----------------------------
DROP TABLE IF EXISTS `ai_dev_pay_config`;
CREATE TABLE `ai_dev_pay_config`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '模版名称',
  `pay_way` tinyint(1) NOT NULL COMMENT '支付方式:1-余额支付;2-微信支付;3-支付宝支付;',
  `config` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '对应支付配置(json字符串)',
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '图标',
  `sort` int(5) NULL DEFAULT NULL COMMENT '排序',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- ----------------------------
-- Records of ai_dev_pay_config
-- ----------------------------
BEGIN;
INSERT INTO `ai_dev_pay_config` VALUES (1, '微信支付', 2, '', '/resource/image/adminapi/pay/wechatpay.png', 101, '微信支付备注'), (2, '支付宝支付', 3, '', '/resource/image/adminapi/pay/alipay.jpg', 100, '支付宝支付备注'),(3, '微信-易支付', 4, '', '/resource/image/adminapi/pay/wechatpay.png', 102, '微信-易支付备注'),(4, '支付宝-易支付', 5, '', '/resource/image/adminapi/pay/alipay.jpg', 103, '支付宝-易支付备注');
COMMIT;

-- ----------------------------
-- Table structure for ai_dev_pay_way
-- ----------------------------
DROP TABLE IF EXISTS `ai_dev_pay_way`;
CREATE TABLE `ai_dev_pay_way`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pay_config_id` int(11) NOT NULL COMMENT '支付配置ID',
  `scene` tinyint(1) NOT NULL COMMENT '场景:1-微信小程序;2-微信公众号;3-H5;4-PC;5-APP;',
  `is_default` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否默认支付:0-否;1-是;',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态:0-关闭;1-开启;',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- ----------------------------
-- Records of ai_dev_pay_way
-- ----------------------------
BEGIN;
INSERT INTO `ai_dev_pay_way` VALUES (3, 1, 1, 1, 1), (4, 1, 2, 1, 1), (7, 1, 4, 1, 1), (8, 2, 4, 0, 1), (9, 2, 2, 0, 1), (10, 1, 3, 1, 1), (11, 2, 3, 0, 1), (12, 1, 5, 1, 1), (13, 2, 5, 0, 1), (15, 3, 2, 0, 1), (16, 3, 3, 0, 1), (17, 3, 4, 0, 1), (19, 4, 2, 0, 1), (20, 4, 3, 0, 1), (21, 4, 4, 0, 1);
COMMIT;
-- ----------------------------
-- Table structure for ai_dict_data
-- ----------------------------
DROP TABLE IF EXISTS `ai_dict_data`;
CREATE TABLE `ai_dict_data`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '数据名称',
  `value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '数据值',
  `type_id` int(11) NOT NULL COMMENT '字典类型id',
  `type_value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '字典类型',
  `sort` int(10) NULL DEFAULT 0 COMMENT '排序值',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态 0-停用 1-正常',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '备注',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '字典数据表';

-- ----------------------------
-- Records of ai_dict_data
-- ----------------------------
BEGIN;
INSERT INTO `ai_dict_data` VALUES (1, '隐藏', '0', 1, 'show_status', 0, 1, '', 1656381543, 1656381543, NULL), (2, '显示', '1', 1, 'show_status', 0, 1, '', 1656381550, 1656381550, NULL), (3, '进行中', '0', 2, 'business_status', 0, 1, '', 1656381410, 1656381410, NULL), (4, '成功', '1', 2, 'business_status', 0, 1, '', 1656381437, 1656381437, NULL), (5, '失败', '2', 2, 'business_status', 0, 1, '', 1656381449, 1656381449, NULL), (6, '待处理', '0', 3, 'event_status', 0, 1, '', 1656381212, 1656381212, NULL), (7, '已处理', '1', 3, 'event_status', 0, 1, '', 1656381315, 1656381315, NULL), (8, '拒绝处理', '2', 3, 'event_status', 0, 1, '', 1656381331, 1656381331, NULL), (9, '禁用', '1', 4, 'system_disable', 0, 1, '', 1656312030, 1656312030, NULL), (10, '正常', '0', 4, 'system_disable', 0, 1, '', 1656312040, 1656312040, NULL), (11, '未知', '0', 5, 'sex', 0, 1, '', 1656062988, 1656062988, NULL), (12, '男', '1', 5, 'sex', 0, 1, '', 1656062999, 1656062999, NULL), (13, '女', '2', 5, 'sex', 0, 1, '', 1656063009, 1656063009, NULL);
COMMIT;

-- ----------------------------
-- Table structure for ai_dict_type
-- ----------------------------
DROP TABLE IF EXISTS `ai_dict_type`;
CREATE TABLE `ai_dict_type`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '字典名称',
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '字典类型名称',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态 0-停用 1-正常',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '备注',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '字典类型表';

-- ----------------------------
-- Records of ai_dict_type
-- ----------------------------
BEGIN;
INSERT INTO `ai_dict_type` VALUES (1, '显示状态', 'show_status', 1, '', 1656381520, 1656381520, NULL), (2, '业务状态', 'business_status', 1, '', 1656381393, 1656381393, NULL), (3, '事件状态', 'event_status', 1, '', 1656381075, 1656381075, NULL), (4, '禁用状态', 'system_disable', 1, '', 1656311838, 1656311838, NULL), (5, '用户性别', 'sex', 1, '', 1656062946, 1656380925, NULL);
COMMIT;

-- ----------------------------
-- Table structure for ai_distribution_apply
-- ----------------------------
DROP TABLE IF EXISTS `ai_distribution_apply`;
CREATE TABLE `ai_distribution_apply`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '用户id',
  `name` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '真实姓名',
  `mobile` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '手机号码',
  `audit_remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '审核备注',
  `audit_time` int(10) NULL DEFAULT NULL COMMENT '审核时间',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：1-待审核；2-审核通过；3-审核不通过',
  `create_time` int(10) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '分销申请表';

-- ----------------------------
-- Records of ai_distribution_apply
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_distribution_order
-- ----------------------------
DROP TABLE IF EXISTS `ai_distribution_order`;
CREATE TABLE `ai_distribution_order`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_type` tinyint(1) NOT NULL COMMENT '订单类型：1-充值订单；2-会员订单；',
  `order_id` int(11) NOT NULL COMMENT '订单ID',
  `order_sn` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '订单编号',
  `order_amount` decimal(10, 2) UNSIGNED NOT NULL COMMENT '实付金额',
  `pay_time` int(10) NULL DEFAULT NULL COMMENT '支付时间',
  `user_id` int(11) NOT NULL COMMENT '下单用户ID',
  `first_user_id` int(11) NOT NULL COMMENT '一级分销商用户ID',
  `first_ratio` decimal(10, 2) UNSIGNED NOT NULL COMMENT '一级分销佣金比例',
  `first_reward` decimal(10, 2) UNSIGNED NOT NULL COMMENT '一级分销奖励',
  `second_user_id` int(11) NULL DEFAULT NULL COMMENT '二级分销商用户ID',
  `second_ratio` decimal(10, 2) UNSIGNED NULL DEFAULT NULL COMMENT '二级分销佣金比例',
  `second_reward` decimal(10, 2) UNSIGNED NULL DEFAULT NULL COMMENT '二级分销奖励',
  `create_time` int(10) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '分销订单表';

-- ----------------------------
-- Records of ai_distribution_order
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_draw_category
-- ----------------------------
DROP TABLE IF EXISTS `ai_draw_category`;
CREATE TABLE `ai_draw_category`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '上级id',
  `level` tinyint(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '层级',
  `name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '类目名称',
  `sort` int(11) NULL DEFAULT NULL COMMENT '排序',
  `status` int(11) NOT NULL COMMENT '状态：1-开启，0-关闭',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '绘画关键词分类';

-- ----------------------------
-- Records of ai_draw_category
-- ----------------------------
BEGIN;
INSERT INTO `ai_draw_category` VALUES (1, 0, 1, '绘画风格', 100, 1, 1687936358, 1687938482, NULL), (2, 1, 2, '风格', 0, 1, 1687936459, 1688040539, NULL), (4, 0, 1, '画面优化', 0, 1, 1688011644, 1688011644, NULL), (5, 4, 2, '画质提升', 0, 1, 1688011668, 1688011668, NULL), (6, 0, 1, '镜头视角', 0, 1, 1688037042, 1688037042, NULL), (7, 0, 1, '人物形象', 0, 1, 1688037055, 1688037055, NULL), (8, 7, 2, '种族职业', 0, 1, 1688037065, 1688037065, NULL), (9, 7, 2, '姿势动作', 0, 1, 1688037084, 1688037084, NULL), (10, 7, 2, '服饰穿搭', 0, 1, 1688037094, 1688037094, NULL), (11, 7, 2, '五官表情', 0, 1, 1688037102, 1688037102, NULL), (12, 7, 2, '发型发色', 0, 1, 1688037111, 1688037111, NULL), (13, 6, 2, '镜头类型', 0, 1, 1688092684, 1688092684, NULL);
COMMIT;

-- ----------------------------
-- Table structure for ai_draw_prompt
-- ----------------------------
DROP TABLE IF EXISTS `ai_draw_prompt`;
CREATE TABLE `ai_draw_prompt`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '分类id',
  `prompt` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '中文关键词',
  `prompt_en` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '英文关键词',
  `sort` int(11) NULL DEFAULT NULL COMMENT '排序',
  `status` int(11) NOT NULL COMMENT '状态：1-开启，0-关闭',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 78 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '绘画提示词示例表' ;

-- ----------------------------
-- Records of ai_draw_prompt
-- ----------------------------
BEGIN;
INSERT INTO `ai_draw_prompt` (`id`, `category_id`, `prompt`, `prompt_en`, `sort`, `status`, `create_time`, `update_time`, `delete_time`) VALUES (14, 2, '人像', 'portrait', 0, 1, 1688039437, 1688089988, NULL), (15, 2, '3D动漫', '3D anime', 0, 1, 1688039437, 1688089987, NULL), (17, 2, '水彩', 'Watercolor', 0, 1, 1688039904, 1688089987, NULL), (18, 2, '二次元', 'pixiv', 0, 1, 1688039904, 1688040697, NULL), (20, 2, '梦幻', 'dream', 0, 1, 1688039904, 1688090321, NULL), (21, 2, '唯美', 'Aestheticism', 0, 1, 1688039904, 1688090316, NULL), (22, 2, '卡通', 'cartoon', 0, 1, 1688039904, 1688090311, NULL), (23, 2, '拟人', 'personification', 0, 1, 1688039904, 1688090303, NULL), (28, 5, '大师作品', 'Master works', 0, 1, 1688090279, 1688090279, NULL), (29, 5, '最好画质', 'Best picture quality', 0, 1, 1688090279, 1688090279, NULL), (30, 5, '更高质量', 'Higher quality', 0, 1, 1688090279, 1688090279, NULL), (31, 5, '超高分辨率', 'Ultra High Resolution', 0, 1, 1688090279, 1688090279, NULL), (32, 5, '超高分辨率', '8k resolution', 0, 1, 1688090279, 1688090279, NULL), (33, 5, '完美的画像', 'Perfect Portrait', 0, 1, 1688090279, 1688090644, NULL), (34, 12, '短发', 'Short hair', 0, 1, 1688090576, 1688090576, NULL), (35, 12, '中等长度头发', 'Medium length hair', 0, 1, 1688090576, 1688090576, NULL), (36, 12, '长发', 'Long hair', 0, 1, 1688090576, 1688090576, NULL), (37, 12, '刘海', 'Bangs', 0, 1, 1688090576, 1688090576, NULL), (38, 12, '眼间刘海', 'Interocular bangs', 0, 1, 1688090576, 1688090576, NULL), (39, 12, '金发', 'blonde', 0, 1, 1688090576, 1688090576, NULL), (40, 12, '银发', 'Silver hair', 0, 1, 1688090576, 1688090576, NULL), (41, 12, '黑发', 'Black hair', 0, 1, 1688090576, 1695784233, NULL), (42, 12, '马尾', 'cauda equina', 0, 1, 1688090576, 1688090576, NULL), (43, 12, '双马尾', 'Pigtail', 0, 1, 1688090576, 1688090576, NULL), (44, 12, '双马尾', 'Fluttering hair', 0, 1, 1688090576, 1688090576, NULL), (45, 11, '露齿笑', 'Grin out one\'s teeth', 0, 1, 1688090993, 1688090993, NULL), (46, 11, '脸红', 'blush', 0, 1, 1688090993, 1688090993, NULL), (47, 11, '悲伤', 'sadness', 0, 1, 1688090993, 1688090993, NULL), (48, 11, '闭眼', 'Close your eyes', 0, 1, 1688090993, 1688090993, NULL), (49, 10, '洛丽塔风格', 'Lolita style', 0, 1, 1688091883, 1688091883, NULL), (50, 10, '白衬衫', 'white shirt', 0, 1, 1688091883, 1688091883, NULL), (51, 10, '西装', 'suit', 0, 1, 1688091883, 1688091883, NULL), (52, 10, '汉服', 'Hanfu', 0, 1, 1688091883, 1688091883, NULL), (53, 10, '和服', 'kimono', 0, 1, 1688091883, 1688091883, NULL), (54, 10, '项圈', 'collar', 0, 1, 1688091883, 1688091883, NULL), (55, 10, '珠宝首饰', 'jewelry', 0, 1, 1688091883, 1688091883, NULL), (56, 10, '戒指', 'ring', 0, 1, 1688091883, 1688091883, NULL), (57, 9, '看向观者', 'Looking towards the viewer', 0, 1, 1688091999, 1688091999, NULL), (58, 9, '跳', 'jump', 0, 1, 1688091999, 1688091999, NULL), (59, 9, '跪着', 'Kneeling', 0, 1, 1688091999, 1688091999, NULL), (60, 9, '比耶', 'Biye', 0, 1, 1688091999, 1688091999, NULL), (61, 9, '捧着花', 'Holding flowers', 0, 1, 1688091999, 1688091999, NULL), (62, 9, '站着', 'stand', 0, 1, 1688091999, 1688091999, NULL), (63, 8, '精灵', 'spirit', 0, 1, 1688092131, 1688092131, NULL), (64, 8, '小恶魔', 'Imp', 0, 1, 1688092131, 1688092131, NULL), (65, 8, '女仆', 'maid', 0, 1, 1688092131, 1688092131, NULL), (66, 8, '兔女郎', 'Playboy Bunny', 0, 1, 1688092131, 1688092131, NULL), (67, 8, '护士', 'nurse', 0, 1, 1688092131, 1688092131, NULL), (68, 8, '服务员', 'waiter', 0, 1, 1688092131, 1688092131, NULL), (69, 8, '法师', 'Mage', 0, 1, 1688092131, 1688092131, NULL), (70, 8, '刺客', 'Assassin', 0, 1, 1688092131, 1688092131, NULL), (71, 13, '全景', 'panorama', 0, 1, 1688092747, 1688092747, NULL), (72, 13, '中景镜头', 'Mid shot', 0, 1, 1688092747, 1688092747, NULL), (73, 13, '长焦镜头', 'Telephoto lens', 0, 1, 1688092747, 1688092747, NULL), (74, 13, '微距镜头', 'Macro lens', 0, 1, 1688092747, 1688092747, NULL), (75, 13, '全身像', 'Whole body portrait', 0, 1, 1688092747, 1688092747, NULL), (76, 13, '半身像', 'Bust', 0, 1, 1688092747, 1688092747, NULL), (77, 13, '特写像', 'Close-up portrait', 0, 1, 1688092747, 1688092747, NULL);
COMMIT;

-- ----------------------------
-- Table structure for ai_draw_prompt_example
-- ----------------------------
DROP TABLE IF EXISTS `ai_draw_prompt_example`;
CREATE TABLE `ai_draw_prompt_example`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `prompt` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '中文关键词',
  `prompt_en` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '英文关键词',
  `sort` int(11) UNSIGNED NULL DEFAULT 0 COMMENT '排序',
  `status` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态：1-开启，0-关闭',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '绘画提示词示例表';

-- ----------------------------
-- Records of ai_draw_prompt_example
-- ----------------------------
BEGIN;
INSERT INTO `ai_draw_prompt_example` VALUES (1, '美少女', 'On a rainy day, an 18-year-old Chinese girl wearing a colorful jacket and a white shirt stood under a transparent umbrella, waiting for the traffic lights. Realistic.', 1, 1, 1689842658, 1689847036, NULL), (2, '猫咪的梦想世界', 'Master works, high quality, beautiful cats, manga style, anime style, dynamic lighting, complex details, beautiful details, Golden Ratio, Unreal Details, Pixiv Trend, Acrylic toning, Ghibli Influence, Leonid Afremov', 2, 1, 1689844654, 1689850441, NULL), (3, '古装美女', 'The antique beauty dressed in Tang Dynasty clothing, holding a wooden Chinese drawer, looks into the camera, elegant color scheme', 5, 1, 1689845493, 1689847531, NULL), (4, '奔跑的兔子', 'a rabbit running . composition profile, Disney animation style. Dynamic, detailed composition.', 4, 1, 1689845535, 1689847236, NULL), (5, '海边星空', 'This is a night sea map, a huge moon, rising from the sea, the light blue sky is shining with stars, couples walking by the sea, dreamy, romantic, super wide angle, night, soft, 8k, Gong Qijun, art –ar 9:16', 3, 1, 1689845813, 1689847185, NULL);
COMMIT;

-- ----------------------------
-- Table structure for ai_draw_records
-- ----------------------------
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       DROP TABLE IF EXISTS `ai_draw_records`;
CREATE TABLE `ai_draw_records`  (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户id',
   `type` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '绘图类型 1-文生图  2-图生图 3-选中放大 4-选中变换',
   `action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '动作 绘图动作',
   `prompt` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '提示词',
   `prompt_en` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '提示词',
   `prompt_desc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '提示词详情',
   `prompt_other` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '提示词其他参数',
   `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '本地图片地址',
   `image_base` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '垫图地址',
   `image_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '绘图服务返回地址',
   `image_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '绘图服务图片id',
   `scale` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '图片比例',
   `task_id` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '0' COMMENT '绘图服务任务id',
   `thumbnail` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '缩略图',
   `notify_snap` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '回调快照信息',
   `able_actions` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '可操作动作',
   `use_tokens` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '消耗tokens',
   `fail_reason` varchar(600) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '失败原因',
   `model` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '绘画模型',
   `version` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '绘画版本',
   `no_content` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '绘画中不想出现的参数',
   `style` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '绘画风格',
   `quality` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '图片质量',
   `engine` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'midjourney' COMMENT '绘画引擎',
   `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态：0-未启动 1-执行中 2-失败 3-成功',
   `censor_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '审核状态：0-未审核；1-合规；2-不合规；3-疑似；4-审核失败；',
   `create_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
   `update_time` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '修改时间',
   `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
   PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '绘画记录表';

-- ----------------------------
-- Records of ai_draw_records
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_draw_records_collect
-- ----------------------------
DROP TABLE IF EXISTS `ai_draw_records_collect`;
CREATE TABLE `ai_draw_records_collect`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `records_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '对话记录ID',
  `create_time` int(10) NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '绘画记录收藏';

-- ----------------------------
-- Records of ai_draw_records_collect
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_draw_square
-- ----------------------------
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       DROP TABLE IF EXISTS `ai_draw_square`;
CREATE TABLE `ai_draw_square`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `source` tinyint(1) NOT NULL COMMENT '来源：1-官方；2-用户；',
  `operate_id` int(11) NOT NULL COMMENT '操作人ID',
  `category_id` int(11) NOT NULL DEFAULT 0 COMMENT '分类id',
  `draw_records_id` int(11) NOT NULL DEFAULT 0 COMMENT '绘画分享来源ID',
  `prompts` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '英文提示词',
  `prompts_cn` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '中文提示词',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '图片',
  `thumbnail` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '缩略图',
  `is_show` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否显示：1-是；0-否；',
  `verify_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '审核状态：0-待审核；1-审核通过；2-审核不通过；',
  `verify_result` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '审核结果',
  `avatar` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '头像',
  `nickname` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '用户昵称',
  `create_time` int(10) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '绘画广场表';

-- ----------------------------
-- Records of ai_draw_square
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_draw_square_category
-- ----------------------------
DROP TABLE IF EXISTS `ai_draw_square_category`;
CREATE TABLE `ai_draw_square_category`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '分类名称',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '分类图片',
  `sort` int(11) UNSIGNED NULL DEFAULT 0 COMMENT '排序',
  `status` tinyint(1) UNSIGNED NOT NULL COMMENT '状态：1-开启，0-关闭',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '绘画广场分类表';

-- ----------------------------
-- Records of ai_draw_square_category
-- ----------------------------
BEGIN;
INSERT INTO `ai_draw_square_category` VALUES (1, '人物', '', 9, 1, 1693451236, 1694070036, NULL);
COMMIT;

-- ----------------------------
-- Table structure for ai_email_log
-- ----------------------------
DROP TABLE IF EXISTS `ai_email_log`;
CREATE TABLE `ai_email_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `scene_id` int(11) NOT NULL COMMENT '场景id',
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '邮箱',
  `content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '发送内容',
  `code` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '验证码',
  `is_verify` tinyint(1) NULL DEFAULT 0 COMMENT '是否已验证；0-否；1-是',
  `check_num` int(5) NULL DEFAULT 0 COMMENT '验证次数',
  `send_status` tinyint(1) NOT NULL COMMENT '发送状态：0-发送中；1-发送成功；2-发送失败',
  `send_time` int(10) NOT NULL COMMENT '发送时间',
  `results` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '发送结果',
  `create_time` int(10) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '邮件记录';

-- ----------------------------
-- Records of ai_email_log
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_feedback
-- ----------------------------
DROP TABLE IF EXISTS `ai_feedback`;
CREATE TABLE `ai_feedback`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `type` tinyint(1) NOT NULL COMMENT '反馈类型：1-故障；2-建议；3-投诉；',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '反馈内容',
  `mobile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '联系方式',
  `create_time` int(10) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '意见反馈表';

-- ----------------------------
-- Records of ai_feedback
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_file
-- ----------------------------
DROP TABLE IF EXISTS `ai_file`;
CREATE TABLE `ai_file`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `cid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '类目ID',
  `source_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '上传者id',
  `source` tinyint(1) NOT NULL DEFAULT 0 COMMENT '来源类型[0-后台,1-用户]',
  `type` tinyint(2) UNSIGNED NOT NULL DEFAULT 10 COMMENT '类型[10=图片, 20=视频]',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '文件名称',
  `uri` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '文件路径',
  `create_time` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 269 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '文件表';

-- ----------------------------
-- Records of ai_file
-- ----------------------------
BEGIN;
INSERT INTO `ai_file` VALUES (1, 0, 0, 0, 10, 'AI智能聊天LOGO4.jpg', 'uploads/images/20230427/20230427145057d8e577914.jpg', 1682578257, 1682578257, NULL), (2, 0, 0, 0, 10, 'AI智能聊天LOGO2.png', 'uploads/images/20230427/2023042714505747cd26257.png', 1682578257, 1682578257, NULL), (3, 0, 0, 0, 10, '4.jpeg', 'uploads/images/20230427/202304271521219e52d5900.jpeg', 1682580081, 1682580081, NULL), (4, 0, 0, 0, 10, '企业微信20221207-172119@2x.png', 'uploads/images/20230427/202304271521293a8d39061.png', 1682580089, 1682580089, NULL), (5, 0, 0, 0, 10, '3.jpeg', 'uploads/images/20230427/202304271521428f9059555.jpeg', 1682580102, 1682580102, NULL), (6, 0, 0, 0, 10, 'u=1953922700,4041156140&fm=26&fmt=auto.jpeg', 'uploads/images/20230427/20230427152142fd4a02461.jpeg', 1682580102, 1682580102, NULL), (7, 0, 0, 0, 10, '图像 97.jpg', 'uploads/images/20230427/2023042715214291ccd8102.jpg', 1682580102, 1682580102, NULL), (8, 0, 0, 0, 10, 'Chat_.png', 'uploads/images/20230427/20230427153620863700244.png', 1682580980, 1682580980, NULL), (9, 0, 0, 0, 10, 'qrcode_for_gh_ae7c932ae2a4_430.jpg', 'uploads/images/20230427/20230427172706a15af5031.jpg', 1682587626, 1682587626, NULL), (10, 1, 0, 0, 10, '猜灯谜@2x.png', 'uploads/images/20230427/20230427185351a972d6752.png', 1682592831, 1682655194, 1682655194), (11, 1, 0, 0, 10, '菜谱大全@2x.png', 'uploads/images/20230427/20230427185352be2652460.png', 1682592832, 1682655194, 1682655194), (12, 1, 0, 0, 10, '吵架助手@2x.png', 'uploads/images/20230427/20230427185352ccad09030.png', 1682592832, 1682655194, 1682655194), (13, 1, 0, 0, 10, '工作总结@2x.png', 'uploads/images/20230427/20230427185352c4e5f1370.png', 1682592832, 1682655194, 1682655194), (14, 1, 0, 0, 10, '哄女友@2x.png', 'uploads/images/20230427/20230427185352042210280.png', 1682592832, 1682655194, 1682655194), (15, 1, 0, 0, 10, '翻译@2x.png', 'uploads/images/20230427/20230427185352ce7358887.png', 1682592832, 1682655194, 1682655194), (16, 1, 0, 0, 10, '单身狗导师@2x.png', 'uploads/images/20230427/20230427185352d6f315466.png', 1682592832, 1682655194, 1682655194), (17, 1, 0, 0, 10, '个性签名@2x.png', 'uploads/images/20230427/202304271853521c99b0727.png', 1682592832, 1682655194, 1682655194), (18, 1, 0, 0, 10, '哄男友@2x.png', 'uploads/images/20230427/202304271853528928d8740.png', 1682592832, 1682655194, 1682655194), (19, 1, 0, 0, 10, '日报周报@2x.png', 'uploads/images/20230427/20230427185401896267807.png', 1682592841, 1682655194, 1682655194), (20, 1, 0, 0, 10, '夸夸小助手@2x.png', 'uploads/images/20230427/202304271854023449e7912.png', 1682592842, 1682655194, 1682655194), (21, 1, 0, 0, 10, '编程高手@2x.png', 'uploads/images/20230427/202304271854026f4a68734.png', 1682592842, 1682655182, 1682655182), (22, 1, 0, 0, 10, '请假小能手@2x.png', 'uploads/images/20230427/20230427185402cc41d4318.png', 1682592842, 1682655182, 1682655182), (23, 1, 0, 0, 10, '朋友圈文案@2x.png', 'uploads/images/20230427/2023042718540202c7c9043.png', 1682592842, 1682655182, 1682655182), (24, 1, 0, 0, 10, 'Emoji翻译@2x.png', 'uploads/images/20230427/20230427185402ee89d1035.png', 1682592842, 1682655182, 1682655182), (25, 1, 0, 0, 10, '表白信@2x.png', 'uploads/images/20230427/2023042718540292a9f5057.png', 1682592842, 1682655182, 1682655182), (26, 1, 0, 0, 10, '情感安慰@2x.png', 'uploads/images/20230427/20230427185402056495811.png', 1682592842, 1682655182, 1682655182), (27, 1, 0, 0, 10, '微博拟写@2x.png', 'uploads/images/20230427/20230427185402228cb7736.png', 1682592842, 1682655182, 1682655182), (28, 1, 0, 0, 10, 'OKR规划@2x.png', 'uploads/images/20230427/20230427185409fb77d0257.png', 1682592849, 1682655182, 1682655182), (29, 1, 0, 0, 10, 'AI取名@2x.png', 'uploads/images/20230427/20230427185409d50f89889.png', 1682592849, 1682655182, 1682655182), (30, 1, 0, 0, 10, '写作文@2x.png', 'uploads/images/20230427/20230427185409fe0aa8211.png', 1682592849, 1682655182, 1682655182), (31, 1, 0, 0, 10, '探店打卡@2x.png', 'uploads/images/20230427/20230427185416c29d02001.png', 1682592856, 1682655182, 1682655182), (32, 1, 0, 0, 10, '知乎风格@2x.png', 'uploads/images/20230427/20230427185416f9dad5711.png', 1682592856, 1682655182, 1682655182), (33, 1, 0, 0, 10, '职场小树洞@2x.png', 'uploads/images/20230427/202304271854166af723059.png', 1682592856, 1682655182, 1682655182), (34, 1, 0, 0, 10, '做个好梦@2x.png', 'uploads/images/20230427/20230427185416fef882914.png', 1682592856, 1682655182, 1682655182), (35, 1, 0, 0, 10, '邮件编写@2x.png', 'uploads/images/20230427/20230427185416ad46b4585.png', 1682592856, 1682655182, 1682655182), (36, 1, 0, 0, 10, '生活技巧@2x.png', 'uploads/images/20230427/20230427185416ab7ee2426.png', 1682592856, 1682655182, 1682655182), (37, 1, 0, 0, 10, '甩锅助手@2x.png', 'uploads/images/20230427/20230427185416323cd6410.png', 1682592856, 1682655182, 1682655182), (38, 1, 0, 0, 10, '写检讨书@2x.png', 'uploads/images/20230427/202304271854169078b9375.png', 1682592856, 1682655182, 1682655182), (39, 1, 0, 0, 10, 'AI对联@2x.png', 'uploads/images/20230427/20230427185416eb0f63634.png', 1682592856, 1682655182, 1682655182), (40, 1, 0, 0, 10, '短视频脚本@2x.png', 'uploads/images/20230427/202304271854222aeb33755.png', 1682592862, 1682655182, 1682655182), (41, 1, 0, 0, 10, '高情商回复@2x.png', 'uploads/images/20230427/20230427185422fc0137247.png', 1682592862, 1682655178, 1682655178), (42, 1, 0, 0, 10, '工作计划@2x.png', 'uploads/images/20230427/20230427185422636057717.png', 1682592862, 1682655178, 1682655178), (43, 1, 0, 0, 10, '道歉信@2x.png', 'uploads/images/20230427/20230427185422685105589.png', 1682592862, 1682655178, 1682655178), (44, 1, 0, 0, 10, '检讨书@2x.png', 'uploads/images/20230427/20230427185422409423923.png', 1682592862, 1682655178, 1682655178), (45, 1, 0, 0, 10, '发言稿@2x.png', 'uploads/images/20230427/20230427185422b8d334842.png', 1682592862, 1682655178, 1682655178), (46, 1, 0, 0, 10, '广告创意@2x.png', 'uploads/images/20230427/20230427185422ceb5e4989.png', 1682592862, 1682655178, 1682655178), (47, 1, 0, 0, 10, '节日祝福@2x.png', 'uploads/images/20230427/20230427185422102897971.png', 1682592862, 1682655178, 1682655178), (48, 1, 0, 0, 10, '产品描述@2x.png', 'uploads/images/20230427/20230427185422e1c181957.png', 1682592862, 1682655178, 1682655178), (49, 1, 0, 0, 10, '写诗词@2x.png', 'uploads/images/20230427/20230427185430ed7df9183.png', 1682592870, 1682655178, 1682655178), (50, 1, 0, 0, 10, '文章总结@2x.png', 'uploads/images/20230427/20230427185430e193b3006.png', 1682592870, 1682655178, 1682655178), (51, 1, 0, 0, 10, '小红书文章@2x.png', 'uploads/images/20230427/2023042718543044a260708.png', 1682592870, 1682655178, 1682655178), (52, 1, 0, 0, 10, '写情话@2x.png', 'uploads/images/20230427/20230427185430566ac2715.png', 1682592870, 1682655178, 1682655178), (53, 1, 0, 0, 10, '文章扩写@2x.png', 'uploads/images/20230427/202304271854309f1d20571.png', 1682592870, 1682655178, 1682655178), (54, 1, 0, 0, 10, '写故事@2x.png', 'uploads/images/20230427/202304271854303106d2020.png', 1682592870, 1682655178, 1682655178), (55, 1, 0, 0, 10, 'SWOT分析法@2x.png', 'uploads/images/20230427/202304271854303021f5421.png', 1682592870, 1682655178, 1682655178), (56, 1, 0, 0, 10, '小红书标题@2x.png', 'uploads/images/20230427/202304271854306963e2267.png', 1682592870, 1682655178, 1682655178), (57, 1, 0, 0, 10, '写文案@2x.png', 'uploads/images/20230427/2023042718543071f298943.png', 1682592870, 1682655178, 1682655178), (58, 1, 0, 0, 10, '写标题@2x.png', 'uploads/images/20230427/20230427185430e27c96593.png', 1682592870, 1682655178, 1682655178), (59, 0, 0, 0, 10, '道歉信.png', 'uploads/images/20230427/20230427190600e6b263221.png', 1682593560, 1682655178, 1682655178), (60, 0, 0, 0, 10, '检讨书.png', 'uploads/images/20230427/202304271906000997b4218.png', 1682593560, 1682655178, 1682655178), (61, 0, 0, 0, 10, '情感安慰@2x.png', 'uploads/images/20230428/20230428121352014671262.png', 1682655232, 1683183188, 1683183188), (62, 0, 0, 0, 10, '单身狗导师@2x.png', 'uploads/images/20230428/202304281213522af450591.png', 1682655232, 1683183188, 1683183188), (63, 0, 0, 0, 10, '表白信@2x.png', 'uploads/images/20230428/20230428121352ee19f4521.png', 1682655232, 1683183188, 1683183188), (64, 0, 0, 0, 10, '编程高手@2x.png', 'uploads/images/20230428/202304281213525589f0849.png', 1682655232, 1683183188, 1683183188), (65, 0, 0, 0, 10, '广告创意@2x.png', 'uploads/images/20230428/2023042812135206cb39969.png', 1682655232, 1683183188, 1683183188), (66, 0, 0, 0, 10, '翻译@2x.png', 'uploads/images/20230428/20230428121352ecc330770.png', 1682655232, 1683183182, 1683183182), (67, 0, 0, 0, 10, '菜谱大全@2x.png', 'uploads/images/20230428/20230428121352df7154109.png', 1682655232, 1683183182, 1683183182), (68, 0, 0, 0, 10, '猜灯谜@2x.png', 'uploads/images/20230428/20230428121352277f26503.png', 1682655232, 1683183182, 1683183182), (69, 0, 0, 0, 10, '产品描述@2x.png', 'uploads/images/20230428/20230428121352388f95525.png', 1682655232, 1683183182, 1683183182), (70, 0, 0, 0, 10, '写文案@2x.png', 'uploads/images/20230428/20230428121352577407894.png', 1682655232, 1683183182, 1683183182), (71, 0, 0, 0, 10, '道歉信@2x.png', 'uploads/images/20230428/202304281213589424b6947.png', 1682655238, 1683183182, 1683183182), (72, 0, 0, 0, 10, '吵架助手@2x.png', 'uploads/images/20230428/20230428121358f86724560.png', 1682655238, 1683183182, 1683183182), (73, 0, 0, 0, 10, '短视频脚本@2x.png', 'uploads/images/20230428/2023042812140618db71656.png', 1682655246, 1683183182, 1683183182), (74, 0, 0, 0, 10, '发言稿@2x.png', 'uploads/images/20230428/2023042812140624e716598.png', 1682655246, 1683183183, 1683183183), (75, 0, 0, 0, 10, '个性签名@2x.png', 'uploads/images/20230428/20230428121406d55310538.png', 1682655246, 1683183183, 1683183183), (76, 0, 0, 0, 10, '哄男友@2x.png', 'uploads/images/20230428/20230428121406be0fa9325.png', 1682655246, 1683183183, 1683183183), (77, 0, 0, 0, 10, '检讨书@2x.png', 'uploads/images/20230428/20230428121406315f78248.png', 1682655246, 1683183183, 1683183183), (78, 0, 0, 0, 10, '工作计划@2x.png', 'uploads/images/20230428/2023042812140638a2b4228.png', 1682655246, 1683183183, 1683183183), (79, 0, 0, 0, 10, '哄女友@2x.png', 'uploads/images/20230428/20230428121406cc4675344.png', 1682655246, 1683183183, 1683183183), (80, 0, 0, 0, 10, '节日祝福@2x.png', 'uploads/images/20230428/20230428121406e2d238943.png', 1682655246, 1683183183, 1683183183), (81, 0, 0, 0, 10, '高情商回复@2x.png', 'uploads/images/20230428/2023042812140645a8d9933.png', 1682655246, 1683183183, 1683183183), (82, 0, 0, 0, 10, '工作总结@2x.png', 'uploads/images/20230428/2023042812140628c670926.png', 1682655246, 1683183183, 1683183183), (83, 0, 0, 0, 10, '夸夸小助手@2x.png', 'uploads/images/20230428/2023042812141206eb97112.png', 1682655252, 1683183183, 1683183183), (84, 0, 0, 0, 10, '朋友圈文案@2x.png', 'uploads/images/20230428/202304281214121aec66120.png', 1682655252, 1683183183, 1683183183), (85, 0, 0, 0, 10, '生活技巧@2x.png', 'uploads/images/20230428/20230428121440ac6844830.png', 1682655280, 1683183183, 1683183183), (86, 0, 0, 0, 10, '请假小能手@2x.png', 'uploads/images/20230428/20230428121440c21701987.png', 1682655280, 1683183178, 1683183178), (87, 0, 0, 0, 10, '小红书文章@2x.png', 'uploads/images/20230428/2023042812144012f3b8430.png', 1682655280, 1683183178, 1683183178), (88, 0, 0, 0, 10, '小红书标题@2x.png', 'uploads/images/20230428/20230428121440698044729.png', 1682655280, 1683183178, 1683183178), (89, 0, 0, 0, 10, '日报周报@2x.png', 'uploads/images/20230428/2023042812144021af97450.png', 1682655280, 1683183178, 1683183178), (90, 0, 0, 0, 10, '甩锅助手@2x.png', 'uploads/images/20230428/2023042812144078d244462.png', 1682655280, 1683183178, 1683183178), (91, 0, 0, 0, 10, '文章总结@2x.png', 'uploads/images/20230428/20230428121440f06993082.png', 1682655280, 1683183178, 1683183178), (92, 0, 0, 0, 10, '探店打卡@2x.png', 'uploads/images/20230428/2023042812144009df82343.png', 1682655280, 1683183178, 1683183178), (93, 0, 0, 0, 10, '微博拟写@2x.png', 'uploads/images/20230428/20230428121440356625545.png', 1682655280, 1683183178, 1683183178), (94, 0, 0, 0, 10, '文章扩写@2x.png', 'uploads/images/20230428/20230428121440c8ba43135.png', 1682655280, 1683183178, 1683183178), (95, 0, 0, 0, 10, '写标题@2x.png', 'uploads/images/20230428/20230428121445a21d01381.png', 1682655285, 1683183178, 1683183178), (96, 0, 0, 0, 10, '写故事@2x.png', 'uploads/images/20230428/20230428121445d177d5066.png', 1682655285, 1683183178, 1683183178), (97, 0, 0, 0, 10, '写诗词@2x.png', 'uploads/images/20230428/20230428121452057814126.png', 1682655292, 1683183178, 1683183178), (98, 0, 0, 0, 10, '写情话@2x.png', 'uploads/images/20230428/202304281214521e62b0853.png', 1682655292, 1683183178, 1683183178), (99, 0, 0, 0, 10, '写检讨书@2x.png', 'uploads/images/20230428/2023042812145290d567275.png', 1682655292, 1683183178, 1683183178), (100, 0, 0, 0, 10, '职场小树洞@2x.png', 'uploads/images/20230428/20230428121452e72882786.png', 1682655292, 1683183178, 1683183178), (101, 0, 0, 0, 10, 'AI对联@2x.png', 'uploads/images/20230428/20230428121452193eb7533.png', 1682655292, 1683183178, 1683183178), (102, 0, 0, 0, 10, '知乎风格@2x.png', 'uploads/images/20230428/20230428121452f0da43822.png', 1682655292, 1683183178, 1683183178), (103, 0, 0, 0, 10, 'AI取名@2x.png', 'uploads/images/20230428/20230428121452df17c9392.png', 1682655292, 1683183178, 1683183178), (104, 0, 0, 0, 10, '写作文@2x.png', 'uploads/images/20230428/20230428121452a94d05084.png', 1682655292, 1683183178, 1683183178), (105, 0, 0, 0, 10, '邮件编写@2x.png', 'uploads/images/20230428/20230428121452554398618.png', 1682655292, 1683183178, 1683183178), (106, 0, 0, 0, 10, '做个好梦@2x.png', 'uploads/images/20230428/20230428121452a18bd4625.png', 1682655292, 1683183194, 1683183194), (107, 0, 0, 0, 10, 'Emoji翻译@2x.png', 'uploads/images/20230428/202304281215004219d3304.png', 1682655300, 1683183194, 1683183194), (108, 0, 0, 0, 10, 'SWOT分析法@2x.png', 'uploads/images/20230428/202304281215000ecb99789.png', 1682655300, 1683183171, 1683183171), (109, 0, 0, 0, 10, 'OKR规划@2x.png', 'uploads/images/20230428/2023042812150029bf56334.png', 1682655300, 1683183171, 1683183171), (110, 0, 0, 0, 10, '日报周报.png', 'uploads/images/20230428/2023042814544145cf12876.png', 1682664881, 1682665276, 1682665276), (111, 0, 0, 0, 10, '工作总结.png', 'uploads/images/20230428/20230428145852d863a3998.png', 1682665132, 1682665278, 1682665278), (112, 0, 0, 0, 10, '日报周报.png', 'uploads/images/20230428/2023042815012318d626421.png', 1682665283, 1682665283, NULL), (113, 0, 0, 0, 10, '工作总结.png', 'uploads/images/20230428/20230428150123247409068.png', 1682665283, 1682665283, NULL), (114, 0, 0, 0, 10, '翻译.png', 'uploads/images/20230428/2023042815033970e269155.png', 1682665419, 1682665419, NULL), (115, 0, 0, 0, 10, '短视频脚本.png', 'uploads/images/20230428/202304281507376d95b8378.png', 1682665657, 1682665657, NULL), (116, 0, 0, 0, 10, '表白信.png', 'uploads/images/20230428/202304281512465ab635040.png', 1682665966, 1682665966, NULL), (117, 0, 0, 0, 10, '写诗词.png', 'uploads/images/20230428/2023042815165822e209435.png', 1682666218, 1682666263, 1682666263), (118, 0, 0, 0, 10, '写诗词.png', 'uploads/images/20230428/2023042815174896d113447.png', 1682666268, 1682666268, NULL), (119, 0, 0, 0, 10, '论文资料.png', 'uploads/images/20230428/202304281523150a3556607.png', 1682666595, 1682667283, 1682667283), (120, 0, 0, 0, 10, '写故事.png', 'uploads/images/20230428/20230428152315859844790.png', 1682666595, 1682666595, NULL), (121, 0, 0, 0, 10, '关键词标题.png', 'uploads/images/20230428/202304281526591d23f0147.png', 1682666819, 1682666911, 1682666911), (122, 0, 0, 0, 10, '关键词标题.png', 'uploads/images/20230428/2023042815283629f972349.png', 1682666916, 1682666916, NULL), (123, 0, 0, 0, 10, '英文写作.png', 'uploads/images/20230428/20230428153120af5a84752.png', 1682667080, 1682667182, 1682667182), (124, 0, 0, 0, 10, '阅读助手.png', 'uploads/images/20230428/20230428153120306dd2174.png', 1682667080, 1682667080, NULL), (125, 0, 0, 0, 10, '英文写作.png', 'uploads/images/20230428/202304281533132edd92654.png', 1682667193, 1682667193, NULL), (126, 0, 0, 0, 10, '论文资料.png', 'uploads/images/20230428/20230428153447e95b56562.png', 1682667287, 1682667287, NULL), (127, 0, 0, 0, 10, '广告创意.png', 'uploads/images/20230428/20230428154146d05d30456.png', 1682667706, 1682667706, NULL), (128, 0, 0, 0, 10, '个性签名.png', 'uploads/images/20230428/202304281541467b6b44347.png', 1682667706, 1682667706, NULL), (129, 0, 0, 0, 10, '朋友圈文案.png', 'uploads/images/20230428/202304281541462fedb5814.png', 1682667706, 1682667706, NULL), (130, 0, 0, 0, 10, '产品描述.png', 'uploads/images/20230428/20230428154146708464259.png', 1682667706, 1682667706, NULL), (131, 0, 0, 0, 10, '外卖好评.png', 'uploads/images/20230428/202304281545204e1e70449.png', 1682667920, 1682667920, NULL), (132, 0, 0, 0, 10, '商品好评.png', 'uploads/images/20230428/20230428154520da4d39685.png', 1682667920, 1682668465, 1682668465), (133, 0, 0, 0, 10, '小红书文案.png', 'uploads/images/20230428/202304281554077824e0359.png', 1682668447, 1682668447, NULL), (134, 0, 0, 0, 10, '商品好评.png', 'uploads/images/20230428/20230428155430f4dd12440.png', 1682668470, 1682668470, NULL), (135, 2, 0, 0, 10, '写作辅助-小红书风格@2x.png', 'uploads/images/20230504/20230504151454fe3e23462.png', 1683184494, 1683184494, NULL), (136, 2, 0, 0, 10, '写作辅助-格言书@2x.png', 'uploads/images/20230504/20230504151454c6a1e3919.png', 1683184494, 1683184494, NULL), (137, 2, 0, 0, 10, '写作辅助-内容总结@2x.png', 'uploads/images/20230504/20230504151454c26496264.png', 1683184494, 1683184494, NULL), (138, 2, 0, 0, 10, '写作辅助-论文式回答@2x.png', 'uploads/images/20230504/2023050415145425f277982.png', 1683184494, 1683184494, NULL), (139, 2, 0, 0, 10, '写作辅助-写作素材搜集@2x.png', 'uploads/images/20230504/20230504151454e52df9391.png', 1683184494, 1683184494, NULL), (140, 2, 0, 0, 10, '写作辅助-写作助理@2x.png', 'uploads/images/20230504/20230504151454a2bbe0181.png', 1683184494, 1683184494, NULL), (141, 2, 0, 0, 10, '写作辅助-文章续写@2x.png', 'uploads/images/20230504/2023050415145490f725284.png', 1683184494, 1683184494, NULL), (142, 2, 0, 0, 10, '写作辅助-写作建议@2x.png', 'uploads/images/20230504/20230504151454efaca3505.png', 1683184494, 1683184494, NULL), (143, 2, 0, 0, 10, '写作辅助-写作标题生成器@2x.png', 'uploads/images/20230504/20230504151454e9ff81328.png', 1683184494, 1683184494, NULL), (144, 0, 0, 0, 10, '旅游计划.png', 'uploads/images/20230504/2023050416014027d0c7900.png', 1683187300, 1683187300, NULL), (145, 0, 0, 0, 10, '电影推荐.png', 'uploads/images/20230504/202305041602579ff496547.png', 1683187377, 1683187598, 1683187598), (146, 0, 0, 0, 10, '节日祝福.png', 'uploads/images/20230504/20230504160257b00f49229.png', 1683187377, 1683187595, 1683187595), (147, 0, 0, 0, 10, '知乎回答.png', 'uploads/images/20230504/20230504160257b23941137.png', 1683187377, 1683187377, NULL), (148, 0, 0, 0, 10, '节日祝福.png', 'uploads/images/20230504/20230504160644d9c374603.png', 1683187604, 1683187604, NULL), (149, 0, 0, 0, 10, '电影推荐.png', 'uploads/images/20230504/20230504160644f743e8550.png', 1683187604, 1683187604, NULL), (150, 0, 0, 0, 10, '高情商回复.png', 'uploads/images/20230504/202305041612548d7589809.png', 1683187974, 1683187974, NULL), (151, 0, 0, 0, 10, '单身狗导师.png', 'uploads/images/20230504/20230504161254730381861.png', 1683187974, 1683187974, NULL), (152, 0, 0, 0, 10, '送女友礼物.png', 'uploads/images/20230504/20230504161254253c90564.png', 1683187974, 1683187974, NULL), (153, 3, 0, 0, 10, '文章-故事-编剧@2x.png', 'uploads/images/20230504/20230504170646c48312494.png', 1683191206, 1683191206, NULL), (154, 3, 0, 0, 10, '文章-故事-讲故事@2x.png', 'uploads/images/20230504/202305041706464b1194148.png', 1683191206, 1683191206, NULL), (155, 3, 0, 0, 10, '文章-故事-论文式回答@2x.png', 'uploads/images/20230504/20230504170646e3b676336.png', 1683191206, 1683191206, NULL), (156, 3, 0, 0, 10, '文章-故事-诗人@2x.png', 'uploads/images/20230504/202305041706468bd320898.png', 1683191206, 1683191206, NULL), (157, 3, 0, 0, 10, '文章-故事-脱口秀@2x.png', 'uploads/images/20230504/20230504170646dab8d3160.png', 1683191206, 1683191206, NULL), (158, 3, 0, 0, 10, '文章-故事-小说家@2x.png', 'uploads/images/20230504/20230504170646890a94841.png', 1683191206, 1683191206, NULL), (159, 3, 0, 0, 10, '文章-故事-新闻记者@2x.png', 'uploads/images/20230504/20230504170646b3aaf5584.png', 1683191206, 1683191206, NULL), (160, 3, 0, 0, 10, '文章-故事-求职信@2x.png', 'uploads/images/20230504/2023050417064608d1d2789.png', 1683191206, 1683191206, NULL), (161, 4, 0, 0, 10, '文本-词语-随机回复@2x.png', 'uploads/images/20230504/20230504170849f7f8f9494.png', 1683191329, 1683191329, NULL), (162, 4, 0, 0, 10, '文本-词语-抄袭检查@2x.png', 'uploads/images/20230504/202305041708493f7cb5173.png', 1683191329, 1683191329, NULL), (163, 4, 0, 0, 10, '文本-词语-文本情绪分析@2x.png', 'uploads/images/20230504/20230504170849fd7b96806.png', 1683191329, 1683191329, NULL), (164, 4, 0, 0, 10, '文本-词语-文本意图分类@2x.png', 'uploads/images/20230504/202305041708493862a2370.png', 1683191329, 1683191329, NULL), (165, 4, 0, 0, 10, '文本-词语-提取联系信息@2x.png', 'uploads/images/20230504/2023050417084984b260784.png', 1683191329, 1683191329, NULL), (166, 4, 0, 0, 10, '文本-词语-同义词@2x.png', 'uploads/images/20230504/202305041708498f03e2690.png', 1683191329, 1683191329, NULL), (167, 5, 0, 0, 10, '发散思维-四重结构归纳@2x.png', 'uploads/images/20230504/20230504171002ea1096585.png', 1683191402, 1683191402, NULL), (168, 5, 0, 0, 10, '发散思维-知识点阐述@2x.png', 'uploads/images/20230504/202305041710024a0b21093.png', 1683191402, 1683191402, NULL), (169, 5, 0, 0, 10, '发散思维-提问助手@2x.png', 'uploads/images/20230504/20230504171002b86f60658.png', 1683191402, 1683191402, NULL), (170, 5, 0, 0, 10, '发散思维-主题解构@2x.png', 'uploads/images/20230504/202305041710029d5d12999.png', 1683191402, 1683191402, NULL), (171, 5, 0, 0, 10, '发散思维-辩手@2x.png', 'uploads/images/20230504/2023050417101034b327661.png', 1683191410, 1683191410, NULL), (172, 5, 0, 0, 10, '发散思维-谬误发现者@2x.png', 'uploads/images/20230504/20230504171010c75092423.png', 1683191410, 1683191410, NULL), (173, 6, 0, 0, 10, '点评-评鉴-科技博主@2x.png', 'uploads/images/20230504/202305041710598da8b1983.png', 1683191459, 1683191459, NULL), (174, 6, 0, 0, 10, '点评-评鉴-科技点评@2x.png', 'uploads/images/20230504/202305041710590b8e76997.png', 1683191459, 1683191459, NULL), (175, 6, 0, 0, 10, '点评-评鉴-期刊评审@2x.png', 'uploads/images/20230504/2023050417105921ab95088.png', 1683191459, 1683191459, NULL), (176, 6, 0, 0, 10, '点评-评鉴-电影电影点评@2x.png', 'uploads/images/20230504/20230504171059c10fa4307.png', 1683191459, 1683191459, NULL), (177, 6, 0, 0, 10, '点评-评鉴-美食点评@2x.png', 'uploads/images/20230504/202305041710597c2e20446.png', 1683191459, 1683191459, NULL), (178, 7, 0, 0, 10, 'IT-编程-代码释义器@2x.png', 'uploads/images/20230504/20230504171235434354082.png', 1683191555, 1683191555, NULL), (179, 7, 0, 0, 10, 'IT-编程-架构师 IT@2x.png', 'uploads/images/20230504/2023050417123576b3e7345.png', 1683191555, 1683191555, NULL), (180, 7, 0, 0, 10, 'IT-编程-全栈程序员@2x.png', 'uploads/images/20230504/20230504171235305017496.png', 1683191555, 1683191555, NULL), (181, 7, 0, 0, 10, 'IT-编程-开发者数据@2x.png', 'uploads/images/20230504/202305041712356c3cd5030.png', 1683191555, 1683191555, NULL), (182, 7, 0, 0, 10, 'IT-编程-开发：微信小程序@2x.png', 'uploads/images/20230504/20230504171235766e86430.png', 1683191555, 1683191555, NULL), (183, 7, 0, 0, 10, 'IT-编程-前端：UX-UI 界面@2x.png', 'uploads/images/20230504/20230504171235c73080884.png', 1683191555, 1683191555, NULL), (184, 7, 0, 0, 10, 'IT-编程-开发：Vue3@2x.png', 'uploads/images/20230504/202305041712356f0b39080.png', 1683191555, 1683191555, NULL), (185, 7, 0, 0, 10, 'IT-编程-前端：网页设计@2x.png', 'uploads/images/20230504/20230504171235e40ef1465.png', 1683191555, 1683191555, NULL), (186, 7, 0, 0, 10, 'IT-编程-前端开发@2x.png', 'uploads/images/20230504/20230504171235c3c4f9295.png', 1683191555, 1683191555, NULL), (187, 7, 0, 0, 10, 'IT-编程-软件测试@2x.png', 'uploads/images/20230504/2023050417123528e1d3306.png', 1683191555, 1683191555, NULL), (188, 7, 0, 0, 10, 'IT-编程-信息生成器@2x.png', 'uploads/images/20230504/2023050417124186f785415.png', 1683191561, 1683191561, NULL), (189, 7, 0, 0, 10, 'IT-编程-网络安全专家@2x.png', 'uploads/images/20230504/20230504171241f8e0e2025.png', 1683191561, 1683191561, NULL), (190, 7, 0, 0, 10, 'IT-编程-智能域名生成器@2x.png', 'uploads/images/20230504/20230504171241d75fa6477.png', 1683191561, 1683191561, NULL), (191, 7, 0, 0, 10, 'IT-编程-搜索引擎 Solr@2x.png', 'uploads/images/20230504/20230504171241660d64416.png', 1683191561, 1683191561, NULL), (192, 7, 0, 0, 10, 'IT-编程-正则生成器@2x.png', 'uploads/images/20230504/202305041712414195f9938.png', 1683191561, 1683191561, NULL), (193, 8, 0, 0, 10, '自助百科-应急反应专家@2x.png', 'uploads/images/20230504/202305041717277a2ea6782.png', 1683191847, 1683191847, NULL), (194, 8, 0, 0, 10, '自助百科-职业顾问@2x.png', 'uploads/images/20230504/202305041717277e30d3928.png', 1683191847, 1683191847, NULL), (195, 8, 0, 0, 10, '自助百科-购物建议@2x.png', 'uploads/images/20230504/20230504171727953908467.png', 1683191847, 1683191847, NULL), (196, 8, 0, 0, 10, '自助百科-趣味建议@2x.png', 'uploads/images/20230504/20230504171727442d91762.png', 1683191847, 1683191847, NULL), (197, 8, 0, 0, 10, '自助百科-生活自助百科@2x.png', 'uploads/images/20230504/20230504171727b83c45568.png', 1683191847, 1683191847, NULL), (198, 8, 0, 0, 10, '自助百科-非小说类书籍总结@2x.png', 'uploads/images/20230504/20230504171727fc4f94779.png', 1683191847, 1683191847, NULL), (199, 8, 0, 0, 10, '职业顾问@2x.png', 'uploads/images/20230504/20230504171727d33806172.png', 1683191847, 1683191847, NULL), (200, 0, 0, 0, 10, '趣味知识-角色扮演@2x.png', 'uploads/images/20230504/20230504171843568310120.png', 1683191923, 1683191923, NULL), (201, 0, 0, 0, 10, '趣味知识-海绵宝宝的神奇海螺@2x.png', 'uploads/images/20230504/202305041718432609e6631.png', 1683191923, 1683191923, NULL), (202, 0, 0, 0, 10, '趣味知识-解梦@2x.png', 'uploads/images/20230504/202305041718433beef0273.png', 1683191923, 1683191923, NULL), (203, 0, 0, 0, 10, '趣味知识-DIY 专家@2x.png', 'uploads/images/20230504/2023050417184370fa19731.png', 1683191923, 1683191923, NULL), (204, 0, 0, 0, 10, '趣味知识-艺术顾问@2x.png', 'uploads/images/20230504/202305041718432caee4801.png', 1683191923, 1683191923, NULL), (205, 0, 0, 0, 10, '趣味知识-占星家@2x.png', 'uploads/images/20230504/2023050417184317cd21716.png', 1683191923, 1683191923, NULL), (206, 0, 0, 0, 10, '趣味知识-魔术师@2x.png', 'uploads/images/20230504/202305041718439e7236361.png', 1683191923, 1683191923, NULL), (207, 0, 0, 0, 10, '支付宝支付.jpg', 'uploads/images/20230504/2023050418330473d614282.jpg', 1683196384, 1683196451, 1683196451), (208, 0, 0, 0, 10, '支付宝支付.jpg', 'uploads/images/20230504/202305041834170591f3990.jpg', 1683196457, 1683196457, NULL), (209, 0, 0, 0, 10, '语言-翻译-摩斯电码翻译.png', 'uploads/images/20230504/20230504184145f7ae63935.png', 1683196905, 1683197057, 1683197057), (210, 0, 0, 0, 10, '语言-翻译-英语对话练习.png', 'uploads/images/20230504/202305041841456fe066662.png', 1683196905, 1683197054, 1683197054), (211, 0, 0, 0, 10, '语言-翻译-语言生成器.png', 'uploads/images/20230504/20230504184145e398a1726.png', 1683196905, 1683197050, 1683197050), (212, 0, 0, 0, 10, '语言-翻译-英语发音助手.png', 'uploads/images/20230504/20230504184145056df3953.png', 1683196905, 1683197044, 1683197044), (213, 0, 0, 0, 10, '语言-翻译-中英互译.png', 'uploads/images/20230504/202305041841454ed951149.png', 1683196905, 1683197047, 1683197047), (214, 0, 0, 0, 10, '语言-翻译-英语对话练习.jpg', 'uploads/images/20230504/20230504184423951506952.jpg', 1683197063, 1683197235, 1683197235), (215, 0, 0, 0, 10, '语言-翻译-英语发音助手.jpg', 'uploads/images/20230504/20230504184423d672e7171.jpg', 1683197063, 1683197235, 1683197235), (216, 0, 0, 0, 10, '语言-翻译-摩斯电码翻译.jpg', 'uploads/images/20230504/20230504184423ff4229442.jpg', 1683197063, 1683197235, 1683197235), (217, 0, 0, 0, 10, '语言-翻译-语言生成器.jpg', 'uploads/images/20230504/202305041844232a9be6287.jpg', 1683197063, 1683197235, 1683197235), (218, 0, 0, 0, 10, '语言-翻译-中英互译.jpg', 'uploads/images/20230504/2023050418442338fc79495.jpg', 1683197063, 1683197235, 1683197235), (219, 0, 0, 0, 10, '语言-翻译-摩斯电码翻译.png', 'uploads/images/20230504/20230504184724faa109766.png', 1683197244, 1683197244, NULL), (220, 0, 0, 0, 10, '语言-翻译-英语对话练习.png', 'uploads/images/20230504/2023050418472433b489400.png', 1683197244, 1683197244, NULL), (221, 0, 0, 0, 10, '语言-翻译-中英互译.png', 'uploads/images/20230504/20230504184724aff647458.png', 1683197244, 1683197244, NULL), (222, 0, 0, 0, 10, '语言-翻译-语言生成器.png', 'uploads/images/20230504/20230504184724b760d7703.png', 1683197244, 1683197244, NULL), (223, 0, 0, 0, 10, '语言-翻译-英语发音助手.png', 'uploads/images/20230504/20230504184724d1bd39026.png', 1683197244, 1683197244, NULL), (224, 9, 0, 0, 10, 'icon_tab_my_ss.png', 'uploads/images/20230504/202305041901598b29d5906.png', 1683198119, 1683198119, NULL), (225, 9, 0, 0, 10, 'icon_tab_QandA_ss.png', 'uploads/images/20230504/20230504190159955ac6100.png', 1683198119, 1683198119, NULL), (226, 9, 0, 0, 10, 'icon_tab_skill_ss.png', 'uploads/images/20230504/202305041901593ee491985.png', 1683198119, 1683198119, NULL), (227, 9, 0, 0, 10, 'icon_tab_creation_ss.png', 'uploads/images/20230504/20230504190159692069376.png', 1683198119, 1683198119, NULL), (228, 9, 0, 0, 10, 'icon_tab_task_ss.png', 'uploads/images/20230504/20230504190159145e26328.png', 1683198119, 1683198119, NULL), (229, 9, 0, 0, 10, 'icon_tab_creation@2x.png', 'uploads/images/20230504/2023050419021699dc63106.png', 1683198136, 1683198234, 1683198234), (230, 9, 0, 0, 10, 'icon_tab_my@2x.png', 'uploads/images/20230504/202305041902166f5fd7148.png', 1683198136, 1683198234, 1683198234), (231, 9, 0, 0, 10, 'icon_tab_task@2x.png', 'uploads/images/20230504/202305041902163ddca7300.png', 1683198136, 1683198235, 1683198235), (232, 9, 0, 0, 10, 'icon_tab_QandA@2x.png', 'uploads/images/20230504/20230504190216d32517006.png', 1683198136, 1683198235, 1683198235), (233, 9, 0, 0, 10, 'icon_tab_skill@2x.png', 'uploads/images/20230504/20230504190216f775b0755.png', 1683198136, 1683198235, 1683198235), (234, 10, 0, 0, 10, 'icon_tab_creation_s@2x.png', 'uploads/images/20230504/20230504190242c022b2150.png', 1683198162, 1683198266, 1683198266), (235, 10, 0, 0, 10, 'icon_tab_skill_s@2x.png', 'uploads/images/20230504/202305041902420c2455008.png', 1683198162, 1683198266, 1683198266), (236, 10, 0, 0, 10, 'icon_tab_my_s@2x.png', 'uploads/images/20230504/202305041902422bd798504.png', 1683198162, 1683198266, 1683198266), (237, 10, 0, 0, 10, 'icon_tab_QandA_s@2x.png', 'uploads/images/20230504/202305041902425b2f71782.png', 1683198162, 1683198266, 1683198266), (238, 10, 0, 0, 10, 'icon_tab_task_s@2x.png', 'uploads/images/20230504/2023050419024253b016684.png', 1683198162, 1683198266, 1683198266), (239, 9, 0, 0, 10, 'icon_tab_QandA.png', 'uploads/images/20230504/20230504190419b1fcf0678.png', 1683198259, 1683198259, NULL), (240, 9, 0, 0, 10, 'icon_tab_creation.png', 'uploads/images/20230504/2023050419041902a3c0412.png', 1683198259, 1683198259, NULL), (241, 9, 0, 0, 10, 'icon_tab_skill.png', 'uploads/images/20230504/20230504190419fda453369.png', 1683198259, 1683198259, NULL), (242, 9, 0, 0, 10, 'icon_tab_task.png', 'uploads/images/20230504/20230504190419f44074794.png', 1683198259, 1683198259, NULL), (243, 9, 0, 0, 10, 'icon_tab_my.png', 'uploads/images/20230504/20230504190419a9f826126.png', 1683198259, 1683198259, NULL), (244, 10, 0, 0, 10, 'icon_tab_skill_s.png', 'uploads/images/20230504/20230504190434a32c20573.png', 1683198274, 1683198274, NULL), (245, 10, 0, 0, 10, 'icon_tab_task_s.png', 'uploads/images/20230504/202305041904341aaad4757.png', 1683198274, 1683198274, NULL), (246, 10, 0, 0, 10, 'icon_tab_my_s.png', 'uploads/images/20230504/202305041904344a0f34205.png', 1683198274, 1683198274, NULL), (247, 10, 0, 0, 10, 'icon_tab_creation_s.png', 'uploads/images/20230504/20230504190434220243525.png', 1683198274, 1683198274, NULL), (248, 10, 0, 0, 10, 'icon_tab_QandA_s.png', 'uploads/images/20230504/2023050419043456f7d1713.png', 1683198274, 1683198274, NULL), (249, 11, 0, 0, 10, 'icon_tab_task_green.png', 'uploads/images/20230504/202305041904449a14a6996.png', 1683198284, 1683198966, 1683198966), (250, 11, 0, 0, 10, 'icon_tab_my_green.png', 'uploads/images/20230504/20230504190444aaa788879.png', 1683198284, 1683198966, 1683198966), (251, 11, 0, 0, 10, 'icon_tab_creation_green.png', 'uploads/images/20230504/202305041904448e8516756.png', 1683198284, 1683198966, 1683198966), (252, 11, 0, 0, 10, 'icon_tab_skill_green.png', 'uploads/images/20230504/20230504190444cc1012821.png', 1683198284, 1683198966, 1683198966), (253, 11, 0, 0, 10, 'icon_tab_QandA_green.png', 'uploads/images/20230504/2023050419044425f707081.png', 1683198284, 1683198966, 1683198966), (254, 12, 0, 0, 10, 'icon_tab_creation_purple.png', 'uploads/images/20230504/20230504190454a92954970.png', 1683198294, 1683198294, NULL), (255, 12, 0, 0, 10, 'icon_tab_my_purple.png', 'uploads/images/20230504/2023050419045420c247694.png', 1683198294, 1683198294, NULL), (256, 12, 0, 0, 10, 'icon_tab_QandA_purple.png', 'uploads/images/20230504/20230504190454a86d25509.png', 1683198294, 1683198294, NULL), (257, 12, 0, 0, 10, 'icon_tab_skill_purple.png', 'uploads/images/20230504/2023050419045492bae7010.png', 1683198294, 1683198294, NULL), (258, 12, 0, 0, 10, 'icon_tab_task_purple.png', 'uploads/images/20230504/20230504190454c0a2b9942.png', 1683198294, 1683198294, NULL), (259, 11, 0, 0, 10, 'icon_tab_creation_green.png', 'uploads/images/20230504/20230504191537810b43786.png', 1683198937, 1683198974, NULL), (260, 11, 0, 0, 10, 'icon_tab_my_green.png', 'uploads/images/20230504/20230504191537f299d9885.png', 1683198937, 1683198974, NULL), (261, 11, 0, 0, 10, 'icon_tab_skill_green.png', 'uploads/images/20230504/20230504191537616931837.png', 1683198937, 1683198974, NULL), (262, 11, 0, 0, 10, 'icon_tab_QandA_green.png', 'uploads/images/20230504/202305041915377c3bf2312.png', 1683198937, 1683198974, NULL), (263, 11, 0, 0, 10, 'icon_tab_task_green.png', 'uploads/images/20230504/202305041915377a1872054.png', 1683198937, 1683198974, NULL), (264, 0, 0, 0, 10, 'erweima.png', 'uploads/images/20230506/20230506093351f79995343.png', 1683336831, 1683336831, NULL), (265, 0, 0, 0, 10, '登录封面.jpg', 'uploads/images/20230509/20230509160002804537249.jpg', 1683619202, 1683619202, NULL), (266, 0, 0, 0, 10, 'AI智能聊天LOGO-旧.png', 'uploads/images/20230509/20230509160033da1cd4921.png', 1683619233, 1683619236, 1683619236), (267, 0, 0, 0, 10, '头像.jpg', 'uploads/images/20230509/2023050916011518c4c3476.jpg', 1683619275, 1683619275, NULL), (268, 0, 0, 0, 10, '默认头像.png', 'uploads/images/20230509/2023050916023679a246417.png', 1683619356, 1683619356, NULL);
COMMIT;

-- ----------------------------
-- Table structure for ai_file_cate
-- ----------------------------
DROP TABLE IF EXISTS `ai_file_cate`;
CREATE TABLE `ai_file_cate`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父级ID',
  `type` tinyint(2) UNSIGNED NOT NULL DEFAULT 10 COMMENT '类型[10=图片，20=视频，30=文件]',
  `name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '分类名称',
  `create_time` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '文件分类表';

-- ----------------------------
-- Records of ai_file_cate
-- ----------------------------
BEGIN;
INSERT INTO `ai_file_cate` VALUES (1, 0, 10, 'AI创作', 1682592809, 1682592809, NULL), (2, 0, 10, '写作辅助', 1683184425, 1683184425, NULL), (3, 0, 10, '文章/故事', 1683191175, 1683191175, NULL), (4, 0, 10, '文本/词语', 1683191311, 1683191311, NULL), (5, 0, 10, '发散思维', 1683191389, 1683191389, NULL), (6, 0, 10, '点评/评鉴', 1683191442, 1683191442, NULL), (7, 0, 10, '编程开发', 1683191537, 1683191537, NULL), (8, 0, 10, '知识百科', 1683191794, 1683191794, NULL), (9, 0, 10, '底部导航黄色', 1683197854, 1683197854, NULL), (10, 0, 10, '底部导航蓝色', 1683198154, 1683198154, NULL), (11, 0, 10, '底部导航绿色', 1683198201, 1683198201, NULL), (12, 0, 10, '底部导航紫色', 1683198208, 1683198208, NULL);
COMMIT;

-- ----------------------------
-- Table structure for ai_generate_column
-- ----------------------------
DROP TABLE IF EXISTS `ai_generate_column`;
CREATE TABLE `ai_generate_column`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `table_id` int(11) NOT NULL DEFAULT 0 COMMENT '表id',
  `column_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '字段名称',
  `column_comment` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '字段描述',
  `column_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '字段类型',
  `is_required` tinyint(1) NULL DEFAULT 0 COMMENT '是否必填 0-非必填 1-必填',
  `is_pk` tinyint(1) NULL DEFAULT 0 COMMENT '是否为主键 0-不是 1-是',
  `is_insert` tinyint(1) NULL DEFAULT 0 COMMENT '是否为插入字段 0-不是 1-是',
  `is_update` tinyint(1) NULL DEFAULT 0 COMMENT '是否为更新字段 0-不是 1-是',
  `is_lists` tinyint(1) NULL DEFAULT 0 COMMENT '是否为列表字段 0-不是 1-是',
  `is_query` tinyint(1) NULL DEFAULT 0 COMMENT '是否为查询字段 0-不是 1-是',
  `query_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '=' COMMENT '查询类型',
  `view_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'input' COMMENT '显示类型',
  `dict_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '字典类型',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '代码生成表字段信息表';

-- ----------------------------
-- Records of ai_generate_column
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_generate_table
-- ----------------------------
DROP TABLE IF EXISTS `ai_generate_table`;
CREATE TABLE `ai_generate_table`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `table_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '表名称',
  `table_comment` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '表描述',
  `template_type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '模板类型 0-单表(curd) 1-树表(curd)',
  `author` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '作者',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '备注',
  `generate_type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '生成方式  0-压缩包下载 1-生成到模块',
  `module_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '模块名',
  `class_dir` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '类目录名',
  `class_comment` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '类描述',
  `admin_id` int(11) NULL DEFAULT 0 COMMENT '管理员id',
  `menu` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '菜单配置',
  `delete` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '删除配置',
  `tree` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '树表配置',
  `relations` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '关联配置',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '代码生成表信息表';

-- ----------------------------
-- Records of ai_generate_table
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_hot_search
-- ----------------------------
DROP TABLE IF EXISTS `ai_hot_search`;
CREATE TABLE `ai_hot_search`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '关键词',
  `sort` smallint(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序号',
  `create_time` int(10) NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '热门搜索表';

-- ----------------------------
-- Records of ai_hot_search
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_index_visit
-- ----------------------------
DROP TABLE IF EXISTS `ai_index_visit`;
CREATE TABLE `ai_index_visit`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '访客ip地址',
  `terminal` tinyint(1) NOT NULL COMMENT '访问终端',
  `visit` int(11) NOT NULL COMMENT '浏览量',
  `create_time` int(10) NULL DEFAULT NULL COMMENT '访问时间',
  `update_time` int(10) NULL DEFAULT NULL,
  `delete_time` int(10) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '首页浏览记录表';

-- ----------------------------
-- Records of ai_index_visit
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_jobs
-- ----------------------------
DROP TABLE IF EXISTS `ai_jobs`;
CREATE TABLE `ai_jobs`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '岗位名称',
  `code` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '岗位编码',
  `sort` int(11) NULL DEFAULT 0 COMMENT '显示顺序',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态（0停用 1正常）',
  `remark` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '岗位表';

-- ----------------------------
-- Records of ai_jobs
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_key_down_rule
-- ----------------------------
DROP TABLE IF EXISTS `ai_key_down_rule`;
CREATE TABLE `ai_key_down_rule`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL COMMENT '规则类型：1-对话,2-绘画',
  `ai_key` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '接口类型',
  `rule` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '停用规则',
  `prompt` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '停用提示',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：1-开启,0-关闭',
  `create_time` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'key自动下架规则';

-- ----------------------------
-- Records of ai_key_down_rule
-- ----------------------------
BEGIN;
INSERT INTO `ai_key_down_rule` VALUES (1, 1, 'gpt3.5', 'Incorrect API key provided', 'key不正确。', 1, 1694750306, 1694750306, NULL), (2, 1, 'gpt3.5', 'This key is associated with a deactivated account', 'key账号被封。', 1, 1694750306, 1694750306, NULL), (3, 1, 'gpt3.5', 'You exceeded your current quota', 'key账号余额不足。', 1, 1694750306, 1694750306, NULL), (4, 1, 'gpt3.5', 'The OpenAI account associated with this API key has been deactivated', 'key关联账号已被停用。', 1, 1694750306, 1694750306, NULL), (5, 1, 'gpt4.0', 'Incorrect API key provided', 'key不正确。', 1, 1694750306, 1694750306, NULL), (6, 1, 'gpt4.0', 'This key is associated with a deactivated account', 'key账号被封。', 1, 1694750306, 1694750306, NULL), (7, 1, 'gpt4.0', 'You exceeded your current quota', 'key账号余额不足。', 1, 1694750306, 1694750306, NULL), (8, 1, 'gpt4.0', 'The OpenAI account associated with this API key has been deactivated', 'key关联账号已被停用。', 1, 1694750306, 1694750306, NULL);
COMMIT;
-- ----------------------------
-- Table structure for ai_key_pool
-- ----------------------------
DROP TABLE IF EXISTS `ai_key_pool`;
CREATE TABLE `ai_key_pool`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL COMMENT '类型：1-对话,2-绘画',
  `ai_key` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '对话、绘画模型的key',
  `key` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'key',
  `appid` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'appid',
  `secret` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'secret',
  `status` tinyint(1) NOT NULL COMMENT '状态：1-开启,0-关闭',
  `notice` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '备注',
  `use_time` int(10) NULL DEFAULT NULL COMMENT '调用时间',
  `create_time` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'key池';

-- ----------------------------
-- Records of ai_key_pool
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_member_benefits
-- ----------------------------
DROP TABLE IF EXISTS `ai_member_benefits`;
CREATE TABLE `ai_member_benefits`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '权益名称',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '权益图标',
  `describe` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '权益描述',
  `status` tinyint(1) NOT NULL COMMENT '状态：1-开启；0-关闭；',
  `sort` int(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `create_time` int(10) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '会员权益';

-- ----------------------------
-- Records of ai_member_benefits
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_member_order
-- ----------------------------
DROP TABLE IF EXISTS `ai_member_order`;
CREATE TABLE `ai_member_order`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `sn` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '订单编号',
  `terminal` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '订单来源：1-微信小程序；2-微信公众号；3-手机H5；4-PC；5-苹果app；6-安卓app；',
  `pay_sn` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '支付编号-冗余字段，针对微信同一主体不同客户端支付需用不同订单号预留。',
  `pay_way` tinyint(2) NOT NULL DEFAULT 2 COMMENT '支付方式 2-微信支付 3-支付宝支付',
  `pay_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '支付状态：0-待支付；1-已支付',
  `pay_time` int(10) NULL DEFAULT NULL COMMENT '支付时间',
  `order_amount` decimal(10, 2) UNSIGNED NOT NULL COMMENT '实付金额',
  `discount_amount` decimal(10, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT '优惠金额',
  `total_amount` decimal(10, 2) UNSIGNED NOT NULL COMMENT '订单总价',
  `transaction_id` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '第三方平台交易流水号',
  `member_package_id` int(11) NULL DEFAULT NULL COMMENT '套餐ID',
  `member_package_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '套餐信息',
  `refund_status` tinyint(1) NULL DEFAULT 0 COMMENT '退款状态 0-未退款 1-已退款',
  `refund_transaction_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '退款交易流水号',
  `create_time` int(10) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '会员订单表';

-- ----------------------------
-- Records of ai_member_order
-- ----------------------------
BEGIN;
COMMIT;
-- ----------------------------
-- Table structure for ai_member_package
-- ----------------------------
DROP TABLE IF EXISTS `ai_member_package`;
CREATE TABLE `ai_member_package`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '套餐名称',
  `duration` int(5) UNSIGNED NOT NULL COMMENT '套餐时长',
  `duration_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '套餐时长类型：1-月；2-日；',
  `is_perpetual` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否永久套餐：1-是；0-否',
  `sell_price` decimal(10, 2) UNSIGNED NOT NULL COMMENT '销售价格',
  `lineation_price` decimal(10, 2) UNSIGNED NULL DEFAULT NULL COMMENT '划线价',
  `is_retrieve` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否开启挽回优惠：1-开启；0-关闭；',
  `retrieve_amount` decimal(10, 2) NULL DEFAULT NULL COMMENT '挽回优惠金额',
  `sort` int(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否上架：1-上架；0-下架；',
  `is_default` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否默认：1-是；0-否；',
  `chat_limit` int(5) UNSIGNED NULL DEFAULT NULL COMMENT '每日对话上限',
  `give_chat_number` int(5) UNSIGNED NULL DEFAULT NULL COMMENT '赠送对话次数',
  `give_draw_number` int(5) UNSIGNED NULL DEFAULT NULL COMMENT '赠送绘画次数',
  `benefits_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '会员权益',
  `is_quota` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否限购：1-是；0-否；',
  `quota_value` int(5) UNSIGNED NULL DEFAULT NULL COMMENT '限购数量',
  `quota_tips` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '限购提示语',
  `tag` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '套餐标签',
  `create_time` int(10) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB  CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '会员套餐' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ai_member_package
-- ----------------------------
BEGIN;
INSERT INTO `ai_member_package` VALUES (1, '月度会员', 1, 1, 0, 15.10, 19.00, 1, 3.00, 0, 1, 0, NULL, 0, 0, NULL, 0,NULL,NULL,NULL,1682579714, 1683297565, NULL), (2, '季度会员', 3, 1, 0, 39.90, 57.00, 1, 10.00, 0, 1, 1, NULL, 2, 2, NULL,  0,NULL,NULL,NULL,1682579746, 1683189217, NULL), (3, '年度会员', 12, 1, 0, 139.00, 228.00, 1, 30.00, 0, 1, 0, NULL, 10, 10, NULL, 0,NULL,NULL, NULL,1682579780, 1683189235, NULL);
COMMIT;

-- ----------------------------
-- Table structure for ai_member_package_comment
-- ----------------------------
DROP TABLE IF EXISTS `ai_member_package_comment`;
CREATE TABLE `ai_member_package_comment`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '评价类型：1-虚拟评价；2-用户自评；',
  `member_package_id` int(11) NOT NULL COMMENT '会员套餐ID',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '头像',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '用户昵称',
  `comment_content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '评价内容',
  `comment_level` tinyint(1) NOT NULL COMMENT '评价等级：1-5星',
  `status` tinyint(1) NOT NULL COMMENT '状态：1-显示；0-隐藏；',
  `create_time` int(10) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '会员套餐评价';

-- ----------------------------
-- Records of ai_member_package_comment
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_notice_record
-- ----------------------------
DROP TABLE IF EXISTS `ai_notice_record`;
CREATE TABLE `ai_notice_record`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '用户id',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标题',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '内容',
  `scene_id` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '场景',
  `read` tinyint(1) NULL DEFAULT 0 COMMENT '已读状态;0-未读,1-已读',
  `recipient` tinyint(1) NULL DEFAULT 0 COMMENT '通知接收对象类型;1-会员;2-商家;3-平台;4-游客(未注册用户)',
  `send_type` tinyint(1) NULL DEFAULT 0 COMMENT '通知发送类型 1-系统通知 2-短信通知 3-微信模板 4-微信小程序',
  `notice_type` tinyint(1) NULL DEFAULT NULL COMMENT '通知类型 1-业务通知 2-验证码',
  `extra` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '其他',
  `create_time` int(10) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '通知记录表';

-- ----------------------------
-- Records of ai_notice_record
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_notice_setting
-- ----------------------------
DROP TABLE IF EXISTS `ai_notice_setting`;
CREATE TABLE `ai_notice_setting`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `scene_id` int(10) NOT NULL COMMENT '场景id',
  `scene_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '场景名称',
  `scene_desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '场景描述',
  `recipient` tinyint(1) NOT NULL DEFAULT 1 COMMENT '接收者 1-用户 2-平台',
  `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '通知类型: 1-业务通知 2-验证码',
  `system_notice` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '系统通知设置',
  `sms_notice` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '短信通知设置',
  `oa_notice` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '公众号通知设置',
  `mnp_notice` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '小程序通知设置',
  `email_notice` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '邮箱通知设置',
  `support` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '支持的发送类型 1-系统通知 2-短信通知 3-微信模板消息 4-小程序提醒',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '通知设置表';

-- ----------------------------
-- Records of ai_notice_setting
-- ----------------------------
BEGIN;
INSERT INTO `ai_notice_setting` VALUES (1, 101, '登录验证码', '用户手机号码登录时发送', 1, 2, '{\"type\":\"system\",\"title\":\"\",\"content\":\"\",\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\"]}', '{\"type\":\"sms\",\"template_id\":\"SMS_222458159\",\"content\":\"您正在登录，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"status\":\"1\",\"is_show\":\"1\",\"tips\":[\"可选变量 验证码:code\",\"示例：您正在登录，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"生效条件：1、管理后台完成短信设置。 2、第三方短信平台申请模板。\"]}', '{\"type\":\"oa\",\"template_id\":\"\",\"template_sn\":\"\",\"name\":\"\",\"first\":\"\",\"remark\":\"\",\"tpl\":[],\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\",\"配置路径：小程序后台 > 功能 > 订阅消息\"]}', '{\"type\":\"mnp\",\"template_id\":\"\",\"template_sn\":\"\",\"name\":\"\",\"tpl\":[],\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\",\"配置路径：小程序后台 > 功能 > 订阅消息\"]}', NULL, '2', NULL), (2, 102, '绑定手机验证码', '用户绑定手机号码时发送', 1, 2, '{\"type\":\"system\",\"title\":\"\",\"content\":\"\",\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\"]}', '{\"type\":\"sms\",\"template_id\":\"SMS_207952628\",\"content\":\"您正在绑定手机号，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"status\":\"1\",\"is_show\":\"1\",\"tips\":[\"可选变量 验证码:code\",\"示例：您正在绑定手机号，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"生效条件：1、管理后台完成短信设置。 2、第三方短信平台申请模板。\"]}', '{\"type\":\"oa\",\"template_id\":\"\",\"template_sn\":\"\",\"name\":\"\",\"first\":\"\",\"remark\":\"\",\"tpl\":[],\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\",\"配置路径：小程序后台 > 功能 > 订阅消息\"]}', '{\"type\":\"mnp\",\"template_id\":\"\",\"template_sn\":\"\",\"name\":\"\",\"tpl\":[],\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\",\"配置路径：小程序后台 > 功能 > 订阅消息\"]}', NULL, '2', NULL), (3, 103, '变更手机验证码', '用户变更手机号码时发送', 1, 2, '{\"type\":\"system\",\"title\":\"\",\"content\":\"\",\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\"]}', '{\"type\":\"sms\",\"template_id\":\"SMS_207952628\",\"content\":\"您正在变更手机号，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"status\":\"1\",\"is_show\":\"1\",\"tips\":[\"可选变量 验证码:code\",\"示例：您正在变更手机号，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"生效条件：1、管理后台完成短信设置。 2、第三方短信平台申请模板。\"]}', '{\"type\":\"oa\",\"template_id\":\"\",\"template_sn\":\"\",\"name\":\"\",\"first\":\"\",\"remark\":\"\",\"tpl\":[],\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\",\"配置路径：小程序后台 > 功能 > 订阅消息\"]}', '{\"type\":\"mnp\",\"template_id\":\"\",\"template_sn\":\"\",\"name\":\"\",\"tpl\":[],\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\",\"配置路径：小程序后台 > 功能 > 订阅消息\"]}', NULL, '2', NULL), (4, 104, '找回登录密码验证码', '用户找回登录密码号码时发送', 1, 2, '{\"type\":\"system\",\"title\":\"\",\"content\":\"\",\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\"]}', '{\"type\":\"sms\",\"template_id\":\"SMS_175615069\",\"content\":\"您正在找回登录密码，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"status\":\"1\",\"is_show\":\"1\",\"tips\":[\"可选变量 验证码:code\",\"示例：您正在找回登录密码，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"生效条件：1、管理后台完成短信设置。 2、第三方短信平台申请模板。\"]}', '{\"type\":\"oa\",\"template_id\":\"\",\"template_sn\":\"\",\"name\":\"\",\"first\":\"\",\"remark\":\"\",\"tpl\":[],\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\",\"配置路径：小程序后台 > 功能 > 订阅消息\"]}', '{\"type\":\"mnp\",\"template_id\":\"\",\"template_sn\":\"\",\"name\":\"\",\"tpl\":[],\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\",\"配置路径：小程序后台 > 功能 > 订阅消息\"]}', '{\"type\":\"email\",\"title\":\"注册成为新用户\",\"content\":\"验证码${code}，您正在注册成为新用户，感谢您的支持！\",\"status\":\"1\",\"tips\":[\"可选变量 验证码:code\",\"示例：您正在申请注册，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"生效条件：1、管理后台完成邮箱设置。\"],\"is_show\":\"1\"}', '2,5', NULL), (5, 100, '注册验证码', '用户注册时发送', 1, 2, '{\"type\":\"system\",\"title\":\"\",\"content\":\"\",\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\"]}', '{\"type\":\"sms\",\"template_id\":\"SMS_175615071\",\"content\":\"验证码${code}，您正在注册成为新用户，感谢您的支持！\",\"status\":\"1\",\"is_show\":\"1\",\"tips\":[\"可选变量 验证码:code\",\"示例：您正在申请注册，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"生效条件：1、管理后台完成短信设置。 2、第三方短信平台申请模板。\"]}', '{\"type\":\"oa\",\"template_id\":\"\",\"template_sn\":\"\",\"name\":\"\",\"first\":\"\",\"remark\":\"\",\"tpl\":[],\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\",\"配置路径：小程序后台 > 功能 > 订阅消息\"]}', '{\"type\":\"mnp\",\"template_id\":\"\",\"template_sn\":\"\",\"name\":\"\",\"tpl\":[],\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\",\"配置路径：小程序后台 > 功能 > 订阅消息\"]}', '{\"type\":\"email\",\"title\":\"注册成为新用户\",\"content\":\"验证码${code}，您正在注册成为新用户，感谢您的支持！\",\"status\":\"1\",\"tips\":[\"可选变量 验证码:code\",\"示例：您正在申请注册，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"生效条件：1、管理后台完成邮箱设置。\"],\"is_show\":\"1\"}', '2,5', NULL);
COMMIT;

-- ----------------------------
-- Table structure for ai_official_account_reply
-- ----------------------------
DROP TABLE IF EXISTS `ai_official_account_reply`;
CREATE TABLE `ai_official_account_reply`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '规则名称',
  `keyword` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '关键词',
  `reply_type` tinyint(1) NOT NULL COMMENT '回复类型 1-关注回复 2-关键字回复 3-默认回复',
  `matching_type` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '匹配方式：1-全匹配；2-模糊匹配',
  `content_type` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '内容类型：1-文本；2-图文；3-超链接；',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '回复内容',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '启动状态：1-启动；0-关闭',
  `sort` int(11) UNSIGNED NOT NULL DEFAULT 50 COMMENT '排序',
  `create_time` int(10) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  `content_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '回复图片',
  `content_image_media_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '微信素材media_id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '公众号消息回调表';

-- ----------------------------
-- Records of ai_official_account_reply
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_operation_log
-- ----------------------------
DROP TABLE IF EXISTS `ai_operation_log`;
CREATE TABLE `ai_operation_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL COMMENT '管理员ID',
  `admin_name` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '管理员名称',
  `account` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '管理员账号',
  `action` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '操作名称',
  `type` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '请求方式',
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '访问链接',
  `params` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '请求数据',
  `result` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '请求结果',
  `ip` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'ip地址',
  `create_time` int(10) NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统日志表';

-- ----------------------------
-- Records of ai_operation_log
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_question_category
-- ----------------------------
DROP TABLE IF EXISTS `ai_question_category`;
CREATE TABLE `ai_question_category`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '类目名称',
  `sort` int(11) NULL DEFAULT NULL COMMENT '排序',
  `status` int(11) NOT NULL COMMENT '状态：1-开启，0-关闭',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `image` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '图标',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '问题分类';

-- ----------------------------
-- Records of ai_question_category
-- ----------------------------
BEGIN;
INSERT INTO `ai_question_category` VALUES (1, '美食', 0, 1, 1681788451, '', 1682503969, NULL), (2, '翻译', 0, 1, 1681789183, '', 1682503977, NULL), (3, '工作', 0, 1, 1681789215, '', 1682504034, NULL), (6, '科学', 100, 1, 1681791042, '', 1682504044, NULL), (7, '编程', 0, 1, 1682501808, '', 1682504386, NULL), (8, '生活', 0, 1, 1682504057, '', 1682504057, NULL);
COMMIT;

-- ----------------------------
-- Table structure for ai_question_sample
-- ----------------------------
DROP TABLE IF EXISTS `ai_question_sample`;
CREATE TABLE `ai_question_sample`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL COMMENT '类目id',
  `sort` int(11) NULL DEFAULT NULL COMMENT '排序',
  `content` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '内容',
  `status` tinyint(1) NOT NULL COMMENT '状态：1-是；0-否',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '问题示例';

-- ----------------------------
-- Records of ai_question_sample
-- ----------------------------
BEGIN;
INSERT INTO `ai_question_sample` VALUES (1, 2, 0, '【好好学习，天天向上】用英文怎么说？', 1, 1681802848, 1682504111, NULL), (9, 3, 0, '请帮我写一份30个字关于产品经理的日报', 1, 1682504369, 1683181038, NULL), (10, 1, 0, '怎么做一道鱼香肉丝？', 1, 1682504380, 1683182321, NULL), (11, 6, 0, '宇宙的尽头是什么？', 1, 1683181363, 1683181363, NULL), (12, 7, 0, '写一段Java的代码', 1, 1683184043, 1683184043, NULL), (13, 8, 0, '有哪些好看的电影推荐？', 1, 1683184116, 1683184192, NULL);
COMMIT;

-- ----------------------------
-- Table structure for ai_recharge_order
-- ----------------------------
DROP TABLE IF EXISTS `ai_recharge_order`;
CREATE TABLE `ai_recharge_order`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `sn` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '订单编号',
  `terminal` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '订单来源：1-微信小程序；2-微信公众号；3-手机H5；4-PC；5-苹果app；6-安卓app；',
  `pay_sn` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '支付编号-冗余字段，针对微信同一主体不同客户端支付需用不同订单号预留。',
  `pay_way` tinyint(2) NOT NULL DEFAULT 2 COMMENT '支付方式 2-微信支付 3-支付宝支付',
  `pay_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '支付状态：0-待支付；1-已支付',
  `pay_time` int(10) NULL DEFAULT NULL COMMENT '支付时间',
  `order_amount` decimal(10, 2) UNSIGNED NOT NULL COMMENT '实付金额',
  `number` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '充值对话次数',
  `draw_number` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '充值绘画次数',
  `transaction_id` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '第三方平台交易流水号',
  `recharge_package_id` int(11) NULL DEFAULT NULL COMMENT '充值套餐ID',
  `recharge_package_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '充值套餐信息',
  `refund_status` tinyint(1) NULL DEFAULT 0 COMMENT '退款状态 0-未退款 1-已退款',
  `refund_transaction_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '退款交易流水号',
  `create_time` int(10) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '充值订单';

-- ----------------------------
-- Records of ai_recharge_order
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_recharge_package
-- ----------------------------
DROP TABLE IF EXISTS `ai_recharge_package`;
CREATE TABLE `ai_recharge_package`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '套餐名称',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '套餐封面',
  `describe` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '套餐描述',
  `sell_price` decimal(10, 2) UNSIGNED NOT NULL COMMENT '套餐价格',
  `is_recommend` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否推荐：1-是；0-否；',
  `sort` int(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '套餐状态：1-开启；0-关闭；',
  `number` int(10) UNSIGNED NOT NULL COMMENT '对话次数',
  `draw_number` int(10) UNSIGNED NOT NULL COMMENT '绘画次数',
  `is_give` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否开启赠送：1-开启；0-关闭；',
  `give_number` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '赠送对话次数',
  `give_draw_number` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '赠送绘画次数',
  `tag` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '套餐标签',
  `create_time` int(10) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '充值套餐';

-- ----------------------------
-- Records of ai_recharge_package
-- ----------------------------
BEGIN;
INSERT INTO `ai_recharge_package` VALUES (1, '套餐1', 'uploads/images/20230629/20230630154254c407f9856.png', '获得100对话次数，1次绘画次数', 10.00, 0, 0, 1, 100, 1, 0, 0, 0,NULL, 1683268859, 1683268859, NULL), (2, '套餐2', 'uploads/images/20230629/20230630154254c407f9856.png', '获得150对话次数，20次绘画次数', 100.00, 1, 0, 1, 150, 20, 0, 0, 0,NULL, 1683268859, 1683268859, NULL), (3, '套餐3', 'uploads/images/20230629/20230630154254c407f9856.png', '获得200对话次数，50次绘画次数', 200.00, 0, 0, 1, 200, 50, 0, 0, 0,NULL, 1683268859, 1683268859, NULL);
COMMIT;

-- ----------------------------
-- Table structure for ai_refund_log
-- ----------------------------
DROP TABLE IF EXISTS `ai_refund_log`;
CREATE TABLE `ai_refund_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `sn` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '编号',
  `record_id` int(11) NOT NULL COMMENT '退款记录id',
  `user_id` int(11) NOT NULL DEFAULT 0 COMMENT '关联用户',
  `handle_id` int(11) NOT NULL DEFAULT 0 COMMENT '处理人id（管理员id）',
  `order_amount` decimal(10, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '订单总的应付款金额，冗余字段',
  `refund_amount` decimal(10, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '本次退款金额',
  `refund_status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '退款状态，0退款中，1退款成功，2退款失败',
  `refund_msg` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '退款信息',
  `create_time` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '退款日志表';

-- ----------------------------
-- Records of ai_refund_log
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_refund_record
-- ----------------------------
DROP TABLE IF EXISTS `ai_refund_record`;
CREATE TABLE `ai_refund_record`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `sn` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '退款编号',
  `user_id` int(11) NOT NULL DEFAULT 0 COMMENT '关联用户',
  `order_id` int(11) NOT NULL DEFAULT 0 COMMENT '来源订单id',
  `order_sn` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '来源单号',
  `order_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '订单来源 member-会员订单 recharge-充值订单',
  `order_amount` decimal(10, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '订单总的应付款金额，冗余字段',
  `refund_amount` decimal(10, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '本次退款金额',
  `transaction_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '第三方平台交易流水号',
  `refund_way` tinyint(1) NOT NULL DEFAULT 1 COMMENT '退款方式 1-线上退款 2-线下退款',
  `refund_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '退款类型 1-后台退款',
  `refund_status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '退款状态，0退款中，1退款成功，2退款失败',
  `create_time` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '退款记录表';

-- ----------------------------
-- Records of ai_refund_record
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_sensitive_word
-- ----------------------------
DROP TABLE IF EXISTS `ai_sensitive_word`;
CREATE TABLE `ai_sensitive_word`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `word` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '敏感词',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '状态：1-开启，0-关闭',
  `create_time` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '敏感词库';

-- ----------------------------
-- Records of ai_sensitive_word
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_skill
-- ----------------------------
DROP TABLE IF EXISTS `ai_skill`;
CREATE TABLE `ai_skill`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '名称',
  `image` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '图标',
  `sort` int(11) NULL DEFAULT NULL COMMENT '排序',
  `category_id` int(11) NOT NULL COMMENT '类别id',
  `status` int(11) NOT NULL COMMENT '状态：1-开启，0-关闭',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '主题内容',
  `tips` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '提示内容',
  `describe` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '描述',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  `max_tokens` int(5) UNSIGNED NOT NULL DEFAULT 150 COMMENT '回复最大长度',
  `temperature` decimal(2, 1) UNSIGNED NOT NULL DEFAULT 0.6 COMMENT '词汇属性',
  `context_num` int(5) UNSIGNED NOT NULL DEFAULT 2 COMMENT '上下文总数',
  `top_p` decimal(2, 1) UNSIGNED NOT NULL DEFAULT 0.9 COMMENT '随机属性',
  `presence_penalty` decimal(2, 1) UNSIGNED NOT NULL DEFAULT 0.5 COMMENT '话题属性',
  `frequency_penalty` decimal(2, 1) UNSIGNED NOT NULL DEFAULT 0.5 COMMENT '重复属性',
  `n` int(5) UNSIGNED NOT NULL DEFAULT 1 COMMENT '最大回复',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 48 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '创作模型';

-- ----------------------------
-- Records of ai_skill
-- ----------------------------
BEGIN;
INSERT INTO `ai_skill` VALUES (1, '写作助理', 'uploads/images/20230504/20230504151454a2bbe0181.png', 0, 1, 1, '作为一名中文写作改进助理，你的任务是改进所提供文本的拼写、语法、清晰、简洁和整体可读性，同时分解长句，减少重复，并提供改进建议。请只提供文本的更正版本，避免包括解释。', '请帮我写一篇300字的作文', NULL, 1682586515, 1683527301, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (2, '小红书风格', 'uploads/images/20230504/20230504151454fe3e23462.png', 0, 1, 1, '请使用 Emoji 风格编辑以下段落，该风格以引人入胜的标题、每个段落中包含表情符号和在末尾添加相关标签为特点。请确保保持原文的意思。', '将文本改写成类似小红书的 Emoji 风格。', NULL, 1682586544, 1683527109, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (3, '论文式回答', 'uploads/images/20230504/2023050415145425f277982.png', 0, 1, 1, '写作辅助 以论文形式讨论问题，能够获得连贯 的、结构化的和更高质量的回答。', '写作辅助 以论文形式讨论问题，能够获得连贯 的、结构化的和更高质量的回答。', NULL, 1682586645, 1683191118, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (4, '文章续写', 'uploads/images/20230504/2023050415145490f725284.png', 0, 1, 1, '点评/评鉴 根据文章主题，延续文章开头部分来 完成文章。', '点评/评鉴 根据文章主题，延续文章开头部分来 完成文章。', NULL, 1682586670, 1683191127, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (5, '写作素材搜集', 'uploads/images/20230504/20230504151454e52df9391.png', 0, 1, 1, 'AI提供指定主题的结论和数据，作为素材。', 'AI提供指定主题的结论和数据，作为素材。', NULL, 1682586696, 1683191143, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (6, '小说家', 'uploads/images/20230504/20230504170646890a94841.png', 0, 2, 1, '根据故事类型输出小说，例如奇幻、浪漫或历史等类型。', '根据故事类型输出小说，例如奇幻、浪漫或历史等类型。', NULL, 1682586741, 1683191218, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (7, '诗人', 'uploads/images/20230504/202305041706468bd320898.png', 0, 2, 1, '根据话题或主题输出诗句。', '根据话题或主题输出诗句。', NULL, 1682588016, 1683191228, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (8, '讲故事', 'uploads/images/20230504/202305041706464b1194148.png', 0, 2, 1, '输入一个主题和目标受众，输出与之相关的故事。', '输入一个主题和目标受众，输出与之相关的故事。', NULL, 1682588037, 1683191242, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (9, '编剧', 'uploads/images/20230504/20230504170646c48312494.png', 0, 2, 1, '根据主题创作一个包含故事背景、人物以及对话的剧本。', '根据主题创作一个包含故事背景、人物以及对话的剧本。', NULL, 1682588077, 1683191260, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (10, '脱口秀', 'uploads/images/20230504/20230504170646dab8d3160.png', 0, 2, 1, '输入一个话题，输出基于该话题的幽默脱口秀，并尽量融入日常元素。', '输入一个话题，输出基于该话题的幽默脱口秀，并尽量融入日常元素。', NULL, 1682588094, 1683191251, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (11, '新闻记者', 'uploads/images/20230504/20230504170646b3aaf5584.png', 0, 2, 1, '引用已有数据资料，用新闻的写作风格输出主题文章', '引用已有数据资料，用新闻的写作风格输出主题文章', NULL, 1682588111, 1683191268, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (12, '求职信', 'uploads/images/20230504/2023050417064608d1d2789.png', 0, 2, 1, '根据自我简介编写求职信', '根据自我简介编写求职信', NULL, 1682588133, 1683195765, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (13, '抄袭检查', 'uploads/images/20230504/202305041708493f7cb5173.png', 0, 3, 1, '判断输入的句子在AI数据库中是否存在', '判断输入的句子在AI数据库中是否存在', NULL, 1682588152, 1683191337, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (14, '同义词', 'uploads/images/20230504/202305041708498f03e2690.png', 0, 3, 1, '输入moreofx，即可列出x的多个同义词', '输入moreofx，即可列出x的多个同义词', NULL, 1682588170, 1683191345, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (15, '提取联系信息', 'uploads/images/20230504/2023050417084984b260784.png', 0, 3, 1, '从文本中提取联系信息', '从文本中提取联系信息', NULL, 1682588185, 1683191371, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (16, '电影点评', 'uploads/images/20230504/20230504171059c10fa4307.png', 0, 6, 1, '从情节、表演、摄影、导演、音乐等方面评论电影', '从情节、表演、摄影、导演、音乐等方面评论电影', NULL, 1682588329, 1683191466, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (17, '美食点评', 'uploads/images/20230504/202305041710597c2e20446.png', 0, 6, 1, '根据餐厅情况，撰写一份有关食品和服务的评论', '根据餐厅情况，撰写一份有关食品和服务的评论', NULL, 1682588344, 1683191477, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (18, '淘宝点评', 'uploads/images/20230504/202305041710590b8e76997.png', 0, 6, 1, '根据简单描述，生成一条有关商品的评论', '根据简单描述，生成一条有关商品的评论', NULL, 1682588360, 1683191511, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (19, '科技博主', 'uploads/images/20230504/202305041710598da8b1983.png', 0, 6, 1, '指导如何撰写科技性文章', '指导如何撰写科技性文章', NULL, 1682588388, 1683191497, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (20, '期刊评审', 'uploads/images/20230504/2023050417105921ab95088.png', 0, 6, 1, '对提交的出版物文章进行审查和评论', '对提交的出版物文章进行审查和评论', NULL, 1682588409, 1683191505, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (21, '开发:微信小程序', 'uploads/images/20230504/20230504171235766e86430.png', 0, 5, 1, '辅助微信小程序开发。', '辅助微信小程序开发。', NULL, 1682588431, 1683191572, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (22, '前端:网页设计', 'uploads/images/20230504/20230504171235e40ef1465.png', 0, 5, 1, '从网页开发和设计的角度，提供界面和功能建议，旨在提高用户体验。', '从网页开发和设计的角度，提供界面和功能建议，旨在提高用户体验。', NULL, 1682588450, 1683191583, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (23, '全栈程序员', 'uploads/images/20230504/20230504171235305017496.png', 0, 5, 1, '从前后端全面思考，提供部署策略。', '从前后端全面思考，提供部署策略。', NULL, 1682588465, 1683191615, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (24, '代码释义器', 'uploads/images/20230504/20230504171235434354082.png', 0, 5, 1, '让AI解释每步代码的作用。', '让AI解释每步代码的作用。', NULL, 1682588485, 1683191645, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (25, '前端开发', 'uploads/images/20230504/20230504171235c3c4f9295.png', 0, 5, 1, '我希望你能担任高级前端开发员。我将描述一个项目的细节，你将用这些工具来编码项目。Create React App, yarn, Ant Design, List, Redux Toolkit, createSlice, thunk, axios. 你应该将文件合并到单一的 index.js 文件中，而不是其他。不要写解释。', '提供项目目标和依赖，输出前端项目代码。', NULL, 1682588499, 1683518662, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (26, '架构师IT', 'uploads/images/20230504/2023050417123576b3e7345.png', 0, 5, 1, '从IT架构师的角度，设计系统方案。', '从IT架构师的角度，设计系统方案。', NULL, 1682588515, 1683191670, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (27, '软件测试', 'uploads/images/20230504/2023050417123528e1d3306.png', 0, 5, 1, '输出指定项目的测试清单。', '输出指定项目的测试清单。', NULL, 1682588532, 1683191683, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (28, '提问助手', 'uploads/images/20230504/20230504171002b86f60658.png', 0, 4, 1, '多角度提问，触发深度思考。', '多角度提问，触发深度思考。', NULL, 1682588678, 1683191693, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (29, '主题解构', 'uploads/images/20230504/202305041710029d5d12999.png', 0, 4, 1, '将指定主题拆解为多个子主题。', '将指定主题拆解为多个子主题。', NULL, 1682588694, 1683191709, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (30, '知识点阐述', 'uploads/images/20230504/202305041710024a0b21093.png', 0, 4, 1, '适合用于教育和知识普及。用比喻的方式解释复杂概念，同时加入五感。使人更身临其境，容易记忆。', '适合用于教育和知识普及。用比喻的方式解释复杂概念，同时加入五感。使人更身临其境，容易记忆。', NULL, 1682588709, 1683191722, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (31, '辩手', 'uploads/images/20230504/2023050417101034b327661.png', 0, 4, 1, '从正反两面分析话题', '从正反两面分析话题', NULL, 1682588734, 1683191733, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (32, '四重结构归纳', 'uploads/images/20230504/20230504171002ea1096585.png', 0, 4, 1, '对文章进行多层次总结归纳，也能用来解释词句并联想。', '对文章进行多层次总结归纳，也能用来解释词句并联想。', NULL, 1682588749, 1683191745, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (33, '谬误发现者', 'uploads/images/20230504/20230504171010c75092423.png', 0, 4, 1, '发现语言逻辑上的漏洞，比如为什么名人推荐的洗发水不一定可信。', '发现语言逻辑上的漏洞，比如为什么名人推荐的洗发水不一定可信。', NULL, 1682588764, 1683191776, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (34, '职业顾问', 'uploads/images/20230504/20230504171727d33806172.png', 0, 7, 1, '基于你的技能、兴趣和经验，提供相关岗位建议。', '请输入你的技能、兴趣和经验', NULL, 1683183272, 1683191876, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (35, '生活自助百科', 'uploads/images/20230504/20230504171727b83c45568.png', 0, 7, 1, '为你的生活/工作提供建议和提示，比如如何改善人际关系。', '如：如何改善人际关系', NULL, 1683183378, 1683191883, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (36, '趣味建议', 'uploads/images/20230504/20230504171727442d91762.png', 0, 7, 1, '根据你想要做的事情(比如周年庆祝)，提供有趣而独特的活动和建议。', '比如：周年庆祝', NULL, 1683183509, 1683191891, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (37, 'AI解梦', 'uploads/images/20230504/202305041718433beef0273.png', 0, 8, 1, '对你描述的梦境进行解读。', '请描述您的梦境', NULL, 1683183704, 1683191929, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (38, '角色扮演', 'uploads/images/20230504/20230504171843568310120.png', 0, 8, 1, '与电影、书籍或其他来源中的角色进行对话。', '请输入', NULL, 1683183741, 1683191937, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (39, '艺术顾问', 'uploads/images/20230504/202305041718432caee4801.png', 0, 8, 1, '为你的画画、作曲、照相等提供意见和建议。', '请输入', NULL, 1683183776, 1683191944, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (40, '中英互译', 'uploads/images/20230504/20230504184724aff647458.png', 0, 9, 1, '英汉互译+可定制风格+可学习英语。', '请输入', NULL, 1683183811, 1683197494, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (41, '语言生成器', 'uploads/images/20230504/20230504184724b760d7703.png', 0, 9, 1, '用AI新造的语言来替代你给出的语言。', '用AI新造的语言来替代你给出的语言。', NULL, 1683183830, 1683197449, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (42, '英语对话练习', 'uploads/images/20230504/2023050418472433b489400.png', 0, 9, 1, '英语交谈对话，回复会限制在100字以内。输入中的语法错误、错别字和事实性错误将被纠正。', '请输入', NULL, 1683183856, 1683197502, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (43, '英语发音助手', 'uploads/images/20230504/20230504184724d1bd39026.png', 0, 9, 1, '用你指定语言字母来英语注音，比如汉语拼音。', '用你指定语言字母来英语注音，比如汉语拼音。', NULL, 1683183878, 1683197348, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (44, '摩斯电码翻译', 'uploads/images/20230504/20230504184724faa109766.png', 0, 9, 1, '将摩斯电码翻译为英文', '将摩斯电码翻译为英文', NULL, 1683183896, 1683197341, NULL, 150, 0.6, 2, 0.9, 0.5, 0.5, 1), (45, 'MJ提示联想器', 'uploads/images/20230713/202307131010562b7617670.png', 0, 10, 1, '从现在开始，你是一名中英翻译，你会根据我输入的中文内容，翻译成对应英文。请注意，你翻译后的内容主要服务于一个绘画AI，它只能理解具象的描述而非抽象的概念，同时根据你对绘画AI的理解，比如它可能的训练模型、自然语言处理方式等方面，进行翻译优化。由于我的描述可能会很散乱，不连贯，你需要综合考虑这些问题，然后对翻译后的英文内容再次优化或重组，从而使绘画AI更能清楚我在说什么。请严格按照此条规则进行翻译，也只输出翻译后的英文内容。 例如，我输入：一只想家的小狗。\n你不能输出：\n/imagine prompt:\nA homesick little dog.\n你必须输出：\n/imagine prompt:\nA small dog that misses home, with a sad look on its face and its tail tucked between its legs. It might be standing in front of a closed door or a gate, gazing longingly into the distance, as if hoping to catch a glimpse of its beloved home.\n当我输入中文内容后，请以\"/imagine prompt:\"作为开头，翻译我需要的英文内容。', '请输入绘画要求，如：邻家女孩，楚楚动人，甜美的笑容，户外，精致的五官，黑色的长发', '提供创意灵感和指导方向，激发其创作潜能和想象力', 1689214313, 1689214313, NULL, 150, 0.7, 2, 0.9, 0.5, 0.5, 1), (46, '图像Prompt生成器', 'uploads/images/20230713/20230713101056812a74363.png', 0, 10, 1, '现在你是一个图像prompt生成器，你可以生成描述图像的prompt。prompt的框架是：类型 + 主体 + 环境 + 构图 +拍摄媒介 + 风格 + 参数。 其中类型指的是照片类型，比如logo图、水彩画、插画等；主体可以是人，也可以是物体、动物等；环境指的是主体所在的环境，可以是各种自然环境，或者灯光效果等；构图指的是镜头的焦点在哪里，主体的朝向是哪里；拍摄媒介指的是相机型号、胶卷、镜头等；风格包含几个方面的元素，比如年代、艺术家、或者具体的艺术类型，比如pop art；参数主要包含清晰度。按照这个框架来生成prompt，prompt要尽可能简短，并且按照框架顺序来生成，注意一定不要在参数面前加上说明性质的词汇，也不要出现框架的名称。介词短语一定要替换为形容词加名词的形式，或者替换为主谓宾结构的短语。每个参数写完后用逗号进行分割。prompt一定要用英文来输出。输出的内容字符控制在1024字以内，请你一直记住上面的要求，接下来的对话中，一直按照上面的要求进行回答，不要出现框架的名称。', '请输入你要生成图像的关键字，如：宇宙中外星宇宙飞船在对战，背景伴随星球与爆炸', '快速生成描述图像内容', 1689214360, 1689214360, NULL, 150, 0.7, 2, 0.9, 0.5, 0.5, 1), (47, 'AI万能助手', 'uploads/images/20230713/20230713104250eef2d6697.png', 0, 10, 1, '根据输入的主题内容，回答各种问题并提供有用的信息；撰写各类文章、摘要、新闻报道、作文、演讲稿、工作报告、诗歌和小说等文字作品；', '作为一款高效的万能AI助手，我可以回答各种问题并提供有用的信息；撰写各类文章、摘要、新闻报道、作文、演讲稿、工作报告、诗歌和小说等文字作品； 【请输入您需要的要求。要求越详细，更能达到您的目的。如遇到答案不满意或者字数太短可点击继续】', '高效的万能AI助手', 1689214388, 1689216174, NULL, 150, 0.7, 2, 0.9, 0.5, 0.5, 1);
COMMIT;

-- ----------------------------
-- Table structure for ai_skill_category
-- ----------------------------
DROP TABLE IF EXISTS `ai_skill_category`;
CREATE TABLE `ai_skill_category`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '类目名称',
  `sort` int(11) NULL DEFAULT NULL COMMENT '排序',
  `status` int(11) NOT NULL COMMENT '状态：1-开启，0-关闭',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '技能类别';

-- ----------------------------
-- Records of ai_skill_category
-- ----------------------------
BEGIN;
INSERT INTO `ai_skill_category` VALUES (1, '写作辅助', 0, 1, 1681810965, 1682586561, NULL), (2, '文章/故事', 0, 1, 1681810988, 1682507430, NULL), (3, '文本/词语', 0, 1, 1681811025, 1682507452, NULL), (4, '发散思维', 0, 1, 1682507567, 1682507567, NULL), (5, '编程开发', 0, 1, 1682507600, 1682507600, NULL), (6, '点评/评鉴', 0, 1, 1682507614, 1682507614, NULL), (7, '知识百科', 0, 1, 1682507642, 1682589478, NULL), (8, '趣味知识', 0, 1, 1682507678, 1682507678, NULL), (9, '语言/翻译', 0, 1, 1682507689, 1682507689, NULL), (10, '效率工具', 100, 1, 1689213980, 1689213980, NULL);
COMMIT;

-- ----------------------------
-- Table structure for ai_sms_log
-- ----------------------------
DROP TABLE IF EXISTS `ai_sms_log`;
CREATE TABLE `ai_sms_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `scene_id` int(11) NOT NULL COMMENT '场景id',
  `mobile` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '手机号码',
  `content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '发送内容',
  `code` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '发送关键字（注册、找回密码）',
  `is_verify` tinyint(1) NULL DEFAULT 0 COMMENT '是否已验证；0-否；1-是',
  `check_num` int(5) NULL DEFAULT 0 COMMENT '验证次数',
  `send_status` tinyint(1) NOT NULL COMMENT '发送状态：0-发送中；1-发送成功；2-发送失败',
  `send_time` int(10) NOT NULL COMMENT '发送时间',
  `results` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '短信结果',
  `create_time` int(10) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '短信记录表';

-- ----------------------------
-- Records of ai_sms_log
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_system_menu
-- ----------------------------
DROP TABLE IF EXISTS `ai_system_menu`;
CREATE TABLE `ai_system_menu`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '上级菜单',
  `type` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '权限类型: M=目录，C=菜单，A=按钮',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '菜单名称',
  `icon` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '菜单图标',
  `sort` smallint(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT '菜单排序',
  `perms` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '权限标识',
  `paths` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '路由地址',
  `component` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '前端组件',
  `selected` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '选中路径',
  `params` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '路由参数',
  `is_cache` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否缓存: 0=否, 1=是',
  `is_show` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否显示: 0=否, 1=是',
  `is_disable` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否禁用: 0=否, 1=是',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 376 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统菜单表';

-- ----------------------------
-- Records of ai_system_menu
-- ----------------------------
BEGIN;
INSERT INTO `ai_system_menu` (`id`, `pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`) VALUES (4, 0, 'M', '权限管理', 'el-icon-Lock', 400, '', 'permission', '', '', '', 0, 1, 0, 1656664556, 1664354975), (5, 0, 'C', '工作台', 'el-icon-Monitor', 1000, 'workbench/index', 'workbench', 'workbench/index', '', '', 0, 1, 0, 1656664793, 1697165258), (6, 4, 'C', '菜单管理', '', 1, 'auth.menu/lists', 'menu', 'permission/menu/index', '', '', 1, 1, 0, 1656664960, 1697529021), (7, 4, 'C', '管理员', '', 1, 'auth.admin/lists', 'admin', 'permission/admin/index', '', '', 0, 1, 0, 1656901567, 1682492485), (8, 4, 'C', '角色管理', '', 1, 'auth.role/lists', 'role', 'permission/role/index', '', '', 0, 1, 0, 1656901660, 1697529028), (12, 8, 'A', '新增', '', 1, 'auth.role/add', '', '', '', '', 0, 1, 0, 1657001790, 1663750625), (14, 8, 'A', '编辑', '', 1, 'auth.role/edit', '', '', '', '', 0, 1, 0, 1657001924, 1663750631), (15, 8, 'A', '删除', '', 1, 'auth.role/delete', '', '', '', '', 0, 1, 0, 1657001982, 1663750637), (16, 6, 'A', '新增', '', 1, 'auth.menu/add', '', '', '', '', 0, 1, 0, 1657072523, 1663750565), (17, 6, 'A', '编辑', '', 1, 'auth.menu/edit', '', '', '', '', 0, 1, 0, 1657073955, 1663750570), (18, 6, 'A', '删除', '', 1, 'auth.menu/delete', '', '', '', '', 0, 1, 0, 1657073987, 1663750578), (19, 7, 'A', '新增', '', 1, 'auth.admin/add', '', '', '', '', 0, 1, 0, 1657074035, 1663750596), (20, 7, 'A', '编辑', '', 1, 'auth.admin/edit', '', '', '', '', 0, 1, 0, 1657074071, 1663750603), (21, 7, 'A', '删除', '', 1, 'auth.admin/delete', '', '', '', '', 0, 1, 0, 1657074108, 1663750609), (23, 0, 'M', '开发工具', 'el-icon-EditPen', 100, '', 'dev_tools', '', '', '', 0, 0, 1, 1657097744, 1686714667), (24, 23, 'C', '代码生成器', 'el-icon-DocumentAdd', 1, 'tools.generator/generateTable', 'code', 'dev_tools/code/index', '', '', 0, 1, 0, 1657098110, 1658989423), (25, 4, 'M', '组织管理', '', 0, '', 'organization', '', '', '', 0, 1, 0, 1657099914, 1697528961), (26, 25, 'C', '部门管理', '', 1, 'dept.dept/lists', 'department', 'organization/department/index', '', '', 1, 1, 0, 1657099989, 1697528984), (27, 25, 'C', '岗位管理', '', 1, 'dept.jobs/lists', 'post', 'organization/post/index', '', '', 0, 1, 0, 1657100044, 1697528990), (28, 0, 'M', '系统设置', 'el-icon-Setting', 200, '', 'setting', '', '', '', 0, 1, 0, 1657100164, 1664355035), (29, 28, 'M', '网站设置', '', 100, '', 'website', '', '', '', 0, 1, 0, 1657100230, 1682492512), (30, 29, 'C', '网站信息', '', 1, 'setting.web.web_setting/getWebsite', 'information', 'setting/website/information', '', '', 0, 1, 0, 1657100306, 1657164412), (31, 29, 'C', '网站备案', '', 1, 'setting.web.web_setting/getCopyright', 'filing', 'setting/website/filing', '', '', 0, 1, 0, 1657100434, 1657164723), (32, 29, 'C', '政策协议', '', 1, 'setting.web.web_setting/getAgreement', 'protocol', 'setting/website/protocol', '', '', 0, 1, 0, 1657100571, 1657164770), (33, 28, 'C', '存储设置', '', 1, 'setting.storage/lists', 'storage', 'setting/storage/index', '', '', 0, 1, 0, 1657160959, 1682492584), (34, 23, 'C', '字典管理', 'el-icon-Box', 1, 'setting.dict.dict_type/lists', 'dict', 'setting/dict/type/index', '', '', 0, 1, 0, 1657161211, 1663225935), (35, 28, 'M', '系统维护', '', 1, '', 'system', '', '', '', 0, 1, 0, 1657161569, 1682492592), (36, 35, 'C', '系统日志', '', 1, 'setting.system.log/lists', 'journal', 'setting/system/journal', '', '', 0, 1, 0, 1657161696, 1657165722), (37, 35, 'C', '系统缓存', '', 1, '', 'cache', 'setting/system/cache', '', '', 0, 1, 0, 1657161896, 1657173767), (38, 35, 'C', '系统环境', '', 1, 'setting.system.system/info', 'environment', 'setting/system/environment', '', '', 0, 1, 0, 1657162000, 1657173794), (39, 24, 'A', '导入数据表', '', 1, 'tools.generator/selectTable', '', '', '', '', 0, 1, 0, 1657162736, 1657162736), (40, 24, 'A', '代码生成', '', 1, 'tools.generator/generate', '', '', '', '', 0, 1, 0, 1657162806, 1657162806), (41, 23, 'C', '编辑数据表', '', 1, 'tools.generator/edit', 'code/edit', 'dev_tools/code/edit', '/dev_tools/code', '', 1, 0, 0, 1657162866, 1663748668), (42, 24, 'A', '同步表结构', '', 1, 'tools.generator/syncColumn', '', '', '', '', 0, 1, 0, 1657162934, 1657162934), (43, 24, 'A', '删除数据表', '', 1, 'tools.generator/delete', '', '', '', '', 0, 1, 0, 1657163015, 1657163015), (44, 24, 'A', '预览代码', '', 1, 'tools.generator/preview', '', '', '', '', 0, 1, 0, 1657163263, 1657163263), (45, 26, 'A', '新增', '', 1, 'dept.dept/add', '', '', '', '', 0, 1, 0, 1657163548, 1663750492), (46, 26, 'A', '编辑', '', 1, 'dept.dept/edit', '', '', '', '', 0, 1, 0, 1657163599, 1663750498), (47, 26, 'A', '删除', '', 1, 'dept.dept/delete', '', '', '', '', 0, 1, 0, 1657163687, 1663750504), (48, 27, 'A', '新增', '', 1, 'dept.jobs/add', '', '', '', '', 0, 1, 0, 1657163778, 1663750524), (49, 27, 'A', '编辑', '', 1, 'dept.jobs/edit', '', '', '', '', 0, 1, 0, 1657163800, 1663750530), (50, 27, 'A', '删除', '', 1, 'dept.jobs/delete', '', '', '', '', 0, 1, 0, 1657163820, 1663750535), (51, 30, 'A', '保存', '', 1, 'setting.web.web_setting/setWebsite', '', '', '', '', 0, 1, 0, 1657164469, 1663750649), (52, 31, 'A', '保存', '', 1, 'setting.web.web_setting/setCopyright', '', '', '', '', 0, 1, 0, 1657164692, 1663750657), (53, 32, 'A', '保存', '', 1, 'setting.web.web_setting/setAgreement', '', '', '', '', 0, 1, 0, 1657164824, 1663750665), (54, 33, 'A', '设置', '', 1, 'setting.storage/setup', '', '', '', '', 0, 1, 0, 1657165303, 1663750673), (55, 34, 'A', '新增', '', 1, 'setting.dict.dict_type/add', '', '', '', '', 0, 1, 0, 1657166966, 1663750783), (56, 34, 'A', '编辑', '', 1, 'setting.dict.dict_type/edit', '', '', '', '', 0, 1, 0, 1657166997, 1663750789), (57, 34, 'A', '删除', '', 1, 'setting.dict.dict_type/delete', '', '', '', '', 0, 1, 0, 1657167038, 1663750796), (58, 62, 'A', '新增', '', 1, 'setting.dict.dict_data/add', '', '', '', '', 0, 1, 0, 1657167317, 1663750758), (59, 62, 'A', '编辑', '', 1, 'setting.dict.dict_data/edit', '', '', '', '', 0, 1, 0, 1657167371, 1663750751), (60, 62, 'A', '删除', '', 1, 'setting.dict.dict_data/delete', '', '', '', '', 0, 1, 0, 1657167397, 1663750768), (61, 37, 'A', '清除系统缓存', '', 1, 'setting.system.cache/clear', '', '', '', '', 0, 1, 0, 1657173837, 1657173939), (62, 23, 'C', '字典数据管理', '', 1, 'setting.dict.dict_data/lists', 'dict/data', 'setting/dict/data/index', '/dev_tools/dict', '', 1, 0, 0, 1657174351, 1663745617), (64, 96, 'C', '素材中心', '', 60, '', 'index', 'material/index', '', '', 0, 1, 0, 1657507296, 1682492067), (66, 26, 'A', '详情', '', 0, 'dept.dept/detail', '', '', '', '', 0, 1, 0, 1663725459, 1663750516), (67, 27, 'A', '详情', '', 0, 'dept.jobs/detail', '', '', '', '', 0, 1, 0, 1663725514, 1663750559), (68, 6, 'A', '详情', '', 0, 'auth.menu/detail', '', '', '', '', 0, 1, 0, 1663725564, 1663750584), (69, 7, 'A', '详情', '', 0, 'auth.admin/detail', '', '', '', '', 0, 1, 0, 1663725623, 1663750615), (70, 224, 'M', '文章资讯', '', 45, '', 'article', '', '', '', 0, 1, 0, 1663749965, 1687414509), (71, 70, 'C', '文章管理', '', 0, 'article.article/lists', 'lists', 'app/article/lists/index', '', '', 0, 1, 0, 1663750101, 1687688036), (72, 70, 'C', '文章添加/编辑', '', 0, 'article.article/add:edit', 'lists/edit', 'app/article/lists/edit', '/yingyong/article/lists', '', 0, 0, 0, 1663750153, 1687688249), (73, 70, 'C', '文章栏目', '', 0, 'article.articleCate/lists', 'column', 'app/article/column/index', '', '', 1, 1, 0, 1663750287, 1687688042), (74, 71, 'A', '新增', '', 0, 'article.article/add', '', '', '', '', 0, 1, 0, 1663750335, 1663750335), (75, 71, 'A', '详情', '', 0, 'article.article/detail', '', '', '', '', 0, 1, 0, 1663750354, 1663750383), (76, 71, 'A', '删除', '', 0, 'article.article/delete', '', '', '', '', 0, 1, 0, 1663750413, 1663750413), (77, 71, 'A', '修改状态', '', 0, 'article.article/updateStatus', '', '', '', '', 0, 1, 0, 1663750442, 1663750442), (78, 73, 'A', '添加', '', 0, 'article.articleCate/add', '', '', '', '', 0, 1, 0, 1663750483, 1663750483), (79, 73, 'A', '删除', '', 0, 'article.articleCate/delete', '', '', '', '', 0, 1, 0, 1663750895, 1663750895), (80, 73, 'A', '详情', '', 0, 'article.articleCate/detail', '', '', '', '', 0, 1, 0, 1663750913, 1663750913), (81, 73, 'A', '修改状态', '', 0, 'article.articleCate/updateStatus', '', '', '', '', 0, 1, 0, 1663750936, 1663750936), (82, 0, 'M', '渠道设置', 'el-icon-Message', 600, '', 'channel', '', '', '', 0, 1, 0, 1663754084, 1664354919), (83, 82, 'C', 'h5设置', '', 0, 'channel.web_page_setting/getConfig', 'h5', 'channel/h5', '', '', 0, 1, 0, 1663754158, 1682492429), (84, 83, 'A', '保存', '', 0, 'channel.web_page_setting/setConfig', '', '', '', '', 0, 1, 0, 1663754259, 1663754259), (85, 82, 'M', '微信公众号', '', 500, '', 'wx_oa', '', '', '', 0, 1, 0, 1663755470, 1701946222), (86, 85, 'C', '公众号配置', '', 0, 'channel.official_account_setting/getConfig', 'config', 'channel/wx_oa/config', '', '', 0, 1, 0, 1663755663, 1664355450), (87, 85, 'C', '菜单管理', '', 0, 'channel.official_account_menu/detail', 'menu', 'channel/wx_oa/menu', '', '', 0, 1, 0, 1663755767, 1664355456), (88, 87, 'A', '保存', '', 0, 'channel.official_account_menu/save', '', '', '', '', 0, 1, 0, 1663755799, 1694055878), (89, 87, 'A', '保存并发布', '', 0, 'channel.official_account_menu/saveAndPublish', '', '', '', '', 0, 1, 0, 1663756490, 1694055889), (90, 85, 'C', '关注回复', '', 0, 'channel.official_account_reply/lists', 'follow', 'channel/wx_oa/reply/follow_reply', '', '', 0, 1, 0, 1663818358, 1663818366), (91, 85, 'C', '关键字回复', '', 0, '', 'keyword', 'channel/wx_oa/reply/keyword_reply', '', '', 0, 1, 0, 1663818445, 1663818445), (93, 85, 'C', '默认回复', '', 0, '', 'default', 'channel/wx_oa/reply/default_reply', '', '', 0, 1, 0, 1663818580, 1663818580), (94, 385, 'C', '小程序配置', '', 0, 'channel.mnp_settings/getConfig', 'config', 'channel/weapp', '', '', 0, 1, 0, 1663831396, 1701914243), (95, 94, 'A', '保存', '', 0, 'channel.mnp_settings/setConfig', '', '', '', '', 0, 1, 0, 1663831436, 1663831436), (96, 0, 'M', '装修管理', 'el-icon-Brush', 700, '', 'decoration', '', '', '', 0, 1, 0, 1663834825, 1664354863), (97, 96, 'C', '页面装修', '', 100, 'decorate.page/detail', 'pages', 'decoration/pages/index', '', '', 0, 1, 0, 1663834879, 1694604672), (98, 97, 'A', '保存', '', 0, 'decorate.page/save', '', '', '', '', 0, 1, 0, 1663834956, 1663834956), (99, 96, 'C', '导航菜单', '', 90, 'decorate.tabbar/detail', 'tabbar', 'decoration/tabbar', '', '', 0, 1, 0, 1663835004, 1688523827), (100, 99, 'A', '保存', '', 0, 'decorate.tabbar/save', '', '', '', '', 0, 1, 0, 1663835018, 1663835018), (101, 224, 'M', '消息管理', '', 40, '', 'message', '', '', '', 0, 1, 0, 1663838602, 1686909589), (102, 101, 'C', '通知设置', '', 1000, 'notice.notice/settingLists', 'notice', 'message/notice/index', '', '', 0, 1, 0, 1663839195, 1695115928), (103, 102, 'A', '详情', '', 0, 'notice.notice/detail', '', '', '', '', 0, 1, 0, 1663839537, 1663839537), (104, 101, 'C', '通知设置编辑', '', 900, 'notice.notice/set', 'notice/edit', 'message/notice/edit', '/message/notice', '', 0, 0, 0, 1663839873, 1695115936), (105, 71, 'A', '编辑', '', 0, 'article.article/edit', '', '', '', '', 0, 1, 0, 1663840043, 1663840053), (106, 71, 'A', '详情', '', 0, 'article.article/detail', '', '', '', '', 0, 1, 0, 1663840284, 1663840494), (107, 101, 'C', '短信设置', '', 800, 'notice.sms_config/getConfig', 'short_letter', 'message/short_letter/index', '', '', 0, 1, 0, 1663898591, 1695115943), (108, 107, 'A', '设置', '', 0, 'notice.sms_config/setConfig', '', '', '', '', 0, 1, 0, 1663898644, 1663898644), (109, 107, 'A', '详情', '', 0, 'notice.sms_config/detail', '', '', '', '', 0, 1, 0, 1663898661, 1663898661), (110, 28, 'C', '热门搜索', '', 50, 'setting.hot_search/getConfig', 'search', 'setting/search/index', '', '', 0, 0, 1, 1663901821, 1682492896), (111, 110, 'A', '保存', '', 0, 'setting.hot_search/setConfig', '', '', '', '', 0, 1, 0, 1663901856, 1663901856), (112, 28, 'M', '用户设置', '', 74, '', 'user', '', '', '', 0, 0, 1, 1663903302, 1688632034), (113, 28, 'C', '用户设置', '', 70, 'setting.user.user/getConfig', 'setup', 'setting/user/setup', '', '', 0, 1, 0, 1663903506, 1694139313), (114, 113, 'A', '保存', '', 0, 'setting.user.user/setConfig', '', '', '', '', 0, 1, 0, 1663903522, 1663903522), (115, 28, 'C', '登录注册', '', 100, 'setting.user.user/getRegisterConfig', 'login_register', 'setting/user/login_register', '', '', 0, 1, 0, 1663903832, 1695115952), (116, 115, 'A', '保存', '', 0, 'setting.user.user/setRegisterConfig', '', '', '', '', 0, 1, 0, 1663903852, 1663903852), (117, 0, 'M', '用户管理', 'el-icon-User', 800, '', 'consumer', '', '', '', 0, 1, 0, 1663904351, 1664354732), (118, 117, 'C', '用户列表', '', 0, 'user.user/lists', 'lists', 'consumer/lists/index', '', '', 0, 1, 0, 1663904392, 1682491998), (119, 117, 'C', '用户详情', '', 0, 'user.user/detail', 'lists/detail', 'consumer/lists/detail', '/consumer/lists', '', 0, 0, 0, 1663904470, 1663928109), (120, 119, 'A', '编辑', '', 0, 'user.user/edit', '', '', '', '', 0, 1, 0, 1663904499, 1663904499), (140, 82, 'C', 'APP设置', '', 0, 'channel.open_setting/getConfig', 'open_setting', 'channel/open_setting', '', '', 0, 1, 0, 1666085713, 1697783995), (141, 140, 'A', '保存', '', 0, 'channel.open_setting/setConfig', '', '', '', '', 0, 1, 0, 1666085751, 1666085776), (143, 35, 'C', '定时任务', '', 0, 'crontab.crontab/lists', 'scheduled_task', 'setting/system/scheduled_task/index', '', '', 0, 1, 0, 1669357509, 1669357711), (144, 35, 'C', '定时任务添加/编辑', '', 0, 'crontab.crontab/add:edit', 'scheduled_task/edit', 'setting/system/scheduled_task/edit', '/setting/system/scheduled_task', '', 0, 0, 0, 1669357670, 1669357765), (145, 143, 'A', '添加', '', 0, 'crontab.crontab/add', '', '', '', '', 0, 1, 0, 1669358282, 1669358282), (146, 143, 'A', '编辑', '', 0, 'crontab.crontab/edit', '', '', '', '', 0, 1, 0, 1669358303, 1669358303), (147, 143, 'A', '删除', '', 0, 'crontab.crontab/delete', '', '', '', '', 0, 1, 0, 1669358334, 1669358334), (148, 0, 'M', '模板示例', 'el-icon-SetUp', 0, '', 'template', '', '', '', 0, 0, 1, 1670206819, 1682491605), (149, 148, 'M', '组件示例', 'el-icon-Coin', 0, '', 'component', '', '', '', 0, 1, 0, 1670207182, 1670207244), (150, 149, 'C', '富文本', '', 0, '', 'rich_text', 'template/component/rich_text', '', '', 0, 1, 0, 1670207751, 1670207751), (151, 149, 'C', '上传文件', '', 0, '', 'upload', 'template/component/upload', '', '', 0, 1, 0, 1670208925, 1670208925), (152, 149, 'C', '图标', '', 0, '', 'icon', 'template/component/icon', '', '', 0, 1, 0, 1670230069, 1670230069), (153, 149, 'C', '文件选择器', '', 0, '', 'file', 'template/component/file', '', '', 0, 1, 0, 1670232129, 1670232129), (154, 149, 'C', '链接选择器', '', 0, '', 'link', 'template/component/link', '', '', 0, 1, 0, 1670292636, 1670292636), (155, 149, 'C', '超出自动打点', '', 0, '', 'overflow', 'template/component/overflow', '', '', 0, 1, 0, 1670292883, 1670292883), (156, 149, 'C', '悬浮input', '', 0, '', 'popover_input', 'template/component/popover_input', '', '', 0, 1, 0, 1670293336, 1670293336), (157, 119, 'A', '余额调整', '', 0, 'user.user/adjustMoney', '', '', '', '', 0, 1, 0, 1677143088, 1677143088), (159, 224, 'C', '用户充值', 'local-icon-fukuan', 60, 'recharge.recharge/getConfig', 'recharge', 'app/recharge/index', '', '', 0, 0, 1, 1677144284, 1685329917), (160, 159, 'A', '保存', '', 0, 'recharge.recharge/setConfig', '', '', '', '', 0, 1, 0, 1677145012, 1677145012), (161, 28, 'M', '支付设置', '', 90, '', 'pay', '', '', '', 0, 1, 0, 1677148075, 1694139292), (162, 161, 'C', '支付方式', '', 0, 'setting.pay.pay_way/getPayWay', 'method', 'setting/pay/method/index', '', '', 0, 1, 0, 1677148207, 1677148207), (163, 161, 'C', '支付配置', '', 0, 'setting.pay.pay_config/lists', 'config', 'setting/pay/config/index', '', '', 0, 1, 0, 1677148260, 1677148374), (164, 162, 'A', '设置支付方式', '', 0, 'setting.pay.pay_way/setPayWay', '', '', '', '', 0, 1, 0, 1677219624, 1677219624), (165, 163, 'A', '配置', '', 0, 'setting.pay.pay_config/setConfig', '', '', '', '', 0, 1, 0, 1677219655, 1677219655), (166, 0, 'M', '财务管理', 'local-icon-user_gaikuang', 650, '', 'finance', '', '', '', 0, 1, 0, 1677552269, 1677842158), (168, 166, 'C', '余额明细', '', 90, 'finance.account_log/lists', 'balance_details', 'finance/balance_details', '', '', 0, 1, 0, 1677552976, 1697014529), (170, 166, 'C', '退款记录', '', 0, 'finance.refund/record', 'refund_record', 'finance/refund_record', '', '', 0, 1, 0, 1677811271, 1685332920), (171, 170, 'A', '重新退款', '', 0, 'finance.refund/refundAgain', '', '', '', '', 0, 1, 0, 1677811295, 1682581650), (172, 170, 'A', '退款日志', '', 0, 'finance.refund/log', '', '', '', '', 0, 1, 0, 1677811361, 1677811361), (173, 0, 'M', '营销中心', 'el-icon-Briefcase', 690, '', 'marketing', '', '', '', 0, 1, 0, 1681293663, 1685329871), (174, 173, 'M', '分享奖励', '', 100, '', 'share', '', '', '', 0, 1, 0, 1681293726, 1682492111), (175, 174, 'C', '分享设置', '', 0, 'task.task_share/getConfig', 'sharesetting', 'marketing/share/setting', '', '', 0, 1, 0, 1681293866, 1686725641), (176, 174, 'C', '分享记录', '', 100, 'task.task_share/lists', 'sharerecord', 'marketing/share/record', '', '', 0, 1, 0, 1681293969, 1696936193), (177, 173, 'M', '邀请奖励', '', 80, '', 'invite', '', '', '', 0, 1, 0, 1681295043, 1682492207), (178, 177, 'C', '邀请奖励', '', 0, 'task.task_invite/getConfig', 'invitesetting', 'marketing/invite/setting', '', '', 0, 1, 0, 1681295079, 1686725759), (179, 177, 'C', '邀请记录', '', 100, 'task.task_invite/lists', 'inviterecord', 'marketing/invite/record', '', '', 0, 1, 0, 1681295110, 1696936201), (180, 173, 'C', '充值套餐', '', 70, 'recharge.recharge_package/lists', 'recharge', 'marketing/recharge/index', '', '', 0, 1, 0, 1681295905, 1687941071), (181, 0, 'M', 'AI问答', 'el-icon-ChatLineSquare', 950, '', 'ai_qa', '', '', '', 0, 1, 0, 1681296371, 1682491695), (182, 181, 'C', '对话记录', '', 0, 'chat_records/lists', 'dialogue_record', 'ai_qa/dialogue_record/index', '', '', 0, 1, 0, 1681296405, 1686715257), (183, 173, 'M', '会员套餐', '', 60, '', 'vipcombo', '', '', '', 0, 1, 0, 1681296406, 1682492298), (184, 183, 'C', '会员套餐', '', 0, 'member.member_package/lists', 'combo', 'marketing/vip_combo/vip_combo', '', '', 0, 1, 0, 1681296429, 1686725928), (185, 181, 'C', '问题示例', '', 0, 'question.questionSample/lists', 'problem_example', 'ai_qa/problem_example/index', '', '', 1, 1, 0, 1681296441, 1688984931), (186, 181, 'C', '问题分类', '', 0, 'question.questionCategory/lists', 'problem_category', 'ai_qa/problem_category/index', '', '', 0, 1, 0, 1681296474, 1686723177), (187, 0, 'M', '订单管理', 'el-icon-Coin', 920, '', 'order', '', '', '', 0, 1, 0, 1681354941, 1682491723), (188, 187, 'C', '充值订单', '', 0, 'recharge.recharge_order/lists', 'rechargeorder', 'order/recharge_order/lists', '', '', 0, 1, 0, 1681355001, 1686724726), (189, 0, 'M', 'AI创作', 'el-icon-EditPen', 940, '', 'creative_center', '', '', '', 0, 1, 0, 1681355062, 1687354636), (190, 189, 'C', '创作记录', '', 100, '', 'creation_record', 'creative_center/creation_record/index', '', '', 0, 1, 0, 1681355104, 1688020160), (191, 189, 'C', '创作分类', '', 0, 'creation.creationCategory/lists', 'creative_category', 'creative_center/creative_category/index', '', '', 0, 1, 0, 1681355328, 1686723885), (192, 187, 'C', '会员订单', '', 100, 'member.member_order/lists', 'viporder', 'order/vip_order/lists', '', '', 0, 1, 0, 1681366476, 1688020227), (193, 82, 'C', 'pc设置', '', 0, '', 'pc', 'channel/pc', '', '', 0, 1, 0, 1681367803, 1682492467), (194, 0, 'M', 'AI技能', 'el-icon-Aim', 930, '', 'skill', '', '', '', 0, 1, 0, 1681373803, 1688009657), (195, 194, 'C', '技能对话', '', 0, '', 'chat', 'skill/chat/index', '', '', 0, 1, 0, 1681373842, 1681373842), (196, 194, 'C', '技能管理', '', 0, 'skill.skill/lists', 'administration', 'skill/admin/index', '', '', 0, 1, 0, 1681374799, 1686724231), (197, 194, 'C', '技能类别', '', 0, 'skill.skillCategory/lists', 'type', 'skill/type/index', '', '', 0, 1, 0, 1681376023, 1686724404), (198, 183, 'C', '购买评价', '', 0, 'member.member_package_comment/lists', 'evaluate', 'marketing/vip_combo/evaluate/lists', '', '', 0, 1, 0, 1681437201, 1686726358), (199, 224, 'M', '分销推广', '', 100, '', 'distribution', '', '', '', 0, 1, 0, 1681438595, 1688020114), (200, 199, 'C', '分销商', '', 100, 'distribution.distributor/lists', 'distributor', 'marketing/distribution/distributor/lists', '', '', 0, 1, 0, 1681438617, 1695868264), (201, 199, 'C', '分销商详情', '', 0, 'distribution.distributor/detail', 'distributor/detial', 'marketing/distribution/distributor/detial', '/yingyong/distribution/distributor', '', 0, 0, 0, 1681440628, 1686727635), (202, 28, 'C', '分享设置', '', 70, 'setting.shareSetting/getConfig', 'share', 'setting/share/index', '', '', 0, 1, 0, 1681608049, 1686729660), (203, 335, 'C', 'AI模型', '', 80, 'setting.chatSetting/getChatConfig', 'AI', 'setting/AI/index', '', '', 0, 1, 0, 1681608636, 1703063349), (204, 166, 'C', '财务中心', '', 100, 'finance.finance/center', 'center', 'finance/center', '', '', 0, 1, 0, 1681613453, 1686729093), (205, 96, 'C', '系统风格', '', 70, '', 'style', 'decoration/style/style', '', '', 0, 1, 0, 1681635044, 1682492058), (206, 189, 'C', '创作模型', '', 80, 'creation.creationModel/lists', 'creative_model', 'creative_center/creative_model/index', '', '', 0, 1, 0, 1681636290, 1691055615), (207, 224, 'M', '意见反馈', '', 10, '', 'feedback', '', '', '', 0, 1, 0, 1682237448, 1688020104), (208, 207, 'C', '反馈列表', '', 0, 'feedback/lists', 'list', 'feedback/list', '', '', 0, 1, 0, 1682237511, 1686729044), (209, 35, 'C', '系统更新', '', 0, 'settings.system.upgrade/lists', 'update', 'setting/system/update/index', '', '', 0, 1, 0, 1683623304, 1683624316), (210, 209, 'A', '下载更新包', '', 0, 'setting.system.upgrade/downloadPkg', '', '', '', '', 0, 1, 0, 1683628408, 1683628408), (211, 209, 'A', '一键更新', '', 0, 'setting.system.upgrade/upgrade', '', '', '', '', 0, 1, 0, 1683628426, 1683628426), (212, 199, 'C', '下级分销商', '', 0, 'distribution.distributor/belowLists', 'distributor/lowdistributor', 'marketing/distribution/distributor/lowdestributor', '/yingyong/distribution/distributor', '', 0, 0, 0, 1684477612, 1686726642), (213, 199, 'C', '分销订单', '', 0, 'distribution.distribution_order/lists', 'order', 'marketing/distribution/order/lists', '', '', 0, 1, 0, 1684479421, 1687414787), (214, 199, 'C', '分销申请', '', 0, 'distribution.distributionApply/lists', 'apply', 'marketing/distribution/apply/lists', '', '', 0, 1, 0, 1684479933, 1686727648), (217, 199, 'C', '提现记录', '', 0, 'distribution.withdraw/lists', 'distribution/withdraw', 'marketing/distribution/withdraw/lists', '', '', 0, 1, 0, 1684485368, 1686728093), (218, 199, 'C', '分销设置', '', 0, 'distribution.config/getConfig', 'setting/distribution', 'marketing/distribution/setting/distribution', '', '', 0, 1, 0, 1684486717, 1686728913), (219, 199, 'C', '提现设置', '', 0, 'distribution.withdraw/getConfig', 'setting/withdraw', 'marketing/distribution/setting/withdraw', '', '', 0, 1, 0, 1684487237, 1686728966), (220, 335, 'C', '对话设置', '', 20, 'setting.ai_setting/getChatConfig', 'dialogue', 'setting/dialogue/index', '', '', 0, 1, 0, 1684809821, 1694772607), (221, 173, 'C', '注册奖励', '', 200, 'setting.user.user/getRegisterReward', 'register', 'marketing/register/index', '', '', 0, 1, 0, 1684810280, 1686725551), (222, 166, 'C', '佣金明细', '', 80, '', 'commission', 'finance/commission', '', '', 0, 1, 0, 1685009695, 1685329948), (223, 335, 'C', '敏感词库', '', 0, 'setting.SensitiveWord/lists', 'sensitive', 'setting/sensitive/index', '', '', 0, 1, 0, 1685089998, 1694772583), (224, 0, 'M', '应用中心', 'el-icon-Aim', 685, '', 'yingyong', '', '', '', 0, 1, 0, 1685329838, 1685329858), (225, 335, 'M', 'AI模型设置', '', 79, 'setting.aiSetting/getconfig', 'AI/setting', 'setting/AI/setting', '/setting/AI', '', 0, 0, 0, 1686713628, 1689680793), (226, 182, 'A', '删除', '', 0, 'chat_records/del', '', '', '', '', 0, 1, 0, 1686715338, 1686715338), (227, 182, 'A', '查看回复', '', 0, 'chat_records/see', '', '', '', '', 0, 1, 0, 1686715418, 1686715418), (228, 185, 'A', '新增', '', 0, 'question.questionSample/add', '', '', '', '', 0, 1, 0, 1686715860, 1686715860), (229, 185, 'A', '编辑', '', 0, 'question.questionSample/edit', '', '', '', '', 0, 1, 0, 1686715881, 1686715881), (230, 185, 'A', '删除', '', 0, 'question.questionSample/del', '', '', '', '', 0, 1, 0, 1686715904, 1686715912), (231, 185, 'A', '更新状态', '', 0, 'question.questionSample/status', '', '', '', '', 0, 1, 0, 1686715928, 1686715928), (232, 186, 'A', '新增', '', 0, 'question.questionCategory/add', '', '', '', '', 0, 1, 0, 1686723234, 1686723234), (233, 186, 'A', '编辑', '', 0, 'question.questionCategory/edit', '', '', '', '', 0, 1, 0, 1686723428, 1686723428), (234, 186, 'A', '删除', '', 0, 'question.questionCategory/del', '', '', '', '', 0, 1, 0, 1686723442, 1686723442), (235, 186, 'A', '更新状态', '', 0, 'question.questionCategory/status', '', '', '', '', 0, 1, 0, 1686723717, 1686723717), (236, 191, 'A', '新增', '', 0, 'creation.creationCategory/add', '', '', '', '', 0, 1, 0, 1686723931, 1686723931), (237, 191, 'A', '编辑', '', 0, 'creation.creationCategory/edit', '', '', '', '', 0, 1, 0, 1686723946, 1686723946), (238, 191, 'A', '删除', '', 0, 'creation.creationCategory/del', '', '', '', '', 0, 1, 0, 1686723961, 1686723961), (239, 191, 'A', '更新状态', '', 0, 'creation.creationCategory/status', '', '', '', '', 0, 1, 0, 1686724050, 1686724050), (240, 206, 'A', '新增', '', 0, 'creation.creationModel/add', '', '', '', '', 0, 1, 0, 1686724093, 1686724093), (241, 206, 'A', '编辑', '', 0, 'creation.creationModel/edit', '', '', '', '', 0, 1, 0, 1686724107, 1686724107), (242, 206, 'A', '删除', '', 0, 'creation.creationModel/del', '', '', '', '', 0, 1, 0, 1686724119, 1686724119), (243, 206, 'A', '更新状态', '', 0, 'creation.creationModel/status', '', '', '', '', 0, 1, 0, 1686724141, 1686724141), (244, 196, 'A', '新增', '', 0, 'skill.skill/add', '', '', '', '', 0, 1, 0, 1686724254, 1686724254), (245, 196, 'A', '编辑', '', 0, 'skill.skill/edit', '', '', '', '', 0, 1, 0, 1686724268, 1686724268), (246, 196, 'A', '删除', '', 0, 'skill.skill/del', '', '', '', '', 0, 1, 0, 1686724284, 1686724284), (247, 196, 'A', '更新状态', '', 0, 'skill.skill/status', '', '', '', '', 0, 1, 0, 1686724302, 1686724302), (248, 197, 'A', '新增', '', 0, 'skill.skillCategory/add', '', '', '', '', 0, 1, 0, 1686724420, 1686724420), (249, 197, 'A', '编辑', '', 0, 'skill.skillCategory/edit', '', '', '', '', 0, 1, 0, 1686724444, 1686724444), (250, 197, 'A', '删除', '', 0, 'skill.skillCategory/del', '', '', '', '', 0, 1, 0, 1686724460, 1686724460), (251, 197, 'A', '更新状态', '', 0, '', '', '', '', '', 0, 1, 0, 1686724474, 1686724474), (252, 188, 'A', '详情', '', 0, 'recharge.recharge_order/detail', '', '', '', '', 0, 1, 0, 1686724758, 1686724758), (253, 188, 'A', '退款', '', 0, 'recharge.recharge_order/refund', '', '', '', '', 0, 1, 0, 1686724773, 1686724773), (254, 192, 'A', '详情', '', 0, 'member.member_order/detail', '', '', '', '', 0, 1, 0, 1686724860, 1686724860), (255, 192, 'A', '退款', '', 0, 'member.member_order/refund', '', '', '', '', 0, 1, 0, 1686724884, 1686724884), (256, 118, 'A', '加入黑名单', '', 0, 'user.user/blacklist', '', '', '', '', 0, 1, 0, 1686725043, 1686725043), (257, 119, 'A', '调整会员时间', '', 0, 'user.user/adjustMember', '', '', '', '', 0, 1, 0, 1686725178, 1686725178), (258, 221, 'A', '保存', '', 0, 'setting.user.user/setRegisterReward', '', '', '', '', 0, 1, 0, 1686725578, 1686725578), (259, 175, 'A', '保存', '', 0, 'task.task_share/setConfig', '', '', '', '', 0, 1, 0, 1686725666, 1686725666), (260, 178, 'A', '保存', '', 0, 'task.task_invite/setConfig', '', '', '', '', 0, 1, 0, 1686725773, 1686725773), (261, 180, 'A', '保存', '', 0, 'recharge.recharge_package/setConfig', '', '', '', '', 0, 1, 0, 1686725859, 1686725859), (262, 184, 'A', '新增', '', 0, 'member.member_package/add', '', '', '', '', 0, 1, 0, 1686725960, 1686725960), (263, 184, 'A', '编辑', '', 0, 'member.member_package/edit', '', '', '', '', 0, 1, 0, 1686726044, 1686726044), (264, 184, 'A', '删除', '', 0, 'member.member_package/del', '', '', '', '', 0, 1, 0, 1686726057, 1686726057), (265, 184, 'A', '更新状态', '', 0, 'member.member_package/status', '', '', '', '', 0, 1, 0, 1686726087, 1686726087), (266, 184, 'A', '设置默认', '', 0, 'member.member_package/default', '', '', '', '', 0, 1, 0, 1686726107, 1686726107), (267, 184, 'A', '获取配置', '', 0, 'member.member_package/getConfig', '', '', '', '', 0, 1, 0, 1686726261, 1686726261), (268, 184, 'A', '更新配置', '', 0, 'member.member_package/setConfig', '', '', '', '', 0, 1, 0, 1686726281, 1686726281), (269, 198, 'A', '新增', '', 0, 'member.member_package_comment/add', '', '', '', '', 0, 1, 0, 1686726382, 1686726382), (270, 198, 'A', '删除', '', 0, 'member.member_package_comment/del', '', '', '', '', 0, 1, 0, 1686726399, 1686726399), (271, 200, 'A', '开通分销商', '', 0, 'distribution.distributor/add', '', '', '', '', 0, 1, 0, 1686726548, 1686726548), (272, 200, 'A', '修改分销状态', '', 0, 'distribution.distributor/status', '', '', '', '', 0, 1, 0, 1686726985, 1686726985), (273, 214, 'A', '详情', '', 0, 'distribution.distributionApply/detail', '', '', '', '', 0, 1, 0, 1686727703, 1686727703), (274, 214, 'A', '审核', '', 0, 'distribution.distributionApply/audit', '', '', '', '', 0, 1, 0, 1686727733, 1686727733), (275, 217, 'A', '提现审核', '', 0, 'distribution.withdraw/verify', '', '', '', '', 0, 1, 0, 1686728422, 1686728422), (276, 217, 'A', '转账', '', 0, 'distribution.withdraw/transfer', '', '', '', '', 0, 1, 0, 1686728441, 1686728441), (277, 217, 'A', '提现详情', '', 0, 'distribution.withdraw/detail', '', '', '', '', 0, 1, 0, 1686728788, 1686728788), (278, 218, 'A', '保存', '', 0, 'distribution.config/setConfig', '', '', '', '', 0, 1, 0, 1686728929, 1686728929), (279, 219, 'A', '保存', '', 0, 'distribution.withdraw/setConfig', '', '', '', '', 0, 1, 0, 1686728980, 1686728980), (280, 220, 'A', '保存', '', 0, 'setting.ai_setting/setChatConfig', '', '', '', '', 0, 1, 0, 1686729267, 1686729267), (281, 203, 'A', '会话保存', '', 0, 'setting.chatSetting/setChatConfig', '', '', '', '', 0, 1, 0, 1686729467, 1703063371), (282, 203, 'A', '会话查询余额', '', 0, 'setting.aiSetting/queryBalance', '', '', '', '', 0, 1, 0, 1686729520, 1687245313), (283, 202, 'A', '保存', '', 0, 'setting.shareSetting/setConfig', '', '', '', '', 0, 1, 0, 1686729675, 1686729675), (284, 223, 'A', '新增', '', 0, 'setting.SensitiveWord/add', '', '', '', '', 0, 1, 0, 1686729814, 1686729814), (285, 223, 'A', '编辑', '', 0, 'setting.SensitiveWord/edit', '', '', '', '', 0, 1, 0, 1686729828, 1686729828), (286, 223, 'A', '删除', '', 0, 'setting.SensitiveWord/del', '', '', '', '', 0, 1, 0, 1686729845, 1686729845), (287, 189, 'C', '创作模型添加/编辑', '', 60, 'creation.creationModel/add:edit', 'creative_model/edit', 'creative_center/creative_model/edit', '/creative_center/creative_model', '', 0, 0, 0, 1687141844, 1688020181), (288, 183, 'C', '会员权益', '', 0, 'member.member_benefits/lists', 'legal_right', 'marketing/vip_combo/legal_right/index', '', '', 0, 1, 0, 1687142635, 1687859683), (292, 365, 'M', 'AI绘画', '', 935, '', 'ai_drawing', '', '', '', 0, 1, 0, 1687245455, 1694056558), (293, 292, 'C', '绘画记录', '', 0, 'draw.draw_records/lists', 'record', 'ai_drawing/drawing_record/index', '', '', 0, 1, 0, 1687245653, 1687331832), (294, 292, 'C', '描述词库', '', 0, 'draw.draw_prompt/lists', 'lexicon', 'ai_drawing/drawing_lexicon/index', '', '', 0, 1, 0, 1687245741, 1687933969), (295, 292, 'C', '描述词分类', '', 0, 'draw.draw_category/lists', 'category', 'ai_drawing/drawing_category/index', '', '', 0, 1, 0, 1687245776, 1695115835), (296, 335, 'C', '内容审核', '', 10, '', 'examine', 'setting/examine/index', '', '', 0, 1, 0, 1687329691, 1694772595), (297, 293, 'A', '删除', '', 0, 'draw.draw_records/delete', '', '', '', '', 0, 1, 0, 1687331850, 1687331850), (298, 288, 'A', '新增', '', 0, 'member.member_benefits/add', '', '', '', '', 0, 1, 0, 1687859703, 1687859703), (299, 288, 'A', '编辑', '', 0, 'member.member_benefits/edit', '', '', '', '', 0, 1, 0, 1687859721, 1687859721), (300, 288, 'A', '删除', '', 0, 'member.member_benefits/del', '', '', '', '', 0, 1, 0, 1687859753, 1687859914), (301, 288, 'A', '修改状态', '', 0, 'member.member_benefits/status', '', '', '', '', 0, 1, 0, 1687859778, 1687859778), (302, 183, 'C', '会员套餐新增编辑', '', 0, 'member.member_package/add:edit', 'combo/edit', 'marketing/vip_combo/edit_vip', '/marketing/vipcombo/combo', '', 0, 0, 0, 1687860071, 1687860071), (303, 173, 'C', '充值套餐添加/编辑', '', 65, 'recharge.recharge_package/add:edit', 'recharge/edit', 'marketing/recharge/edit', '/marketing/recharge', '', 0, 0, 0, 1687941211, 1687941556), (304, 180, 'A', '新增', '', 0, 'recharge.recharge_package/add', '', '', '', '', 0, 1, 0, 1687941309, 1687941309), (305, 180, 'A', '编辑', '', 0, 'recharge.recharge_package/edit', '', '', '', '', 0, 1, 0, 1687941323, 1687941323), (306, 180, 'A', '删除', '', 0, 'recharge.recharge_package/del', '', '', '', '', 0, 1, 0, 1687941342, 1687941342), (307, 180, 'A', '修改状态', '', 0, 'recharge.recharge_package/status', '', '', '', '', 0, 1, 0, 1687941364, 1687941364), (308, 180, 'A', '修改推荐', '', 0, 'echarge.recharge_package/recommend', '', '', '', '', 0, 1, 0, 1687941396, 1687941396), (309, 294, 'A', '添加', '', 0, 'draw.draw_prompt/add', '', '', '', '', 0, 1, 0, 1687942617, 1687942617), (310, 294, 'A', '编辑', '', 0, 'draw.draw_prompt/edit', '', '', '', '', 0, 1, 0, 1687942630, 1687942630), (311, 294, 'A', '删除', '', 0, 'draw.draw_prompt/delete', '', '', '', '', 0, 1, 0, 1687942641, 1687942641), (312, 294, 'A', '状态', '', 0, 'draw.draw_prompt/status', '', '', '', '', 0, 1, 0, 1687942651, 1687942651), (313, 295, 'A', '添加', '', 0, 'draw.draw_category/add', '', '', '', '', 0, 1, 0, 1687942675, 1695115853), (314, 295, 'A', '编辑', '', 0, 'draw.draw_category/edit', '', '', '', '', 0, 1, 0, 1687942685, 1695115862), (315, 295, 'A', '删除', '', 0, 'draw.draw_category/delete', '', '', '', '', 0, 1, 0, 1687942695, 1695115868), (316, 295, 'A', '状态', '', 0, 'draw.draw_category/status', '', '', '', '', 0, 1, 0, 1687942710, 1695115875), (317, 119, 'A', '调整绘画余额', '', 0, 'user.user/adjustUserDraw', '', '', '', '', 0, 1, 0, 1688010816, 1688010816), (319, 223, 'A', '保存', '', 0, 'setting.sensitive_word/setConfig', '', '', '', '', 0, 1, 0, 1688091457, 1688091457), (320, 173, 'M', '卡密兑换', '', 0, '', 'redeem_code', '', '', '', 0, 1, 0, 1688626627, 1688626627), (321, 320, 'C', '卡密管理', '', 0, 'cardcode.cardCode/lists', 'lists', 'marketing/redeem_code/lists/index', '', '', 0, 1, 0, 1688626661, 1689061237), (322, 320, 'C', '兑换记录', '', 0, 'cardcode.cardCodeRecord/lists', 'record', 'marketing/redeem_code/record/index', '', '', 0, 1, 0, 1688626771, 1697182869), (323, 28, 'C', '公告设置', '', 72, 'setting.web.web_setting/getBulletinConfig', 'notice_config', 'setting/notice_config/index', '', '', 0, 1, 0, 1688632007, 1688632024), (324, 323, 'A', '保存', '', 0, 'setting.web.web_setting/setBulletinConfig', '', '', '', '', 0, 1, 0, 1688632067, 1688632067), (325, 320, 'C', '卡密设置', '', 0, 'cardcode.cardCode/getConfig', 'setting', 'marketing/redeem_code/setting/index', '', '', 0, 1, 0, 1688710408, 1689061313), (326, 321, 'A', '卡密添加', '', 0, 'cardcode.cardCode/add', '', '', '', '', 0, 1, 0, 1689061253, 1689061253), (327, 325, 'A', '保存', '', 0, 'cardcode.cardCode/setConfig', '', '', '', '', 0, 1, 0, 1689061328, 1689061328), (328, 321, 'A', '卡密删除', '', 0, 'cardcode.cardCode/del', '', '', '', '', 0, 1, 0, 1689157189, 1689157189), (329, 321, 'A', '卡密详情', '', 0, 'cardcode.cardCode/detail', '', '', '', '', 0, 1, 0, 1689157204, 1689157204), (330, 292, 'C', '绘画示例', '', 0, 'draw.draw_prompt_example/lists', 'example', 'ai_drawing/drawing_example/index', '', '', 0, 1, 0, 1689584038, 1689584038), (331, 330, 'A', '删除', '', 0, 'draw.draw_prompt_example/delete', '', '', '', '', 0, 1, 0, 1689584065, 1689584065), (332, 330, 'A', '新增', '', 0, 'draw.draw_prompt_example/add', '', '', '', '', 0, 1, 0, 1689584082, 1689584082), (333, 330, 'A', '编辑', '', 0, 'draw.draw_prompt_example/edit', '', '', '', '', 0, 1, 0, 1689584095, 1689584095), (334, 330, 'A', '修改状态', '', 0, 'draw.draw_prompt_example/status', '', '', '', '', 0, 1, 0, 1689584118, 1689584118), (335, 0, 'M', 'AI设置', 'el-icon-Aim', 250, '', 'ai', '', '', '', 0, 1, 0, 1689680767, 1689680767), (336, 335, 'C', '模型计费', '', 77, 'setting.chatSetting/getChatBillingConfig', 'ai_model_cost', 'setting/ai_model_cost/index', '', '', 0, 1, 0, 1689733693, 1703063427), (337, 336, 'A', '保存会话', '', 0, 'setting.chatSetting/setChatBillingConfig', '', '', '', '', 0, 1, 0, 1689733725, 1703063437), (338, 336, 'A', '保存绘画', '', 0, 'setting.drawSetting/setDrawBillingConfig', '', '', '', '', 0, 1, 0, 1689733743, 1703060720), (339, 376, 'C', 'key池管理', '', 76, 'setting.KeyPool/lists', 'ai_key', 'setting/ai_key/index', '', '', 0, 1, 0, 1690274986, 1695635978), (340, 339, 'A', '新增', '', 0, 'setting.KeyPool/add', '', '', '', '', 0, 1, 0, 1690275004, 1690275004), (341, 339, 'A', '编辑', '', 0, 'setting.KeyPool/edit', '', '', '', '', 0, 1, 0, 1690275015, 1690275015), (342, 339, 'A', '删除', '', 0, 'setting.KeyPool/del', '', '', '', '', 0, 1, 0, 1690275027, 1690275027), (343, 339, 'A', '状态', '', 0, 'setting.KeyPool/status', '', '', '', '', 0, 1, 0, 1690275051, 1690275051), (344, 161, 'C', 'IOS支付', '', 0, 'setting.pay.PayConfig/getIosPayConfig', 'setup', 'setting/pay/setup/index', '', '', 0, 1, 0, 1690342418, 1698921323), (345, 344, 'A', '保存', '', 0, 'setting.pay.PayConfig/setIosPayConfig', '', '', '', '', 0, 1, 0, 1690342428, 1690342428), (346, 367, 'A', '保存设置', '', 0, 'setting.KeyPool/setConfig', '', '', '', '', 0, 1, 0, 1692324467, 1694772850), (347, 365, 'M', '绘画广场', '', 925, '', 'draw/square', '', '', '', 0, 1, 0, 1693536303, 1694056571), (348, 347, 'C', '绘画管理', '', 0, 'draw.draw_square/lists', 'manage', 'ai_square/manage/index', '', '', 0, 1, 0, 1693536358, 1693536358), (349, 348, 'A', '新增', '', 0, 'draw.draw_square/add', '', '', '', '', 0, 1, 0, 1693536377, 1693536377), (350, 348, 'A', '编辑', '', 0, 'draw.draw_square/edit', '', '', '', '', 0, 1, 0, 1693536390, 1693536390), (351, 348, 'A', '删除', '', 0, 'draw.draw_square/del', '', '', '', '', 0, 1, 0, 1693536402, 1693536402), (352, 348, 'A', '状态', '', 0, 'draw.draw_square/isShow', '', '', '', '', 0, 1, 0, 1693536434, 1693536434), (353, 348, 'A', '审核', '', 0, 'draw.draw_square/verifyStatus', '', '', '', '', 0, 1, 0, 1693536448, 1693536448), (354, 347, 'C', '绘画分类', '', 0, 'draw.draw_square_category/lists', 'category', 'ai_square/category/index', '', '', 0, 1, 0, 1693536537, 1693536537), (355, 354, 'A', '新增', '', 0, 'draw.draw_square_category/add', '', '', '', '', 0, 1, 0, 1693536558, 1693536558), (356, 354, 'A', '编辑', '', 0, 'draw.draw_square_category/edit', '', '', '', '', 0, 1, 0, 1693536570, 1693536570), (357, 354, 'A', '删除', '', 0, 'draw.draw_square_category/del', '', '', '', '', 0, 1, 0, 1693536583, 1693536583), (358, 354, 'A', '状态', '', 0, 'draw.draw_square_category/status', '', '', '', '', 0, 1, 0, 1693536597, 1693536597), (359, 347, 'C', '广场设置', '', 0, 'draw.draw_square/getConfig', 'setting', 'ai_square/setting/index', '', '', 0, 1, 0, 1693536645, 1693536645), (360, 359, 'A', '保存', '', 0, 'draw.draw_square/setConfig', '', '', '', '', 0, 1, 0, 1693536658, 1693536658), (361, 119, 'A', '重置密码', '', 0, 'user.user/rePassword', '', '', '', '', 0, 1, 0, 1693909371, 1693909371), (362, 101, 'C', '邮箱设置', '', 80, 'notice.email_config/getConfig', 'email', 'message/email/index', '', '', 0, 1, 0, 1693998412, 1695115913), (363, 362, 'A', '保存', '', 0, 'notice.email_config/setConfig', '', '', '', '', 0, 1, 0, 1693998428, 1693998428), (364, 86, 'A', '保存', '', 0, 'channel.official_account_setting/setConfig', '', '', '', '', 0, 1, 0, 1694055919, 1694055919), (365, 0, 'M', 'AI应用', 'el-icon-Star', 925, '', 'ai_yingyong', '', '', '', 0, 1, 0, 1694056544, 1694137673), (366, 28, 'M', '登录注册', '', 80, '', 'login', '', '', '', 0, 0, 1, 1694139202, 1695115987), (367, 376, 'C', 'key下架规则', '', 30, 'setting.key_down_rule/lists', 'key_down_rule', 'setting/ai_key_rule/index', '', '', 0, 1, 0, 1694772706, 1695635986), (368, 367, 'A', '新增', '', 0, 'setting.key_down_rule/add', '', '', '', '', 0, 1, 0, 1694772753, 1694772753), (369, 367, 'A', '删除', '', 0, 'setting.key_down_rule/del', '', '', '', '', 0, 1, 0, 1694772774, 1694772774), (370, 367, 'A', '编辑', '', 0, 'setting.key_down_rule/edit', '', '', '', '', 0, 1, 0, 1694772794, 1694772794), (371, 367, 'A', '状态', '', 0, 'setting.key_down_rule/status', '', '', '', '', 0, 1, 0, 1694772809, 1694772809), (372, 365, 'M', '思维导图', '', 0, '', 'mind_map', '', '', '', 0, 1, 0, 1695030985, 1695030985), (373, 372, 'C', '生成记录', '', 0, '', 'record', 'ai_qa/dialogue_record/index', '', 'type=4', 0, 1, 0, 1695031033, 1695031033), (374, 372, 'C', '导图设置', '', 0, 'setting.mindmap/getConfig', 'setting', 'mind_map/setting', '', '', 0, 1, 0, 1695263195, 1695265346), (375, 374, 'A', '保存', '', 0, 'setting.mindmap/setConfig', '', '', '', '', 0, 1, 0, 1695265368, 1695265368), (376, 335, 'M', 'key管理', '', 50, '', 'key_setting', '', '', '', 0, 1, 0, 1695635962, 1695636002), (377, 335, 'C', '语音配置', '', 50, 'setting.VoiceSetting/getConfig', 'voice_config', 'setting/voice_config/index', '', '', 0, 1, 0, 1696904875, 1697014209), (378, 377, 'A', '保存', '', 0, 'setting.VoiceSetting/setConfig', '', '', '', '', 0, 1, 0, 1696904891, 1696904891), (379, 365, 'M', '艺术二维码', '', 0, '', 'art_QR_code', '', '', '', 0, 1, 0, 1697595679, 1697595679), (380, 379, 'C', '生成记录', '', 0, 'record.atrQRCode/lists', 'record', 'art_QR_code/record', '', '', 0, 1, 0, 1697595708, 1697626663), (381, 379, 'C', '生成设置', '', 0, 'setting.atrQRCode/config', 'setup', 'art_QR_code/setup', '', '', 0, 1, 0, 1697595735, 1697626516), (382, 381, 'A', '保存', '', 0, 'setting.atrQRCode/setting', '', '', '', '', 0, 1, 0, 1697626469, 1697626563), (383, 380, 'A', '删除', '', 0, 'record.atrQRCode/del', '', '', '', '', 0, 1, 0, 1697626645, 1697626645), (384, 119, 'A', '调整会员记录排序', '', 0, 'user.user/userMemberSort', '', '', '', '', 0, 1, 0, 1698896857, 1698896857), (385, 82, 'M', '微信小程序', '', 400, '', 'weapp', '', '', '', 0, 1, 0, 1701914042, 1701946228), (386, 385, 'C', '小程序一键上传', '', 0, 'channel.mnp_settings/uploadMnp', 'upload', 'channel/weapp_upload', '', '', 0, 1, 0, 1701914233, 1701946196), (387, 335, 'C', '联网配置', '', 15, 'setting.ai_setting/getNetworkConfig', 'networking_config', 'setting/networking_config/index', '', '', 0, 1, 0, 1703475556, 1703727972), (388, 387, 'A', '保存', '', 0, 'setting.ai_setting/setNetworkConfig', '', '', '', '', 0, 1, 0, 1703672462, 1703672462), (389, 335, 'C', '绘画设置', '', 13, 'setting.draw_setting/setDrawSetting', 'draw_setup', 'setting/draw_setup/index', '', '', 0, 1, 0, 1703729411, 1703730027), (390, 161, 'C', '易支付配置', '', 0, 'setting.pay.pay_config/getEpayConfig', 'e_pay', 'setting/pay/e_pay/index', '', '', 0, 1, 0, 1704797163, 1704797184), (391, 390, 'A', '保存', '', 0, 'setting.pay.pay_config/setEpayConfig', '', '', '', '', 0, 1, 0, 1704797177, 1704797177), (392, 96, 'C', 'PC首页', '', 95, '', 'pc_index', 'decoration/pc_index', '', '', 0, 1, 0, 1706265294, 1706856970);
COMMIT;

-- ----------------------------
-- Table structure for ai_system_role
-- ----------------------------
DROP TABLE IF EXISTS `ai_system_role`;
CREATE TABLE `ai_system_role`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `desc` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '描述',
  `sort` int(11) NULL DEFAULT 0 COMMENT '排序',
  `create_time` int(10) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '角色表';

-- ----------------------------
-- Records of ai_system_role
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_system_role_menu
-- ----------------------------
DROP TABLE IF EXISTS `ai_system_role_menu`;
CREATE TABLE `ai_system_role_menu`  (
  `role_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '角色ID',
  `menu_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '菜单ID',
  PRIMARY KEY (`role_id`, `menu_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '角色菜单关系表';

-- ----------------------------
-- Records of ai_system_role_menu
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_task_invite
-- ----------------------------
DROP TABLE IF EXISTS `ai_task_invite`;
CREATE TABLE `ai_task_invite`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '邀请人id',
  `new_user_id` int(11) NULL DEFAULT NULL COMMENT '新用户ID',
  `task_share_id` int(11) NULL DEFAULT NULL COMMENT '分享链接ID',
  `rewards` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '获得对话奖励',
  `draw_rewards` int(10) NULL DEFAULT NULL COMMENT '获取绘画奖励',
  `create_time` int(10) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '邀请记录表';

-- ----------------------------
-- Records of ai_task_invite
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_task_share
-- ----------------------------
DROP TABLE IF EXISTS `ai_task_share`;
CREATE TABLE `ai_task_share`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `channel` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '分享渠道: [1-微信小程序 2-微信公众号 3-手机H5 4-电脑PC 5-苹果APP 6-安卓APP]',
  `click_num` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '点击量',
  `invite_num` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '成功邀请人数',
  `rewards` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '获得对话奖励',
  `draw_rewards` int(10) NULL DEFAULT NULL COMMENT '获取绘画奖励',
  `create_time` int(10) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '分享记录表';

-- ----------------------------
-- Records of ai_task_share
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_user
-- ----------------------------
DROP TABLE IF EXISTS `ai_user`;
CREATE TABLE `ai_user`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `sn` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '编号',
  `avatar` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '头像',
  `real_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '真实姓名',
  `nickname` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户昵称',
  `account` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户账号',
  `password` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户密码',
  `mobile` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '用户电话',
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '邮箱',
  `sex` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户性别: [1=男, 2=女]',
  `channel` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '注册渠道: [1-微信小程序 2-微信公众号 3-手机H5 4-电脑PC 5-苹果APP 6-安卓APP 7-后台创建]',
  `is_disable` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否禁用: [0=否, 1=是]',
  `login_ip` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '最后登录IP',
  `login_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后登录时间',
  `is_new_user` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否是新注册用户: [1-是, 0-否]',
  `balance` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '余额（条数）',
  `balance_draw` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '绘画余额（条数）',
  `total_quiz` int(10) NOT NULL DEFAULT 0 COMMENT '累计提问次数',
  `total_draw` int(10) NOT NULL DEFAULT 0 COMMENT '累计绘画次数',
  `total_amount` decimal(10, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '累计消费金额',
  `member_end_time` bigint(20) NULL DEFAULT NULL COMMENT '会员到期时间',
  `member_perpetual` tinyint(1) NULL DEFAULT 0 COMMENT '会员是否永久 ：1-是；0-否',
  `is_distribution` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否分销商: [1-是, 0-否]',
  `distribution_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '分销状态：1-正常；0-冻结',
  `distribution_time` int(10) NULL DEFAULT NULL COMMENT '成为分销商的时间',
  `inviter_id` int(11) NOT NULL DEFAULT 0 COMMENT '邀请人id',
  `first_leader` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '第一个上级',
  `second_leader` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '第二个上级',
  `user_money` decimal(10, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '可提现佣金',
  `total_user_money` decimal(10, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '累计总佣金',
  `is_blacklist` tinyint(1) NULL DEFAULT 0 COMMENT '是否在黑名单：1-是；0-否；',
  `cancelled_remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '注销原因',
  `create_time` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `sn`(`sn`) USING BTREE COMMENT '编号唯一'
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户表';

-- ----------------------------
-- Records of ai_user
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_user_account_log
-- ----------------------------
DROP TABLE IF EXISTS `ai_user_account_log`;
CREATE TABLE `ai_user_account_log`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sn` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '流水号',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `change_object` tinyint(1) NOT NULL DEFAULT 0 COMMENT '变动对象',
  `change_type` smallint(5) NOT NULL COMMENT '变动类型',
  `action` tinyint(1) NOT NULL DEFAULT 0 COMMENT '动作 1-增加 2-减少',
  `change_amount` decimal(10, 2) NOT NULL COMMENT '变动数量',
  `left_amount` decimal(10, 2) NOT NULL DEFAULT 100.00 COMMENT '变动后数量',
  `source_sn` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '关联单号',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '备注',
  `extra` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '预留扩展字段',
  `create_time` int(10) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '账户流水记录表';

-- ----------------------------
-- Records of ai_user_account_log
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_user_auth
-- ----------------------------
DROP TABLE IF EXISTS `ai_user_auth`;
CREATE TABLE `ai_user_auth`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `openid` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '微信openid',
  `unionid` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '微信unionid',
  `terminal` tinyint(1) NOT NULL DEFAULT 1 COMMENT '客户端类型：1-微信小程序；2-微信公众号；3-手机H5；4-电脑PC；5-苹果APP；6-安卓APP',
  `create_time` int(10) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `openid`(`openid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户授权表';

-- ----------------------------
-- Records of ai_user_auth
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_user_member
-- ----------------------------
DROP TABLE IF EXISTS `ai_user_member`;
CREATE TABLE `ai_user_member`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `operate_id` int(11) NOT NULL COMMENT '操作员ID',
  `order_id` int(11) NULL DEFAULT NULL COMMENT '订单ID',
  `channel` tinyint(1) NOT NULL COMMENT '购买来源：1-用户自购；2-后台调整；',
  `package_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '套餐名称',
  `member_end_time` bigint(20) NULL DEFAULT NULL COMMENT '到期时间',
  `add_member_time` int(10) NULL DEFAULT NULL COMMENT '增加的会员时间',
  `is_perpetual` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否永久套餐：1-是；0-否',
  `package_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '套餐信息',
  `refund_status` tinyint(1) NULL DEFAULT 0 COMMENT '退款状态 0-未退款 1-已退款',
  `sort` int(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `create_time` int(10) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '会员开通记录表';

-- ----------------------------
-- Records of ai_user_member
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_user_session
-- ----------------------------
DROP TABLE IF EXISTS `ai_user_session`;
CREATE TABLE `ai_user_session`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `terminal` tinyint(1) NOT NULL DEFAULT 1 COMMENT '客户端类型：1-微信小程序；2-微信公众号；3-手机H5；4-电脑PC；5-苹果APP；6-安卓APP',
  `token` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '令牌',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '更新时间',
  `expire_time` int(10) NOT NULL COMMENT '到期时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `admin_id_client`(`user_id`, `terminal`) USING BTREE COMMENT '一个用户在一个终端只有一个token',
  UNIQUE INDEX `token`(`token`) USING BTREE COMMENT 'token是唯一的'
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户会话表';

-- ----------------------------
-- Records of ai_user_session
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for ai_withdraw_apply
-- ----------------------------
DROP TABLE IF EXISTS `ai_withdraw_apply`;
CREATE TABLE `ai_withdraw_apply`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sn` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '提现单号',
  `batch_no` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '商家批次单号',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '提现方式：1-支付宝；2-微信零钱；3-微信收款码；4-支付宝收款码；',
  `money` decimal(10, 2) UNSIGNED NOT NULL COMMENT '提现金额',
  `left_money` decimal(10, 2) UNSIGNED NOT NULL COMMENT '到账金额(扣除手续费)',
  `handling_fee` decimal(10, 2) UNSIGNED NOT NULL COMMENT '手续费',
  `real_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '真实姓名',
  `account` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '提现账号',
  `money_qr_code` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '收款二维码',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '提现状态：1-待审核；2-提现中；3-提现成功；4-提现失败；',
  `verify_status` tinyint(1) NULL DEFAULT 1 COMMENT '审核状态：1-待审核；2-审核通过；3-审核拒绝；',
  `verify_remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '审核备注',
  `verify_time` int(10) NULL DEFAULT NULL COMMENT '审核时间',
  `transfer_remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '转账备注',
  `transfer_result` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '转账结果',
  `query_result` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '查询结果',
  `finish_time` int(10) NULL DEFAULT NULL COMMENT '完成时间',
  `create_time` int(10) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '提现申请表';

-- ----------------------------
-- Records of ai_withdraw_apply
-- ----------------------------
BEGIN;
COMMIT;
-- ----------------------------
-- Table structure for ai_qrcode_records
-- ----------------------------
DROP TABLE IF EXISTS `ai_qrcode_records`;
CREATE TABLE `ai_qrcode_records`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户id',
  `way` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '生成方式 1-自定义 2-模板',
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '绘图类型 1-文本模式  2-图片模式',
  `prompt` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '提示词',
  `prompt_params` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '提示词参数',
  `qr_content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '二维码内容',
  `qr_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '二维码图片',
  `model_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '模型id',
  `template_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '模板id',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '本地图片地址',
  `task_id` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '0' COMMENT '绘图服务任务id',
  `notify_snap` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '回调快照信息',
  `fail_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '失败原因',
  `model` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT 'ai生成模型',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态：0-未处理 1-执行中 2-失败 3-成功',
  `use_tokens` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '消耗tokens',
  `aspect_ratio` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '二维码宽高比',
  `pixel_style` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '二维码像素风格',
  `marker_shape` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '二维码框风格',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '艺术二维码记录表';

-- ----------------------------
-- Records of ai_qrcode_records
-- ----------------------------
BEGIN;
COMMIT;
-- ----------------------------
-- Table structure for ai_key_log
-- ----------------------------
DROP TABLE IF EXISTS `ai_key_log`;
CREATE TABLE `ai_key_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL COMMENT '类型：1-对话,2-绘画 3-语音合成 4-艺术二维码',
  `ai_key` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '对话、绘画模型的key',
  `key` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'key',
  `secret` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'secret',
  `record_id` int(11) NULL DEFAULT 0 COMMENT '关联记录id',
  `create_time` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '创建时间',
PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 30 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'key使用记录表' ROW_FORMAT = Dynamic;
-- ----------------------------
-- Records of ai_key_log
-- ----------------------------
-- ----------------------------
-- Table structure for ai_draw_task
-- ----------------------------
DROP TABLE IF EXISTS `ai_draw_task`;
CREATE TABLE `ai_draw_task`  (
   `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键id',
   `action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '动作  generate upsample variation',
   `task_id` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '任务id',
   `notify_hook` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '回调地址',
   `notify_msg` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '回调错误信息',
   `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '图片地址',
   `prompt` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '提示词-中文',
   `prompt_desc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '完整描述',
   `index` int(1) UNSIGNED NULL DEFAULT 0 COMMENT '图片索引',
   `image_base` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '垫图图片地址',
   `image_msg_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '消息id，基于消息id进行图片变换操作',
   `image_msg_hash` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '消息hash,基于消息hash进行图片变换操作',
   `image_url` varchar(600) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '图片地址',
   `msg_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '图片消息id',
   `msg_hash` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '图片消息hash',
   `request_snap` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '请求快照',
   `response_snap` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT 'mj响应快照',
   `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '绘画token',
   `fail_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '失败原因',
   `progress` int(3) NULL DEFAULT 0 COMMENT '任务进度',
   `status` int(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '任务状态：0-未处理 1-已提交 2-执行中 3-成功 4-失败',
   `submit_time` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '提交时间',
   `start_time` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '开始执行时间',
   `finish_time` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '完成时间',
   `create_time` int(11) UNSIGNED NOT NULL COMMENT '创建时间',
   `update_time` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
   `delete_time` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
   PRIMARY KEY (`id`) USING BTREE,
   UNIQUE INDEX `task_id`(`task_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 20 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '绘画任务表';
-- ----------------------------
-- Records of ai_draw_task
-- ----------------------------
-- ----------------------------
-- Table structure for ai_draw_square_praise
-- ----------------------------
DROP TABLE IF EXISTS `ai_draw_square_praise`;
CREATE TABLE `ai_draw_square_praise`  (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
   `square_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '绘画广场ID',
   `create_time` int(10) NULL DEFAULT NULL COMMENT '创建时间',
   PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '绘画广场点赞表';
SET FOREIGN_KEY_CHECKS = 1;
