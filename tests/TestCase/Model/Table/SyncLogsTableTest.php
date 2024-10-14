<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SyncLogsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SyncLogsTable Test Case
 */
class SyncLogsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SyncLogsTable
     */
    protected $SyncLogs;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.SyncLogs',
        'app.SyncRecords',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('SyncLogs') ? [] : ['className' => SyncLogsTable::class];
        $this->SyncLogs = $this->getTableLocator()->get('SyncLogs', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->SyncLogs);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\SyncLogsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\SyncLogsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
