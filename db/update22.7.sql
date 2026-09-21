-- DROP TABLE IF EXISTS `ops_variable`;
CREATE TABLE IF NOT EXISTS `ops_variable` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `spaceID` int unsigned NOT NULL DEFAULT 0 COMMENT '空间ID',
  `repoID` int unsigned NOT NULL DEFAULT 0 COMMENT '仓库ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `key` varchar(50) NOT NULL DEFAULT '' COMMENT '变量键',
  `value` text DEFAULT NULL COMMENT '变量值',
  `scope` varchar(30) NOT NULL DEFAULT 'system' COMMENT '变量作用域',
  `type` varchar(30) NOT NULL DEFAULT 'ssh' COMMENT 'plaintext（明文）|ciphertext(密文)',
  `desc` varchar(500) NOT NULL DEFAULT '' COMMENT '详情',
  `createdBy` varchar(30) NOT NULL DEFAULT '' COMMENT '由谁创建',
  `createdDate` datetime DEFAULT NULL COMMENT '创建时间',
  `editedBy` varchar(30) NOT NULL DEFAULT '' COMMENT '由谁编辑',
  `editedDate` datetime DEFAULT NULL COMMENT '编辑时间',
  `deleted` tinyint unsigned NOT NULL DEFAULT 0 COMMENT '是否删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB COMMENT='凭证表';
CREATE UNIQUE INDEX `uk_spaceID_repoID_name` ON `ops_variable` (`spaceID`, `repoID`, `name`);
CREATE UNIQUE INDEX `uk_key` ON `ops_variable` (`key`);
