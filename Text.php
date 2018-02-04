<?php

/*
 * This file is part of the OpenStates Framework (osf) package.
 * (c) Guillaume Ponçon <guillaume.poncon@openstates.com>
 * For the full copyright and license information, please read the LICENSE file distributed with the project.
 */

namespace Osf\Stream;

use DateTime;

/**
 * Text tools and helpers, compatibles Unicode
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package osf
 * @subpackage stream
 */
class Text
{
    const ENCODING = 'UTF-8';
    const ENCODING_ISO = 'ISO-8859-15';
    const VALID_CONSTANTS = ['APP_NAME', 'APP_SNAM', 'APP_HOST'];
    const TRANSITION_WORDS = ['de', 'la', 'les', 'le', 'des', 'sur', 'en', 'sous'];
    
    /**
     * Crop a string with a '...' if too long
     * @param type $txt
     * @param int $maxChars
     * @param string $etc
     * @return string
     */
    public static function crop($txt, int $maxChars = 20, string $etc = '...'): string
    {
        $txt = (string) $txt;
        if (mb_strlen($txt, self::ENCODING) > $maxChars) {
            return mb_substr($txt, 0, $maxChars - mb_strlen($etc, self::ENCODING), self::ENCODING) . $etc;
        }
        return $txt;
    }
    
    /**
     * Cast a value to string unless it's null
     * @param mixed $val
     * @return string|null
     */
    public static function strOrNull($val): ?string
    {
        return $val === null ? null : (string) $val;
    }
    
    /**
     * Return int if numeric, null otherwise
     * @param mixed $val
     * @return int|null
     */
    public static function numericOrNull($val): ?int
    {
        return is_numeric($val) ? (int) $val : null;
    }
    
    /**
     * Format a phone number
     * @param mixed $phoneNumber
     * @return string
     */
    public static function phoneFormat($phoneNumber): string
    {
        if (!$phoneNumber) {
            return (string) $phoneNumber;
        }
        $prefix = trim($phoneNumber)[0] == '+';
        $value = preg_replace('/[^0-9]/', '', (string) $phoneNumber);
        $valueLen = mb_strlen($value, self::ENCODING);
        if ($prefix && $valueLen == 11) {
            $pattern[] = '/([0-9][0-9])([0-9])([0-9][0-9])([0-9][0-9])([0-9][0-9])([0-9][0-9])/';
            $replace[] = '$1 ($2) $3 $4 $5 $6';
        } else if ($prefix && $valueLen == 12 && $value[2] == 0) {
            $pattern[] = '/([0-9][0-9]).([0-9])([0-9][0-9])([0-9][0-9])([0-9][0-9])([0-9][0-9])/';
            $replace[] = '$1 ($2) $3 $4 $5 $6';
        } elseif ($valueLen == 12) {
            $pattern[] = '/^([0-9][0-9][0-9])([0-9][0-9][0-9])([0-9][0-9][0-9])([0-9][0-9][0-9])$/';
            $replace[] = '$1 $2 $3 $4 $5';
        } elseif ($valueLen == 11) {
            $pattern[] = '/^([0-9][0-9][0-9])([0-9][0-9])([0-9][0-9])([0-9][0-9])([0-9][0-9])$/';
            $replace[] = '$1 $2 $3 $4';
        } elseif ($valueLen == 10) {
            $pattern[] = '/^([0-9][0-9])([0-9][0-9])([0-9][0-9])([0-9][0-9])([0-9][0-9])$/';
            $replace[] = '$1 $2 $3 $4 $5';
        } elseif ($valueLen == 9) {
            $pattern[] = '/^([0-9][0-9][0-9])([0-9][0-9][0-9])([0-9][0-9][0-9])$/';
            $replace[] = '$1 $2 $3';
        } elseif ($valueLen == 8) {
            $pattern[] = '/^([0-9][0-9])([0-9][0-9])([0-9][0-9])([0-9][0-9])$/';
            $replace[] = '$1 $2 $3 $4';
        } elseif ($valueLen == 7) {
            $pattern[] = '/^([0-9][0-9][0-9])([0-9][0-9])([0-9][0-9])$/';
            $replace[] = '$1 $2 $3';
        } elseif ($valueLen == 6) {
            $pattern[] = '/^([0-9][0-9])([0-9][0-9])([0-9][0-9])$/';
            $replace[] = '$1 $2 $3';
        }
        
        if (!isset($pattern)) {
            return ($prefix ? '+' : '') . $value;
        }
        return ($prefix ? '+' : '') . trim(preg_replace($pattern, $replace, $value));
    }
    
