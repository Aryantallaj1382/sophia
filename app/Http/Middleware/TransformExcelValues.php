<?php

namespace App\Http\Middleware;

use Illuminate\Support\Str;
use Maatwebsite\Excel\Middleware\CellMiddleware;

class TransformExcelValues extends CellMiddleware
{
    /**
     * @param mixed $value
     * @return mixed
     */
    public function __invoke($value, callable $next)
    {
        $forTransform = ['-', '_', 'under review'];

        return $next(
            in_array(Str::lower(Str::trim($value)), $forTransform) ? null : $value
        );
    }

}
