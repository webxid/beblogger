<?php

namespace WebXID\BeBlogger\Helpers;

class ConvertString
{
    /**
     * @param string $string
     *
     * @return string
     */
    public static function slugify(string $string)
    {
        $string = trim($string);

        $tranclit = [
            'Київ' => 'Kyiv',
            "А"=>"a","Б"=>"b","В"=>"v","Г"=>"g","Ґ"=>"g","Д"=>"d","Е"=>"e","Є"=>"ye","Ё"=>"e","Ж"=>"zh","З"=>"z","И"=>"y","І"=>"i","Ї"=>"yi","Й"=>"j","К"=>"k", "Л"=>"l","М"=>"m","Н"=>"n","О"=>"o","П"=>"p", "Р"=>"r","С"=>"s","Т"=>"t","У"=>"u","Ф"=>"f", "Х"=>"kh","Ц"=>"ts","Ч"=>"ch","Ш"=>"sh","Щ"=>"sch", "Ы"=>"y","Э"=>"e","Ю"=>"yu","Я"=>"ya",
            "Ь"=>"","Ъ"=>"",

            "а"=>"a","б"=>"b","в"=>"v","г"=>"g","ґ"=>"g","д"=>"d","е"=>"e","є"=>"ye","ё"=>"e","ж"=>"zh","з"=>"z","и"=>"y","і"=>"i","ї"=>"yi","й"=>"j","к"=>"k", "л"=>"l","м"=>"m","н"=>"n","о"=>"o","п"=>"p", "р"=>"r","с"=>"s","т"=>"t","у"=>"u","ф"=>"f", "х"=>"kh","ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch", "ы"=>"y","э"=>"e","ю"=>"yu","я"=>"ya",
            "ь"=>"", "ъ"=>"",

            ") "=>"", " ("=>"", " - "=>"-", " "=>"-", "$"=>"", "@"=>"", "!"=>"", "?"=>"", "&"=>"-",
            "="=>"-", "|"=>"-", "/"=>"-", "\\"=>"-", "#"=>"", "\""=>"", "'"=>"", ";"=>"", ":"=>"", ", "=>"-", ","=>"",
            "."=>"-", "+"=>"_", "("=>"", ")"=>"", "*"=>"", "^"=>"", "№"=>"", ">"=>"", "<"=>"", "%"=>"", "`"=>"",
            ']'=>'', '['=>'', '{'=>'', '}'=>'',"»"=>'',"«"=>'',
        ];

        return strtolower(trim(strtr($string, $tranclit), '-_'));
    }
}
