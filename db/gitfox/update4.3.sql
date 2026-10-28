-- DROP TABLE IF EXISTS `ops_variable`;
CREATE TABLE IF NOT EXISTS `ops_variable` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `spaceID` int unsigned NOT NULL DEFAULT 0 COMMENT '空间ID',
  `repoID` int unsigned NOT NULL DEFAULT 0 COMMENT '仓库ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `key` varchar(50) NOT NULL DEFAULT '' COMMENT '变量键',
  `value` text DEFAULT NULL COMMENT '变量值',
  `scope` varchar(30) NOT NULL DEFAULT 'system' COMMENT '变量作用域',
  `type` varchar(30) NOT NULL DEFAULT 'plain' COMMENT 'plain（明文）|cipher(密文)',
  `desc` varchar(500) NOT NULL DEFAULT '' COMMENT '详情',
  `createdBy` varchar(30) NOT NULL DEFAULT '' COMMENT '由谁创建',
  `createdDate` datetime DEFAULT NULL COMMENT '创建时间',
  `editedBy` varchar(30) NOT NULL DEFAULT '' COMMENT '由谁编辑',
  `editedDate` datetime DEFAULT NULL COMMENT '编辑时间',
  `deleted` tinyint unsigned NOT NULL DEFAULT 0 COMMENT '是否删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB COMMENT='变量表';
CREATE UNIQUE INDEX `uk_spaceID_repoID_name` ON `ops_variable` (`spaceID`, `repoID`, `name`);
CREATE UNIQUE INDEX `uk_key` ON `ops_variable` (`key`);

REPLACE INTO `zt_grouppriv` (`group`, `module`, `method`) VALUES
(21, 'variable', 'browse'),
(21, 'variable', 'create'),
(21, 'variable', 'edit'),
(21, 'variable', 'delete'),
(22, 'variable', 'browse'),
(23, 'variable', 'browse');

-- 具备“处理问题”权限包（resolveIssue/closeIssue/ignoreIssue/activeIssue）中任一权限的分组，默认获得人工审查问题的处理权限。
REPLACE INTO `zt_grouppriv` (`group`, `module`, `method`) SELECT DISTINCT `group`, 'codescan', 'resolveManualIssue' FROM `zt_grouppriv` WHERE `module` = 'codescan' AND `method` IN ('resolveIssue', 'closeIssue', 'ignoreIssue', 'activeIssue');
REPLACE INTO `zt_grouppriv` (`group`, `module`, `method`) SELECT DISTINCT `group`, 'codescan', 'closeManualIssue' FROM `zt_grouppriv` WHERE `module` = 'codescan' AND `method` IN ('resolveIssue', 'closeIssue', 'ignoreIssue', 'activeIssue');

-- 具备代码评审问题详情权限的分组，默认获得人工审查问题详情入口。
REPLACE INTO `zt_grouppriv` (`group`, `module`, `method`) SELECT DISTINCT `group`, 'codescan', 'manualIssueView' FROM `zt_grouppriv` WHERE `module` = 'codescan' AND `method` = 'issueView';

-- DevOps 开发者默认拥有解决人工审查问题的权限。
REPLACE INTO `zt_grouppriv` (`group`, `module`, `method`) SELECT `id`, 'codescan', 'resolveManualIssue' FROM `zt_group` WHERE `role` = 'DEVOPSUSER';