    /**
     * Removes useless chars from a telephone number
     * @param string $phoneNumber
     * @return string
     */
    public static function phoneClean($phoneNumber): string
    {
        return preg_replace('/[^+0-9]/', '', (string) $phoneNumber);
    }
    
    /**
     * Format a price
     * @param mixed $value
     * @param string $currency
     * @param bool $withSymbol
     * @return string
     */
    public static function currencyFormat($value, $currency = 'EUR', bool $withSymbol = true): string
    {
        $number = number_format((float) $value, 2, '.', ',');
        switch ($currency) {
            case 'EUR' : 
                return $number . ($withSymbol ? ' €' : '');
            case 'DOL' : 
                return ($withSymbol ? '$' : '') . $number;
            default : 
                return $number;
        }
    }
    
    /**
     * jean-albert dupont -> Jean-Albert Dupont
     * @param mixed $str
     * @return string
     */
    public static function ucPhrase($str): string
    {
        $str = mb_strtolower(self::cleanPhrase($str), self::ENCODING);
        $names = explode(' ', $str);
        array_walk($names, 'self::ucFirstTouch');
        $names = explode('-', implode(' ', $names));
        array_walk($names, 'self::ucFirstTouch');
        return implode('-', $names);
    }
    
    /**
     * @param mixed $value
     * @return string
     */
    protected static function ucFirstTouch(&$value): string
    {
        $value = self::ucFirst($value, true);
        return $value;
    }
    
    /**
     * " Jean -  Albert    Dupont " -> "Jean-Albert Dupont"
     * @param string $str
     * @return string
     */
    public static function cleanPhrase($str)
    {
        return preg_replace(['/  +/', '/ *\- */'], [' ', '-'], trim($str));
    }
    
    /**
     * unicode strtolower
     * @param mixed $txt
     * @param bool $cleanPhrase
     * @return string
     */
    public static function toLower($txt, bool $cleanPhrase = false): string
    {
        $txt = $cleanPhrase ? self::cleanPhrase($txt) : $txt;
        return mb_strtolower($txt, self::ENCODING);
    }
    
    /**
     * unicode strtoupper
     * @param mixed $txt
     * @param bool $cleanPhrase
     * @return string
     */
    public static function toUpper($txt, bool $cleanPhrase = false): string
    {
        $txt = $cleanPhrase ? self::cleanPhrase($txt) : $txt;
        return mb_strtoupper($txt, self::ENCODING);
    }
    
    /**
     * unicode ucfirst
     * @param mixed $txt
     * @return string
     */
    public static function ucFirst($txt, bool $transitionWordToLower = false): string
    {
        if ($transitionWordToLower) {
            $txtLower = self::toLower($txt);
            if (in_array($txtLower, self::TRANSITION_WORDS)) {
                return $txt;
            }
        }
        $firstChar = mb_substr($txt, 0, 1, self::ENCODING);
        $then = mb_substr($txt, 1, 2147483647, self::ENCODING);
        return mb_strtoupper($firstChar, self::ENCODING) . $then;
    }
    
    /**
     * unicode strlen
     * @param mixed $txt
     * @return int
     */
    public static function strLen($txt)
    {
        return mb_strlen((string) $txt, self::ENCODING);
    }
    
    /**
     * 
     * @param  $txt
     * @return string
     */
    
