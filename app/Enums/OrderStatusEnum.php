<?php

namespace App\Enums;

enum OrderStatusEnum: int
{
    case Pending = 1;
    case Confirmed = 2;
    case Failed = 3;
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Не подтверждён',
            self::Confirmed => 'Подтверждён',
            self::Failed => 'Ошибка',
        };
    }
}
