<?php

namespace Modules\Core\Traits;

trait ExcelTraits
{
    /**
     * @param $image
     * @param $line
     * @param $index
     * @param $sheet
     * @param int $maxhigh
     * @param array $get_image_info
     * @return mixed
     * @throws \PHPExcel_Exception
     */
    public function exportImage($image, $line, $index, $sheet,$maxhigh=50,$get_image_info=[]){
        $maxhigh = $maxhigh == '' ? $maxhigh = 50 : $maxhigh ;
        $y = $maxhigh <= 50 ? 10 : $maxhigh-50;

        $x = 28;
        if(isset($get_image_info[0])){
            if($get_image_info[0] > 225)
            {
                $width = $get_image_info[0];
                $x = intval(50-$width/10);
                $x < 0 ? $x = 0 : '';
            }
        }

        $objDrawing = new \PHPExcel_Worksheet_Drawing;
        if ($image) {
            $objDrawing->setPath($image);
        }
        $objDrawing->setCoordinates($line .$index);
        $objDrawing->setWidth(50);
        $objDrawing->setHeight(50);
        $objDrawing->setOffsetX($x);
        $objDrawing->setOffsetY($y);
        $objDrawing->setWorksheet($sheet);
        return $sheet->setHeight($index, $maxhigh);
    }
}