    /**
     * Lite transliteration (replace accents, special chars and spaces)
     * @param mixed $txt
     * @param bool $convertPoint
     * @param bool $allowWhiteSpaces
     * @param string $regex
     * @return string
     */
    public static function getAlpha($txt, bool $convertPoint = false, bool $allowWhiteSpaces = false, string $regex = '/[^a-zA-Z0-9 ._+-]/'): string
    {
        $from = utf8_decode("ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüŸŶÿŷÑñ /" . ($convertPoint ? '.' : ''));
        $to = "aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuYYyynn" . ($allowWhiteSpaces ? ' ' : '-') . "-" . ($convertPoint ? '-' : '');
        $txt = utf8_encode(strtr(utf8_decode($txt), $from, $to));
        return preg_replace($regex, '', $txt);
    }
    
    /**
     * Build a percentage from a float (0.1 = 10%)
     * @param mixed $value
     * @param bool $withSymbol
     * @param int $precision
     * @return string
     */
    public static function toPercentage($value, bool $withSymbol = true, $precision = 0): string
    {
        return self::percentageFormat(100 * (float) $value, $withSymbol, $precision);
    }
    
    /**
     * Round and add the % symbol
     * @param mixed $value
     * @param bool $withSymbol
     * @param int $precision
     * @return string
     */
    public static function percentageFormat($value, bool $withSymbol = true, int $precision = 0): string
    {
        $percentValue = round((float) $value, $precision);
        return $percentValue . ($withSymbol ? ' %' : '');
    }
    
    /**
     * Format a date with the specified locale
     * @param DateTime $date
     * @param string $locale
     * @param bool $short
     * @return string
     * @todo use an external library ?
     */
    public static function formatDate(DateTime $date, $locale = null, bool $short = false)
    {
        $locale = $locale ?? 'fr';
        $localDates = [
            'fr'    => $short ? 'd/m/y' : 'd/m/Y',
            'en'    => $short ? 'd/m/y' : 'd/m/Y',
            'en_US' => $short ? 'm/d/y' : 'm/d/Y'
        ];
        return (string) $date->format($localDates[$locale]);
    }
    
    /**
     * Date format (long : dimanche 1 octobre 2017).
     * @param DateTime $date
     * @param string $locale
     * @param bool $short
     * @return string
     * @todo use an external library ?
     */
    public static function formatDateLong(DateTime $date, $locale = null): string
    {
        $locale = $locale ?? 'fr';
        $localDates = [
            'fr'    => '%A %d %B %Y',
        ];
        return self::toLower(strftime($localDates[$locale], $date->getTimestamp()));
    }
    
    /**
     * Format a datetime with the specified locale
     * @param DateTime $date
     * @param string $locale
     * @return string
     * @todo use an external library ?
     */
    public static function formatDateTime(DateTime $date, $locale = null, string $mask = null, bool $short = false): string
    {
        $locale = $locale ?? 'fr';
        if ($mask === null) {
            $localDates = [
                'fr'    => $short ? __("d/m/y H:i") : __("d/m/Y H:i"),
                'en'    => $short ? __("d/m/y H:i") : __("d/m/Y H:i"),
                'en_US' => $short ? __("m/d/y H:i") : __("m/d/Y H:i")
            ];
            $mask = $localDates[$locale];
        }
        return (string) $date->format($mask);
    }
    
    /**
     * Color transformation : #FF00FF -> [255, 0, 255]
     * @param string $hexColor
     * @param int $dr
     * @param int $dg
     * @param int $db
     * @return array
     */
    public static function explodeColor($hexColor, int $dr = null, int $dg = null, int $db = null): array
    {
        $colors = [];
        preg_match_all('/^#?([0-9A-Fa-f]{2})([0-9A-Fa-f]{2})([0-9A-Fa-f]{2})$/', trim($hexColor), $colors);
        if (isset($colors[1][0]) && isset($colors[2][0]) && isset($colors[3][0])) {
            return [hexdec($colors[1][0]), hexdec($colors[2][0]), hexdec($colors[3][0])];
        }
        return [$dr, $dg, $db];
    }
    
