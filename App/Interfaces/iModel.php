<?php

namespace App\Interfaces;

interface iModel{
    public function init($model);
    public function all();
    public function some(int $limit);
    public function where($column, $value);
    public function find($value);

}