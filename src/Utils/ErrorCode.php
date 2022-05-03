<?php

namespace App\Utils;

class ErrorCode
{
    public const ERROR_CODE_AUTH                = 0;
    public const ERROR_CODE_CHECK_IN            = 1;
    public const ERROR_CODE_CHECK_OUT           = 2;
    public const ERROR_CODE_LIST_CHILD_CHECK    = 3;
    public const ERROR_CODE_LIST_CHILD_TIME     = 4;

    public const CHILD_NOT_FOUND                = 'child not found';
    public const CHILD_ALREADY_CHECKED_IN       = 'child already checked in';
    public const CHILD_ALREADY_CHECKED_OUT      = 'child already checked out';
    public const ATTENDANCE_NOT_FOUND           = 'attendance not found';
}
