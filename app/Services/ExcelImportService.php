<?php

namespace App\Services;

use App\Models\Row;
use App\Models\ImportLog;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Database\QueryException;

class ExcelImportService implements ToModel, WithHeadingRow, WithChunkReading
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
                'id' => $row['id'],
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
        if (!preg_match('/^\d{1,2}\.\d{1,2}\.\d{4}$/', $row['date'])) {
            $errors[] = 'Invalid date format';
        }
        if (!is_numeric($row['id']) || $row['id']<1){
            $errors[] = 'Invalid id format';
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

    public static function truncateAndResetAutoIncrement()
    {
        DB::table('rows')->truncate();
        DB::table('import_logs')->truncate();
        DB::statement('ALTER TABLE `import_logs` AUTO_INCREMENT = 1');
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}