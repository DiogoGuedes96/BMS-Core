<?php

namespace App\Services;

class Service
{
    public function result($content = '', $success = true)
    {
        $result = new \stdClass();
        $result->content = $content;
        $result->success = $success;

        return $result;
    }
}
