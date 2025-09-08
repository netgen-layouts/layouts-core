<?php

declare(strict_types=1);

namespace Netgen\Layouts\Utils;

use Netgen\Layouts\Exception\RuntimeException;

use function array_key_exists;
use function mb_str_split;
use function mb_strlen;
use function mb_strtolower;
use function sprintf;

final class FlagGenerator
{
    private const CHARACTER_MAP = [
        'a' => '&#x1F1E6;',
        'b' => '&#x1F1E7;',
        'c' => '&#x1F1E8;',
        'd' => '&#x1F1E9;',
        'e' => '&#x1F1EA;',
        'f' => '&#x1F1EB;',
        'g' => '&#x1F1EC;',
        'h' => '&#x1F1ED;',
        'i' => '&#x1F1EE;',
        'j' => '&#x1F1EF;',
        'k' => '&#x1F1F0;',
        'l' => '&#x1F1F1;',
        'm' => '&#x1F1F2;',
        'n' => '&#x1F1F3;',
        'o' => '&#x1F1F4;',
        'p' => '&#x1F1F5;',
        'q' => '&#x1F1F6;',
        'r' => '&#x1F1F7;',
        's' => '&#x1F1F8;',
        't' => '&#x1F1F9;',
        'u' => '&#x1F1FA;',
        'v' => '&#x1F1FB;',
        'w' => '&#x1F1FC;',
        'x' => '&#x1F1FD;',
        'y' => '&#x1F1FE;',
        'z' => '&#x1F1FF;',
    ];

    public static function fromCountryCode(string $countryCode): string
    {
        if (mb_strlen($countryCode) !== 2) {
            throw new RuntimeException(sprintf('Invalid country code: %s.', $countryCode));
        }

        $flag = '';

        $characters = mb_str_split(mb_strtolower($countryCode));

        foreach ($characters as $character) {
            if (!array_key_exists($character, self::CHARACTER_MAP)) {
                throw new RuntimeException(sprintf('Invalid country code: %s.', $countryCode));
            }

            $flag .= self::CHARACTER_MAP[$character];
        }

        return $flag;
    }
}
