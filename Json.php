<?php

/*
 * This file is part of the OpenStates Framework (osf) package.
 * (c) Guillaume Ponçon <guillaume.poncon@openstates.com>
 * For the full copyright and license information, please read the LICENSE file distributed with the project.
 */

namespace Osf\Stream;

use Osf\Container\OsfContainer as Container;

/**
 * Json manipulations
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package osf
 * @subpackage stream
 */
class Json
{
    public static function encode($value, bool $prettyPrintIfDev = true)
    {
        return json_encode($value, self::getOptions($prettyPrintIfDev));
    }
    
    public static function decode($value)
    {
        return json_decode($value);
    }
    
    protected static function getOptions(bool $prettyPrintIfDev)
    {
        $pp = $prettyPrintIfDev && Container::getApplication()->isDevelopment();
        return JSON_UNESCAPED_UNICODE | ($pp ? JSON_PRETTY_PRINT : 0);
    }
}
