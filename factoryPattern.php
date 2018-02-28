<?php

abstract class Algorithm
{
    abstract protected function sum($number1, $number2);
}

class AlgorithmAdd extends Algorithm
{
    public function sum($number1, $number2)
    {
        $res = $number1 + $number2;

        return $res;
    }
}

class AlgorithmSubtraction extends Algorithm
{
    public function sum($number1, $number2)
    {
        $res = $number1 - $number2;

        return $res;
    }
}

class AlgorithmMultiplication extends Algorithm
{
    public function sum($number1, $number2)
    {
        $res = $number1 * $number2;

        return $res;
    }
}

class AlgorithmDivision extends Algorithm
{
    public function sum($number1, $number2)
    {
        if (!isset($number1) || !isset($number2)) {
            echo '参数不完整';

            return false;
        }

        if ($number2 == 0) {
            echo '被除数不能为0';

            return false;
        }
        $res = round($number1 / $number2, 2);

        return $res;
    }
}

class Perform extends Algorithm
{
    private $obj;

    public function __construct($type)
    {
        switch ($type) {
            case '+':
                $this->obj = new AlgorithmAdd();
                break;

            case '-':
                $this->obj = new AlgorithmSubtraction();
                break;

            case '*':
                $this->obj = new AlgorithmMultiplication();
                break;

            case '/':
                $this->obj = new AlgorithmDivision();
                break;

            default:
                # code...
                break;
        }
    }

    public function sum($number1, $number2)
    {
        return $this->obj->sum($number1, $number2);
    }
}

$perform = new Perform('/');
$res = $perform->sum(10, 0);
var_dump($res);