    /**
     * some_phrase => SomePhrase
     * @param mixed $word
     * @param bool $ucFirstWord
     * @return string
     */
    public static function camelCase($word, bool $ucFirstWord = true): string
    {
        static $words = [];
        
        if (!isset($words[$word])) {
            $words[$word] = str_replace(' ', '', ucwords(strtr($word, '_', ' ')));
            if (!$ucFirstWord) {
                substr_replace($words[$word], $word{0}, 0, 1);
            }
        }
        return $words[$word];
    }
    
    /**
     * CamelCase -> camel-case
     * @param string $word
     * @param string $separator
     * @return string
     */
    public static function fromCamelCaseToLower($word, string $separator = '-'): string
    {
        $str = '';
        $active = false;
        $word = (string) $word;
        for ($i = 0; $i <= mb_strlen($word); $i++) {
            $char = mb_substr($word, $i, 1, self::ENCODING);
            $str .= ($active && $char >= 'A' && $char <= 'Z' ? $separator : '') . mb_strtolower($char);
            $active = $char !== ' ';
        }
        return $str;
    }
    
    /**
     * Transliteration for a search engine
     * @param string|null $txt
     * @return string|null
     */
    public static function transliterate($txt)
    {
        if ($txt === null) {
            return null;
        }
        return strtolower(\Patchwork\Utf8::toAscii((string) $txt));
    }
    
    /**
     * Replace {C:CONST_NAME} by the constant value
     * @param type $txt
     */
    public static function substituteConstants($txt)
    {
        return preg_replace_callback('/\{C:([A-Z_]+)\}/', ['\Osf\Stream\Text', 'filterConstant'], $txt);
    }
    
    /**
     * Substitute constant in the $values[1] (see self::VALID_CONSTANTS)
     * @param array $values
     * @return mixed
     */
    protected static function filterConstant(array $values)
    {
        if (isset($values[1]) && in_array($values[1], self::VALID_CONSTANTS)) {
            $values[1] = constant($values[1]);
        }
        return $values[1];
    }
    
    /**
     * Format a siret number with 14 digits
     * @param string $value
     * @return string
     */
    public static function formatSiret($value): string
    {
        $value = (string) (int) $value;
        if (!self::isSiret($value)) {
            return '';
        }
        return preg_replace('/^([0-9]{3})([0-9]{3})([0-9]{3})([0-9]{5})$/', '$1 $2 $3 $4', $value);
    }
    
    /**
     * Extract french siren from siret
     * @param string $siret
     * @param bool $format
     * @return string
     */
    public static function siretToSiren($siret, bool $format = false): string
    {
        $value = (string) (int) $siret;
        if (!self::isSiret($value)) {
            return '';
        }
        return $format 
             ? preg_replace('/^([0-9]{3})([0-9]{3})([0-9]{3})([0-9]{5})$/', '$1 $2 $3', $value)
             : substr($value, 0, 9);
    }
    
    /**
     * @param string $value
     * @return bool
     */
    protected static function isSiret(string $value): bool
    {
        return preg_match('/^[0-9]{14}$/', $value);
    }
    
    /**
     * Intracom TVA formatting
     * @param string $value
     * @return string
     */
    public static function formatTvaIntra($value)
    {
        $value = self::toUpper(trim(str_replace(' ', '', $value)));
        return preg_replace('/^([A-Z]{2})([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{3})$/', '$1 $2 $3 $4 $5', $value);
    }
    
    /**
     * UTF8 -> iso-8859-x
     * @param string $unicodeString
     * @return string
     */
    public static function toIso($unicodeString)
    {
        return iconv(self::ENCODING, self::ENCODING_ISO, (string) $unicodeString);
    }
    
    /**
     * iso-8859-x -> UTF-8
     * @param string $isoString
     * @return string
     */
    public static function toUnicode($isoString)
    {
        return iconv(self::ENCODING_ISO, self::ENCODING, (string) $isoString);
    }
}
