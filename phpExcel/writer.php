<?php

require_once('./PHPExcel-1.8.0/Classes/PHPExcel.php');

try
{
	echo 1;
    $pdo = new PDO('mysql:host=localhost;dbname=meal;', 'root', '');
    //设置字符集
    $pdo->exec('set names utf8');
    //设置错误模式
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = " SELECT * FROM `meal_orders` LIMIT 50 ";
    //query()仅用于select查询语句，返回一个预处理对象
    $stmt = $pdo->query($sql);
    $dataAry = $stmt->fetchAll(2);
}
catch (PDOException $e)
{
    echo $e->getMessage();
    exit;
}

// 实例化一个PHPExcel()对象
$objPHPExcel = new PHPExcel();

// 选取当前的sheet对象
$objSheet = $objPHPExcel->getActiveSheet();
// 对当前sheet对象命名
$objSheet->setTitle('helen');

// 取巧模式：利用fromArray()填充数据
$headerAry = ['id', 'total', 'addtime', 'user_id', 'foodname', 'price', 'num', 'system_id'];
array_unshift($dataAry, $headerAry);

// 利用fromArray()直接一次性填充数据
$objSheet->fromArray($dataAry);
// 设置单元格的值
//$objSheet->setCellValue('A2', 'asdasda');
$count = count($dataAry);
for ($i = 2; $i < $count + 1; $i++)
{
    // 设置字体为颜色
    $objSheet->getStyle("B{$i}")->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
    // 设置字体居中
    $objSheet->getStyle("B{$i}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    // 设置字体粗体
    $objSheet->getStyle("F{$i}")->getFont()->setBold(true);
}

// 设定写入excel的类型
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');

$fileName = time().'.xls';
// 保存文件
$objWriter->save($fileName);
