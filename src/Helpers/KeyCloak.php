<?php

namespace TCEMT\KeyCloak\Helpers;

class KeyCloak {

    /**
     * Check if users has permission to resource
     */
    public function allows(string $resource): bool
    {
        return Gate::allows($resource);
    }

    /**
     * Check if users has permission to resource
     */
    public function deny(string $resource): bool
    {
        return Gate::denies($resource);
    }
}
