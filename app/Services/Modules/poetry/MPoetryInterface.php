<?php

namespace App\Services\Modules\poetry;

interface MPoetryInterface
{
    public function ListPoetry($id,$idblock);

    public function getItem($id);

}
