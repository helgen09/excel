<?php

namespace App\Services;

use App\Models\Row;
use App\Models\ImportLog;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Redis;

class ExcelImportService implements ToModel, WithHeadingRow
{
    protected $redisKey;
    protected $errors = [];

    public function __construct()
    {
        $this->redisKey = 'import_progress:' . uniqid();
    }

    public function model(array $row)
    {
        $rowNumber = $this->getCurrentRowNumber();
        $validationErrors = $this->validateRow($row);

        if (empty($validationErrors)) {
            $data = [
                'name' => $row['name'],
                'date' => \Carbon\Carbon::createFromFormat('d.m.Y', $row['date'])->format('Y-m-d'),
            ];

            Redis::set($this->redisKey, $rowNumber);
            event(new \App\Events\RowCreated($data));
            return new Row($data);
        } else {
            $this->errors[$rowNumber] = $validationErrors;
            ImportLog::create([
                'row_number' => $rowNumber,
                'errors' => implode(', ', $validationErrors),
            ]);
            return null;
        }
    }

    protected function validateRow($row)
    {
        $errors = [];

        if (empty($row['name'])) {
            $errors[] = 'Name is required';
        }

        if (!preg_match('/^\d{1,2}\.\d{1,2}\.\d{4}$/', $row['date']) || !checkdate(...explode('.', $row['date']))) {
            $errors[] = 'Invalid date format';
        }

        return $errors;
    }

    protected function getCurrentRowNumber()
    {
        return Redis::get($this->redisKey, 0) + 1;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getRedisKey()
    {
        return $this->redisKey;
    }
}