<?php


namespace App;


use phpDocumentor\Reflection\Types\Self_;

class InvoiceEnum
{
    const STATUS_NEW = 0;
    const STATUS_FLORIS_PICKED = 1;
    const STATUS_FLORIS_DONE = 2;
    const STATUS_SEND_IMAGE_CUSTOMER = 3;
    const STATUS_CUSTOMER_CONFIRMED = 4;
    const STATUS_SHIPPER_PICKED = 5;
    const STATUS_SHIPPER_DONE = 6;

    public static function getStatus()
    {
        return [
            0 => 'Chưa làm',
            1 => 'Đã nhận',
            2 => 'Làm xong',
            3 => 'Đã gửi ảnh',
            4 => 'Khách ok',
            5 => 'Đang giao',
            6 => 'Giao thành công',
        ];
    }

    public static function getStatusFlorist()
    {
        return [
            0 => 'Chưa làm',
            1 => 'Đã nhận',
            2 => 'Làm xong',
        ];
    }

    public static function getStatusShip()
    {
        return [
            5 => 'Đang giao',
            6 => 'Giao thành công',
        ];
    }

    public static function getStatusName($id)
    {
        $status = [
            0 => 'Chưa làm',
            1 => 'Đã nhận',
            2 => 'Làm xong',
            3 => 'Đã gửi ảnh',
            4 => 'Khách ok',
            5 => 'Đang giao',
            6 => 'Giao thành công',
        ];

        return $status[$id] ?? 'Chưa làm';
    }
}
