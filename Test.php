<?php

/*
 * This file is part of the OpenStates Framework (osf) package.
 * (c) Guillaume Ponçon <guillaume.poncon@openstates.com>
 * For the full copyright and license information, please read the LICENSE file distributed with the project.
 */

namespace Osf\Stream;

use Osf\Stream\Text as T;
use Osf\Stream\Html as H;
use Osf\Test\Runner as OsfTest;

/**
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package osf
 * @subpackage test
 */
class Test extends OsfTest
{
    public static function run()
    {
        self::reset();
        
        self::assertEqual(T::crop('AZERTYUIOP', 6), 'AZE...');
        self::assertEqual(T::crop('AZERTY', 6), 'AZERTY');
        self::assertEqual(T::crop('AZERT', 6), 'AZERT');
        self::assertEqual(T::crop('êtreêtre', 6), 'êtr...');
        
        self::assertEqual(T::currencyFormat(45.5), '45.50 €');
        self::assertEqual(T::currencyFormat('6 euros'), '6.00 €');
        
        self::assertEqual(T::ucFirst('bonJour'), 'BonJour');
        self::assertEqual(T::ucFirst('être'), 'Être');
        self::assertEqual(T::ucFirst('êtrE cooL'), 'ÊtrE cooL');
        self::assertEqual(T::ucPhrase('êtrE cooL'), 'Être Cool');
        self::assertEqual(T::ucPhrase(' jean - françois'), 'Jean-François');
        self::assertEqual(T::ucPhrase(' élodie  du timon être-çon '), 'Élodie Du Timon Être-Çon');
        self::assertEqual(T::ucPhrase(' DE LA   RIVE-HIER '), 'De la Rive-Hier');
        
        self::assertEqual(T::formatDate(new \DateTime('2011-01-01T15:03:01.012345Z')), '01/01/2011');
        
        self::assertEqual(T::phoneFormat('0123456789'), '01 23 45 67 89');
        self::assertEqual(T::phoneFormat('012345678'), '012 345 678');
        self::assertEqual(T::phoneFormat('01234567'), '01 23 45 67');
        self::assertEqual(T::phoneFormat('0123456'), '012 34 56');
        self::assertEqual(T::phoneFormat('0 1 2 34 56 7 8 9'), '01 23 45 67 89');
        self::assertEqual(T::phoneFormat('   0123  45678'), '012 345 678');
        self::assertEqual(T::phoneFormat('012345  67'), '01 23 45 67');
        self::assertEqual(T::phoneFormat('01234  56'), '012 34 56');
        self::assertEqual(T::phoneFormat('  +=djklm33 qsf1 sd2 34f sq5fq6 7qsdf qs8 9'), '+33 (1) 23 45 67 89');
        self::assertEqual(T::phoneFormat('  +=djklm33 q0sf1 sd2 34f sq5fq6 7qsdf qs8 9'), '+33 (1) 23 45 67 89');
        self::assertEqual(T::phoneFormat('   0123sq sq 45sdf678'), '012 345 678');
        self::assertEqual(T::phoneFormat('0123sqdf4qs5  67 qdsfsq'), '01 23 45 67');
        self::assertEqual(T::phoneFormat('   + sdfjkmf01s23f4  qsd5+ 6 '), '+012 34 56');
        self::assertEqual(T::phoneFormat('+33123456789'), '+33 (1) 23 45 67 89');
        self::assertEqual(T::phoneFormat('+33(1)23456789'), '+33 (1) 23 45 67 89');
        self::assertEqual(T::phoneFormat('+33123456789'), '+33 (1) 23 45 67 89');
        self::assertEqual(T::phoneFormat('+ 33  (1 ) 234  5 6 7 89'), '+33 (1) 23 45 67 89');
        
        self::assertEqual(T::explodeColor('#fa34d9'), [250, 52, 217]);
        self::assertEqual(T::explodeColor('#fA34d9'), [250, 52, 217]);
        self::assertEqual(T::explodeColor(' FA34D9'), [250, 52, 217]);
        self::assertEqual(T::explodeColor('#fi34d9'), [null, null, null]);
        self::assertEqual(T::explodeColor('#fi34d9', 1, 2, 3), [1, 2, 3]);
        self::assertEqual(T::explodeColor('#FA34D9', 1, 2, 3), [250, 52, 217]);
        
        self::assertEqual(T::transliterate('être & avoir'), 'etre & avoir');
        
        self::assertEqual(H::toText('<b>Bonjour</b>'), 'Bonjour');
        
        self::assertEqual(T::fromCamelCaseToLower('CamelCase'), 'camel-case');
        self::assertEqual(T::fromCamelCaseToLower('CamelCase', '_'), 'camel_case');
        self::assertEqual(T::fromCamelCaseToLower('A CamelCase VerySuperPhrase'), 'a camel-case very-super-phrase');
        
        return self::getResult();
    }
}
