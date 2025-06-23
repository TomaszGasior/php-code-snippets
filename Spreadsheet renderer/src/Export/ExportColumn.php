<?php

namespace App\Export;

enum ExportColumn: string
{
    case ID = 'id';
    case NAME = 'name';
    case PRICE = 'price';
}
