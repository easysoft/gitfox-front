<?php
declare(strict_types = 1);

require_once dirname(__FILE__, 5) . '/test/lib/test.class.php';

class upgradeZenTest extends baseTest
{
    protected $moduleName = 'upgrade';
    protected $className  = 'zen';

    /**
     * 构造函数，加载 upgrade 模型供 zen 层方法调用。
     * Constructor, load upgrade model for the zen methods.
     *
     * @param  string $moduleName
     * @param  string $className
     * @access public
     * @return void
     */
    public function __construct($moduleName = '', $className = '')
    {
        parent::__construct($moduleName, $className);

        $this->instance->loadModel('upgrade');
    }

    /**
     * 设置版本信息并返回旧版本信息。
     * Set the version information and return the old version information.
     *
     * @param  string $installedVersion
     * @param  string $version
     * @param  string $edition
     * @access protected
     * @return array
     */
    protected function setVersions(string $installedVersion, string $version, string $edition = ''): array
    {
        global $config;

        $oldVersions = array(
            'installedVersion' => isset($config->installedVersion) ? $config->installedVersion : '',
            'version'          => $config->version,
            'edition'          => $config->edition,
            'errors'           => upgradeModel::$errors
        );

        if($installedVersion) $config->installedVersion = $installedVersion;
        if($version)          $config->version          = $version;
        if($edition)          $config->edition          = $edition;
        upgradeModel::$errors = array();

        return $oldVersions;
    }

    /**
     * 恢复版本信息。
     * Restore the version information.
     *
     * @param  array  $oldVersions
     * @access protected
     * @return void
     */
    protected function restoreVersions(array $oldVersions): void
    {
        global $config;

        $config->installedVersion = $oldVersions['installedVersion'];
        $config->version          = $oldVersions['version'];
        $config->edition          = $oldVersions['edition'];
        upgradeModel::$errors     = $oldVersions['errors'];
    }

    /**
     * 测试获取要升级到的版本列表。
     * Test get upgrade versions.
     *
     * @param  string $fromVersion
     * @param  string $installedVersion
     * @param  string $version
     * @param  string $edition
     * @access public
     * @return string
     */
    public function getUpgradeVersionsTest(string $fromVersion, string $installedVersion = '22_7', string $version = '22_7', string $edition = ''): string
    {
        $oldVersions = $this->setVersions($installedVersion, $version, $edition);

        $versions = $this->invokeArgs('getUpgradeVersions', array($fromVersion));

        $this->restoreVersions($oldVersions);
        if(dao::isError()) return dao::getError();

        return implode(',', array_keys($versions));
    }

    /**
     * 测试获取要升级到的版本列表时记录的错误信息。
     * Test the errors of getUpgradeVersions.
     *
     * @param  string $fromVersion
     * @param  string $installedVersion
     * @param  string $version
     * @param  string $edition
     * @access public
     * @return string
     */
    public function getUpgradeVersionsErrorTest(string $fromVersion, string $installedVersion = '22_7', string $version = '22_7', string $edition = ''): string
    {
        $oldVersions = $this->setVersions($installedVersion, $version, $edition);

        $this->invokeArgs('getUpgradeVersions', array($fromVersion));
        $errors = upgradeModel::$errors;

        $this->restoreVersions($oldVersions);
        return implode(',', $errors);
    }

    /**
     * 测试获取升级变更内容的条数。
     * Test the count of the upgrade changes.
     *
     * @param  string $fromVersion
     * @param  string $toVersion
     * @param  string $installedVersion
     * @param  string $version
     * @param  string $edition
     * @access public
     * @return int
     */
    public function getUpgradeChangesCountTest(string $fromVersion, string $toVersion, string $installedVersion = '22_7', string $version = '22_7', string $edition = ''): int
    {
        $oldVersions       = $this->setVersions($installedVersion, $version, $edition);
        $oldGitfoxVersions = $this->instance->config->upgrade->gitfoxVersion;
        $this->instance->config->upgrade->gitfoxVersion = array();

        $changes = $this->invokeArgs('getUpgradeChanges', array($fromVersion, $toVersion));

        $this->instance->config->upgrade->gitfoxVersion = $oldGitfoxVersions;
        $this->restoreVersions($oldVersions);
        return count($changes);
    }

    /**
     * 测试获取升级变更内容中第一条变更的字段值。
     * Test the field value of the first upgrade change.
     *
     * @param  string $fromVersion
     * @param  string $toVersion
     * @param  string $field
     * @param  string $installedVersion
     * @param  string $version
     * @param  string $edition
     * @access public
     * @return string
     */
    public function getUpgradeChangesFieldTest(string $fromVersion, string $toVersion, string $field, string $installedVersion = '22_7', string $version = '22_7', string $edition = ''): string
    {
        $oldVersions       = $this->setVersions($installedVersion, $version, $edition);
        $oldGitfoxVersions = $this->instance->config->upgrade->gitfoxVersion;
        $this->instance->config->upgrade->gitfoxVersion = array();

        $changes = $this->invokeArgs('getUpgradeChanges', array($fromVersion, $toVersion));

        $this->instance->config->upgrade->gitfoxVersion = $oldGitfoxVersions;
        $this->restoreVersions($oldVersions);

        $change = reset($changes);
        if(empty($change) || !isset($change[$field])) return 'none';

        return (string)$change[$field];
    }

