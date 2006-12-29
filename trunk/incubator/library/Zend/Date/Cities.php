<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Date
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: DateObject.php 2511 2006-12-26 22:54:37Z bkarwin $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * @category   Zend
 * @package    Zend_Date
 * @subpackage Zend_Date_Cities
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

// Additional data for sunset/sunrise calculations
// Holds the geographical data for all capital and many other cities worldwide
// Original data from http://www.fallingrain.com/world/
$City = array(
    'Abidjan'     => array('latitude' =>    5.3411111, 'longitude' =>   -4.0280556),
    'Abu Dhabi'   => array('latitude' =>   24.4666667, 'longitude' =>   54.3666667),
    'Abuja'       => array('latitude' =>    9.1758333, 'longitude' =>    7.1808333),
    'Accra'       => array('latitude' =>    5.55,      'longitude' =>   -0.2166667),
    'Adamstown'   => array('latitude' =>  -25.0666667, 'longitude' => -130.0833333),
    'Addis Ababa' => array('latitude' =>    9.0333333, 'longitude' =>   38.7),
    'Adelaide'    => array('latitude' =>  -34.9333333, 'longitude' =>  138.6),
    'Algiers'     => array('latitude' =>   36.7630556, 'longitude' =>    3.0505556),
    'Alofi'       => array('latitude' =>  -19.0166667, 'longitude' => -169.9166667),
    'Amman'       => array('latitude' =>   31.95,      'longitude' =>   35.9333333),
    'Amsterdam'   => array('latitude' =>   52.35,      'longitude' =>    4.9166667),
    'Andorra la Vella' => array('latitude' => 42.5,    'longitude' =>    1.5166667),
    'Ankara'      => array('latitude' =>   39.9272222, 'longitude' =>   32.8644444),
    'Antananarivo' => array('latitude' => -18.9166667, 'longitude' =>   47.5166667),
    'Apia'        => array('latitude' =>  -13.8333333, 'longitude' => -171.7333333),
    'Ashgabat'    => array('latitude' =>   37.95,      'longitude' =>   58.3833333),
    'Asmara'      => array('latitude' =>   15.3333333, 'longitude' =>   38.9333333),
    'Astana'      => array('latitude' =>   51.1811111, 'longitude' =>   71.4277778),
    'Asunción'    => array('latitude' =>  -25.2666667, 'longitude' =>  -57.6666667),
    'Athens'      => array('latitude' =>   37.9833333, 'longitude' =>   23.7333333),
    'Auckland'    => array('latitude' =>  -36.8666667, 'longitude' =>  174.7666667),
    'Avarua'      => array('latitude' =>  -21.2,       'longitude' => -159.7666667),
    'Baghdad'     => array('latitude' =>   33.3386111, 'longitude' =>   44.3938889),
    'Baku'        => array('latitude' =>   40.3952778, 'longitude' =>   49.8822222),
    'Bamako'      => array('latitude' =>   12.65,      'longitude' =>   -8),
    'Bandar Seri Begawan'      => array('latitude' => 4.8833333, 'longitude' => 114.9333333),
    'Bankok'      => array('latitude' =>   13.5833333, 'longitude' =>  100.2166667),
    'Bangui'      => array('latitude' =>    4.3666667, 'longitude' =>   18.5833333),
    'Banjul'      => array('latitude' =>   13.4530556, 'longitude' =>  -16.5775),
    'Basel'       => array('latitude' =>   47.5666667, 'longitude' =>    7.6),
    'Basseterre'  => array('latitude' =>   17.3,       'longitude' =>  -62.7166667),
    'Beijing'     => array('latitude' =>   39.9288889, 'longitude' =>  116.3883333),
    'Beirut'      => array('latitude' =>   33.8719444, 'longitude' =>   35.5097222),
    'Belgrade'    => array('latitude' =>   44.8186111, 'longitude' =>   20.4680556),
    'Belmopan'    => array('latitude' =>   17.25,      'longitude' =>  -88.7666667),
    'Berlin'      => array('latitude' =>   52.5166667, 'longitude' =>   13.4),
    'Bern'        => array('latitude' =>   46.9166667, 'longitude' =>    7.4666667),
    'Bishkek'     => array('latitude' =>   42.8730556, 'longitude' =>   74.6002778),
    'Bissau'      => array('latitude' =>   11.85,      'longitude' =>  -15.5833333),
    'Bloemfontein' => array('latitude' => -29.1333333, 'longitude' =>   26.2),
    'Bogotá'      => array('latitude' =>    4.6,       'longitude' =>  -74.0833333),
    'Brasilia'    => array('latitude' =>  -15.7833333, 'longitude' =>  -47.9166667),
    'Bratislava'  => array('latitude' =>   48.15,      'longitude' =>   17.1166667),
    'Brazzaville' => array('latitude' =>   -4.2591667, 'longitude' =>   15.2847222),
    'Bridgetown'  => array('latitude' => 0, 'longitude' => 0),
    'Brisbane'    => array('latitude' => 0, 'longitude' => 0),
    'Brussels'    => array('latitude' => 0, 'longitude' => 0),
    'Bucharest'   => array('latitude' => 0, 'longitude' => 0),
    'Budapest'    => array('latitude' => 0, 'longitude' => 0),
    'Buenos Aires' => array('latitude' => 0, 'longitude' => 0),
    'Bujumbura'   => array('latitude' => 0, 'longitude' => 0),
    'Cairo'       => array('latitude' => 0, 'longitude' => 0),
    'Calgari'     => array('latitude' => 0, 'longitude' => 0),
    'Canberra'    => array('latitude' => 0, 'longitude' => 0),
    'Cape Town'   => array('latitude' => 0, 'longitude' => 0),
    'Caracas'     => array('latitude' => 0, 'longitude' => 0),
    'Castries'    => array('latitude' => 0, 'longitude' => 0),
    'Charlotte Amalie' => array('latitude' => 0, 'longitude' => 0),
    'Chicago'     => array('latitude' => 0, 'longitude' => 0),
    'Chisinau'    => array('latitude' => 0, 'longitude' => 0),
    'Cockburn Town' => array('latitude' => 0, 'longitude' => 0),
    'Columbo'     => array('latitude' => 0, 'longitude' => 0),
    'Conakry'     => array('latitude' => 0, 'longitude' => 0),
    'Copenhagen'  => array('latitude' => 0, 'longitude' => 0),
    'Cotonou'     => array('latitude' => 0, 'longitude' => 0),
    'Dakar'       => array('latitude' => 0, 'longitude' => 0),
    'Damascus'    => array('latitude' => 0, 'longitude' => 0),
    'Dar es Salaam' => array('latitude' => 0, 'longitude' => 0),
    'Dhaka'       => array('latitude' => 0, 'longitude' => 0),
    'Dili'        => array('latitude' => 0, 'longitude' => 0),
    'Djibouti'    => array('latitude' => 0, 'longitude' => 0),
    'Dodoma'      => array('latitude' => 0, 'longitude' => 0),
    'Doha'        => array('latitude' => 0, 'longitude' => 0),
    'Dubai'       => array('latitude' => 0, 'longitude' => 0),
    'Dublin'      => array('latitude' => 0, 'longitude' => 0),
    'Dushanbe'    => array('latitude' => 0, 'longitude' => 0),
    'Episkopi Cantonment' => array('latitude' => 0, 'longitude' => 0),
    'Fagatogo'    => array('latitude' => 0, 'longitude' => 0),
    'Fongafale'   => array('latitude' => 0, 'longitude' => 0),
    'Freetown'    => array('latitude' => 0, 'longitude' => 0),
    'Gaborone'    => array('latitude' => 0, 'longitude' => 0),
    'Geneva'      => array('latitude' => 0, 'longitude' => 0),
    'George Town' => array('latitude' => 0, 'longitude' => 0),
    'Georgetown'  => array('latitude' => 0, 'longitude' => 0),
    'Gibraltar'   => array('latitude' => 0, 'longitude' => 0),
    'Glasgow'     => array('latitude' => 0, 'longitude' => 0),
    'Grytviken'   => array('latitude' => 0, 'longitude' => 0),
    'Guatemala City' => array('latitude' => 0, 'longitude' => 0),
    'Hagatna'     => array('latitude' => 0, 'longitude' => 0),
    'The Hague'   => array('latitude' => 0, 'longitude' => 0),
    'Hamilton'    => array('latitude' => 0, 'longitude' => 0),
    'Hanoi'       => array('latitude' => 0, 'longitude' => 0),
    'Harare'      => array('latitude' => 0, 'longitude' => 0),
    'Hargeisa'    => array('latitude' => 0, 'longitude' => 0),
    'Havana'      => array('latitude' => 0, 'longitude' => 0),
    'Helsinki'    => array('latitude' => 0, 'longitude' => 0),
    'Honiara'     => array('latitude' => 0, 'longitude' => 0),
    'Islamabad'   => array('latitude' => 0, 'longitude' => 0),
    'Istanbul'    => array('latitude' => 0, 'longitude' => 0),
    'Jakarta'     => array('latitude' => 0, 'longitude' => 0),
    'Jamestown'   => array('latitude' => 0, 'longitude' => 0),
    'Jerusalem'   => array('latitude' => 0, 'longitude' => 0),
    'Johannesburg' => array('latitude' => 0, 'longitude' => 0),
    'Kabul'       => array('latitude' => 0, 'longitude' => 0),
    'Kampala'     => array('latitude' => 0, 'longitude' => 0),
    'Kathmandu'   => array('latitude' => 0, 'longitude' => 0),
    'Khartoum'    => array('latitude' => 0, 'longitude' => 0),
    'Kigali'      => array('latitude' => 0, 'longitude' => 0),
    'Kingston'    => array('latitude' => 0, 'longitude' => 0),
    'Kingstown'   => array('latitude' => 0, 'longitude' => 0),
    'Kinshasa'    => array('latitude' => 0, 'longitude' => 0),
    'Kolkata'     => array('latitude' => 0, 'longitude' => 0),
    'Kuala Lumpur' => array('latitude' => 0, 'longitude' => 0),
    'Kuwait City' => array('latitude' => 0, 'longitude' => 0),
    'Kiev'        => array('latitude' => 0, 'longitude' => 0),
    'La Paz'      => array('latitude' => 0, 'longitude' => 0),
    'Laâyoune'    => array('latitude' => 0, 'longitude' => 0),
    'Libreville'  => array('latitude' => 0, 'longitude' => 0),
    'Lilongwe'    => array('latitude' => 0, 'longitude' => 0),
    'Lima'        => array('latitude' => 0, 'longitude' => 0),
    'Lisbon'      => array('latitude' => 0, 'longitude' => 0),
    'Ljubljana'   => array('latitude' => 0, 'longitude' => 0),
    'Lobamba'     => array('latitude' => 0, 'longitude' => 0),
    'Lomé'        => array('latitude' => 0, 'longitude' => 0),
    'London'      => array('latitude' => 0, 'longitude' => 0),
    'Los Angeles' => array('latitude' => 0, 'longitude' => 0),
    'Luanda'      => array('latitude' => 0, 'longitude' => 0),
    'Lusaka'      => array('latitude' => 0, 'longitude' => 0),
    'Luxembourg'  => array('latitude' => 0, 'longitude' => 0),
    'Madrid'      => array('latitude' => 0, 'longitude' => 0),
    'Majuro'      => array('latitude' => 0, 'longitude' => 0),
    'Malabo'      => array('latitude' => 0, 'longitude' => 0),
    'Malé'        => array('latitude' => 0, 'longitude' => 0),
    'Managua'     => array('latitude' => 0, 'longitude' => 0),
    'Manama'      => array('latitude' => 0, 'longitude' => 0),
    'Manila'      => array('latitude' => 0, 'longitude' => 0),
    'Maputo'      => array('latitude' => 0, 'longitude' => 0),
    'Mariehamn'   => array('latitude' => 0, 'longitude' => 0),
    'Maseru'      => array('latitude' => 0, 'longitude' => 0),
    'Mata-Utu'    => array('latitude' => 0, 'longitude' => 0),
    'Mbabane'     => array('latitude' => 0, 'longitude' => 0),
    'Melbourne'   => array('latitude' => 0, 'longitude' => 0),
    'Melekeok'    => array('latitude' => 0, 'longitude' => 0),
    'Mexiko City' => array('latitude' => 0, 'longitude' => 0),
    'Minsk'       => array('latitude' => 0, 'longitude' => 0),
    'Mogadishu'   => array('latitude' => 0, 'longitude' => 0),
    'Monaco'      => array('latitude' => 0, 'longitude' => 0),
    'Monrovia'    => array('latitude' => 0, 'longitude' => 0),
    'Montevideo'  => array('latitude' => 0, 'longitude' => 0),
    'Montreal'    => array('latitude' => 0, 'longitude' => 0),
    'Moroni'      => array('latitude' => 0, 'longitude' => 0),
    'Moscow'      => array('latitude' => 0, 'longitude' => 0),
    'Muscat'      => array('latitude' => 0, 'longitude' => 0),
    'Nairobi'     => array('latitude' => 0, 'longitude' => 0),
    'Nassau'      => array('latitude' => 0, 'longitude' => 0),
    'Naypyidaw'   => array('latitude' => 0, 'longitude' => 0),
    'N´Djamena'   => array('latitude' => 0, 'longitude' => 0),
    'New Dehli'   => array('latitude' => 0, 'longitude' => 0),
    'New York'    => array('latitude' => 0, 'longitude' => 0),
    'Newcastle'   => array('latitude' => 0, 'longitude' => 0),
    'Niamey'      => array('latitude' => 0, 'longitude' => 0),
    'Nicosia'     => array('latitude' => 0, 'longitude' => 0),
    'Nouakchott'  => array('latitude' => 0, 'longitude' => 0),
    'Nouméa'      => array('latitude' => 0, 'longitude' => 0),
    'Nuku´alofa'  => array('latitude' => 0, 'longitude' => 0),
    'Nuuk'        => array('latitude' => 0, 'longitude' => 0),
    'Oranjestad'  => array('latitude' => 0, 'longitude' => 0),
    'Oslo'        => array('latitude' => 0, 'longitude' => 0),
    'Ouagadougou' => array('latitude' => 0, 'longitude' => 0),
    'Palikir'     => array('latitude' => 0, 'longitude' => 0),
    'Panama City' => array('latitude' => 0, 'longitude' => 0),
    'Papeete'     => array('latitude' => 0, 'longitude' => 0),
    'Paramaribo'  => array('latitude' => 0, 'longitude' => 0),
    'Paris'       => array('latitude' => 0, 'longitude' => 0),
    'Perth'       => array('latitude' => 0, 'longitude' => 0),
    'Phnom Penh'  => array('latitude' => 0, 'longitude' => 0),
    'Podgorica'   => array('latitude' => 0, 'longitude' => 0),
    'Port Louis'  => array('latitude' => 0, 'longitude' => 0),
    'Port Moresby' => array('latitude' => 0, 'longitude' => 0),
    'Port Vila'   => array('latitude' => 0, 'longitude' => 0),
    'Port-au-Prince' => array('latitude' => 0, 'longitude' => 0),
    'Port of Spain'  => array('latitude' => 0, 'longitude' => 0),
    'Porto-Novo'  => array('latitude' => 0, 'longitude' => 0),
    'Prague'      => array('latitude' => 0, 'longitude' => 0),
    'Praia'       => array('latitude' => 0, 'longitude' => 0),
    'Pretoria'    => array('latitude' => 0, 'longitude' => 0),
    'Putrajaya'   => array('latitude' => 0, 'longitude' => 0),
    'P´yongyang'  => array('latitude' => 0, 'longitude' => 0),
    'Quito'       => array('latitude' => 0, 'longitude' => 0),
    'Rabat'       => array('latitude' => 0, 'longitude' => 0),
    'Reykjavik'   => array('latitude' => 0, 'longitude' => 0),
    'Riga'        => array('latitude' => 0, 'longitude' => 0),
    'Rio de Janero' => array('latitude' => 0, 'longitude' => 0),
    'Riyadh'      => array('latitude' => 0, 'longitude' => 0),
    'Road Town'   => array('latitude' => 0, 'longitude' => 0),
    'Rome'        => array('latitude' => 0, 'longitude' => 0),
    'Roseau'      => array('latitude' => 0, 'longitude' => 0),
    'Rotterdam'   => array('latitude' => 0, 'longitude' => 0),
    'Saipan'      => array('latitude' => 0, 'longitude' => 0),
    'Salvador'    => array('latitude' => 0, 'longitude' => 0),
    'San José'    => array('latitude' => 0, 'longitude' => 0),
    'San Juan'    => array('latitude' => 0, 'longitude' => 0),
    'San Marino'  => array('latitude' => 0, 'longitude' => 0),
    'San Salvador' => array('latitude' => 0, 'longitude' => 0),
    'Sanaá'       => array('latitude' => 0, 'longitude' => 0),
    'Santa Cruz'  => array('latitude' => 0, 'longitude' => 0),
    'Santiago'    => array('latitude' => 0, 'longitude' => 0),
    'Santo Domingo' => array('latitude' => 0, 'longitude' => 0),
    'Sao Paulo'   => array('latitude' => 0, 'longitude' => 0),
    'Sao Tomé'    => array('latitude' => 0, 'longitude' => 0),
    'Sarajevo'    => array('latitude' => 0, 'longitude' => 0),
    'Seoul'       => array('latitude' => 0, 'longitude' => 0),
    'Shanghai'    => array('latitude' => 0, 'longitude' => 0),
    'Sidney'      => array('latitude' => 0, 'longitude' => 0),
    'Singapore'   => array('latitude' => 0, 'longitude' => 0),
    'Skopje'      => array('latitude' => 0, 'longitude' => 0),
    'Sofia'       => array('latitude' => 0, 'longitude' => 0),
    'South Tarawa' => array('latitude' => 0, 'longitude' => 0),
    'Sri Jayawardenepura' => array('latitude' => 0, 'longitude' => 0),
    'St. George´s' => array('latitude' => 0, 'longitude' => 0),
    'St. John´s'  => array('latitude' => 0, 'longitude' => 0),
    'Stanley'     => array('latitude' => 0, 'longitude' => 0),
    'Stockholm'   => array('latitude' => 0, 'longitude' => 0),
    'Sucre'       => array('latitude' => 0, 'longitude' => 0),
    'Suva'        => array('latitude' => 0, 'longitude' => 0),
    'Taipei'      => array('latitude' => 0, 'longitude' => 0),
    'Tallinn'     => array('latitude' => 0, 'longitude' => 0),
    'Tashkent'    => array('latitude' => 0, 'longitude' => 0),
    'Tbilisi'     => array('latitude' => 0, 'longitude' => 0),
    'Tegucigalpa' => array('latitude' => 0, 'longitude' => 0),
    'Tehran'      => array('latitude' => 0, 'longitude' => 0),
    'The Hague'   => array('latitude' => 0, 'longitude' => 0),
    'Thimphu'     => array('latitude' => 0, 'longitude' => 0),
    'Tirana'      => array('latitude' => 0, 'longitude' => 0),
    'Tiraspol'    => array('latitude' => 0, 'longitude' => 0),
    'Tokyo'       => array('latitude' => 0, 'longitude' => 0),
    'Toronto'     => array('latitude' => 0, 'longitude' => 0),
    'Tórshavn'    => array('latitude' => 0, 'longitude' => 0),
    'Tripoli'     => array('latitude' => 0, 'longitude' => 0),
    'Tunis'       => array('latitude' => 0, 'longitude' => 0),
    'Ulaanbaatar' => array('latitude' => 0, 'longitude' => 0),
    'Vaduz'       => array('latitude' => 0, 'longitude' => 0),
    'Valletta'    => array('latitude' => 0, 'longitude' => 0),
    'The Valley'  => array('latitude' => 0, 'longitude' => 0),
    'Valparaíso'  => array('latitude' => 0, 'longitude' => 0),
    'Vancouver'   => array('latitude' => 0, 'longitude' => 0),
    'Vatican City' => array('latitude' => 0, 'longitude' => 0),
    'Victoria'    => array('latitude' => 0, 'longitude' => 0),
    'Vienna'      => array('latitude' => 0, 'longitude' => 0),
    'Vientaine'   => array('latitude' => 0, 'longitude' => 0),
    'Vilnius'     => array('latitude' => 0, 'longitude' => 0),
    'Warsaw'      => array('latitude' => 0, 'longitude' => 0),
    'Washington DC' => array('latitude' => 0, 'longitude' => 0),
    'Wellington'  => array('latitude' => 0, 'longitude' => 0),
    'West Island' => array('latitude' => 0, 'longitude' => 0),
    'Willemstad'  => array('latitude' => 0, 'longitude' => 0),
    'Windhoek'    => array('latitude' => 0, 'longitude' => 0),
    'Yamoussoukro' => array('latitude' => 0, 'longitude' => 0),
    'Yaoundé'     => array('latitude' => 0, 'longitude' => 0),
    'Yeravan'     => array('latitude' => 0, 'longitude' => 0),
    'Zürich'      => array('latitude' => 0, 'longitude' => 0),
    'Zagreb'      => array('latitude' => 0, 'longitude' => 0)
);