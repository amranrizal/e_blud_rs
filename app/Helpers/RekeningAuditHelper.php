<?php

namespace App\Helpers;

use App\Models\RekeningAudit;

class RekeningAuditHelper
{
    public static function log($rekening, string $action, $before = null, $after = null)
    {
        RekeningAudit::create([
            'rekening_id' => $rekening->id,
            'action'      => $action,
            'before'      => $before ? json_encode($before) : null,
            'after'       => $after ? json_encode($after) : null,
            'user_id'     => auth()->id(),
        ]);
    }
}
