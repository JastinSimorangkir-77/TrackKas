<?php

namespace App\Http\Controllers;

use App\Support\ReportData;
use App\Support\ReportPeriod;
use App\Support\ReportScope;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class ReportsController extends Controller
{
    /**
     * Download the report of the chosen period and scope as a PDF file.
     *
     * The form that calls this lives on the transaction page, so there is no
     * report page of its own; the numbers only ever appear inside the PDF.
     */
    public function pdf(Request $request): Response
    {
        $validated = $request->validate([
            'periode' => ['nullable', 'string', 'in:harian,mingguan,bulanan,tahunan'],
            'tanggal' => ['nullable', 'date'],
            // Which transaction types the report holds. Left out means both.
            'jenis' => ['nullable', 'string', Rule::in(array_keys(ReportScope::options()))],
        ]);

        $period = ReportPeriod::make(
            $validated['periode'] ?? ReportPeriod::HARIAN,
            $validated['tanggal'] ?? null,
        );

        $scope = ReportScope::make($validated['jenis'] ?? null);

        $report = ReportData::build($request->user(), $period, $scope);

        return Pdf::loadView('laporan.pdf', $report)
            ->setPaper('a4', 'portrait')
            ->download($report['filename']);
    }
}
