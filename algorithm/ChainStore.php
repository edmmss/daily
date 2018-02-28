<?php

class Node_linked
{
    public $data;
    public $next = null;

    public function __construct($value = null)
    {
        $this->data = $value;
    }

    public function update($newValue)
    {
        $this->data = $newValue;
    }
}

class Linear_linked
{
    protected $_head;

    public function __construct()
    {
        $this->_head = new Node_linked();
    }

    public function head()
    {
        return $this->_head;
    }

    public function length()
    {
        $length = 0;
        $prev = $this->head();

        while ($prev->next) {
            $prev = $prev->next;
            $length++;
        }

        return $length;
    }

    public function insert($value, $index)
    {
        $length = $this->length();
        if ($index < 0 || $index > $length) {
            return false;
        }

        $node = new Node_linked($value);
        $prev = $this->head();

        for ($i = 0; $i < $index; $i++) {
            $prev = $prev->next;
        }

        $node->next = $prev->next;
        $prev->next = $node;

        return true;
    }
}
