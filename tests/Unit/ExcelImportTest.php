<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ExcelImportService;

class ExcelImportTest extends TestCase
{
    public function test_validation()
    {
        $service = new ExcelImportService();
        $errors = $service->validateRow(['name' => '', 'date' => 'invalid']);
        $this->assertContains('Name is required', $errors);
        $this->assertContains('Invalid date format', $errors);
    }
}