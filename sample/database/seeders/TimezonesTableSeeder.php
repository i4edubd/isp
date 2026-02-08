<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimezonesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        DB::table('timezones')->delete();

        DB::table('timezones')->insert(array(
            0 =>
            array(
                'id' => 1,
                'country_id' => 1,
                'iso2' => 'AF',
                'name' => 'Asia/Kabul',
            ),
            1 =>
            array(
                'id' => 2,
                'country_id' => 2,
                'iso2' => 'AX',
                'name' => 'Europe/Mariehamn',
            ),
            2 =>
            array(
                'id' => 3,
                'country_id' => 3,
                'iso2' => 'AL',
                'name' => 'Europe/Tirane',
            ),
            3 =>
            array(
                'id' => 4,
                'country_id' => 4,
                'iso2' => 'DZ',
                'name' => 'Africa/Algiers',
            ),
            4 =>
            array(
                'id' => 5,
                'country_id' => 5,
                'iso2' => 'AS',
                'name' => 'Pacific/Pago_Pago',
            ),
            5 =>
            array(
                'id' => 6,
                'country_id' => 6,
                'iso2' => 'AD',
                'name' => 'Europe/Andorra',
            ),
            6 =>
            array(
                'id' => 7,
                'country_id' => 7,
                'iso2' => 'AO',
                'name' => 'Africa/Luanda',
            ),
            7 =>
            array(
                'id' => 8,
                'country_id' => 8,
                'iso2' => 'AI',
                'name' => 'America/Anguilla',
            ),
            8 =>
            array(
                'id' => 9,
                'country_id' => 9,
                'iso2' => 'AQ',
                'name' => 'Antarctica/Casey',
            ),
            9 =>
            array(
                'id' => 10,
                'country_id' => 9,
                'iso2' => 'AQ',
                'name' => 'Antarctica/Davis',
            ),
            10 =>
            array(
                'id' => 11,
                'country_id' => 9,
                'iso2' => 'AQ',
                'name' => 'Antarctica/DumontDUrville',
            ),
            11 =>
            array(
                'id' => 12,
                'country_id' => 9,
                'iso2' => 'AQ',
                'name' => 'Antarctica/Mawson',
            ),
            12 =>
            array(
                'id' => 13,
                'country_id' => 9,
                'iso2' => 'AQ',
                'name' => 'Antarctica/McMurdo',
            ),
            13 =>
            array(
                'id' => 14,
                'country_id' => 9,
                'iso2' => 'AQ',
                'name' => 'Antarctica/Palmer',
            ),
            14 =>
            array(
                'id' => 15,
                'country_id' => 9,
                'iso2' => 'AQ',
                'name' => 'Antarctica/Rothera',
            ),
            15 =>
            array(
                'id' => 16,
                'country_id' => 9,
                'iso2' => 'AQ',
                'name' => 'Antarctica/Syowa',
            ),
            16 =>
            array(
                'id' => 17,
                'country_id' => 9,
                'iso2' => 'AQ',
                'name' => 'Antarctica/Troll',
            ),
            17 =>
            array(
                'id' => 18,
                'country_id' => 9,
                'iso2' => 'AQ',
                'name' => 'Antarctica/Vostok',
            ),
            18 =>
            array(
                'id' => 19,
                'country_id' => 10,
                'iso2' => 'AG',
                'name' => 'America/Antigua',
            ),
            19 =>
            array(
                'id' => 20,
                'country_id' => 11,
                'iso2' => 'AR',
                'name' => 'America/Argentina/Buenos_Aires',
            ),
            20 =>
            array(
                'id' => 21,
                'country_id' => 11,
                'iso2' => 'AR',
                'name' => 'America/Argentina/Catamarca',
            ),
            21 =>
            array(
                'id' => 22,
                'country_id' => 11,
                'iso2' => 'AR',
                'name' => 'America/Argentina/Cordoba',
            ),
            22 =>
            array(
                'id' => 23,
                'country_id' => 11,
                'iso2' => 'AR',
                'name' => 'America/Argentina/Jujuy',
            ),
            23 =>
            array(
                'id' => 24,
                'country_id' => 11,
                'iso2' => 'AR',
                'name' => 'America/Argentina/La_Rioja',
            ),
            24 =>
            array(
                'id' => 25,
                'country_id' => 11,
                'iso2' => 'AR',
                'name' => 'America/Argentina/Mendoza',
            ),
            25 =>
            array(
                'id' => 26,
                'country_id' => 11,
                'iso2' => 'AR',
                'name' => 'America/Argentina/Rio_Gallegos',
            ),
            26 =>
            array(
                'id' => 27,
                'country_id' => 11,
                'iso2' => 'AR',
                'name' => 'America/Argentina/Salta',
            ),
            27 =>
            array(
                'id' => 28,
                'country_id' => 11,
                'iso2' => 'AR',
                'name' => 'America/Argentina/San_Juan',
            ),
            28 =>
            array(
                'id' => 29,
                'country_id' => 11,
                'iso2' => 'AR',
                'name' => 'America/Argentina/San_Luis',
            ),
            29 =>
            array(
                'id' => 30,
                'country_id' => 11,
                'iso2' => 'AR',
                'name' => 'America/Argentina/Tucuman',
            ),
            30 =>
            array(
                'id' => 31,
                'country_id' => 11,
                'iso2' => 'AR',
                'name' => 'America/Argentina/Ushuaia',
            ),
            31 =>
            array(
                'id' => 32,
                'country_id' => 12,
                'iso2' => 'AM',
                'name' => 'Asia/Yerevan',
            ),
            32 =>
            array(
                'id' => 33,
                'country_id' => 13,
                'iso2' => 'AW',
                'name' => 'America/Aruba',
            ),
            33 =>
            array(
                'id' => 34,
                'country_id' => 14,
                'iso2' => 'AU',
                'name' => 'Antarctica/Macquarie',
            ),
            34 =>
            array(
                'id' => 35,
                'country_id' => 14,
                'iso2' => 'AU',
                'name' => 'Australia/Adelaide',
            ),
            35 =>
            array(
                'id' => 36,
                'country_id' => 14,
                'iso2' => 'AU',
                'name' => 'Australia/Brisbane',
            ),
            36 =>
            array(
                'id' => 37,
                'country_id' => 14,
                'iso2' => 'AU',
                'name' => 'Australia/Broken_Hill',
            ),
            37 =>
            array(
                'id' => 38,
                'country_id' => 14,
                'iso2' => 'AU',
                'name' => 'Australia/Currie',
            ),
            38 =>
            array(
                'id' => 39,
                'country_id' => 14,
                'iso2' => 'AU',
                'name' => 'Australia/Darwin',
            ),
            39 =>
            array(
                'id' => 40,
                'country_id' => 14,
                'iso2' => 'AU',
                'name' => 'Australia/Eucla',
            ),
            40 =>
            array(
                'id' => 41,
                'country_id' => 14,
                'iso2' => 'AU',
                'name' => 'Australia/Hobart',
            ),
            41 =>
            array(
                'id' => 42,
                'country_id' => 14,
                'iso2' => 'AU',
                'name' => 'Australia/Lindeman',
            ),
            42 =>
            array(
                'id' => 43,
                'country_id' => 14,
                'iso2' => 'AU',
                'name' => 'Australia/Lord_Howe',
            ),
            43 =>
            array(
                'id' => 44,
                'country_id' => 14,
                'iso2' => 'AU',
                'name' => 'Australia/Melbourne',
            ),
            44 =>
            array(
                'id' => 45,
                'country_id' => 14,
                'iso2' => 'AU',
                'name' => 'Australia/Perth',
            ),
            45 =>
            array(
                'id' => 46,
                'country_id' => 14,
                'iso2' => 'AU',
                'name' => 'Australia/Sydney',
            ),
            46 =>
            array(
                'id' => 47,
                'country_id' => 15,
                'iso2' => 'AT',
                'name' => 'Europe/Vienna',
            ),
            47 =>
            array(
                'id' => 48,
                'country_id' => 16,
                'iso2' => 'AZ',
                'name' => 'Asia/Baku',
            ),
            48 =>
            array(
                'id' => 49,
                'country_id' => 17,
                'iso2' => 'BS',
                'name' => 'America/Nassau',
            ),
            49 =>
            array(
                'id' => 50,
                'country_id' => 18,
                'iso2' => 'BH',
                'name' => 'Asia/Bahrain',
            ),
            50 =>
            array(
                'id' => 51,
                'country_id' => 19,
                'iso2' => 'BD',
                'name' => 'Asia/Dhaka',
            ),
            51 =>
            array(
                'id' => 52,
                'country_id' => 20,
                'iso2' => 'BB',
                'name' => 'America/Barbados',
            ),
            52 =>
            array(
                'id' => 53,
                'country_id' => 21,
                'iso2' => 'BY',
                'name' => 'Europe/Minsk',
            ),
            53 =>
            array(
                'id' => 54,
                'country_id' => 22,
                'iso2' => 'BE',
                'name' => 'Europe/Brussels',
            ),
            54 =>
            array(
                'id' => 55,
                'country_id' => 23,
                'iso2' => 'BZ',
                'name' => 'America/Belize',
            ),
            55 =>
            array(
                'id' => 56,
                'country_id' => 24,
                'iso2' => 'BJ',
                'name' => 'Africa/Porto-Novo',
            ),
            56 =>
            array(
                'id' => 57,
                'country_id' => 25,
                'iso2' => 'BM',
                'name' => 'Atlantic/Bermuda',
            ),
            57 =>
            array(
                'id' => 58,
                'country_id' => 26,
                'iso2' => 'BT',
                'name' => 'Asia/Thimphu',
            ),
            58 =>
            array(
                'id' => 59,
                'country_id' => 27,
                'iso2' => 'BO',
                'name' => 'America/La_Paz',
            ),
            59 =>
            array(
                'id' => 60,
                'country_id' => 28,
                'iso2' => 'BQ',
                'name' => 'America/Anguilla',
            ),
            60 =>
            array(
                'id' => 61,
                'country_id' => 29,
                'iso2' => 'BA',
                'name' => 'Europe/Sarajevo',
            ),
            61 =>
            array(
                'id' => 62,
                'country_id' => 30,
                'iso2' => 'BW',
                'name' => 'Africa/Gaborone',
            ),
            62 =>
            array(
                'id' => 63,
                'country_id' => 31,
                'iso2' => 'BV',
                'name' => 'Europe/Oslo',
            ),
            63 =>
            array(
                'id' => 64,
                'country_id' => 32,
                'iso2' => 'BR',
                'name' => 'America/Araguaina',
            ),
            64 =>
            array(
                'id' => 65,
                'country_id' => 32,
                'iso2' => 'BR',
                'name' => 'America/Bahia',
            ),
            65 =>
            array(
                'id' => 66,
                'country_id' => 32,
                'iso2' => 'BR',
                'name' => 'America/Belem',
            ),
            66 =>
            array(
                'id' => 67,
                'country_id' => 32,
                'iso2' => 'BR',
                'name' => 'America/Boa_Vista',
            ),
            67 =>
            array(
                'id' => 68,
                'country_id' => 32,
                'iso2' => 'BR',
                'name' => 'America/Campo_Grande',
            ),
            68 =>
            array(
                'id' => 69,
                'country_id' => 32,
                'iso2' => 'BR',
                'name' => 'America/Cuiaba',
            ),
            69 =>
            array(
                'id' => 70,
                'country_id' => 32,
                'iso2' => 'BR',
                'name' => 'America/Eirunepe',
            ),
            70 =>
            array(
                'id' => 71,
                'country_id' => 32,
                'iso2' => 'BR',
                'name' => 'America/Fortaleza',
            ),
            71 =>
            array(
                'id' => 72,
                'country_id' => 32,
                'iso2' => 'BR',
                'name' => 'America/Maceio',
            ),
            72 =>
            array(
                'id' => 73,
                'country_id' => 32,
                'iso2' => 'BR',
                'name' => 'America/Manaus',
            ),
            73 =>
            array(
                'id' => 74,
                'country_id' => 32,
                'iso2' => 'BR',
                'name' => 'America/Noronha',
            ),
            74 =>
            array(
                'id' => 75,
                'country_id' => 32,
                'iso2' => 'BR',
                'name' => 'America/Porto_Velho',
            ),
            75 =>
            array(
                'id' => 76,
                'country_id' => 32,
                'iso2' => 'BR',
                'name' => 'America/Recife',
            ),
            76 =>
            array(
                'id' => 77,
                'country_id' => 32,
                'iso2' => 'BR',
                'name' => 'America/Rio_Branco',
            ),
            77 =>
            array(
                'id' => 78,
                'country_id' => 32,
                'iso2' => 'BR',
                'name' => 'America/Santarem',
            ),
            78 =>
            array(
                'id' => 79,
                'country_id' => 32,
                'iso2' => 'BR',
                'name' => 'America/Sao_Paulo',
            ),
            79 =>
            array(
                'id' => 80,
                'country_id' => 33,
                'iso2' => 'IO',
                'name' => 'Indian/Chagos',
            ),
            80 =>
            array(
                'id' => 81,
                'country_id' => 34,
                'iso2' => 'BN',
                'name' => 'Asia/Brunei',
            ),
            81 =>
            array(
                'id' => 82,
                'country_id' => 35,
                'iso2' => 'BG',
                'name' => 'Europe/Sofia',
            ),
            82 =>
            array(
                'id' => 83,
                'country_id' => 36,
                'iso2' => 'BF',
                'name' => 'Africa/Ouagadougou',
            ),
            83 =>
            array(
                'id' => 84,
                'country_id' => 37,
                'iso2' => 'BI',
                'name' => 'Africa/Bujumbura',
            ),
            84 =>
            array(
                'id' => 85,
                'country_id' => 38,
                'iso2' => 'KH',
                'name' => 'Asia/Phnom_Penh',
            ),
            85 =>
            array(
                'id' => 86,
                'country_id' => 39,
                'iso2' => 'CM',
                'name' => 'Africa/Douala',
            ),
            86 =>
            array(
                'id' => 87,
                'country_id' => 40,
                'iso2' => 'CA',
                'name' => 'America/Atikokan',
            ),
            87 =>
            array(
                'id' => 88,
                'country_id' => 40,
                'iso2' => 'CA',
                'name' => 'America/Blanc-Sablon',
            ),
            88 =>
            array(
                'id' => 89,
                'country_id' => 40,
                'iso2' => 'CA',
                'name' => 'America/Cambridge_Bay',
            ),
            89 =>
            array(
                'id' => 90,
                'country_id' => 40,
                'iso2' => 'CA',
                'name' => 'America/Creston',
            ),
            90 =>
            array(
                'id' => 91,
                'country_id' => 40,
                'iso2' => 'CA',
                'name' => 'America/Dawson',
            ),
            91 =>
            array(
                'id' => 92,
                'country_id' => 40,
                'iso2' => 'CA',
                'name' => 'America/Dawson_Creek',
            ),
            92 =>
            array(
                'id' => 93,
                'country_id' => 40,
                'iso2' => 'CA',
                'name' => 'America/Edmonton',
            ),
            93 =>
            array(
                'id' => 94,
                'country_id' => 40,
                'iso2' => 'CA',
                'name' => 'America/Fort_Nelson',
            ),
            94 =>
            array(
                'id' => 95,
                'country_id' => 40,
                'iso2' => 'CA',
                'name' => 'America/Glace_Bay',
            ),
            95 =>
            array(
                'id' => 96,
                'country_id' => 40,
                'iso2' => 'CA',
                'name' => 'America/Goose_Bay',
            ),
            96 =>
            array(
                'id' => 97,
                'country_id' => 40,
                'iso2' => 'CA',
                'name' => 'America/Halifax',
            ),
            97 =>
            array(
                'id' => 98,
                'country_id' => 40,
                'iso2' => 'CA',
                'name' => 'America/Inuvik',
            ),
            98 =>
            array(
                'id' => 99,
                'country_id' => 40,
                'iso2' => 'CA',
                'name' => 'America/Iqaluit',
            ),
            99 =>
            array(
                'id' => 100,
                'country_id' => 40,
                'iso2' => 'CA',
                'name' => 'America/Moncton',
            ),
            100 =>
            array(
                'id' => 101,
                'country_id' => 40,
                'iso2' => 'CA',
                'name' => 'America/Nipigon',
            ),
            101 =>
            array(
                'id' => 102,
                'country_id' => 40,
                'iso2' => 'CA',
                'name' => 'America/Pangnirtung',
            ),
            102 =>
            array(
                'id' => 103,
                'country_id' => 40,
                'iso2' => 'CA',
                'name' => 'America/Rainy_River',
            ),
            103 =>
            array(
                'id' => 104,
                'country_id' => 40,
                'iso2' => 'CA',
                'name' => 'America/Rankin_Inlet',
            ),
            104 =>
            array(
                'id' => 105,
                'country_id' => 40,
                'iso2' => 'CA',
                'name' => 'America/Regina',
            ),
            105 =>
            array(
                'id' => 106,
                'country_id' => 40,
                'iso2' => 'CA',
                'name' => 'America/Resolute',
            ),
            106 =>
            array(
                'id' => 107,
                'country_id' => 40,
                'iso2' => 'CA',
                'name' => 'America/St_Johns',
            ),
            107 =>
            array(
                'id' => 108,
                'country_id' => 40,
                'iso2' => 'CA',
                'name' => 'America/Swift_Current',
            ),
            108 =>
            array(
                'id' => 109,
                'country_id' => 40,
                'iso2' => 'CA',
                'name' => 'America/Thunder_Bay',
            ),
            109 =>
            array(
                'id' => 110,
                'country_id' => 40,
                'iso2' => 'CA',
                'name' => 'America/Toronto',
            ),
            110 =>
            array(
                'id' => 111,
                'country_id' => 40,
                'iso2' => 'CA',
                'name' => 'America/Vancouver',
            ),
            111 =>
            array(
                'id' => 112,
                'country_id' => 40,
                'iso2' => 'CA',
                'name' => 'America/Whitehorse',
            ),
            112 =>
            array(
                'id' => 113,
                'country_id' => 40,
                'iso2' => 'CA',
                'name' => 'America/Winnipeg',
            ),
            113 =>
            array(
                'id' => 114,
                'country_id' => 40,
                'iso2' => 'CA',
                'name' => 'America/Yellowknife',
            ),
            114 =>
            array(
                'id' => 115,
                'country_id' => 41,
                'iso2' => 'CV',
                'name' => 'Atlantic/Cape_Verde',
            ),
            115 =>
            array(
                'id' => 116,
                'country_id' => 42,
                'iso2' => 'KY',
                'name' => 'America/Cayman',
            ),
            116 =>
            array(
                'id' => 117,
                'country_id' => 43,
                'iso2' => 'CF',
                'name' => 'Africa/Bangui',
            ),
            117 =>
            array(
                'id' => 118,
                'country_id' => 44,
                'iso2' => 'TD',
                'name' => 'Africa/Ndjamena',
            ),
            118 =>
            array(
                'id' => 119,
                'country_id' => 45,
                'iso2' => 'CL',
                'name' => 'America/Punta_Arenas',
            ),
            119 =>
            array(
                'id' => 120,
                'country_id' => 45,
                'iso2' => 'CL',
                'name' => 'America/Santiago',
            ),
            120 =>
            array(
                'id' => 121,
                'country_id' => 45,
                'iso2' => 'CL',
                'name' => 'Pacific/Easter',
            ),
            121 =>
            array(
                'id' => 122,
                'country_id' => 46,
                'iso2' => 'CN',
                'name' => 'Asia/Shanghai',
            ),
            122 =>
            array(
                'id' => 123,
                'country_id' => 46,
                'iso2' => 'CN',
                'name' => 'Asia/Urumqi',
            ),
            123 =>
            array(
                'id' => 124,
                'country_id' => 47,
                'iso2' => 'CX',
                'name' => 'Indian/Christmas',
            ),
            124 =>
            array(
                'id' => 125,
                'country_id' => 48,
                'iso2' => 'CC',
                'name' => 'Indian/Cocos',
            ),
            125 =>
            array(
                'id' => 126,
                'country_id' => 49,
                'iso2' => 'CO',
                'name' => 'America/Bogota',
            ),
            126 =>
            array(
                'id' => 127,
                'country_id' => 50,
                'iso2' => 'KM',
                'name' => 'Indian/Comoro',
            ),
            127 =>
            array(
                'id' => 128,
                'country_id' => 51,
                'iso2' => 'CG',
                'name' => 'Africa/Brazzaville',
            ),
            128 =>
            array(
                'id' => 129,
                'country_id' => 52,
                'iso2' => 'CK',
                'name' => 'Pacific/Rarotonga',
            ),
            129 =>
            array(
                'id' => 130,
                'country_id' => 53,
                'iso2' => 'CR',
                'name' => 'America/Costa_Rica',
            ),
            130 =>
            array(
                'id' => 131,
                'country_id' => 54,
                'iso2' => 'CI',
                'name' => 'Africa/Abidjan',
            ),
            131 =>
            array(
                'id' => 132,
                'country_id' => 55,
                'iso2' => 'HR',
                'name' => 'Europe/Zagreb',
            ),
            132 =>
            array(
                'id' => 133,
                'country_id' => 56,
                'iso2' => 'CU',
                'name' => 'America/Havana',
            ),
            133 =>
            array(
                'id' => 134,
                'country_id' => 57,
                'iso2' => 'CW',
                'name' => 'America/Curacao',
            ),
            134 =>
            array(
                'id' => 135,
                'country_id' => 58,
                'iso2' => 'CY',
                'name' => 'Asia/Famagusta',
            ),
            135 =>
            array(
                'id' => 136,
                'country_id' => 58,
                'iso2' => 'CY',
                'name' => 'Asia/Nicosia',
            ),
            136 =>
            array(
                'id' => 137,
                'country_id' => 59,
                'iso2' => 'CZ',
                'name' => 'Europe/Prague',
            ),
            137 =>
            array(
                'id' => 138,
                'country_id' => 60,
                'iso2' => 'CD',
                'name' => 'Africa/Kinshasa',
            ),
            138 =>
            array(
                'id' => 139,
                'country_id' => 60,
                'iso2' => 'CD',
                'name' => 'Africa/Lubumbashi',
            ),
            139 =>
            array(
                'id' => 140,
                'country_id' => 61,
                'iso2' => 'DK',
                'name' => 'Europe/Copenhagen',
            ),
            140 =>
            array(
                'id' => 141,
                'country_id' => 62,
                'iso2' => 'DJ',
                'name' => 'Africa/Djibouti',
            ),
            141 =>
            array(
                'id' => 142,
                'country_id' => 63,
                'iso2' => 'DM',
                'name' => 'America/Dominica',
            ),
            142 =>
            array(
                'id' => 143,
                'country_id' => 64,
                'iso2' => 'DO',
                'name' => 'America/Santo_Domingo',
            ),
            143 =>
            array(
                'id' => 144,
                'country_id' => 65,
                'iso2' => 'TL',
                'name' => 'Asia/Dili',
            ),
            144 =>
            array(
                'id' => 145,
                'country_id' => 66,
                'iso2' => 'EC',
                'name' => 'America/Guayaquil',
            ),
            145 =>
            array(
                'id' => 146,
                'country_id' => 66,
                'iso2' => 'EC',
                'name' => 'Pacific/Galapagos',
            ),
            146 =>
            array(
                'id' => 147,
                'country_id' => 67,
                'iso2' => 'EG',
                'name' => 'Africa/Cairo',
            ),
            147 =>
            array(
                'id' => 148,
                'country_id' => 68,
                'iso2' => 'SV',
                'name' => 'America/El_Salvador',
            ),
            148 =>
            array(
                'id' => 149,
                'country_id' => 69,
                'iso2' => 'GQ',
                'name' => 'Africa/Malabo',
            ),
            149 =>
            array(
                'id' => 150,
                'country_id' => 70,
                'iso2' => 'ER',
                'name' => 'Africa/Asmara',
            ),
            150 =>
            array(
                'id' => 151,
                'country_id' => 71,
                'iso2' => 'EE',
                'name' => 'Europe/Tallinn',
            ),
            151 =>
            array(
                'id' => 152,
                'country_id' => 72,
                'iso2' => 'ET',
                'name' => 'Africa/Addis_Ababa',
            ),
            152 =>
            array(
                'id' => 153,
                'country_id' => 73,
                'iso2' => 'FK',
                'name' => 'Atlantic/Stanley',
            ),
            153 =>
            array(
                'id' => 154,
                'country_id' => 74,
                'iso2' => 'FO',
                'name' => 'Atlantic/Faroe',
            ),
            154 =>
            array(
                'id' => 155,
                'country_id' => 75,
                'iso2' => 'FJ',
                'name' => 'Pacific/Fiji',
            ),
            155 =>
            array(
                'id' => 156,
                'country_id' => 76,
                'iso2' => 'FI',
                'name' => 'Europe/Helsinki',
            ),
            156 =>
            array(
                'id' => 157,
                'country_id' => 77,
                'iso2' => 'FR',
                'name' => 'Europe/Paris',
            ),
            157 =>
            array(
                'id' => 158,
                'country_id' => 78,
                'iso2' => 'GF',
                'name' => 'America/Cayenne',
            ),
            158 =>
            array(
                'id' => 159,
                'country_id' => 79,
                'iso2' => 'PF',
                'name' => 'Pacific/Gambier',
            ),
            159 =>
            array(
                'id' => 160,
                'country_id' => 79,
                'iso2' => 'PF',
                'name' => 'Pacific/Marquesas',
            ),
            160 =>
            array(
                'id' => 161,
                'country_id' => 79,
                'iso2' => 'PF',
                'name' => 'Pacific/Tahiti',
            ),
            161 =>
            array(
                'id' => 162,
                'country_id' => 80,
                'iso2' => 'TF',
                'name' => 'Indian/Kerguelen',
            ),
            162 =>
            array(
                'id' => 163,
                'country_id' => 81,
                'iso2' => 'GA',
                'name' => 'Africa/Libreville',
            ),
            163 =>
            array(
                'id' => 164,
                'country_id' => 82,
                'iso2' => 'GM',
                'name' => 'Africa/Banjul',
            ),
            164 =>
            array(
                'id' => 165,
                'country_id' => 83,
                'iso2' => 'GE',
                'name' => 'Asia/Tbilisi',
            ),
            165 =>
            array(
                'id' => 166,
                'country_id' => 84,
                'iso2' => 'DE',
                'name' => 'Europe/Berlin',
            ),
            166 =>
            array(
                'id' => 167,
                'country_id' => 84,
                'iso2' => 'DE',
                'name' => 'Europe/Busingen',
            ),
            167 =>
            array(
                'id' => 168,
                'country_id' => 85,
                'iso2' => 'GH',
                'name' => 'Africa/Accra',
            ),
            168 =>
            array(
                'id' => 169,
                'country_id' => 86,
                'iso2' => 'GI',
                'name' => 'Europe/Gibraltar',
            ),
            169 =>
            array(
                'id' => 170,
                'country_id' => 87,
                'iso2' => 'GR',
                'name' => 'Europe/Athens',
            ),
            170 =>
            array(
                'id' => 171,
                'country_id' => 88,
                'iso2' => 'GL',
                'name' => 'America/Danmarkshavn',
            ),
            171 =>
            array(
                'id' => 172,
                'country_id' => 88,
                'iso2' => 'GL',
                'name' => 'America/Nuuk',
            ),
            172 =>
            array(
                'id' => 173,
                'country_id' => 88,
                'iso2' => 'GL',
                'name' => 'America/Scoresbysund',
            ),
            173 =>
            array(
                'id' => 174,
                'country_id' => 88,
                'iso2' => 'GL',
                'name' => 'America/Thule',
            ),
            174 =>
            array(
                'id' => 175,
                'country_id' => 89,
                'iso2' => 'GD',
                'name' => 'America/Grenada',
            ),
            175 =>
            array(
                'id' => 176,
                'country_id' => 90,
                'iso2' => 'GP',
                'name' => 'America/Guadeloupe',
            ),
            176 =>
            array(
                'id' => 177,
                'country_id' => 91,
                'iso2' => 'GU',
                'name' => 'Pacific/Guam',
            ),
            177 =>
            array(
                'id' => 178,
                'country_id' => 92,
                'iso2' => 'GT',
                'name' => 'America/Guatemala',
            ),
            178 =>
            array(
                'id' => 179,
                'country_id' => 93,
                'iso2' => 'GG',
                'name' => 'Europe/Guernsey',
            ),
            179 =>
            array(
                'id' => 180,
                'country_id' => 94,
                'iso2' => 'GN',
                'name' => 'Africa/Conakry',
            ),
            180 =>
            array(
                'id' => 181,
                'country_id' => 95,
                'iso2' => 'GW',
                'name' => 'Africa/Bissau',
            ),
            181 =>
            array(
                'id' => 182,
                'country_id' => 96,
                'iso2' => 'GY',
                'name' => 'America/Guyana',
            ),
            182 =>
            array(
                'id' => 183,
                'country_id' => 97,
                'iso2' => 'HT',
                'name' => 'America/Port-au-Prince',
            ),
            183 =>
            array(
                'id' => 184,
                'country_id' => 98,
                'iso2' => 'HM',
                'name' => 'Indian/Kerguelen',
            ),
            184 =>
            array(
                'id' => 185,
                'country_id' => 99,
                'iso2' => 'HN',
                'name' => 'America/Tegucigalpa',
            ),
            185 =>
            array(
                'id' => 186,
                'country_id' => 100,
                'iso2' => 'HK',
                'name' => 'Asia/Hong_Kong',
            ),
            186 =>
            array(
                'id' => 187,
                'country_id' => 101,
                'iso2' => 'HU',
                'name' => 'Europe/Budapest',
            ),
            187 =>
            array(
                'id' => 188,
                'country_id' => 102,
                'iso2' => 'IS',
                'name' => 'Atlantic/Reykjavik',
            ),
            188 =>
            array(
                'id' => 189,
                'country_id' => 103,
                'iso2' => 'IN',
                'name' => 'Asia/Kolkata',
            ),
            189 =>
            array(
                'id' => 190,
                'country_id' => 104,
                'iso2' => 'ID',
                'name' => 'Asia/Jakarta',
            ),
            190 =>
            array(
                'id' => 191,
                'country_id' => 104,
                'iso2' => 'ID',
                'name' => 'Asia/Jayapura',
            ),
            191 =>
            array(
                'id' => 192,
                'country_id' => 104,
                'iso2' => 'ID',
                'name' => 'Asia/Makassar',
            ),
            192 =>
            array(
                'id' => 193,
                'country_id' => 104,
                'iso2' => 'ID',
                'name' => 'Asia/Pontianak',
            ),
            193 =>
            array(
                'id' => 194,
                'country_id' => 105,
                'iso2' => 'IR',
                'name' => 'Asia/Tehran',
            ),
            194 =>
            array(
                'id' => 195,
                'country_id' => 106,
                'iso2' => 'IQ',
                'name' => 'Asia/Baghdad',
            ),
            195 =>
            array(
                'id' => 196,
                'country_id' => 107,
                'iso2' => 'IE',
                'name' => 'Europe/Dublin',
            ),
            196 =>
            array(
                'id' => 197,
                'country_id' => 108,
                'iso2' => 'IL',
                'name' => 'Asia/Jerusalem',
            ),
            197 =>
            array(
                'id' => 198,
                'country_id' => 109,
                'iso2' => 'IT',
                'name' => 'Europe/Rome',
            ),
            198 =>
            array(
                'id' => 199,
                'country_id' => 110,
                'iso2' => 'JM',
                'name' => 'America/Jamaica',
            ),
            199 =>
            array(
                'id' => 200,
                'country_id' => 111,
                'iso2' => 'JP',
                'name' => 'Asia/Tokyo',
            ),
            200 =>
            array(
                'id' => 201,
                'country_id' => 112,
                'iso2' => 'JE',
                'name' => 'Europe/Jersey',
            ),
            201 =>
            array(
                'id' => 202,
                'country_id' => 113,
                'iso2' => 'JO',
                'name' => 'Asia/Amman',
            ),
            202 =>
            array(
                'id' => 203,
                'country_id' => 114,
                'iso2' => 'KZ',
                'name' => 'Asia/Almaty',
            ),
            203 =>
            array(
                'id' => 204,
                'country_id' => 114,
                'iso2' => 'KZ',
                'name' => 'Asia/Aqtau',
            ),
            204 =>
            array(
                'id' => 205,
                'country_id' => 114,
                'iso2' => 'KZ',
                'name' => 'Asia/Aqtobe',
            ),
            205 =>
            array(
                'id' => 206,
                'country_id' => 114,
                'iso2' => 'KZ',
                'name' => 'Asia/Atyrau',
            ),
            206 =>
            array(
                'id' => 207,
                'country_id' => 114,
                'iso2' => 'KZ',
                'name' => 'Asia/Oral',
            ),
            207 =>
            array(
                'id' => 208,
                'country_id' => 114,
                'iso2' => 'KZ',
                'name' => 'Asia/Qostanay',
            ),
            208 =>
            array(
                'id' => 209,
                'country_id' => 114,
                'iso2' => 'KZ',
                'name' => 'Asia/Qyzylorda',
            ),
            209 =>
            array(
                'id' => 210,
                'country_id' => 115,
                'iso2' => 'KE',
                'name' => 'Africa/Nairobi',
            ),
            210 =>
            array(
                'id' => 211,
                'country_id' => 116,
                'iso2' => 'KI',
                'name' => 'Pacific/Enderbury',
            ),
            211 =>
            array(
                'id' => 212,
                'country_id' => 116,
                'iso2' => 'KI',
                'name' => 'Pacific/Kiritimati',
            ),
            212 =>
            array(
                'id' => 213,
                'country_id' => 116,
                'iso2' => 'KI',
                'name' => 'Pacific/Tarawa',
            ),
            213 =>
            array(
                'id' => 214,
                'country_id' => 117,
                'iso2' => 'XK',
                'name' => 'Europe/Belgrade',
            ),
            214 =>
            array(
                'id' => 215,
                'country_id' => 118,
                'iso2' => 'KW',
                'name' => 'Asia/Kuwait',
            ),
            215 =>
            array(
                'id' => 216,
                'country_id' => 119,
                'iso2' => 'KG',
                'name' => 'Asia/Bishkek',
            ),
            216 =>
            array(
                'id' => 217,
                'country_id' => 120,
                'iso2' => 'LA',
                'name' => 'Asia/Vientiane',
            ),
            217 =>
            array(
                'id' => 218,
                'country_id' => 121,
                'iso2' => 'LV',
                'name' => 'Europe/Riga',
            ),
            218 =>
            array(
                'id' => 219,
                'country_id' => 122,
                'iso2' => 'LB',
                'name' => 'Asia/Beirut',
            ),
            219 =>
            array(
                'id' => 220,
                'country_id' => 123,
                'iso2' => 'LS',
                'name' => 'Africa/Maseru',
            ),
            220 =>
            array(
                'id' => 221,
                'country_id' => 124,
                'iso2' => 'LR',
                'name' => 'Africa/Monrovia',
            ),
            221 =>
            array(
                'id' => 222,
                'country_id' => 125,
                'iso2' => 'LY',
                'name' => 'Africa/Tripoli',
            ),
            222 =>
            array(
                'id' => 223,
                'country_id' => 126,
                'iso2' => 'LI',
                'name' => 'Europe/Vaduz',
            ),
            223 =>
            array(
                'id' => 224,
                'country_id' => 127,
                'iso2' => 'LT',
                'name' => 'Europe/Vilnius',
            ),
            224 =>
            array(
                'id' => 225,
                'country_id' => 128,
                'iso2' => 'LU',
                'name' => 'Europe/Luxembourg',
            ),
            225 =>
            array(
                'id' => 226,
                'country_id' => 129,
                'iso2' => 'MO',
                'name' => 'Asia/Macau',
            ),
            226 =>
            array(
                'id' => 227,
                'country_id' => 130,
                'iso2' => 'MK',
                'name' => 'Europe/Skopje',
            ),
            227 =>
            array(
                'id' => 228,
                'country_id' => 131,
                'iso2' => 'MG',
                'name' => 'Indian/Antananarivo',
            ),
            228 =>
            array(
                'id' => 229,
                'country_id' => 132,
                'iso2' => 'MW',
                'name' => 'Africa/Blantyre',
            ),
            229 =>
            array(
                'id' => 230,
                'country_id' => 133,
                'iso2' => 'MY',
                'name' => 'Asia/Kuala_Lumpur',
            ),
            230 =>
            array(
                'id' => 231,
                'country_id' => 133,
                'iso2' => 'MY',
                'name' => 'Asia/Kuching',
            ),
            231 =>
            array(
                'id' => 232,
                'country_id' => 134,
                'iso2' => 'MV',
                'name' => 'Indian/Maldives',
            ),
            232 =>
            array(
                'id' => 233,
                'country_id' => 135,
                'iso2' => 'ML',
                'name' => 'Africa/Bamako',
            ),
            233 =>
            array(
                'id' => 234,
                'country_id' => 136,
                'iso2' => 'MT',
                'name' => 'Europe/Malta',
            ),
            234 =>
            array(
                'id' => 235,
                'country_id' => 137,
                'iso2' => 'IM',
                'name' => 'Europe/Isle_of_Man',
            ),
            235 =>
            array(
                'id' => 236,
                'country_id' => 138,
                'iso2' => 'MH',
                'name' => 'Pacific/Kwajalein',
            ),
            236 =>
            array(
                'id' => 237,
                'country_id' => 138,
                'iso2' => 'MH',
                'name' => 'Pacific/Majuro',
            ),
            237 =>
            array(
                'id' => 238,
                'country_id' => 139,
                'iso2' => 'MQ',
                'name' => 'America/Martinique',
            ),
            238 =>
            array(
                'id' => 239,
                'country_id' => 140,
                'iso2' => 'MR',
                'name' => 'Africa/Nouakchott',
            ),
            239 =>
            array(
                'id' => 240,
                'country_id' => 141,
                'iso2' => 'MU',
                'name' => 'Indian/Mauritius',
            ),
            240 =>
            array(
                'id' => 241,
                'country_id' => 142,
                'iso2' => 'YT',
                'name' => 'Indian/Mayotte',
            ),
            241 =>
            array(
                'id' => 242,
                'country_id' => 143,
                'iso2' => 'MX',
                'name' => 'America/Bahia_Banderas',
            ),
            242 =>
            array(
                'id' => 243,
                'country_id' => 143,
                'iso2' => 'MX',
                'name' => 'America/Cancun',
            ),
            243 =>
            array(
                'id' => 244,
                'country_id' => 143,
                'iso2' => 'MX',
                'name' => 'America/Chihuahua',
            ),
            244 =>
            array(
                'id' => 245,
                'country_id' => 143,
                'iso2' => 'MX',
                'name' => 'America/Hermosillo',
            ),
            245 =>
            array(
                'id' => 246,
                'country_id' => 143,
                'iso2' => 'MX',
                'name' => 'America/Matamoros',
            ),
            246 =>
            array(
                'id' => 247,
                'country_id' => 143,
                'iso2' => 'MX',
                'name' => 'America/Mazatlan',
            ),
            247 =>
            array(
                'id' => 248,
                'country_id' => 143,
                'iso2' => 'MX',
                'name' => 'America/Merida',
            ),
            248 =>
            array(
                'id' => 249,
                'country_id' => 143,
                'iso2' => 'MX',
                'name' => 'America/Mexico_City',
            ),
            249 =>
            array(
                'id' => 250,
                'country_id' => 143,
                'iso2' => 'MX',
                'name' => 'America/Monterrey',
            ),
            250 =>
            array(
                'id' => 251,
                'country_id' => 143,
                'iso2' => 'MX',
                'name' => 'America/Ojinaga',
            ),
            251 =>
            array(
                'id' => 252,
                'country_id' => 143,
                'iso2' => 'MX',
                'name' => 'America/Tijuana',
            ),
            252 =>
            array(
                'id' => 253,
                'country_id' => 144,
                'iso2' => 'FM',
                'name' => 'Pacific/Chuuk',
            ),
            253 =>
            array(
                'id' => 254,
                'country_id' => 144,
                'iso2' => 'FM',
                'name' => 'Pacific/Kosrae',
            ),
            254 =>
            array(
                'id' => 255,
                'country_id' => 144,
                'iso2' => 'FM',
                'name' => 'Pacific/Pohnpei',
            ),
            255 =>
            array(
                'id' => 256,
                'country_id' => 145,
                'iso2' => 'MD',
                'name' => 'Europe/Chisinau',
            ),
            256 =>
            array(
                'id' => 257,
                'country_id' => 146,
                'iso2' => 'MC',
                'name' => 'Europe/Monaco',
            ),
            257 =>
            array(
                'id' => 258,
                'country_id' => 147,
                'iso2' => 'MN',
                'name' => 'Asia/Choibalsan',
            ),
            258 =>
            array(
                'id' => 259,
                'country_id' => 147,
                'iso2' => 'MN',
                'name' => 'Asia/Hovd',
            ),
            259 =>
            array(
                'id' => 260,
                'country_id' => 147,
                'iso2' => 'MN',
                'name' => 'Asia/Ulaanbaatar',
            ),
            260 =>
            array(
                'id' => 261,
                'country_id' => 148,
                'iso2' => 'ME',
                'name' => 'Europe/Podgorica',
            ),
            261 =>
            array(
                'id' => 262,
                'country_id' => 149,
                'iso2' => 'MS',
                'name' => 'America/Montserrat',
            ),
            262 =>
            array(
                'id' => 263,
                'country_id' => 150,
                'iso2' => 'MA',
                'name' => 'Africa/Casablanca',
            ),
            263 =>
            array(
                'id' => 264,
                'country_id' => 151,
                'iso2' => 'MZ',
                'name' => 'Africa/Maputo',
            ),
            264 =>
            array(
                'id' => 265,
                'country_id' => 152,
                'iso2' => 'MM',
                'name' => 'Asia/Yangon',
            ),
            265 =>
            array(
                'id' => 266,
                'country_id' => 153,
                'iso2' => 'NA',
                'name' => 'Africa/Windhoek',
            ),
            266 =>
            array(
                'id' => 267,
                'country_id' => 154,
                'iso2' => 'NR',
                'name' => 'Pacific/Nauru',
            ),
            267 =>
            array(
                'id' => 268,
                'country_id' => 155,
                'iso2' => 'NP',
                'name' => 'Asia/Kathmandu',
            ),
            268 =>
            array(
                'id' => 269,
                'country_id' => 156,
                'iso2' => 'NL',
                'name' => 'Europe/Amsterdam',
            ),
            269 =>
            array(
                'id' => 270,
                'country_id' => 157,
                'iso2' => 'NC',
                'name' => 'Pacific/Noumea',
            ),
            270 =>
            array(
                'id' => 271,
                'country_id' => 158,
                'iso2' => 'NZ',
                'name' => 'Pacific/Auckland',
            ),
            271 =>
            array(
                'id' => 272,
                'country_id' => 158,
                'iso2' => 'NZ',
                'name' => 'Pacific/Chatham',
            ),
            272 =>
            array(
                'id' => 273,
                'country_id' => 159,
                'iso2' => 'NI',
                'name' => 'America/Managua',
            ),
            273 =>
            array(
                'id' => 274,
                'country_id' => 160,
                'iso2' => 'NE',
                'name' => 'Africa/Niamey',
            ),
            274 =>
            array(
                'id' => 275,
                'country_id' => 161,
                'iso2' => 'NG',
                'name' => 'Africa/Lagos',
            ),
            275 =>
            array(
                'id' => 276,
                'country_id' => 162,
                'iso2' => 'NU',
                'name' => 'Pacific/Niue',
            ),
            276 =>
            array(
                'id' => 277,
                'country_id' => 163,
                'iso2' => 'NF',
                'name' => 'Pacific/Norfolk',
            ),
            277 =>
            array(
                'id' => 278,
                'country_id' => 164,
                'iso2' => 'KP',
                'name' => 'Asia/Pyongyang',
            ),
            278 =>
            array(
                'id' => 279,
                'country_id' => 165,
                'iso2' => 'MP',
                'name' => 'Pacific/Saipan',
            ),
            279 =>
            array(
                'id' => 280,
                'country_id' => 166,
                'iso2' => 'NO',
                'name' => 'Europe/Oslo',
            ),
            280 =>
            array(
                'id' => 281,
                'country_id' => 167,
                'iso2' => 'OM',
                'name' => 'Asia/Muscat',
            ),
            281 =>
            array(
                'id' => 282,
                'country_id' => 168,
                'iso2' => 'PK',
                'name' => 'Asia/Karachi',
            ),
            282 =>
            array(
                'id' => 283,
                'country_id' => 169,
                'iso2' => 'PW',
                'name' => 'Pacific/Palau',
            ),
            283 =>
            array(
                'id' => 284,
                'country_id' => 170,
                'iso2' => 'PS',
                'name' => 'Asia/Gaza',
            ),
            284 =>
            array(
                'id' => 285,
                'country_id' => 170,
                'iso2' => 'PS',
                'name' => 'Asia/Hebron',
            ),
            285 =>
            array(
                'id' => 286,
                'country_id' => 171,
                'iso2' => 'PA',
                'name' => 'America/Panama',
            ),
            286 =>
            array(
                'id' => 287,
                'country_id' => 172,
                'iso2' => 'PG',
                'name' => 'Pacific/Bougainville',
            ),
            287 =>
            array(
                'id' => 288,
                'country_id' => 172,
                'iso2' => 'PG',
                'name' => 'Pacific/Port_Moresby',
            ),
            288 =>
            array(
                'id' => 289,
                'country_id' => 173,
                'iso2' => 'PY',
                'name' => 'America/Asuncion',
            ),
            289 =>
            array(
                'id' => 290,
                'country_id' => 174,
                'iso2' => 'PE',
                'name' => 'America/Lima',
            ),
            290 =>
            array(
                'id' => 291,
                'country_id' => 175,
                'iso2' => 'PH',
                'name' => 'Asia/Manila',
            ),
            291 =>
            array(
                'id' => 292,
                'country_id' => 176,
                'iso2' => 'PN',
                'name' => 'Pacific/Pitcairn',
            ),
            292 =>
            array(
                'id' => 293,
                'country_id' => 177,
                'iso2' => 'PL',
                'name' => 'Europe/Warsaw',
            ),
            293 =>
            array(
                'id' => 294,
                'country_id' => 178,
                'iso2' => 'PT',
                'name' => 'Atlantic/Azores',
            ),
            294 =>
            array(
                'id' => 295,
                'country_id' => 178,
                'iso2' => 'PT',
                'name' => 'Atlantic/Madeira',
            ),
            295 =>
            array(
                'id' => 296,
                'country_id' => 178,
                'iso2' => 'PT',
                'name' => 'Europe/Lisbon',
            ),
            296 =>
            array(
                'id' => 297,
                'country_id' => 179,
                'iso2' => 'PR',
                'name' => 'America/Puerto_Rico',
            ),
            297 =>
            array(
                'id' => 298,
                'country_id' => 180,
                'iso2' => 'QA',
                'name' => 'Asia/Qatar',
            ),
            298 =>
            array(
                'id' => 299,
                'country_id' => 181,
                'iso2' => 'RE',
                'name' => 'Indian/Reunion',
            ),
            299 =>
            array(
                'id' => 300,
                'country_id' => 182,
                'iso2' => 'RO',
                'name' => 'Europe/Bucharest',
            ),
            300 =>
            array(
                'id' => 301,
                'country_id' => 183,
                'iso2' => 'RU',
                'name' => 'Asia/Anadyr',
            ),
            301 =>
            array(
                'id' => 302,
                'country_id' => 183,
                'iso2' => 'RU',
                'name' => 'Asia/Barnaul',
            ),
            302 =>
            array(
                'id' => 303,
                'country_id' => 183,
                'iso2' => 'RU',
                'name' => 'Asia/Chita',
            ),
            303 =>
            array(
                'id' => 304,
                'country_id' => 183,
                'iso2' => 'RU',
                'name' => 'Asia/Irkutsk',
            ),
            304 =>
            array(
                'id' => 305,
                'country_id' => 183,
                'iso2' => 'RU',
                'name' => 'Asia/Kamchatka',
            ),
            305 =>
            array(
                'id' => 306,
                'country_id' => 183,
                'iso2' => 'RU',
                'name' => 'Asia/Khandyga',
            ),
            306 =>
            array(
                'id' => 307,
                'country_id' => 183,
                'iso2' => 'RU',
                'name' => 'Asia/Krasnoyarsk',
            ),
            307 =>
            array(
                'id' => 308,
                'country_id' => 183,
                'iso2' => 'RU',
                'name' => 'Asia/Magadan',
            ),
            308 =>
            array(
                'id' => 309,
                'country_id' => 183,
                'iso2' => 'RU',
                'name' => 'Asia/Novokuznetsk',
            ),
            309 =>
            array(
                'id' => 310,
                'country_id' => 183,
                'iso2' => 'RU',
                'name' => 'Asia/Novosibirsk',
            ),
            310 =>
            array(
                'id' => 311,
                'country_id' => 183,
                'iso2' => 'RU',
                'name' => 'Asia/Omsk',
            ),
            311 =>
            array(
                'id' => 312,
                'country_id' => 183,
                'iso2' => 'RU',
                'name' => 'Asia/Sakhalin',
            ),
            312 =>
            array(
                'id' => 313,
                'country_id' => 183,
                'iso2' => 'RU',
                'name' => 'Asia/Srednekolymsk',
            ),
            313 =>
            array(
                'id' => 314,
                'country_id' => 183,
                'iso2' => 'RU',
                'name' => 'Asia/Tomsk',
            ),
            314 =>
            array(
                'id' => 315,
                'country_id' => 183,
                'iso2' => 'RU',
                'name' => 'Asia/Ust-Nera',
            ),
            315 =>
            array(
                'id' => 316,
                'country_id' => 183,
                'iso2' => 'RU',
                'name' => 'Asia/Vladivostok',
            ),
            316 =>
            array(
                'id' => 317,
                'country_id' => 183,
                'iso2' => 'RU',
                'name' => 'Asia/Yakutsk',
            ),
            317 =>
            array(
                'id' => 318,
                'country_id' => 183,
                'iso2' => 'RU',
                'name' => 'Asia/Yekaterinburg',
            ),
            318 =>
            array(
                'id' => 319,
                'country_id' => 183,
                'iso2' => 'RU',
                'name' => 'Europe/Astrakhan',
            ),
            319 =>
            array(
                'id' => 320,
                'country_id' => 183,
                'iso2' => 'RU',
                'name' => 'Europe/Kaliningrad',
            ),
            320 =>
            array(
                'id' => 321,
                'country_id' => 183,
                'iso2' => 'RU',
                'name' => 'Europe/Kirov',
            ),
            321 =>
            array(
                'id' => 322,
                'country_id' => 183,
                'iso2' => 'RU',
                'name' => 'Europe/Moscow',
            ),
            322 =>
            array(
                'id' => 323,
                'country_id' => 183,
                'iso2' => 'RU',
                'name' => 'Europe/Samara',
            ),
            323 =>
            array(
                'id' => 324,
                'country_id' => 183,
                'iso2' => 'RU',
                'name' => 'Europe/Saratov',
            ),
            324 =>
            array(
                'id' => 325,
                'country_id' => 183,
                'iso2' => 'RU',
                'name' => 'Europe/Ulyanovsk',
            ),
            325 =>
            array(
                'id' => 326,
                'country_id' => 183,
                'iso2' => 'RU',
                'name' => 'Europe/Volgograd',
            ),
            326 =>
            array(
                'id' => 327,
                'country_id' => 184,
                'iso2' => 'RW',
                'name' => 'Africa/Kigali',
            ),
            327 =>
            array(
                'id' => 328,
                'country_id' => 185,
                'iso2' => 'SH',
                'name' => 'Atlantic/St_Helena',
            ),
            328 =>
            array(
                'id' => 329,
                'country_id' => 186,
                'iso2' => 'KN',
                'name' => 'America/St_Kitts',
            ),
            329 =>
            array(
                'id' => 330,
                'country_id' => 187,
                'iso2' => 'LC',
                'name' => 'America/St_Lucia',
            ),
            330 =>
            array(
                'id' => 331,
                'country_id' => 188,
                'iso2' => 'PM',
                'name' => 'America/Miquelon',
            ),
            331 =>
            array(
                'id' => 332,
                'country_id' => 189,
                'iso2' => 'VC',
                'name' => 'America/St_Vincent',
            ),
            332 =>
            array(
                'id' => 333,
                'country_id' => 190,
                'iso2' => 'BL',
                'name' => 'America/St_Barthelemy',
            ),
            333 =>
            array(
                'id' => 334,
                'country_id' => 191,
                'iso2' => 'MF',
                'name' => 'America/Marigot',
            ),
            334 =>
            array(
                'id' => 335,
                'country_id' => 192,
                'iso2' => 'WS',
                'name' => 'Pacific/Apia',
            ),
            335 =>
            array(
                'id' => 336,
                'country_id' => 193,
                'iso2' => 'SM',
                'name' => 'Europe/San_Marino',
            ),
            336 =>
            array(
                'id' => 337,
                'country_id' => 194,
                'iso2' => 'ST',
                'name' => 'Africa/Sao_Tome',
            ),
            337 =>
            array(
                'id' => 338,
                'country_id' => 195,
                'iso2' => 'SA',
                'name' => 'Asia/Riyadh',
            ),
            338 =>
            array(
                'id' => 339,
                'country_id' => 196,
                'iso2' => 'SN',
                'name' => 'Africa/Dakar',
            ),
            339 =>
            array(
                'id' => 340,
                'country_id' => 197,
                'iso2' => 'RS',
                'name' => 'Europe/Belgrade',
            ),
            340 =>
            array(
                'id' => 341,
                'country_id' => 198,
                'iso2' => 'SC',
                'name' => 'Indian/Mahe',
            ),
            341 =>
            array(
                'id' => 342,
                'country_id' => 199,
                'iso2' => 'SL',
                'name' => 'Africa/Freetown',
            ),
            342 =>
            array(
                'id' => 343,
                'country_id' => 200,
                'iso2' => 'SG',
                'name' => 'Asia/Singapore',
            ),
            343 =>
            array(
                'id' => 344,
                'country_id' => 201,
                'iso2' => 'SX',
                'name' => 'America/Anguilla',
            ),
            344 =>
            array(
                'id' => 345,
                'country_id' => 202,
                'iso2' => 'SK',
                'name' => 'Europe/Bratislava',
            ),
            345 =>
            array(
                'id' => 346,
                'country_id' => 203,
                'iso2' => 'SI',
                'name' => 'Europe/Ljubljana',
            ),
            346 =>
            array(
                'id' => 347,
                'country_id' => 204,
                'iso2' => 'SB',
                'name' => 'Pacific/Guadalcanal',
            ),
            347 =>
            array(
                'id' => 348,
                'country_id' => 205,
                'iso2' => 'SO',
                'name' => 'Africa/Mogadishu',
            ),
            348 =>
            array(
                'id' => 349,
                'country_id' => 206,
                'iso2' => 'ZA',
                'name' => 'Africa/Johannesburg',
            ),
            349 =>
            array(
                'id' => 350,
                'country_id' => 207,
                'iso2' => 'GS',
                'name' => 'Atlantic/South_Georgia',
            ),
            350 =>
            array(
                'id' => 351,
                'country_id' => 208,
                'iso2' => 'KR',
                'name' => 'Asia/Seoul',
            ),
            351 =>
            array(
                'id' => 352,
                'country_id' => 209,
                'iso2' => 'SS',
                'name' => 'Africa/Juba',
            ),
            352 =>
            array(
                'id' => 353,
                'country_id' => 210,
                'iso2' => 'ES',
                'name' => 'Africa/Ceuta',
            ),
            353 =>
            array(
                'id' => 354,
                'country_id' => 210,
                'iso2' => 'ES',
                'name' => 'Atlantic/Canary',
            ),
            354 =>
            array(
                'id' => 355,
                'country_id' => 210,
                'iso2' => 'ES',
                'name' => 'Europe/Madrid',
            ),
            355 =>
            array(
                'id' => 356,
                'country_id' => 211,
                'iso2' => 'LK',
                'name' => 'Asia/Colombo',
            ),
            356 =>
            array(
                'id' => 357,
                'country_id' => 212,
                'iso2' => 'SD',
                'name' => 'Africa/Khartoum',
            ),
            357 =>
            array(
                'id' => 358,
                'country_id' => 213,
                'iso2' => 'SR',
                'name' => 'America/Paramaribo',
            ),
            358 =>
            array(
                'id' => 359,
                'country_id' => 214,
                'iso2' => 'SJ',
                'name' => 'Arctic/Longyearbyen',
            ),
            359 =>
            array(
                'id' => 360,
                'country_id' => 215,
                'iso2' => 'SZ',
                'name' => 'Africa/Mbabane',
            ),
            360 =>
            array(
                'id' => 361,
                'country_id' => 216,
                'iso2' => 'SE',
                'name' => 'Europe/Stockholm',
            ),
            361 =>
            array(
                'id' => 362,
                'country_id' => 217,
                'iso2' => 'CH',
                'name' => 'Europe/Zurich',
            ),
            362 =>
            array(
                'id' => 363,
                'country_id' => 218,
                'iso2' => 'SY',
                'name' => 'Asia/Damascus',
            ),
            363 =>
            array(
                'id' => 364,
                'country_id' => 219,
                'iso2' => 'TW',
                'name' => 'Asia/Taipei',
            ),
            364 =>
            array(
                'id' => 365,
                'country_id' => 220,
                'iso2' => 'TJ',
                'name' => 'Asia/Dushanbe',
            ),
            365 =>
            array(
                'id' => 366,
                'country_id' => 221,
                'iso2' => 'TZ',
                'name' => 'Africa/Dar_es_Salaam',
            ),
            366 =>
            array(
                'id' => 367,
                'country_id' => 222,
                'iso2' => 'TH',
                'name' => 'Asia/Bangkok',
            ),
            367 =>
            array(
                'id' => 368,
                'country_id' => 223,
                'iso2' => 'TG',
                'name' => 'Africa/Lome',
            ),
            368 =>
            array(
                'id' => 369,
                'country_id' => 224,
                'iso2' => 'TK',
                'name' => 'Pacific/Fakaofo',
            ),
            369 =>
            array(
                'id' => 370,
                'country_id' => 225,
                'iso2' => 'TO',
                'name' => 'Pacific/Tongatapu',
            ),
            370 =>
            array(
                'id' => 371,
                'country_id' => 226,
                'iso2' => 'TT',
                'name' => 'America/Port_of_Spain',
            ),
            371 =>
            array(
                'id' => 372,
                'country_id' => 227,
                'iso2' => 'TN',
                'name' => 'Africa/Tunis',
            ),
            372 =>
            array(
                'id' => 373,
                'country_id' => 228,
                'iso2' => 'TR',
                'name' => 'Europe/Istanbul',
            ),
            373 =>
            array(
                'id' => 374,
                'country_id' => 229,
                'iso2' => 'TM',
                'name' => 'Asia/Ashgabat',
            ),
            374 =>
            array(
                'id' => 375,
                'country_id' => 230,
                'iso2' => 'TC',
                'name' => 'America/Grand_Turk',
            ),
            375 =>
            array(
                'id' => 376,
                'country_id' => 231,
                'iso2' => 'TV',
                'name' => 'Pacific/Funafuti',
            ),
            376 =>
            array(
                'id' => 377,
                'country_id' => 232,
                'iso2' => 'UG',
                'name' => 'Africa/Kampala',
            ),
            377 =>
            array(
                'id' => 378,
                'country_id' => 233,
                'iso2' => 'UA',
                'name' => 'Europe/Kiev',
            ),
            378 =>
            array(
                'id' => 379,
                'country_id' => 233,
                'iso2' => 'UA',
                'name' => 'Europe/Simferopol',
            ),
            379 =>
            array(
                'id' => 380,
                'country_id' => 233,
                'iso2' => 'UA',
                'name' => 'Europe/Uzhgorod',
            ),
            380 =>
            array(
                'id' => 381,
                'country_id' => 233,
                'iso2' => 'UA',
                'name' => 'Europe/Zaporozhye',
            ),
            381 =>
            array(
                'id' => 382,
                'country_id' => 234,
                'iso2' => 'AE',
                'name' => 'Asia/Dubai',
            ),
            382 =>
            array(
                'id' => 383,
                'country_id' => 235,
                'iso2' => 'GB',
                'name' => 'Europe/London',
            ),
            383 =>
            array(
                'id' => 384,
                'country_id' => 236,
                'iso2' => 'US',
                'name' => 'America/Adak',
            ),
            384 =>
            array(
                'id' => 385,
                'country_id' => 236,
                'iso2' => 'US',
                'name' => 'America/Anchorage',
            ),
            385 =>
            array(
                'id' => 386,
                'country_id' => 236,
                'iso2' => 'US',
                'name' => 'America/Boise',
            ),
            386 =>
            array(
                'id' => 387,
                'country_id' => 236,
                'iso2' => 'US',
                'name' => 'America/Chicago',
            ),
            387 =>
            array(
                'id' => 388,
                'country_id' => 236,
                'iso2' => 'US',
                'name' => 'America/Denver',
            ),
            388 =>
            array(
                'id' => 389,
                'country_id' => 236,
                'iso2' => 'US',
                'name' => 'America/Detroit',
            ),
            389 =>
            array(
                'id' => 390,
                'country_id' => 236,
                'iso2' => 'US',
                'name' => 'America/Indiana/Indianapolis',
            ),
            390 =>
            array(
                'id' => 391,
                'country_id' => 236,
                'iso2' => 'US',
                'name' => 'America/Indiana/Knox',
            ),
            391 =>
            array(
                'id' => 392,
                'country_id' => 236,
                'iso2' => 'US',
                'name' => 'America/Indiana/Marengo',
            ),
            392 =>
            array(
                'id' => 393,
                'country_id' => 236,
                'iso2' => 'US',
                'name' => 'America/Indiana/Petersburg',
            ),
            393 =>
            array(
                'id' => 394,
                'country_id' => 236,
                'iso2' => 'US',
                'name' => 'America/Indiana/Tell_City',
            ),
            394 =>
            array(
                'id' => 395,
                'country_id' => 236,
                'iso2' => 'US',
                'name' => 'America/Indiana/Vevay',
            ),
            395 =>
            array(
                'id' => 396,
                'country_id' => 236,
                'iso2' => 'US',
                'name' => 'America/Indiana/Vincennes',
            ),
            396 =>
            array(
                'id' => 397,
                'country_id' => 236,
                'iso2' => 'US',
                'name' => 'America/Indiana/Winamac',
            ),
            397 =>
            array(
                'id' => 398,
                'country_id' => 236,
                'iso2' => 'US',
                'name' => 'America/Juneau',
            ),
            398 =>
            array(
                'id' => 399,
                'country_id' => 236,
                'iso2' => 'US',
                'name' => 'America/Kentucky/Louisville',
            ),
            399 =>
            array(
                'id' => 400,
                'country_id' => 236,
                'iso2' => 'US',
                'name' => 'America/Kentucky/Monticello',
            ),
            400 =>
            array(
                'id' => 401,
                'country_id' => 236,
                'iso2' => 'US',
                'name' => 'America/Los_Angeles',
            ),
            401 =>
            array(
                'id' => 402,
                'country_id' => 236,
                'iso2' => 'US',
                'name' => 'America/Menominee',
            ),
            402 =>
            array(
                'id' => 403,
                'country_id' => 236,
                'iso2' => 'US',
                'name' => 'America/Metlakatla',
            ),
            403 =>
            array(
                'id' => 404,
                'country_id' => 236,
                'iso2' => 'US',
                'name' => 'America/New_York',
            ),
            404 =>
            array(
                'id' => 405,
                'country_id' => 236,
                'iso2' => 'US',
                'name' => 'America/Nome',
            ),
            405 =>
            array(
                'id' => 406,
                'country_id' => 236,
                'iso2' => 'US',
                'name' => 'America/North_Dakota/Beulah',
            ),
            406 =>
            array(
                'id' => 407,
                'country_id' => 236,
                'iso2' => 'US',
                'name' => 'America/North_Dakota/Center',
            ),
            407 =>
            array(
                'id' => 408,
                'country_id' => 236,
                'iso2' => 'US',
                'name' => 'America/North_Dakota/New_Salem',
            ),
            408 =>
            array(
                'id' => 409,
                'country_id' => 236,
                'iso2' => 'US',
                'name' => 'America/Phoenix',
            ),
            409 =>
            array(
                'id' => 410,
                'country_id' => 236,
                'iso2' => 'US',
                'name' => 'America/Sitka',
            ),
            410 =>
            array(
                'id' => 411,
                'country_id' => 236,
                'iso2' => 'US',
                'name' => 'America/Yakutat',
            ),
            411 =>
            array(
                'id' => 412,
                'country_id' => 236,
                'iso2' => 'US',
                'name' => 'Pacific/Honolulu',
            ),
            412 =>
            array(
                'id' => 413,
                'country_id' => 237,
                'iso2' => 'UM',
                'name' => 'Pacific/Midway',
            ),
            413 =>
            array(
                'id' => 414,
                'country_id' => 237,
                'iso2' => 'UM',
                'name' => 'Pacific/Wake',
            ),
            414 =>
            array(
                'id' => 415,
                'country_id' => 238,
                'iso2' => 'UY',
                'name' => 'America/Montevideo',
            ),
            415 =>
            array(
                'id' => 416,
                'country_id' => 239,
                'iso2' => 'UZ',
                'name' => 'Asia/Samarkand',
            ),
            416 =>
            array(
                'id' => 417,
                'country_id' => 239,
                'iso2' => 'UZ',
                'name' => 'Asia/Tashkent',
            ),
            417 =>
            array(
                'id' => 418,
                'country_id' => 240,
                'iso2' => 'VU',
                'name' => 'Pacific/Efate',
            ),
            418 =>
            array(
                'id' => 419,
                'country_id' => 241,
                'iso2' => 'VA',
                'name' => 'Europe/Vatican',
            ),
            419 =>
            array(
                'id' => 420,
                'country_id' => 242,
                'iso2' => 'VE',
                'name' => 'America/Caracas',
            ),
            420 =>
            array(
                'id' => 421,
                'country_id' => 243,
                'iso2' => 'VN',
                'name' => 'Asia/Ho_Chi_Minh',
            ),
            421 =>
            array(
                'id' => 422,
                'country_id' => 244,
                'iso2' => 'VG',
                'name' => 'America/Tortola',
            ),
            422 =>
            array(
                'id' => 423,
                'country_id' => 245,
                'iso2' => 'VI',
                'name' => 'America/St_Thomas',
            ),
            423 =>
            array(
                'id' => 424,
                'country_id' => 246,
                'iso2' => 'WF',
                'name' => 'Pacific/Wallis',
            ),
            424 =>
            array(
                'id' => 425,
                'country_id' => 247,
                'iso2' => 'EH',
                'name' => 'Africa/El_Aaiun',
            ),
            425 =>
            array(
                'id' => 426,
                'country_id' => 248,
                'iso2' => 'YE',
                'name' => 'Asia/Aden',
            ),
            426 =>
            array(
                'id' => 427,
                'country_id' => 249,
                'iso2' => 'ZM',
                'name' => 'Africa/Lusaka',
            ),
            427 =>
            array(
                'id' => 428,
                'country_id' => 250,
                'iso2' => 'ZW',
                'name' => 'Africa/Harare',
            ),
        ));
    }
}
