<?php

namespace Tests\Unit\Models;

use Carbon\Carbon;
use Tests\Support\FleetTestData;
use Tests\Unit\UnitTestCase;

class InsuranceContractGlobalTest extends UnitTestCase
{
    public function test_contract_is_expired_when_end_date_is_past(): void
    {
        Carbon::setTestNow('2026-06-01');

        $contract = FleetTestData::insuranceContract(null, [
            'start_date' => '2025-01-01',
            'end_date' => '2025-12-31',
        ]);

        $this->assertTrue($contract->isExpired());
    }

    public function test_contract_is_expiring_soon_within_thirty_days(): void
    {
        Carbon::setTestNow('2026-06-01');

        $contract = FleetTestData::insuranceContract(null, [
            'start_date' => '2026-01-01',
            'end_date' => '2026-06-20',
        ]);

        $this->assertFalse($contract->isExpired());
        $this->assertTrue($contract->isExpiringSoon(30));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }
}
