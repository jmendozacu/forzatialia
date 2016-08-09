<?php

class POS_System_Model_System_Config_Backend_Comment
{
    public function getCommentText($element, $currentValue)
    {
        $message = 'Do not change the Sales Channel ID without first confirming all required products have been set up for the new sales channel, and all other products disabled on the website.';
        $message = '<span style="color:#df280a">'.$message.'</span>';

        return $message;
    }
}
