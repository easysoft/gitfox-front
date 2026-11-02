<?php
declare(strict_types = 1);
require_once dirname(__FILE__, 5) . '/test/lib/test.class.php';
class reporeviewflowZenTest extends baseTest
{
    protected $moduleName = 'reporeviewflow';
    protected $className  = 'zen';

    /**
     * 测试buildDefinition 方法。
     * Test buildDefinition method in zen layer.
     *
     * @param  object $definition
     * @access public
     * @return object
     */
    public function buildDefinitionTest(object $definition): object
    {
        return $this->invokeArgs('buildDefinition', [$definition]);
    }

    /**
     * 测试 getIssueTypeList 方法。
     * Test getIssueTypeList method in zen layer.
     *
     * @access public
     * @return array
     */
    public function getIssueTypeListTest(): array
    {
        return $this->invokeArgs('getIssueTypeList');
    }
}
