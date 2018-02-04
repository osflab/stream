<?php

/*
 * This file is part of the OpenStates Framework (osf) package.
 * (c) Guillaume Ponçon <guillaume.poncon@openstates.com>
 * For the full copyright and license information, please read the LICENSE file distributed with the project.
 */

namespace Osf\Stream;

/**
 * Html tools and helpers
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates 2013
 * @version 1.0
 * @since OSF-2.0 - 7 déc. 2013
 * @package osf
 * @subpackage stream
 */
class Html
{
    /**
     * Build an html element
     * @param string $name
     * @param array $attributes
     * @param string $content
     * @param bool $closeEmptyTag
     * @return string
     */
    public static function buildHtmlElement($name, array $attributes = [], $content = null, $closeEmptyTag = true, array $cssClasses = []): string
    {
        $html = '<' . $name . self::buildAttrs($attributes, $cssClasses);
        if ($content !== null) {
            $html .= '>' . $content . '</' . $name . '>';
        } else {
            $html .= $closeEmptyTag ? ' />' : '>';
        }
        return $html;
    }
    
    /**
     * Transform ['a' => 'b'] to ' a="b"' ...
     * @param array $attributes
     * @return string
     */
    public static function buildAttrs(array $attributes, array $cssClasses = []): string
    {
        $output = '';
        if ($cssClasses) {
            if (array_key_exists('class', $attributes) && $attributes['class']) {
                $attributes['class'] .= ' ' . implode(' ', $cssClasses);
            } else {
                $attributes['class'] = implode(' ', $cssClasses);
            }
        }
        foreach ($attributes as $key => $value) {
            $output .= ' ' . $key . ($value !== null ? '="' . str_replace('"', '\"', $value) . '"' : '');
        }
        return $output;
    }
    
    /**
     * Build an html script element
     * @param mixed $script
     * @param array $attributes
     * @return string
     */
    public static function buildHtmlScript($script, array $attributes = []): string
    {
        $scriptCleaned = trim($script);
        if ($scriptCleaned !== '') {
            return self::buildHtmlElement('script', $attributes, "\n" . $scriptCleaned . "\n");
        }
        return '';
    }
    
    /**
     * Convert html to text using html2text
     * @see https://github.com/soundasleep/html2text
     * @param string $html
     * @return string
     */
    public static function toText($html, bool $ignoreErrors = false): string
    {
        return (string) (new \Html2Text\Html2Text())->convert((string) $html, $ignoreErrors);
    }
    
    /**
     * htmlspecialchars
     * @param mixed $html
     * @return string
     */
    public static function escape($content, bool $nl2br = false): string
    {
        $escaped = htmlspecialchars((string) $content);
        return $nl2br ? nl2br($escaped) : $escaped;
    }
}
