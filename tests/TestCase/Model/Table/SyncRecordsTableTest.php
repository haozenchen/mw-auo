<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SyncRecordsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SyncRecordsTable Test Case
 */
class SyncRecordsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SyncRecordsTable
     */
    protected $SyncRecords;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.SyncRecords',
        'app.SaasAdmins',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('SyncRecords') ? [] : ['className' => SyncRecordsTable::class];
        $this->SyncRecords = $this->getTableLocator()->get('SyncRecords', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->SyncRecords);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\SyncRecordsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\SyncRecordsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
