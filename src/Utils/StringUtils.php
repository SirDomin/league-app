<?php

namespace App\Utils;

class StringUtils
{
    public static function extractColumnType($commentString): ?string {
        $pattern = '/@ORM\\\(ManyToOne|OneToMany|OneToOne|ManyToMany)\b/';
        preg_match($pattern, $commentString, $matches);

        if (!empty($matches)) {
            return 'relation';
        }

        $pattern = '/@ORM\\\Column\(type="(.*?)"\)/';
        preg_match($pattern, $commentString, $matches);

        if (isset($matches[1])) {
            return $matches[1];
        } else {
            return 'string';
        }
    }
}