    /**
     * 测试升级变更内容中是否包含指定的升级方法。
     * Test whether the upgrade changes contain the given method.
     *
     * @param  string $fromVersion
     * @param  string $toVersion
     * @param  string $method
     * @param  string $edition
     * @access public
     * @return int
     */
    public function getUpgradeChangesHasMethodTest(string $fromVersion, string $toVersion, string $method, string $edition = ''): int
    {
        $oldVersions       = $this->setVersions('22_7', '22_7', $edition);
        $oldGitfoxVersions = $this->instance->config->upgrade->gitfoxVersion;
        $this->instance->config->upgrade->gitfoxVersion = array();

        $changes = $this->invokeArgs('getUpgradeChanges', array($fromVersion, $toVersion));

        $this->instance->config->upgrade->gitfoxVersion = $oldGitfoxVersions;
        $this->restoreVersions($oldVersions);

        foreach($changes as $change)
        {
            if(isset($change['method']) && $change['method'] === $method) return 1;
        }
        return 0;
    }

    /**
     * 测试获取升级变更中的GitFox版本。
     * Test GitFox versions in upgrade changes.
     *
     * @param  string $fromVersion
     * @param  string $toVersion
     * @param  array  $gitfoxVersions
     * @param  array  $openVersions
     * @access public
     * @return string
     */
    public function getUpgradeChangesGitfoxVersionsTest(string $fromVersion, string $toVersion, array $gitfoxVersions, array $openVersions): string
    {
        $oldVersions       = $this->setVersions('22_7', '23_0');
        $oldGitfoxVersions = $this->instance->config->upgrade->gitfoxVersion;
        $oldOpenVersions   = $this->instance->lang->upgrade->fromVersions;
        $oldExecFlow       = $this->instance->config->upgrade->execFlow;
        $hasTargetGitfoxVersion = property_exists($this->instance->config, 'gitfoxVersion');
        $oldTargetGitfoxVersion = $hasTargetGitfoxVersion ? $this->instance->config->gitfoxVersion : null;

        $this->instance->config->upgrade->gitfoxVersion = $gitfoxVersions;
        $this->instance->lang->upgrade->fromVersions   = $openVersions;
        $this->instance->config->gitfoxVersion         = '4.6';
        $this->instance->config->upgrade->execFlow['gitfox4_3'] = array('functions' => 'disableFeaturesByMode');
        $this->instance->config->upgrade->execFlow['gitfox4_4'] = array('functions' => 'disableFeaturesByMode');
        $this->instance->config->upgrade->execFlow['gitfox4_5'] = array('functions' => 'disableFeaturesByMode');

        $changes  = $this->invokeArgs('getUpgradeChanges', array($fromVersion, $toVersion));
        $versions = array();
        foreach($changes as $change)
        {
            if(strpos($change['version'], 'gitfox') === 0) $versions[] = $change['version'];
        }

        $this->instance->config->upgrade->gitfoxVersion = $oldGitfoxVersions;
        $this->instance->lang->upgrade->fromVersions   = $oldOpenVersions;
        $this->instance->config->upgrade->execFlow       = $oldExecFlow;
        if($hasTargetGitfoxVersion) $this->instance->config->gitfoxVersion = $oldTargetGitfoxVersion;
        else unset($this->instance->config->gitfoxVersion);
        $this->restoreVersions($oldVersions);
        return implode(',', array_unique($versions));
    }

    /**
     * 测试升级变更中的GitFox SQL版本。
     * Test GitFox SQL versions in upgrade changes.
     *
     * @access public
     * @return string
     */
    public function getUpgradeChangesGitfoxSqlVersionTest(): string
    {
        $oldVersions       = $this->setVersions('22_7', 'gitfox99.2');
        $oldGitfoxVersions = $this->instance->config->upgrade->gitfoxVersion;
        $oldOpenVersions   = $this->instance->lang->upgrade->fromVersions;
        $sqlFile           = $this->instance->upgrade->getUpgradeFile('gitfox99.1');
        if(file_exists($sqlFile)) return '0';

        try
        {
            file_put_contents($sqlFile, "CREATE TABLE IF NOT EXISTS `zt_gitfox_change_test` (`id` int);\n");
            $this->instance->config->upgrade->gitfoxVersion = array('gitfox99_1' => '22_7', 'gitfox99_2' => '22_7');
            $this->instance->lang->upgrade->fromVersions   = array('22_7' => '22.7');

            $changes  = $this->invokeArgs('getUpgradeChanges', array('22_7', 'gitfox99_2'));
            $versions = array();
            foreach($changes as $change)
            {
                if($change['type'] == 'sql' && strpos($change['version'], 'gitfox') === 0) $versions[] = $change['version'];
            }
            return implode(',', array_unique($versions));
        }
        finally
        {
            if(file_exists($sqlFile)) unlink($sqlFile);
            $this->instance->config->upgrade->gitfoxVersion = $oldGitfoxVersions;
            $this->instance->lang->upgrade->fromVersions   = $oldOpenVersions;
            $this->restoreVersions($oldVersions);
        }
    }
}
