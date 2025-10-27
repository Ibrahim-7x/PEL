<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use App\Models\InitialCustomerInformation;
use App\Models\HappyCallStatus;
use App\Models\Feedback;
use App\Models\Coms;

class ExportController extends Controller
{
    // Pages
    public function initialCustomerPage() {
        return view('reports.initial_customer');
    }

    public function happyCallPage() {
        return view('reports.happy_call');
    }

    public function feedbackPage() {
        return view('reports.feedback');
    }

    public function comsPage() {
        return view('reports.coms');
    }

    // Export logic
    public function exportInitialCustomer(Request $request) {
        $data = InitialCustomerInformation::whereBetween('created_at', [$request->start_date, $request->end_date])->get();

        return $this->exportToExcel($data, 'initial_customer.xlsx');
    }

    public function exportHappyCall(Request $request) {
        $data = HappyCallStatus::whereBetween('created_at', [$request->start_date, $request->end_date])->get();

        return $this->exportToExcel($data, 'happy_call.xlsx');
    }

    public function exportFeedback(Request $request) {
        $data = Feedback::whereBetween('created_at', [$request->start_date, $request->end_date])->get();

        return $this->exportToExcel($data, 'feedback.xlsx');
    }

    public function exportComs(Request $request) {
        $data = Coms::whereBetween('created_at', [$request->start_date, $request->end_date])->get();

        return $this->exportToExcel($data, 'coms.xlsx');
    }

    private function exportToExcel($data, $filename) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        if ($data->isEmpty()) {
            $sheet->setCellValue('A1', 'No records found for selected date range');
        } else {
            $columns = array_keys($data->first()->toArray());

            // Set headers
            foreach ($columns as $colIndex => $column) {
                $colLetter = Coordinate::stringFromColumnIndex($colIndex + 1);
                $sheet->setCellValue($colLetter . '1', $column);
            }

            // Set rows
            foreach ($data as $rowIndex => $row) {
                $rowData = $row->toArray();
                $excelRow = $rowIndex + 2;
                foreach ($columns as $colIndex => $column) {
                    $colLetter = Coordinate::stringFromColumnIndex($colIndex + 1);
                    $value = $rowData[$column] ?? null;
                    $sheet->setCellValue($colLetter . $excelRow, is_array($value) ? json_encode($value) : $value);
                }
            }
        }

        $writer = new Xlsx($spreadsheet);
        $filePath = storage_path('app/' . $filename);
        $writer->save($filePath);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }
}
