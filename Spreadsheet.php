<?php

/*
 * This file is part of the OpenStates Framework (osf) package.
 * (c) Guillaume Ponçon <guillaume.poncon@openstates.com>
 * For the full copyright and license information, please read the LICENSE file distributed with the project.
 */

namespace Osf\Stream;

use PhpOffice\PhpSpreadsheet\Spreadsheet as PssSpreadsheet;
use PhpOffice\PhpSpreadsheet\Settings as PssSettings;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Cache\Bridge\SimpleCache\SimpleCacheBridge;
use Cache\Adapter\Redis\RedisCachePool;
use Osf\Container\VendorContainer;

/**
 * phpspreadsheet proxy
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package osf
 * @subpackage stream
 */
class Spreadsheet
{
    /**
     * FR: Crée un nouveau tableur
     * @return PssSpreadsheet
     */
    public static function newSpreadsheet(): PssSpreadsheet
    {
        self::init();
        return new PssSpreadsheet();
    }
    
    /**
     * FR: Charge un fichier
     * @param string $filename
     * @return PssSpreadsheet
     */
    public static function loadSpreadsheet(string $filename): PssSpreadsheet
    {
        self::init();
        return IOFactory::load($filename);
    }
    
    /**
     * Lazy initialization
     * @staticvar boolean $initialized
     */
    protected static function init()
    {
        static $initialized = false;
        
        if (!$initialized) {
            VendorContainer::loadComposer();
            self::initCache();
        }
    }
    
    /**
     * FR: Cache redis pour php spreadsheet
     */
    protected static function initCache()
    {
        $client = VendorContainer::getRedis();
        $pool = new RedisCachePool($client);
        $simpleCache = new SimpleCacheBridge($pool);
        PssSettings::setCache($simpleCache);
    }
}
