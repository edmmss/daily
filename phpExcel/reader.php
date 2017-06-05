<?php

require_once('./PHPExcel-1.8.0/Classes/PHPExcel.php');


$filePath = './data.xls';

// 建立reader对象 ，分别用两个不同的类对象读取2007和2003版本的excel文件
$phpReader = new PHPExcel_Reader_Excel2007();
// 判断当前是否在读取文件
if (!$phpReader->canRead($filePath))
{
    $phpReader = new PHPExcel_Reader_Excel5();
    if (!$phpReader->canRead($filePath))
    {
        echo 'no Excel';
        return false;
    }
}

$PHPExcel = $phpReader->load($filePath); //读取文件
$currentSheet = $PHPExcel->getSheet(0); //读取第一个工作簿
$allColumn = $currentSheet->getHighestColumn(); // 所有列数
$allRow = $currentSheet->getHighestRow(); // 所有行数

$data = []; //下面是读取想要获取的列的内容
 for ($rowIndex = 2; $rowIndex <= $allRow; $rowIndex++)
 {
     $data[] = [
         'id'        => $currentSheet->getCell('A'.$rowIndex)->getValue(),
         'total'     => $currentSheet->getCell('B'.$rowIndex)->getValue(),
         'foodname'  => $currentSheet->getCell('E'.$rowIndex)->getValue(),
         'price'     => $currentSheet->getCell('F'.$rowIndex)->getValue(),
         'stsyem_id' => $currentSheet->getCell('H'.$rowIndex)->getValue(),
     ];
 }

 var_dump($data);

