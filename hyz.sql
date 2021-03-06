/*
Navicat MySQL Data Transfer

Source Server         : yh
Source Server Version : 50617
Source Host           : localhost:3306
Source Database       : hyz

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2017-12-14 18:32:43
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `hyz_activity`
-- ----------------------------
DROP TABLE IF EXISTS `hyz_activity`;
CREATE TABLE `hyz_activity` (
  `activity_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ctime` int(11) DEFAULT '0' COMMENT '创建时间',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态 0禁用 1正常',
  `images` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '图片',
  `activity_name` varchar(30) CHARACTER SET utf8 DEFAULT NULL COMMENT '活动名',
  `activity_type` tinyint(1) DEFAULT '1' COMMENT '活动类型',
  `activity_info` text CHARACTER SET utf8 COMMENT '活动详情',
  `stop_time` int(11) DEFAULT NULL COMMENT '截止时间',
  `price` decimal(14,0) DEFAULT NULL COMMENT '价格',
  `now_num` int(11) DEFAULT NULL COMMENT '当前报名人数',
  `title` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '活动标题',
  `target_num` int(11) DEFAULT NULL COMMENT '目标人数',
  PRIMARY KEY (`activity_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of hyz_activity
-- ----------------------------
INSERT INTO `hyz_activity` VALUES ('1', '1512793771', '1', null, '活动1', '1', '这是一个测试活动', '1512880168', '200', '1', '活动1标题', null);
INSERT INTO `hyz_activity` VALUES ('2', '1513064019', '1', null, '旅游', '2', '旅游项目', null, '12', '6', '旅游', '2000');

-- ----------------------------
-- Table structure for `hyz_apply`
-- ----------------------------
DROP TABLE IF EXISTS `hyz_apply`;
CREATE TABLE `hyz_apply` (
  `apply_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `apply_type` tinyint(1) DEFAULT NULL COMMENT '申请类型 1:申请活动 2:申请商家',
  `activity_id` int(10) DEFAULT NULL COMMENT '活动id(如是申请活动）',
  `order_id` int(10) DEFAULT NULL COMMENT '订单id',
  `user_id` int(10) DEFAULT NULL COMMENT '申请人',
  `apply_real_name` varchar(10) CHARACTER SET utf8 DEFAULT NULL COMMENT '申请人真实姓名',
  `sex` tinyint(1) DEFAULT NULL COMMENT '性别',
  `age` int(1) DEFAULT NULL COMMENT '年龄',
  `id_card_no` varchar(30) CHARACTER SET utf8 DEFAULT NULL COMMENT '身份证',
  `tel` varchar(15) CHARACTER SET utf8 DEFAULT NULL COMMENT '手机号',
  `company` varchar(30) CHARACTER SET utf8 DEFAULT NULL COMMENT '单位',
  `job` varchar(30) CHARACTER SET utf8 DEFAULT NULL COMMENT '职位',
  `ctime` int(11) DEFAULT NULL COMMENT '申请时间',
  `apply_status` tinyint(1) DEFAULT '0' COMMENT '申请状态 1:通过 2:未支付',
  `remark` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '用户申请备注',
  `admin_remark` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '管理员审核备注',
  `province` varchar(30) CHARACTER SET utf8 DEFAULT NULL COMMENT '省',
  `city` varchar(30) CHARACTER SET utf8 DEFAULT NULL COMMENT '市',
  `county` varchar(30) CHARACTER SET utf8 DEFAULT NULL COMMENT '区',
  `address` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '详细地址',
  `other_info` text CHARACTER SET utf8 COMMENT '其他定制信息 如：（演唱：独唱）',
  `apply_price` decimal(14,2) DEFAULT NULL COMMENT '点赞（金额/每次）',
  `like_num` int(11) DEFAULT NULL COMMENT '点赞数',
  `like_userid` varchar(255) DEFAULT NULL COMMENT '点赞的人',
  PRIMARY KEY (`apply_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of hyz_apply
-- ----------------------------
INSERT INTO `hyz_apply` VALUES ('2', '1', '1', '1', '3', '报名人真实姓名', '1', '24', '500112199309110866', '18765438990', '社团部', '部长', '1512794077', '1', '申请参加活动1', '同意参加', '重庆', '重庆', '渝北', '人和立交', '吉他独演', '2.00', null, null);
INSERT INTO `hyz_apply` VALUES ('3', '1', '2', '4', '1', '杨辉', null, null, null, null, null, null, '1513067523', '1', null, null, null, null, null, '渝北区', null, null, null, null);

-- ----------------------------
-- Table structure for `hyz_bank_account`
-- ----------------------------
DROP TABLE IF EXISTS `hyz_bank_account`;
CREATE TABLE `hyz_bank_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '会员id',
  `account_bank` varchar(32) NOT NULL COMMENT '所属银行名称',
  `account_no` varchar(32) NOT NULL COMMENT '银行卡号',
  `account_name` varchar(50) NOT NULL COMMENT '开户户名',
  `id_card` varchar(18) DEFAULT NULL COMMENT '用户身份证号码',
  `account_addr` varchar(100) DEFAULT NULL COMMENT '开户行分行地址',
  `is_default` tinyint(3) DEFAULT '0' COMMENT '是否为默认 1是0否',
  `is_del` tinyint(3) DEFAULT '0' COMMENT '是否删除1 已删除 0未删除',
  `add_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='银行账户信息';

-- ----------------------------
-- Records of hyz_bank_account
-- ----------------------------

-- ----------------------------
-- Table structure for `hyz_cart`
-- ----------------------------
DROP TABLE IF EXISTS `hyz_cart`;
CREATE TABLE `hyz_cart` (
  `cart_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) DEFAULT NULL,
  `product_id` int(10) DEFAULT NULL COMMENT '商品id',
  `product_num` smallint(5) DEFAULT NULL COMMENT '商品数量',
  `period_id` int(10) DEFAULT NULL COMMENT '商品期数',
  `ctime` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL COMMENT '0:已删除 1:正常',
  PRIMARY KEY (`cart_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of hyz_cart
-- ----------------------------
INSERT INTO `hyz_cart` VALUES ('1', '11', '1', '17', '10', null, '1');

-- ----------------------------
-- Table structure for `hyz_comment`
-- ----------------------------
DROP TABLE IF EXISTS `hyz_comment`;
CREATE TABLE `hyz_comment` (
  `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `comment_type` tinyint(1) DEFAULT NULL COMMENT '评论类型 1：商品评论 2:活动评论',
  `source_id` int(10) DEFAULT NULL COMMENT '来源id 根据comment_type判断应该是product_id还是activity_id',
  `ctime` int(11) DEFAULT NULL,
  `pid` int(10) DEFAULT NULL COMMENT '评论父级id',
  `fabulous` int(10) DEFAULT NULL COMMENT '点赞数',
  `user_id` int(10) DEFAULT NULL,
  `content` text CHARACTER SET utf8 COMMENT '评论内容',
  `images` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '图片',
  PRIMARY KEY (`comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of hyz_comment
-- ----------------------------

-- ----------------------------
-- Table structure for `hyz_order`
-- ----------------------------
DROP TABLE IF EXISTS `hyz_order`;
CREATE TABLE `hyz_order` (
  `order_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单号',
  `order_sn` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `user_id` int(10) DEFAULT NULL,
  `order_type` tinyint(1) DEFAULT '0' COMMENT '订单类型 1:商品订单 2:活动订单 3:点赞订单',
  `activity_id` int(10) DEFAULT NULL COMMENT '活动id(如是申请活动）',
  `apply_id` int(10) DEFAULT NULL COMMENT '活动申请id（如果是点赞订单）',
  `period_time` int(10) DEFAULT NULL COMMENT '商品期数（如是商品抽奖订单）',
  `order_product_id` int(10) DEFAULT NULL COMMENT '产品编号',
  `product_num` smallint(5) DEFAULT '1' COMMENT '商品购买数量',
  `order_money` decimal(14,2) DEFAULT '0.00' COMMENT '订单金额',
  `freight` decimal(14,2) DEFAULT '0.00' COMMENT '运费',
  `is_shop` tinyint(1) DEFAULT NULL COMMENT '是否为商家订单',
  `order_status` tinyint(1) DEFAULT '0' COMMENT '0已下单 1已付款 2已完成 3已取消（无效）',
  `order_time` int(11) DEFAULT '0' COMMENT '下单时间',
  `pay_time` int(11) DEFAULT '0' COMMENT '付款时间',
  `shipping_status` tinyint(1) DEFAULT '0' COMMENT '发货状态 0未发货 1已发货 2已收货',
  `shipping_id` tinyint(1) DEFAULT '0' COMMENT '发货方式id',
  `addressee` varchar(60) CHARACTER SET utf8 DEFAULT NULL COMMENT '收件人',
  `province` varchar(30) CHARACTER SET utf8 DEFAULT NULL COMMENT '省',
  `city` varchar(30) CHARACTER SET utf8 DEFAULT NULL COMMENT '市',
  `county` varchar(30) CHARACTER SET utf8 DEFAULT NULL COMMENT '区',
  `address` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '收货人详细地址',
  `tel` varchar(30) CHARACTER SET utf8 DEFAULT NULL COMMENT '收货人电话',
  `delivery_time` int(11) DEFAULT '0' COMMENT '发货时间',
  `order_note` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '订单备注',
  PRIMARY KEY (`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of hyz_order
-- ----------------------------
INSERT INTO `hyz_order` VALUES ('1', '2017120949102495', '3', '2', '1', null, null, null, '1', '200.00', '0.00', null, '1', '1512880168', '1512880168', '0', '0', null, '重庆', '重庆', '渝北区', '人和立交', '15823232323', '0', null);
INSERT INTO `hyz_order` VALUES ('2', '2017120949102496', '3', '1', null, null, '1', '1', '1', '654.00', '0.00', null, '1', '1512880168', '1512880168', '0', '0', null, '重庆', '重庆', '渝北区', '双龙大道', '15823232323', '0', null);
INSERT INTO `hyz_order` VALUES ('3', '2017120949102497', '3', '3', '1', '2', null, null, '1', '2.00', '0.00', null, '1', '1512880168', '1512880168', '0', '0', null, '重庆', '重庆', '江北区', '北辰名都', '15823232323', '0', null);
INSERT INTO `hyz_order` VALUES ('4', '2017121251501015', '1', '2', '2', null, null, null, '2', '12.00', '0.00', null, '0', '1513067523', '0', '0', '0', null, null, null, null, null, null, '0', null);

-- ----------------------------
-- Table structure for `hyz_period`
-- ----------------------------
DROP TABLE IF EXISTS `hyz_period`;
CREATE TABLE `hyz_period` (
  `period_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '商品期次id',
  `p_id` int(11) DEFAULT NULL COMMENT '商品id',
  `period_name` varchar(20) DEFAULT NULL COMMENT '活动名',
  `target_num` int(11) DEFAULT NULL COMMENT '目标数量',
  `now_num` int(11) DEFAULT NULL COMMENT '已参与数量',
  `period_time` int(11) DEFAULT NULL COMMENT '期次',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `status_period` int(1) DEFAULT NULL COMMENT '期次状态',
  `period_price` decimal(10,0) DEFAULT NULL COMMENT '抽奖价格/每次',
  PRIMARY KEY (`period_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of hyz_period
-- ----------------------------
INSERT INTO `hyz_period` VALUES ('1', '1', null, '100', '50', '1', null, '1', null);
INSERT INTO `hyz_period` VALUES ('2', '2', null, '321', '100', '1', null, '1', null);
INSERT INTO `hyz_period` VALUES ('3', '1', null, '321', '60', '2', null, '2', null);

-- ----------------------------
-- Table structure for `hyz_product`
-- ----------------------------
DROP TABLE IF EXISTS `hyz_product`;
CREATE TABLE `hyz_product` (
  `product_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_name` varchar(20) CHARACTER SET utf8 DEFAULT NULL COMMENT '商品名称',
  `price` decimal(14,2) DEFAULT '0.00' COMMENT '价格',
  `images` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '图片',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态 1正常 0禁用',
  `ctime` int(11) DEFAULT NULL COMMENT '创建时间',
  `change_time` int(11) DEFAULT '0' COMMENT '修改时间',
  `product_type` tinyint(1) DEFAULT NULL COMMENT '商品类型',
  `product_info` text CHARACTER SET utf8 COMMENT '产品详情',
  PRIMARY KEY (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of hyz_product
-- ----------------------------
INSERT INTO `hyz_product` VALUES ('1', '自行车', '654.00', null, '1', null, '0', '1', '神奇的自行车');
INSERT INTO `hyz_product` VALUES ('2', '洗衣机', '2199.00', null, '1', null, '0', '2', null);
INSERT INTO `hyz_product` VALUES ('3', '冰箱', '3126.00', null, '1', null, '0', '2', null);

-- ----------------------------
-- Table structure for `hyz_shop`
-- ----------------------------
DROP TABLE IF EXISTS `hyz_shop`;
CREATE TABLE `hyz_shop` (
  `shop_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) DEFAULT NULL,
  `shop_name` varchar(255) DEFAULT NULL COMMENT '店铺名',
  `province` varchar(30) DEFAULT NULL COMMENT '省',
  `city` varchar(30) DEFAULT NULL COMMENT '市',
  `county` varchar(30) DEFAULT NULL COMMENT '区县',
  `shop_address` varchar(255) DEFAULT NULL COMMENT '详细地址',
  `shop_reg_no` varchar(55) DEFAULT NULL COMMENT '营业执照编号',
  `ctime` bigint(11) DEFAULT NULL COMMENT '入驻时间',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态 0未审核 1已审核',
  `images_business_license` varchar(255) DEFAULT '',
  `images_store_photo` varchar(255) DEFAULT '',
  PRIMARY KEY (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of hyz_shop
-- ----------------------------

-- ----------------------------
-- Table structure for `hyz_sms_records`
-- ----------------------------
DROP TABLE IF EXISTS `hyz_sms_records`;
CREATE TABLE `hyz_sms_records` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) DEFAULT NULL COMMENT '用户id',
  `tel` varchar(20) DEFAULT NULL COMMENT '手机号',
  `sms_type` tinyint(1) DEFAULT '1' COMMENT '短信验证码类型 1找回密码 2：注册',
  `code` char(6) DEFAULT NULL COMMENT '验证码',
  `ctime` bigint(20) DEFAULT NULL COMMENT '创建时间',
  `expiration_time` bigint(20) DEFAULT NULL COMMENT '过期时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of hyz_sms_records
-- ----------------------------
INSERT INTO `hyz_sms_records` VALUES ('1', '1', '18883168051', '1', '9999', '1513050423', '1513050423');

-- ----------------------------
-- Table structure for `hyz_support`
-- ----------------------------
DROP TABLE IF EXISTS `hyz_support`;
CREATE TABLE `hyz_support` (
  `id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of hyz_support
-- ----------------------------

-- ----------------------------
-- Table structure for `hyz_user`
-- ----------------------------
DROP TABLE IF EXISTS `hyz_user`;
CREATE TABLE `hyz_user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_card` varchar(12) CHARACTER SET utf8 DEFAULT NULL COMMENT '会员卡号（冗余）',
  `user_name` varchar(30) CHARACTER SET utf8 DEFAULT NULL COMMENT '用户名',
  `real_name` varchar(30) CHARACTER SET utf8 DEFAULT NULL COMMENT '真实名字',
  `tel` varchar(15) CHARACTER SET utf8 DEFAULT NULL COMMENT '手机',
  `pass` varchar(64) CHARACTER SET utf8 DEFAULT NULL COMMENT '密码',
  `salt` char(4) CHARACTER SET utf8 DEFAULT NULL COMMENT '密码加密盐值',
  `id_card_no` varchar(30) CHARACTER SET utf8 DEFAULT NULL COMMENT '身份证',
  `province` varchar(30) CHARACTER SET utf8 DEFAULT NULL COMMENT '省',
  `city` varchar(30) CHARACTER SET utf8 DEFAULT NULL COMMENT '市',
  `county` varchar(30) CHARACTER SET utf8 DEFAULT NULL COMMENT '区',
  `user_type` tinyint(1) DEFAULT '1' COMMENT '用户类型 1普通用户 2商家',
  `status` tinyint(1) DEFAULT '1' COMMENT '用户状态 1正常 0禁用',
  `ctime` int(10) DEFAULT NULL COMMENT '注册时间（冗余）',
  `openid` varchar(255) DEFAULT NULL COMMENT '微信openid',
  `qq` varchar(15) DEFAULT NULL COMMENT 'qq号',
  `job` varchar(50) DEFAULT NULL COMMENT '职位',
  `user_img` varchar(255) DEFAULT NULL,
  `user_qq` varchar(50) DEFAULT NULL COMMENT '用户自己填写的qq',
  `email` varchar(50) DEFAULT NULL COMMENT '用户自己填写的email',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of hyz_user
-- ----------------------------
INSERT INTO `hyz_user` VALUES ('1', null, 'jl', 'yh', '18883168051', 'e10adc3949ba59abbe56e057f20f883e', null, null, '重庆市', '重庆市', '渝北区', '1', '1', '1512459129', null, null, 'lol', null, '156105667', '156105667@qq.com');
INSERT INTO `hyz_user` VALUES ('2', null, null, null, '13594284610', '4b8ed884a65b3ba32fc5e44cf6a34428', '6246', null, null, null, null, '1', '1', '1512723594', null, null, 'it', null, null, null);
INSERT INTO `hyz_user` VALUES ('3', null, null, null, '15823232323', '4e4f85ed0e1dff0c9f5c77d9d66875ec', '2841', null, null, null, null, '1', '1', '1512726651', null, null, 'it', null, null, null);
INSERT INTO `hyz_user` VALUES ('4', null, null, null, '13594284611', '1b94a68fcd0d6886507d1bdcc2114569', '3828', null, null, null, null, '1', '1', '1512815747', null, null, 'it', null, null, null);
