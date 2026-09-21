-- DROP TABLE IF EXISTS `ops_credentials`;
CREATE TABLE IF NOT EXISTS `ops_credentials` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `spaceID` int unsigned NOT NULL DEFAULT 0 COMMENT '空间ID',
  `repoID` int unsigned NOT NULL DEFAULT 0 COMMENT '仓库ID',
  `owner` varchar(30) NOT NULL DEFAULT '' COMMENT '关联用户账号',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `identifier` varchar(50) NOT NULL DEFAULT '' COMMENT '凭证唯一标识',
  `type` varchar(30) NOT NULL DEFAULT 'ssh' COMMENT '凭证类型 ssh|secret',
  `secret` text DEFAULT NULL COMMENT '内容',
  `algorithm` varchar(50) NOT NULL DEFAULT '' COMMENT '算法类型',
  `desc` varchar(500) NOT NULL DEFAULT '' COMMENT '详情',
  `verifiedDate` datetime DEFAULT NULL COMMENT '验证时间',
  `createdBy` varchar(30) NOT NULL DEFAULT '' COMMENT '由谁创建',
  `createdDate` datetime DEFAULT NULL COMMENT '创建时间',
  `editedBy` varchar(30) NOT NULL DEFAULT '' COMMENT '由谁编辑',
  `editedDate` datetime DEFAULT NULL COMMENT '编辑时间',
  `deleted` tinyint unsigned NOT NULL DEFAULT 0 COMMENT '是否删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB COMMENT='凭证表';
CREATE UNIQUE INDEX `uk_spaceID_repoID_name` ON `ops_credentials` (`spaceID`, `repoID`, `name`);
CREATE UNIQUE INDEX `uk_identifier` ON `ops_credentials` (`identifier`);
