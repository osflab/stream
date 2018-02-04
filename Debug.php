<?php

/*
 * This file is part of the OpenStates Framework (osf) package.
 * (c) Guillaume Ponçon <guillaume.poncon@openstates.com>
 * For the full copyright and license information, please read the LICENSE file distributed with the project.
 */

namespace Osf\Stream;

/**
 * Debug tools
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package osf
 * @subpackage stream
 */
class Debug
{
    /**
     * Get info about a variable type
     * @param mixed $var
     * @return string
     */
    public static function getType($var): string
    {
        $type = gettype($var);
        if ($type === 'object') {
            $type .= ': ' . get_class($var);
        }
        return (string) $type;
    }
}
