<?php

namespace modules\Hotel\Enums;

enum PaymentStatus: string
{
    case UNPAID = 1;
    case PARTIAL = 2; // Untuk sistem deposit/DP
    case PAID = 3;
    case REFUNDED = 4;
}
