<?php

namespace App\Observers;

use App\Models\BloodUnit;
use App\Models\SerologyTest;

class SerologyTestObserver
{
    protected array $requiredTests = [
        'HIV',
        'Hepatitis B',
        'Hepatitis C',
        'Syphilis',
    ];

    /**
     * Handle the SerologyTest "created" or "updated" event.
     */
    public function saved(SerologyTest $serologyTest): void
    {
        $this->updateBloodUnitStatus($serologyTest);
    }

    /**
     * Handle the SerologyTest "deleted" event.
     */
    public function deleted(SerologyTest $serologyTest): void
    {
        $this->updateBloodUnitStatus($serologyTest);
    }

    protected function updateBloodUnitStatus(SerologyTest $serologyTest): void
    {
        /** @var BloodUnit $bloodUnit */
        $bloodUnit = $serologyTest->bloodUnit;

        if (!$bloodUnit) {
            return;
        }

        $recordedTests = $bloodUnit->serologyTests;

        // Check for "Failure" Condition (Immediate Discard)
        foreach ($recordedTests as $test) {
            if (in_array($test->result, ['positive', 'indeterminate'])) {
                $bloodUnit->update([
                    'serology_test_status' => 'failed',
                    'status' => 'discarded',
                ]);
                return;
            }
        }

        // Check for "Pass" Condition (Ready for Issue)
        $completedTestTypes = $recordedTests->pluck('test_type')->unique()->toArray();

        $allRequiredTestsCompleted = empty(array_diff($this->requiredTests, $completedTestTypes));

        if ($allRequiredTestsCompleted && $recordedTests->every(fn ($test) => $test->result === 'negative')) {
            $bloodUnit->update([
                'serology_test_status' => 'passed',
                'status' => 'ready_for_issue',
            ]);
            return;
        }

        // "Pending" Condition
        if ($bloodUnit->serology_test_status !== 'pending' || $bloodUnit->status !== 'test_awaited') {
            $bloodUnit->update([
                'serology_test_status' => 'pending',
                'status' => 'test_awaited',
            ]);
        }
    }
}
