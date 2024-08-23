<?php

namespace App\Enums;

/**
 * Enum representing 20 of the most common languages.
 * Uses ISO 639-1 two-letter codes as identifiers.
 */
enum CommonLanguage: string
{
    case ENGLISH = 'en';
    case SPANISH = 'es';
    case CHINESE = 'zh';
    case HINDI = 'hi';
    case ARABIC = 'ar';
    case BENGALI = 'bn';
    case PORTUGUESE = 'pt';
    case RUSSIAN = 'ru';
    case JAPANESE = 'ja';
    case GERMAN = 'de';
    case FRENCH = 'fr';
    case ITALIAN = 'it';
    case KOREAN = 'ko';
    case TURKISH = 'tr';
    case VIETNAMESE = 'vi';
    case POLISH = 'pl';
    case DUTCH = 'nl';
    case UKRAINIAN = 'uk';
    case PERSIAN = 'fa';
    case INDONESIAN = 'id';

    /**
     * Get the full name of the language in English.
     *
     * @return string The full name of the language
     */
    public function fullName(): string
    {
        return match ($this) {
            self::ENGLISH => 'English',
            self::SPANISH => 'Spanish',
            self::CHINESE => 'Chinese',
            self::HINDI => 'Hindi',
            self::ARABIC => 'Arabic',
            self::BENGALI => 'Bengali',
            self::PORTUGUESE => 'Portuguese',
            self::RUSSIAN => 'Russian',
            self::JAPANESE => 'Japanese',
            self::GERMAN => 'German',
            self::FRENCH => 'French',
            self::ITALIAN => 'Italian',
            self::KOREAN => 'Korean',
            self::TURKISH => 'Turkish',
            self::VIETNAMESE => 'Vietnamese',
            self::POLISH => 'Polish',
            self::DUTCH => 'Dutch',
            self::UKRAINIAN => 'Ukrainian',
            self::PERSIAN => 'Persian',
            self::INDONESIAN => 'Indonesian',
        };
    }

    /**
     * Get an array of all language codes.
     *
     * @return array An array of all language codes
     */
    public static function getCodes(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get an associative array of language codes and their full names.
     *
     * @return array An associative array where keys are language codes and values are full names
     */
    public static function getNamedList(): array
    {
        $list = [];
        foreach (self::cases() as $case) {
            $list[$case->value] = $case->fullName();
        }
        return $list;
    }
}
