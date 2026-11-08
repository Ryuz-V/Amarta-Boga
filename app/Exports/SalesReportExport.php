<?php

namespace App\Exports;

use App\Models\Order;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    protected string $startDate;
    protected string $endDate;

    public function __construct(string $startDate, string $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return Order::with(['table', 'user', 'payments', 'items'])
            ->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ])
            ->where('status', '!=', 'cancelled')
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            ['LAPORAN PENJUALAN AMARTA BOGA RESTO'],
            ['Periode: ' . Carbon::parse($this->startDate)->format('d/m/Y') . ' s/d ' . Carbon::parse($this->endDate)->format('d/m/Y')],
            [''],
            [
                'No. Invoice',
                'Tanggal & Waktu',
                'Nama Tamu',
                'Meja / Tipe',
                'Kasir',
                'Jumlah Item',
                'Subtotal (Rp)',
                'Diskon (Rp)',
                'PB1 Pajak (Rp)',
                'Service Charge (Rp)',
                'Total Akhir (Rp)',
                'Metode Pembayaran',
                'Status Transaksi',
            ]
        ];
    }

    public function map($order): array
    {
        return [
            $order->invoice_number,
            $order->created_at->format('d/m/Y H:i:s'),
            $order->customer_name,
            $order->table ? $order->table->name : strtoupper($order->order_type),
            $order->user?->name ?? 'Kasir',
            $order->items->sum('quantity'),
            (float) $order->subtotal,
            (float) $order->discount_amount,
            (float) $order->tax_amount,
            (float) $order->service_charge,
            (float) $order->total_amount,
            $order->payments->pluck('payment_method')->implode(', ') ?: 'Tunai',
            strtoupper($order->status),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => '#,##0',
            'H' => '#,##0',
            'I' => '#,##0',
            'J' => '#,##0',
            'K' => '#,##0',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Judul Laporan
        $sheet->mergeCells('A1:M1');
        $sheet->mergeCells('A2:M2');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(11);

        // Header Tabel Kolom (Row 4)
        $sheet->getStyle('A4:M4')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 10,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'EA580C'], // Brand Orange
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        return [];
    }
}
