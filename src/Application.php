<?php namespace App;

/*
 * Copyright 2019 Mohamed Benrebia <mohamed@ipfinder.io>
 *
 * Licensed under the Apache License, Version 2.0 (the License);
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an AS IS BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 *
 * @see       This file is part of the box project.
 * @see       https://github.com/humbug/box/
 * @category  App
 * @author    Mohamed Benrebia <mohamed@ipfinder.io>
 * @copyright 2019 Mohamed Benrebia
 * @license   https://opensource.org/licenses/Apache-2.0 Apache License, Version 2.0
 * @link      https://ipfinder.io
 */

use App\Banners;
use App\Color;
use App\Shell\Shell;

use ipfinder\ipfinder\Exception\IPfinderException;
use ipfinder\ipfinder\IPfinder;
use ipfinder\ipfinder\Validation\Asnvalidation;
use ipfinder\ipfinder\Validation\Firewallvalidation;
use ipfinder\ipfinder\Validation\Ipvalidation;
use ipfinder\ipfinder\Validation\Tokenvalidation;

/**
 * The main class.
 *
 * @category  App
 * @author    Mohamed Benrebia <mohamed@ipfinder.io>
 * @copyright 2019 Mohamed Benrebia
 * @license   https://opensource.org/licenses/Apache-2.0 Apache License, Version 2.0
 * @link      https://ipfinder.io
 * @version   1.0.2
 */
class Application extends Color
{
    /**
     * Current version ipfinder cli
     *
     * @var string
     */
    const VERSION = '1.0.2';

    /**
     * The hidden folder name
     *
     * @var string
     */
    const IPFINDER = '.ipfinder';

    /**
     * the hidden token file name
     *
     * @var string
     */
    const TOKEN = '.ipfindertoken';

    /**
     * current file to save data
     *
     * @var string
     */
    const QUERY = 'query';

    /**
     * current FOLDER to save data
     *
     * @var string
     */
    const DATA = 'DATA';

