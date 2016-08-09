<?php

class POS_System_Model_System_Config_Source_Cron extends Mage_Core_Model_Abstract
{
    public function toOptionArray()
    {
        // Loop through hours (24)
        for ($hour = 0; $hour < 24; ++$hour) {
            // Loop through every 15 minutes of each houw
            for ($minute = 0; $minute < 60; $minute += 5) {
                $options[] = [

                    // Cron-format
                    'value' => "{$minute} {$hour} * * *",

                    // "1:00 am"
                    'label' => sprintf(
                        '%d:%s %s',
                        $hour % 12 ?: 12,
                        str_pad($minute, 2, '0', STR_PAD_LEFT),
                        $hour < 12 ? 'am' : 'pm'
                    ),
                ];
            }
        }

        return $options;
    }
}
