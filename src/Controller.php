<?php

namespace Mustafa\Staller;

class controller
{
    public function View(string $filename, array|object $data = []): mixed
    {
        return Response::View($filename, $data);
    }

    public function Call(string $action)
    {
        return $this->{$action}();
    }
}