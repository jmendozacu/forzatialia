<?php

use Illuminate\Support\Debug\Dumper;

class REX_Logger
{
    const TYPE_PERFORMANCE = 'performance';
    const TYPE_SYNC = 'sync';
    const TYPE_ERROR = 'error';
    const TYPE_ALL = 'all';
    const TYPE_DEBUG = 'debug';

    const CAT_PRODUCTS = 'products';
    const CAT_ORDERS = 'orders';
    const CAT_CUSTOMERS = 'customers';
    const CAT_PAYMENTS = 'payments';
    const CAT_ATTRIBUTES = 'attrs';
    const CAT_OTHERS = 'others';
    const CAT_PROFILER = 'profiler';

    public static $categories = [
        self::CAT_PRODUCTS => 'Products',
        self::CAT_ORDERS => 'Orders',
        self::CAT_CUSTOMERS => 'Customers',
        self::CAT_PAYMENTS => 'Payments',
        self::CAT_ATTRIBUTES => 'Attributes',
        self::CAT_OTHERS => 'Others',
        self::CAT_PROFILER => 'Profiler',
    ];

    public static $logLevel = null;

    public static function getCatNameById($cat)
    {
        if (array_key_exists($cat, self::$categories)) {
            return self::$categories[$cat];
        } else {
            return;
        }
    }

    public static function log($message, $type = self::TYPE_DEBUG, $cat = null)
    {
        if (
            php_sapi_name() === 'cli' &&
            class_exists('Illuminate\Support\Debug\Dumper') &&
            function_exists('dd')) {
            (new Dumper())->dump([
                'from' => str_replace(Mage::getBaseDir().'/', '', debug_backtrace()[0]['file']).'() :'.debug_backtrace()[0]['line'],
                'log' => [
                    $type => $message,
                ],
            ]);
        }

        Mage::log(($cat ? '['.self::getCatNameById($cat).'] ' : ' ').$message, self::$logLevel, 'rex.'.$type.'.log');
    }
}
