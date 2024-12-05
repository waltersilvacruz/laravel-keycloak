<?php

namespace TCEMT\KeyCloak\Helpers;

class KeyCloak {

    /**
     * Check if users has permission to resource
     */
    public function allows(string $resource): bool
    {
        return true;
    }
}
