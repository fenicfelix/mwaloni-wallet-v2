<?php

namespace Wallet\Core\Http\Traits;

trait MwaloniAdmin
{
    // Trait methods and properties for MwaloniAdmin

    function cleanPhoneNumber($phone_number)
    {
        $phone_number = "254" . substr(str_replace(" ", "", $phone_number), -9);
        return $phone_number;
    }
}