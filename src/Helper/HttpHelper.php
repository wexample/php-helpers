<?php

namespace Wexample\Helpers\Helper;

class HttpHelper
{
    public const string CONTENT_TYPE_JSON = 'application/json';
    public const string CONTENT_TYPE_FORM_URLENCODED = 'application/x-www-form-urlencoded';
    public const string CONTENT_TYPE_MULTIPART = 'multipart/form-data';
    public const string CONTENT_TYPE_TEXT = 'text/plain';
    public const string CONTENT_TYPE_OCTET_STREAM = 'application/octet-stream';

    public const array CONTENT_TYPES = [
        self::CONTENT_TYPE_JSON,
        self::CONTENT_TYPE_FORM_URLENCODED,
        self::CONTENT_TYPE_MULTIPART,
        self::CONTENT_TYPE_TEXT,
        self::CONTENT_TYPE_OCTET_STREAM,
    ];
}