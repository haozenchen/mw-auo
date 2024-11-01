<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SaasSettingsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SaasSettingsTable Test Case
 */
class SaasSettingsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SaasSettingsTable
     */
    protected $SaasSettings;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.SaasSettings',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('SaasSettings') ? [] : ['className' => SaasSettingsTable::class];
        $this->SaasSettings = $this->getTableLocator()->get('SaasSettings', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->SaasSettings);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\SaasSettingsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