    /**
     * This is a static class, do not instantiate it
     *
     */
    public function __construct()
    {

        if (isset($_SERVER['HOME'])) {
            $this->home    = $_SERVER['HOME'] . '/';
        } else {
            $this->home   = $_SERVER['HOMEPATH'] . '/';
        }


        $this->version  = self::VERSION;
        $this->ipfinder = $this->home . self::IPFINDER;
        $this->ipfinderdata = $this->ipfinder.'/'.self::DATA;
        $this->token    = $this->home . self::TOKEN.".json";

        $this->query = $this->ipfinder . '/' . self::QUERY . ".csv";
        $this->date  = $this->ipfinder . '/' . self::QUERY . '-' . date("Y-m-d") . ".json";


        // create hidden folder
        (!is_dir($this->ipfinder) ? mkdir($this->ipfinder, 0777, true) : null);

        // Data FOLDER inside  hidden folder
        (!is_dir($this->ipfinderdata) ? mkdir($this->ipfinderdata, 0777, true) : null);

        // create hidden file
        (!file_exists($this->token) ? touch($this->token) : null);

        // create csv and json file
        (!file_exists($this->query) ? touch($this->query) : null);

        (!file_exists($this->date) ? touch($this->date) : null);

        $token_api = file_get_contents($this->token, true);
        $arr  = json_decode($token_api);

        if (filesize($this->token) == 0) {
            $this->lang = require_once(__DIR__."/lang/en.php");
            $this->lib = new IPfinder();
            $this->output = 'table';
        } else {
            if (file_exists(__DIR__ . "/lang/{$arr->__lang}.php")) {
                $this->lang = require __DIR__ . "/lang/{$arr->__lang}.php";
            } else {
                $this->lang = require __DIR__ . "/lang/en.php";
            }

         // Tokenvalidation::validate($arr->__token);
            $this->lib = new IPfinder($arr->__token);

            $this->output = $arr->__output;
        }

     // sendbox
     // $this->lib    = new IPfinder($this->token_api, 'http://api.sample.com/v1/');
        $this->color  = new Color();

     //   echo $this->colors['LIGHT_GREEN']."asdasdsd";
        $this->banner = new Banners('version: ' . $this->version);
    }
    /**
     * Set the Command options
     *
     * @param  array   $commande_shortopts   The shortopts lits
     * @param  array   $commande_longopts    The longopts row
     * @return array   list app command $this->options
     */
    public function Command($commande_shortopts = [], $commande_longopts = [])
    {
        $this->options = getopt($commande_longopts, $commande_shortopts);

        $this->key = array_keys($this->options);
        $val = array_values($this->options);

        if (isset($val[0])) {
        $this->first  = $val[0];
        }
        if (isset($val[1])) {
            $this->second = $val[1];
        } else {
            $this->second = null;
        }
        //$this->second = $val[1];
        if (isset($val[2])) {
            $this->third  = $val[2];
        } elseif (isset($val[1])) {
            $this->third  = $val[1];
        } else {
            $this->third  = null;
        }

        return $this->options;
    }
    /**
     * Save data to query-Y-m-d.json
     *
     * @param  string    $data The json data
     *
     * @return all body in this day in json file
     */
    public function __query_date(string $data)
    {
        $fop = fopen($this->date, 'a+');
        fwrite($fop, "$data\n");
        fclose($fop);
    }
    /**
     * Save input to query.csv
     *
     * @param  string    $k the command value
     * @param  string    $c The command key
     *
     * @return string    date , value , key in csv file
     */
    public function __query(string $k, string $c)
    {
        $fop = fopen($this->query, 'a+');
        fwrite($fop, date("Y-m-d H:i:s") . "," . $k . ",$c\n");
        fclose($fop);
    }
    /**
     * save to a given file
     *
     * @param  string|null    $file file name
     * @param  string         $data body
     *
     * @return string         file with data
     */
    public function __output(string $file = null, string $data)
    {

        if (isset($file)) {
            $path = $this->ipfinderdata.'/'.$file;
            $fop = fopen($path, 'a+');
            fwrite($fop, "$data\n");
            fclose($fop);
            print $this->colors['LIGHT_GREEN']."[!] ✓ File save in >>> \e[0;31;42m $path\e[0m\n";
        }
    }

