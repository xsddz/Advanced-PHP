<?php

class Yps_CommonUtils {
    /**
     * fail check and print msg
     */
    public static function failCheck($status, $failStatus, $msg) {
        if ($status === $failStatus) {
            printf("{$msg}\t\t.......\t\033[31m[FAIL]\033[0m\n");
            throw new Exception("check fail: {$msg}");
        } else {
            printf("{$msg}\t\t.......\t[OK]\n");
        }
    }
}


