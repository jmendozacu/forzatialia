<?php

/**
 * Statuses of cron task on POS System (for bulk synchronisation methods only)
 */
class POS_System_Model_Task_Status extends Mage_Core_Model_Abstract
{

    const STATUS_WAIT       = 5;
    const STATUS_SCHEDULE   = 10;
    const STATUS_INPROGRESS = 20;
    const STATUS_INPROSHE   = 30;
    const STATUS_DONE       = 40;
    const STATUS_CANCEL     = 50;

}
