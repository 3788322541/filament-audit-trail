<?php

namespace Zhenjun\AuditTrail\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Zhenjun\AuditTrail\Concerns\Auditable;

class Post extends Model
{
    use Auditable;
    use SoftDeletes;

    protected $table = 'posts';

    protected $guarded = [];
}
