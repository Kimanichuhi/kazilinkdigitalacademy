<?php

namespace Modules\Payment\Enums;

enum MpesaTransactionStatus: string
{
    case Pending = 'pending';
    case Success = 'success';
    case Failed = 'failed';
    case Cancelled = 'cancelled';
    case Timeout = 'timeout';
}
