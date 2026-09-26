<?php

namespace Zhenjun\AuditTrail\Tests\Enums;

enum PostStatus: string
{
    case Published = 'published';
    case Draft = 'draft';
}