    /**
     *  Out data to console
     * @param  array  $data data form call as array
     * @param  string $json data form call as json
     * @return string  print data to console
     */
    public function __printData($data = [], string $json)
    {
        if ($this->output !== 'table') {
            print "\e[0;37;41m$json \e[0m\n";
        } else {
            array_walk_recursive($data, function ($value, $key) {
                (is_bool($value) ? $v = $value ? 'True' : 'False' : $v = $value);
                (is_null($v) ? $t = 'null' : $t = $v);
                print str_pad("{$this->color->white}|" . $key, 40) . "| {$this->color->white}{!} \e[0;37;41m$t \e[0m \n";
            });
        }


        print "{$this->color->white}--------------------------------------------------------------------------------{$this->color->white}\n";
    }
    /**
     *
     *  do not instantiate it
     *
     */
    public function Menu()
    {
        print $this->banner->rand;
        print "
{$this->color->white}[!]{$this->lang['web']}:: {$this->color->red}https://ipfinder.io
{$this->color->white}[!]{$this->lang['php_version']}::{$this->color->red}[ " . phpversion() . " ]
{$this->color->white}[!]{$this->lang['cli_version']}::{$this->color->red}[ " . $this->version . " ]
{$this->color->white}[!]{$this->lang['config_file']}::{$this->color->red}[ " . $this->token . " ]
{$this->color->white}[!]{$this->lang['config_file']}::{$this->color->red}[ " . $this->token . " ]
{$this->color->white}[!]{$this->lang['uname']}::{$this->color->red}[ " . php_uname() . "]
{$this->color->white}[!]{$this->lang['pwd']} ::{$this->color->red}[ " . getcwd() . "]
{$this->color->white}[!]{$this->lang['help']}::{$this->color->red}ipfinder --help

        \n";
    }
    /**
     *
     *  do not instantiate it
     *
     */
    public function Help(int $o)
    {
        if ($o == 1) {
            print "
{$this->color->white}#### ########     ######## #### ##    ## ########  ######## ########     ##     ## ######## ##       ########
{$this->color->green} ##  ##     ##    ##        ##  ###   ## ##     ## ##       ##     ##    ##     ## ##       ##       ##     ##
{$this->color->white} ##  ##     ##    ##        ##  ####  ## ##     ## ##       ##     ##    ##     ## ##       ##       ##     ##
{$this->color->blue} ##  ########     ######    ##  ## ## ## ##     ## ######   ########     ######### ######   ##       ########
{$this->color->white} ##  ##           ##        ##  ##  #### ##     ## ##       ##   ##      ##     ## ##       ##       ##
{$this->color->purple} ##  ##           ##        ##  ##   ### ##     ## ##       ##    ##     ##     ## ##       ##       ##
{$this->color->white}#### ##           ##       #### ##    ## ########  ######## ##     ##    ##     ## ######## ######## ##

{$this->color->white}[!]{$this->lang['web']}:: {$this->color->red}https://ipfinder.io
{$this->color->white}[!]{$this->lang['php_version']}::{$this->color->red}[ " . phpversion() . " ]
{$this->color->white}[!]{$this->lang['cli_version']}::{$this->color->red}[ " . $this->version . " ]
{$this->color->white}[!]{$this->lang['config_file']}::{$this->color->red}[ " . $this->token . " ]
{$this->color->white}[!]{$this->lang['uname']}::{$this->color->red}[ " . php_uname() . "]
{$this->color->white}[!]{$this->lang['pwd']} ::{$this->color->red}[ " . getcwd() . "]
{$this->color->white}[!]{$this->lang['help']}::{$this->color->red}ipfinder --help

{$this->color->end}-h ,--help {$this->color->blue} {$this->lang['help_app']}
{$this->color->end}-o ,--output {$this->color->blue} {$this->lang['output']}
{$this->color->end}-u ,--update {$this->color->blue} {$this->lang['update']}
{$this->color->end}-a ,--auth{$this->color->blue}   {$this->lang['l_ipaddress']}
{$this->color->end}-m ,--format{$this->color->blue}   {$this->lang['e_format']}
\t[Example]:
\t\t{$this->color->light_cyan} $ ipfinder -a{$this->color->light_cyan}
{$this->color->end}-i ,--ip{$this->color->blue}  {$this->lang['f_ipaddress']}
\t[Example]:
\t\t{$this->color->light_cyan} $ ipfinder --ip 1.0.0.0{$this->color->light_cyan}
\t\t{$this->color->light_cyan} $ ipfinder -i 2c0f:fb50:4003::{$this->color->light_cyan}
\t\t{$this->color->light_cyan} $ ipfinder -i filename{$this->color->light_cyan}
{$this->color->end}-n ,--asn{$this->color->blue}   {$this->lang['f_asnumber']}
\t[Example]:
\t\t{$this->color->light_cyan} $ ipfinder --ip 1.0.0.0{$this->color->light_cyan}
\t\t{$this->color->light_cyan} $ ipfinder -i 2c0f:fb50:4003::{$this->color->light_cyan}
\t\t{$this->color->light_cyan} $ ipfinder -i filename{$this->color->light_cyan}
{$this->color->end}-r ,--ranges{$this->color->blue} {$this->lang['f_ranges']}
\t[Example]:
\t\t{$this->color->light_cyan} $ ipfinder --ranges 'Telecom Algeria'{$this->color->light_cyan}
\t\t{$this->color->light_cyan} $ ipfinder -r 'Telecom Algeria'{$this->color->light_cyan}
{$this->color->end}-f ,--firewall{$this->color->blue} {$this->lang['f_firewall']}
\t[Example]:
\t\t{$this->color->light_cyan} $ ipfinder -f AS1 --format juniper_junos {$this->color->light_cyan}
\t\t{$this->color->light_cyan} $ ipfinder --firewall DZ -m web_config_allow {$this->color->light_cyan}
{$this->color->end}-d ,--domain{$this->color->blue} {$this->lang['f_domain']}
\t[Example]:
\t\t{$this->color->light_cyan} $ ipfinder -d google.com {$this->color->light_cyan}
\t\t{$this->color->light_cyan} $ ipfinder --domain google.com {$this->color->light_cyan}
{$this->color->end}-dh ,--dhistory{$this->color->blue} {$this->lang['f_hdomain']}
\t[Example]:
\t\t{$this->color->light_cyan} $ ipfinder -dh google.com {$this->color->light_cyan}
\t\t{$this->color->light_cyan} $ ipfinder --dhistory google.com {$this->color->light_cyan}
{$this->color->end}-dl ,--dlist{$this->color->blue} {$this->lang['f_ldomain']}
\t[Example]:
\t\t{$this->color->light_cyan} $ ipfinder -dl AS1  {$this->color->light_cyan}
\t\t{$this->color->light_cyan} $ ipfinder --dlist DZ  {$this->color->light_cyan}
{$this->color->end}-s ,--status{$this->color->blue}   {$this->lang['f_information']}

\t[Example]:
\t\t{$this->color->light_cyan} $ ipfinder -s{$this->color->light_cyan}
{$this->color->end}-g ,--config{$this->color->blue}  {$this->lang['token']}
\t[Example]:
\t\t{$this->color->light_cyan} $ ipfinder --config{$this->color->light_cyan}
{$this->color->end}-l ,--shell{$this->color->blue}  {$this->lang['shell']}
\n";
        } else {
            print "
 __  .______    _______  __  .__   __.  _______   _______ .______
|  | |   _  \  |   ____||  | |  \ |  | |       \ |   ____||   _  \
|  | |  |_)  | |  |__   |  | |   \|  | |  .--.  ||  |__   |  |_)  |
|  | |   ___/  |   __|  |  | |  . `  | |  |  |  ||   __|  |      /
|  | |  |      |  |     |  | |  |\   | |  '--'  ||  |____ |  |\  \----.
|__| | _|      |__|     |__| |__| \__| |_______/ |_______|| _| `._____| {$this->color->red}version:{$this->version}{$this->color->red}{$this->color->white} official documentation https://ipfinder.io/docs{$this->color->white}

{$this->color->red}[1,help,h]:: {$this->color->white}{$this->lang['help_app']}
{$this->color->red}[2,auth,a]:: {$this->color->white}{$this->lang['l_ipaddress']}
{$this->color->red}[3,status,s]:: {$this->color->white}{$this->lang['f_information']}
{$this->color->red}[4,ip,i]:: {$this->color->white}{$this->lang['f_ipaddress']}
{$this->color->red}[5,asn,n]:: {$this->color->white}{$this->lang['f_asnumber']}
{$this->color->red}[6,ranges,r]:: {$this->color->white}{$this->lang['f_ranges']}
{$this->color->red}[7,firewall,f]:: {$this->color->white}{$this->lang['f_firewall']}
{$this->color->red}[8,domain,d]:: {$this->color->white}{$this->lang['f_domain']}
{$this->color->red}[9,dhistory,dh]:: {$this->color->white}{$this->lang['f_hdomain']}
{$this->color->red}[10,dlist,dl]:: {$this->color->white}{$this->lang['f_ldomain']}
{$this->color->red}[11,exit,x]:: {$this->color->white}{$this->lang['exit']}
{$this->color->red}[12,clear,c]:: {$this->color->white}{$this->lang['help_app']}
            ";
        }
    }
    /**
     * read file
     *
     * @param  string    $file the file from bulk
     *
     * @return list ip or asn with count
     */
    public function __getfile(string $file)
    {

        $this->path  = file($file, FILE_IGNORE_NEW_LINES);
        $this->count = count($this->path);
        foreach ($this->path as $key) {
            $this->ouuut[] = $key;
        }
    }
    /**
     * Get details for an Your IP address.
     * @param  string|null    $file file name
     *
     * @return Your IP address data.
     */
    public function __getAuth(string $file = null)
    {

        try {
            print $this->banner->rand;
            $details = $this->lib->Authentication();
            print $this->__printData($details, $this->lib->raw_body);
            $this->__query_date($this->lib->raw_body);
            $this->json = $this->lib->raw_body;
            $this->__output($file, $this->json);
        } catch (IPfinderException $e) {
            print "{$this->color->red}{$this->lang['error']} {$this->color->red}" . $e->getMessage() . "\n";
        }
    }
    /**
     *  details for an IP address or more .
     * @param  string         $p    IP address or file name exists.
     * @param  string|null    $file file name
     *
     *
     * @return IP address data.
     * @throws IPfinderException
     */
    public function __getIp(string $p, string $file = null)
    {

        try {
            print $this->banner->rand;
            if (!file_exists($p)) {
                Ipvalidation::validate($p);
                $details = $this->lib->getAddressInfo($p);
                print $this->__printData($details, $this->lib->raw_body);
                $this->__query_date($this->lib->raw_body);
                $this->json = $this->lib->raw_body;

                $this->__output($file, $this->json);
                // echo json_encode($this->lib->raw_body,JSON_UNESCAPED_UNICODE);
            } else {
                $d = $this->__getfile($p);
                foreach ($this->ouuut as $key) {
                    Ipvalidation::validate($key);
                    $details = $this->lib->getAddressInfo($key);
                    // print_r($details);
                    print $this->__printData($details, $this->lib->raw_body);
                    $this->__query_date($this->lib->raw_body);
                    $this->json = $this->lib->raw_body;

                    $this->__output($file, $this->json);
                }
                print "[!] {$this->lang['to_ip']}     >>> \e[0;31;42m {$this->count}\e[0m\n";
            }
        } catch (IPfinderException $e) {
            print "{$this->color->red}{$this->lang['error']} {$this->color->red}" . $e->getMessage() . " $p \n";
        }
    }
    /**
     * Get details for an AS number or more ..
     * @param  string          $p     AS number or file name exists.
     * @param  string|null     $file  file name
     *
     *
     * @return AS number data.
     * @throws IPfinderException
     */
    public function __getAsn(string $p, string $file = null)
    {

        try {
            print $this->banner->rand;
            if (!file_exists($p)) {
                Asnvalidation::validate($p);
                $details = $this->lib->getAsn($p);
                print $this->__printData($details, $this->lib->raw_body);
                $this->__query_date($this->lib->raw_body);
                $this->json = $this->lib->raw_body;

                $this->__output($file, $this->json);
            } else {
                $d = $this->__getfile($p);
                foreach ($this->ouuut as $key) {
                    Asnvalidation::validate($key);
                    $details = $this->lib->getAsn($key);
                    // print_r($details);
                    print $this->__printData($details, $this->lib->raw_body);
                    $this->__query_date($this->lib->raw_body);
                    $this->json = $this->lib->raw_body;

                    $this->__output($file, $this->json);
                }
                print "[!] {$this->lang['to_as']}    >>> \e[0;31;42m {$this->count}\e[0m\n";
            }
        } catch (IPfinderException $e) {
            print "{$this->color->red}{$this->lang['error']} {$this->color->red}" . $e->getMessage() . "\n";
        }
    }
    /**
     * Get details for an Firewall.
     *
     * @param  string         $p    AS number, alpha-2 country only.
     * @param  string         $f    list formats supported
     * @param  string|null    $file file name
     *
     * @return Firewall data
     * @throws IPfinderException
     */
    public function __firewall(string $p, string $f, string $file = null)
    {

        try {
            Firewallvalidation::validate($p, $f);
            $details = $this->lib->getFirewall($p, $f);
            print $this->color->yellow . $details . $this->color->yellow;
            $this->__query_date($this->lib->raw_body);
            $this->json = $this->lib->raw_body;
            $this->__output($file, $this->json);
        } catch (IPfinderException $e) {
            print "{$this->color->red}{$this->lang['error']} {$this->color->red}" . $e->getMessage() . "\n";
        }
    }
    /**
     * Get details for an Organization name.
     *
     * @param  string         $p     Organization name.
     * @param  string|null    $file  file name
     *
     * @return Organization name data.
     */
    public function __getRanges(string $p, string $file = null)
    {

        try {
            print $this->banner->rand;
            $details = $this->lib->getRanges($p);
            print $this->__printData($details, $this->lib->raw_body);
            $this->__query_date($this->lib->raw_body);
            $this->json = $this->lib->raw_body;
            $this->__output($file, $this->json);
        } catch (IPfinderException $e) {
            print "{$this->color->red}{$this->lang['error']} {$this->color->red}" . $e->getMessage() . "\n";
        }
    }

    /**
     * Get domain ip information
     * @param  string      $d    valid domain name
     * @param  string|null $file file name
     * @return domain ip data
     * @throws IPfinderException
     */
    public function __getDomain(string $d, string $file = null)
    {
        try {
            print $this->banner->rand;
            $details = $this->lib->getDomain($d);
            print $this->__printData($details, $this->lib->raw_body);
            $this->__query_date($this->lib->raw_body);
            $this->json = $this->lib->raw_body;
            $this->__output($file, $this->json);
        } catch (IPfinderException $e) {
            print "{$this->color->red}{$this->lang['error']} {$this->color->red}" . $e->getMessage() . "\n";
        }
    }

    /**
     * Get domain  IP history information
     * @param  string      $d    valid domain name
     * @param  string|null $file file name
     * @return Domain History data
     * @throws IPfinderException
     */
    public function __getDomainHistory(string $d, string $file = null)
    {
        try {
            print $this->banner->rand;
            $details = $this->lib->getDomainHistory($d);
            print $this->__printData($details, $this->lib->raw_body);
            $this->__query_date($this->lib->raw_body);
            $this->json = $this->lib->raw_body;
            $this->__output($file, $this->json);
        } catch (IPfinderException $e) {
            print "{$this->color->red}{$this->lang['error']} {$this->color->red}" . $e->getMessage() . "\n";
        }
    }
    /**
     * Get List Domain
     * @param  string      $by     passing  a  ASN,Country,Ranges
     * @param  string|null $file $file file name
     * @return List Domain By ASN, Country,Ranges
     */
    public function __getDomainBy(string $by, string $file = null)
    {
        try {
            print $this->banner->rand;
            $details = $this->lib->getDomainBy($by);
            print $this->__printData($details, $this->lib->raw_body);
            $this->__query_date($this->lib->raw_body);
            $this->json = $this->lib->raw_body;
            $this->__output($file, $this->json);
        } catch (IPfinderException $e) {
            print "{$this->color->red}{$this->lang['error']} {$this->color->red}" . $e->getMessage() . "\n";
        }
    }

    /**
     * Get details for an API Token .
     * @param  string|null    $file  file name
     *
     * @return The Token data.
     */
    public function __getStatus(string $file = null)
    {

        try {
            print $this->banner->rand;
            $details = $this->lib->getStatus();
            print $this->__printData($details, $this->lib->raw_body);
            $this->json = $this->lib->raw_body;

            $this->__output($file, $this->json);
        } catch (IPfinderException $e) {
            print "{$this->color->red}{$this->lang['error']} {$this->color->red}" . $e->getMessage() . "\n";
        }
    }
    /**
     * Get API Token as input
     *
     * @param  array    $conf token and lang
     *
     *
     * @return save  $conf in $this->token
     */
    public function __config(array $conf)
    {
        try {
            $this->conf       = fopen($this->token, "w");
            $json = json_encode($conf, JSON_PRETTY_PRINT);
          //  $trim = str_replace('\n', "", $json); // remove newlines from jsondata

            fwrite($this->conf, $json);
         //   print "your Token : {$this->color->red}{$this->conf_token}\n";
        } catch (Exception $e) {
            print "{$this->color->red}{$this->lang['error']} {$this->color->red}" . $e->getMessage() . "\n";
        }
    }

    /**
     * check App VERSION from github
     *
     * @return true or false
     */
    public function __update()
    {
        // https://github.com/ipfinder-io/PATH/VERSION
        $get_version_from_gith = file_get_contents('https://github.com/ipfinder-io/ip-finder-cli/blob/master/VERSION');
        if ($get_version_from_gith == $this->version) {
            print $this->color->yellow.$this->lang['n_update'];
        } else {
            print $this->color->yellow.$this->lang['sure'];
            if (trim(fgets(STDIN)) == 'y') {
                $file = system("which ipfinder");
                unlink("$file");
                $code = file_get_contents("https://github.com/ipfinder-io/ip-finder-cli/releases/download/{$get_version_from_gith}/ipfinder.phar");
                $var  = fopen('ipfinder.phar', 'a');
                fwrite($var, $code);
                fclose($var);
                chmod('ipfinder.phar', 0777);
                print $this->color->yellow.$this->lang['succes'];
                sleep(3);
                system("mv ipfinder.phar /usr/bin/ipfinder |  ipfinder -h");
                exit();
            } else {
                echo "ABORTING!\n";
            }
        }
    }
    /**
     *
     * run APP
     *
     */
    public function run()
    {

        $co = $this->Command(array(
            "auth::",
            "ip:",
            "asn:",
            "ranges:",
            "firewall:",
            "format:",
            "status::",
            "help::",
            "config::",
            "shell::",
            "update::",
            "output:",
            "domain:",
            "dhistory:",
            "dlist:",
            "version::"), 'a::i:n:r:f:m:s::h::g::l::u::o:v::d:dh:dl:');
        if ($co == null) {
            $this->Menu();
            exit(1);
        } else {
            $this->__query($this->first, $this->key[0]);
        }

        foreach ($co as $key => $value) {
            if ($key == 'h' || $key == 'help') {
                $this->Help(1);
            } elseif ($key == 'a' || $key == 'auth') {
                $this->__getAuth($this->third);
            } elseif ($key == 'v' || $key == 'version') {
                print "IPFinder Command Line Interface ".$this->version." by ipfinder.io Teams \n\n";
            } elseif ($key == 'i' || $key == 'ip') {
                $this->__getIp($this->first, $this->third);
            } elseif ($key == 'n' || $key == 'asn') {
                $this->__getAsn($this->first, $this->third);
            } elseif ($key == 'r' || $key == 'ranges') {
                $this->__getRanges($this->first, $this->third);
            } elseif ($key == 'f' || $key == 'firewall' || $key == 'm' || $key == 'format') {
                $this->__firewall($this->first, $this->second, $this->third);
            } elseif ($key == 'd' || $key == 'domain') {
                $this->__getDomain($this->first, $this->third);
            } elseif ($key == 'dh' || $key == 'dhistory') {
                $this->__getDomainHistory($this->first, $this->third);
            } elseif ($key == 'dl' || $key == 'dlist') {
                $this->__getDomainBy($this->first, $this->third);
            } elseif ($key == 's' || $key == 'status') {
                $this->__getStatus($this->third);
            } elseif ($key == 'g' || $key == 'config') {
                print $this->color->yellow.$this->lang['e_token'];
                $token = Shell::run();
                print $this->color->yellow.$this->lang['e_lang'];
                $lang = Shell::run();
                print $this->color->yellow.$this->lang['t_output'];
                $output = Shell::run();
                $this->__config(array('__token' =>$token,'__lang'=> $lang,'__output'=> $output));
            } elseif ($key == 'u' || $key == 'update') {
                $this->__update();
            } elseif ($key == 'l' || $key == 'shell') {
                    $this->Help(2);
                    start:print $this->color->white."\n$ ipfinder: >>> ";

                    $sh = trim(fgets(STDIN, 1024));
                switch ($sh) {
                    case 'help':
                    case 'h':
                    case 1:
                        $this->Help(2);
                        goto start;
                        break;
                    case 'auth':
                    case 'a':
                    case 2:
                        $this->__getAuth(null);
                        goto start;
                        break;
                    case 'status':
                    case 's':
                    case 3:
                        $this->__getStatus(null);
                        goto start;
                        break;
                    case 'ip':
                    case 'i':
                    case 4:
                        print $this->color->yellow.$this->lang['e_ip'];
                        $ip = Shell::run();
                        print $this->color->yellow.$this->lang['e_output'];
                        $out = Shell::run();
                        $this->__getIp($ip, $out);
                        goto start;
                        break;
                    case 'asn':
                    case 'n':
                    case 5:
                        print $this->color->yellow.$this->lang['e_asn'];
                        $asn = Shell::run();
                        print $this->color->yellow.$this->lang['e_output'];
                        $out = Shell::run();
                        $this->__getAsn($asn, $out);
                        goto start;
                        break;
                    case 'ranges':
                    case 'r':
                    case 6:
                        print $this->color->yellow.$this->lang['e_org'];
                        $range = Shell::run();
                        print $this->color->yellow.$this->lang['e_output'];
                        $out = Shell::run();
                        $this->__getRanges($range, $out);

                        goto start;
                        break;
                    case 'firewall':
                    case 'f':
                    case 7:
                        print $this->color->yellow.$this->lang['e_firewall'];
                        $firewall = Shell::run();
                        print $this->color->yellow.$this->lang['e_format'];
                        $format = Shell::run();
                        print $this->color->yellow.$this->lang['e_output'];
                        $out = Shell::run();
                        $this->__firewall($firewall, $format, $out);

                        goto start;
                        break;
                    case 'domain':
                    case 'd':
                    case 8:
                        print $this->color->yellow.$this->lang['e_domain'];
                        $domain = Shell::run();
                        print $this->color->yellow.$this->lang['e_output'];
                        $out = Shell::run();
                        $this->__getDomain($domain, $out);

                        goto start;
                        break;
                    case 'dhistory':
                    case 'dh':
                    case 9:
                        print $this->color->yellow.$this->lang['e_domain'];
                        $domain = Shell::run();
                        print $this->color->yellow.$this->lang['e_output'];
                        $out = Shell::run();
                        $this->__getDomainHistory($domain, $out);
                        goto start;
                        break;
                    case 'dlist':
                    case 'dl':
                    case 10:
                        print $this->color->yellow.$this->lang['e_ldomainn'];
                        $by = Shell::run();
                        print $this->color->yellow.$this->lang['e_output'];
                        $out = Shell::run();
                        $this->__getDomainBy($by, $out);
                        goto start;
                        break;
                    case 'exit':
                    case 'x':
                    case 11:
                        print $this->lang['exit']."\n";
                        exit();
                        break;
                    case 'clear':
                    case 'c':
                    case 12:
                        system('clear');
                        $this->Help(2);
                        goto start;
                        break;
                    default:
                        print $this->color->yellow.$this->lang['p_option'];
                        goto start;
                        break;
                }
            } else {
                //
                //
                //
            }
        }
    }
}
