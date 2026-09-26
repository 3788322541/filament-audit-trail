<?php

namespace Zhenjun\AuditTrail\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Zhenjun\AuditTrail\Concerns\Auditable;

class Widget extends Model
{
    use Auditable;

    protected $table = 'widgets';

    protected $guarded = [];
}
