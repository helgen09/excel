<?php

namespace App\Http\Controllers;

use App\Services\ExcelImportService;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function index(){
        
    }

    public function upload(Request $request, ExcelImportService $importService)
    {
        $importService->truncateAndResetAutoIncrement();
        $request->validate(['file' => 'required|mimes:xlsx']);
        $file = $request->file('file');
        Excel::import($importService, $file, null, \Maatwebsite\Excel\Excel::XLSX);
        $this->generateErrorReport($importService->getErrors());
        return response()->json(['redis_key' => $importService->getRedisKey()]);
    }

    protected function generateErrorReport($errors)
    {
        $report = [];
        foreach ($errors as $rowNumber => $errorMessages) {
            $report[] = "$rowNumber - " . implode(', ', $errorMessages);
        }
        file_put_contents(storage_path('result.txt'), implode("\n", $report));
    }
}