<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ProductsExport implements FromArray, WithColumnWidths, WithHeadings, WithEvents
{
    use RegistersEventListeners;
    public  $reportNameHeader = [];
    public $companyNameHeader = [];
    public $headers = [];
    public $data = [];
    public $widths = [];
    public $drawing;
    public static  $lang;
    public $pdf;

    public $title = 'Products Records';

    function __construct(array $data = [],$lang)
    {
        $this->data = $data;
        $this->drawing = new Drawing();
        $this->setProperties();
        self::$lang = $lang;
    }

    function setProperties()
    {
        $this->setTitle();
        $this->setReportNameHeader();
        $this->headings();
        $this->columnWidths();
        $this->setData($this->data);
    }


    public function setTitle(){
        $title =  'Products Records';
        // dd($title);
        $title = (self::$lang == "ar") ? 'سجل المنتجات' : 'Products Records';
        $this->title = $title;
    }

    public function setReportNameHeader(){
        $this->reportNameHeader = [ 'Report Name', $this->title];
        if (self::$lang == "ar") {
            $this->reportNameHeader = [ 'اسم التقرير' , $this->title];
            while (count($this->reportNameHeader) < 13) {
                array_push($this->reportNameHeader, " ");
            }
            $this->reportNameHeader = array_reverse($this->reportNameHeader);
        }
    }
    public function headings(): array
    {
        $headers = [
            'Product ID',
            'Product Name',
            'Product Description',
            'Product Price',
            'Product Quantity',
            'Product Category',
            'Product Brand',
            'Product Featured',
        ];

        if (self::$lang == "ar") {
            $headers = [
                'رقم المنتج',
                'اسم المنتج',
                'وصف المنتج',
                'سعر المنتج',
                'كمية المنتج',
                'فئة المنتج',
                'ماركة المنتج',
                'حالة المنتج',
            ];

            while (count($headers) < 9) {
                array_push($headers, " ");
            }


        }
        $this->headers = $headers;
        return $this->headers;
    }


    public function columnWidths():array
    {
        $widths = [
            'A' => 15,
            'B' => 30,
            'C' => 40,
            'D' => 20,
            'E' => 20,
            'F' => 30,
            'G' => 30,
            'H' => 20,
        ];

        $this->widths = $widths;
        return $this->widths;
    }

    private function setData($dataArr = [])
    {
        $this->data = array_map(function ($row) {
            return [
                $row['0'],
                $row['1'],
                $row['2'],
                $row['3'],
                $row['4'],
                $row['5'],
                $row['6'],
                $row['7'],
                '',
                ''
            ];
        }, $dataArr);
    }

    public function array():array
    {
        return $this->data;
    }    

    public static function afterSheet(AfterSheet $event )
    {
        if(self::$lang == "ar")
        {
            return [
                $event->sheet->getDelegate()->setRightToLeft(true),
                $event->sheet->getDelegate()->getStyle('A1:F1')->getAlignment()->setHorizontal('right'),
                $event->sheet->getDelegate()->getStyle('A1:F1')->getFont()->setBold(true),
                $event->sheet->getDelegate()->getStyle('A2:F100')->getAlignment()->setHorizontal('right')
            ];

        }else
        {
            return [
                $event->sheet->getDelegate()->getStyle('A1:F1')->getAlignment()->setHorizontal('left'),
                $event->sheet->getDelegate()->getStyle('A1:F1')->getFont()->setBold(true),
                $event->sheet->getDelegate()->getStyle('A2:F100')->getAlignment()->setHorizontal('left')
            ];
        }
    }

}
