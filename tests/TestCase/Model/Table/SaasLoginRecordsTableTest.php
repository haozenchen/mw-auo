<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SaasLoginRecordsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SaasLoginRecordsTable Test Case
 */
class SaasLoginRecordsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SaasLoginRecordsTable
     */
    protected $SaasLoginRecords;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.SaasLoginRecords',
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
        $config = $this->getTableLocator()->exists('SaasLoginRecords') ? [] : ['className' => SaasLoginRecordsTable::class];
        $this->SaasLoginRecords = $this->getTableLocator()->get('SaasLoginRecords', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->SaasLoginRecords);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\SaasLoginRecordsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\SaasLoginRecordsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
