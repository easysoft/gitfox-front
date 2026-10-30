-- DevOps独立版初始化 SQL。
-- GitFox独立版复用开源版的默认权限分组，仅调整 vision，升级回正常版本后可继续复用分组权限。;
UPDATE `zt_group` SET `vision` = 'gitfox' WHERE `vision` = 'rnd' AND `role` IN ('admin', 'DEVOPSADMIN', 'DEVOPSINSPECTOR', 'DEVOPSUSER');

-- 阶段执行表增加审批状态字段。
ALTER TABLE `ops_stage_executions` ADD COLUMN `approvalStatus` varchar(30) NOT NULL DEFAULT '' COMMENT '审批状态：''=未发起，inProgress=审批中，approved=通过，rejected=拒绝，canceled=已取消' AFTER `status`;
ALTER TABLE `ops_stage_executions` ADD COLUMN `approvalConfig` varchar(500) NOT NULL DEFAULT '' COMMENT '审批配置' AFTER `approvalStatus`;