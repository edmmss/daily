<?php
class LinearStructure
{
	public $arr = [1, 2, 3, 4];
	function __construct()
	{
	}

	public function length()
	{
		return count($this->arr);
	}

	public function arrIsset($arr, $index)
	{
		return isset($arr[$index]);
	}

	public function isArrEmpty($arr)
	{
		return empty($arr);
	}

	public function clear($arr)
	{
		$this->arr = [];
	}

	public function pop()
	{
		$this->delete($this->length()-1);
	}

	public function shift()
	{
		$this->delete(0);
	}

	public function push($value)
	{
		$this->insert($this->length(), $value);
	}

	public function unshift($value)
	{
		$this->insert(0, $value);
	}

	public function insert($index, $value)
	{
		$length = $this->length();
		if ($index < 0 || $index > $length)
		{
			return false;
		}

		for ($i = $length; $i > $index; $i--)
		{
			$this->arr[$i] = $this->arr[$i-1];
		}

		$this->arr[$index] = $value;
		return true;
	}

	public function delete($index)
	{
		$length = $this->length();
		if ($index < 0 || $index > $length - 1)
		{
			return false;
		}

		$deleteVal = $this->arr[$index]; 

		for ($i = $index; $i < $length - 1; $i++)
		{
			$this->arr[$i] = $this->arr[$i+1];
		}

		unset($this->arr[$length-1]);
		return $deleteVal;
	}

	public function update($index, $value)
	{
		$length = $this->length();
		if ($index < 0 || $index > $length - 1 || !$this->arrIsset($this->arr, $index) || $this->isArrEmpty($this->arr))
		{
			return false;
		}

		$this->arr[$index] = $value;
		return true;
	}

	public function keyToValue($index)
	{
		$length = $this->length();
		if ($index < 0 || $index > $length - 1)
		{
			return false;
		}

		$value = $this->arr[$index];
		return $value; 
	}

	public function valueToKey($value)
	{
		$length = $this->length();
		for ($i = 0; $i < $length; $i++)
		{
			if ($this->arr[$i] == $value)
			{
				return $i;
			}
		}

		return false;
	}

	// 并集
	public function union($linear1, $linear2)
	{
		if (!$linear1 instanceof self || !$linear2 instanceof self)
		{
			return false;
		}

		$length  = $linear1->length();
		$length2 = $linear2->length();
		$tmpAry  = $linear1->arr;

		for ($i = $length; $i < $length + $length2; $i++)
		{
			$tmpAry[$i] = $linear2->arr[$i-$length];
		}

		return $tmpAry;
	}

	// 交集
	public function intersection($linear1, $linear2)
	{
		if (!$linear1 instanceof self || !$linear2 instanceof self)
		{
			return false;
		}

		$length  = $linear1->length();
		$length2 = $linear2->length();
		$tmpAry  = []; 

		for ($i = 0; $i < $length; $i++) 
		{ 
			for ($k = 0; $k < $length2; $k++) 
			{ 
				if ($linear1->arr[$i] == $linear2->arr[$k])
				{
					$tmpAry[] = $linear1->arr[$i];
					break;
				}
			}
		}

		return $tmpAry;
	}

}

$linear1 = new LinearStructure();
$linear2 = new LinearStructure();
$linear1->insert(0, 10);
$linear2->update(3, 30);

var_dump($linear1->arr);
var_dump($linear2->arr);

$unionAry = $linear1->union($linear1, $linear2);
var_dump($unionAry);
$intersection = $linear1->intersection($linear1, $linear2);
var_dump($intersection);
	




