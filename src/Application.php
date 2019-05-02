<?php namespace App;

/**
 *
 * All rights reserved.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  App
 * @author    Mohamed Benrebia <mohamed@ipfinder.io>
 * @copyright 2019 Mohamed Benrebia
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
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



/**
 * The main class.
 *
 * @category  App
 * @author    Mohamed Benrebia <mohamed@ipfinder.io>
 * @copyright 2019 Mohamed Benrebia
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @link      https://ipfinder.io
 * @version   1.0.0
 */
class Application
{
    /**
     * Current version ipfinder cli
     *
     * @var string
     */
    const VERSION = 'v1.0.0';

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
     * This is a static class, do not instantiate it
     *
     */
    public function __construct()
    {
        $this->version = '1.0.0';
        if (isset($_SERVER['HOME'])) {
           $this->home    = $_SERVER['HOME'] . '/';
        } else {
            $this->home   = $_SERVER['HOMEPATH'] . '/';
        }
        

        $this->version  = self::VERSION;
        $this->ipfinder = $this->home . self::IPFINDER;
        $this->token    = $this->home . self::TOKEN.".json";

        $this->query = $this->ipfinder . '/' . self::QUERY . ".csv";
        $this->date  = $this->ipfinder . '/' . self::QUERY . '-' . date("Y-m-d") . ".json";

        $this->version = '1.0.0';

        // create hidden folder
        (!is_dir($this->ipfinder) ? mkdir($this->ipfinder, 0777, true) : null);

        // create hidden file
        (!file_exists($this->token) ? touch($this->token) : null);

        // create csv and json file
        (!file_exists($this->query) ? touch($this->query) : null);

        (!file_exists($this->date) ? touch($this->date) : null);

        $token_api = file_get_contents($this->token, true);
        $arr  = json_decode($token_api);

        if (filesize($this->token) == 0) {
            $this->lang = require_once(__DIR__.'/lang/en.php');
            $this->lib = new IPfinder('free');
        } else {
            
            if (file_exists(__DIR__ . '/lang/{$arr->__lang}.php')) {
               $this->lang = require __DIR__ . '/lang/{$arr->__token}.php';
            }  else {
               $this->lang = require __DIR__ . '/lang/en.php';
            }
            
            $this->lib = new IPfinder($arr->__token);
        }
        
        // sendbox
        // $this->lib    = new IPfinder($this->token_api, 'http://api.sample.com/v1/');
        $this->color  = new Color();
        $this->banner = new Banners('version: ' . $this->version);

    }
    /**
     * Set the Command options
     *
     * @param  array   $commande_shortopts   The shortopts lits
     * @param  array   $commande_longopts    The longopts row
     * @return   list app command $this->options
     */
    public function Command($commande_shortopts = [], $commande_longopts = [])
    {
        $this->options = getopt($commande_longopts, $commande_shortopts);

        foreach ($this->options as $this->first) {
            break;
        }

        $s = array_slice($this->options, 1, 1);
        if (empty($s)) {
            $this->second = 'apache_allow';
        } else {
            foreach ($s as $value) {
                $this->second = $value;
            }
        }
        $this->c = array_keys($this->options);
        $this->v = array_values($this->options);
        return $this->options;
    }
    /**
     * Save data to query-Y-m-d.json
     *
     * @param  string    $data The json data
     *
     * @return body in json file
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
     * @return input , date , command in csv file
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
     * @param  string    $file file name
     * @param  string    $data body
     *
     * @return file with data
     */
    public function __output(string $file, string $data)
    {
        if (isset($file)) {
            $fop = fopen($file, 'a+');
            fwrite($fop, "$data\n");
            fclose($fop);
        } else {
            print 'give other file name';
        }

    }
    /**
     * Out data to console
     *
     * @param  array    $array  data form call
     *
     * @return print data to console
     */
    public function __printData($array = [])
    {

        array_walk_recursive($array, function ($value, $key) {
            (is_bool($value) ? $v = $value ? 'True' : 'False' : $v = $value);
            (is_null($v) ? $t = 'null' : $t = $v);
            print str_pad("{$this->color->red}| " . $key, 40) . "| {$this->color->white}{!} $t \n";
        });

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
    public function Help()
    {
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
\t\t{$this->color->light_cyan} $ ipfinder --ip 1.0.0.0{$this->color->light_cyan}
\t\t{$this->color->light_cyan} $ ipfinder -i 2c0f:fb50:4003::{$this->color->light_cyan}
\t\t{$this->color->light_cyan} $ ipfinder -i filename{$this->color->light_cyan}
{$this->color->end}-f ,--firewall{$this->color->blue} {$this->lang['f_firewall']}
\t[Example]:
\t\t{$this->color->light_cyan} $ ipfinder -f AS1 --format juniper_junos {$this->color->light_cyan}
\t\t{$this->color->light_cyan} $ ipfinder --firewall DZ -m web_config_allow {$this->color->light_cyan}
{$this->color->end}-s ,--status{$this->color->blue}   {$this->lang['f_information']}
\t[Example]:
\t\t{$this->color->light_cyan} $ ipfinder -s{$this->color->light_cyan}
{$this->color->end}-g ,--config{$this->color->blue}  {$this->lang['token']}
\t[Example]:
\t\t{$this->color->light_cyan} $ ipfinder --config {Your_token_here}{$this->color->light_cyan}
{$this->color->end}-l ,--shell{$this->color->blue}  {$this->lang['shell']}
\n";

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
     * @param  string    $file file name
     *
     * @return Your IP address data.
     */
    public function __getAuth(string $file)
    {

        try {
            print $this->banner->rand;
            $details = $this->lib->Authentication();
            print $this->__printData($details);
            $this->__query_date($this->lib->raw_body);
            $this->json = $this->lib->raw_body;
            $this->__output($file, $this->json);
        } catch (IPfinderException $e) {
            print "{$this->color->red}{$this->lang['error']} {$this->color->red}" . $e->getMessage() . "\n";
        }
    }
    /**
     *  details for an IP address or more .
     * @param  string    $p    IP address or file name exists.
     * @param  string    $file file name
     *
     *
     * @return IP address data.
     * @throws IPfinderException
     */
    public function __getIp(string $p, string $file)
    {

        try {

            print $this->banner->rand;
            if (!file_exists($p)) {
                Ipvalidation::validate($p);
                $details = $this->lib->getAddressInfo($p);
                print $this->__printData($details);
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
                    print $this->__printData($details);
                    $this->__query_date($this->lib->raw_body);
                    $this->json = $this->lib->raw_body;

                    $this->__output($file, $this->json);
                }
                print "{$this->color->red}[!] {$this->lang['to_ip']} =>  : {$this->count}{$this->color->red}\n";
            }

        } catch (IPfinderException $e) {
            print "{$this->color->red}{$this->lang['error']} {$this->color->red}" . $e->getMessage() . " $p \n";
        }
    }
    /**
     * Get details for an AS number or more ..
     * @param  string     $p     AS number or file name exists.
     * @param  string     $file  file name
     *
     *
     * @return AS number data.
     * @throws IPfinderException
     */
    public function __getAsn(string $p, string $file)
    {

        try {

            print $this->banner->rand;
            if (!file_exists($p)) {
                Asnvalidation::validate($p);
                $details = $this->lib->getAsn($p);
                print $this->__printData($details);
                $this->__query_date($this->lib->raw_body);
                $this->json = $this->lib->raw_body;

                $this->__output($file, $this->json);
            } else {

                $d = $this->__getfile($p);
                foreach ($this->ouuut as $key) {
                    Asnvalidation::validate($key);
                    $details = $this->lib->getAsn($key);
                    // print_r($details);
                    print $this->__printData($details);
                    $this->__query_date($this->lib->raw_body);
                    $this->json = $this->lib->raw_body;

                    $this->__output($file, $this->json);
                }
                print "{$this->color->red}[!] {$this->lang['to_as']} =>  : {$this->count}{$this->color->red}\n";
            }

        } catch (IPfinderException $e) {
            print "{$this->color->red}{$this->lang['error']} {$this->color->red}" . $e->getMessage() . "\n";
        }
    }
    /**
     * Get details for an Firewall.
     *
     * @param  string    $p    AS number, alpha-2 country only.
     * @param  string    $f    list formats supported
     * @param  string    $file file name
     *
     * @return Firewall data
     * @throws IPfinderException
     */
    public function __firewall(string $p, string $f, string $file = 'test')
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
     * @param  string    $p     Organization name.
     * @param  string    $file  file name
     *
     * @return Organization name data.
     */
    public function __getRanges(string $p, string $file)
    {

        try {
            print $this->banner->rand;
            $details = $this->lib->getRanges($p);
            print $this->__printData($details);
            $this->__query_date($this->lib->raw_body);
            $this->json = $this->lib->raw_body;
            $this->__output($file, $this->json);

        } catch (IPfinderException $e) {
            print "{$this->color->red}{$this->lang['error']} {$this->color->red}" . $e->getMessage() . "\n";
        }
    }
    /**
     * Get details for an API Token .
     * @param  string    $file  file name
     *
     * @return The Token data.
     */
    public function __getStatus(string $file)
    {

        try {
            print $this->banner->rand;
            $details = $this->lib->getStatus();
            print $this->__printData($details);
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
            $json = json_encode($conf,JSON_PRETTY_PRINT); 
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
            "version::"), 'a::i:n:r:f:m:s::h::g::l::u::o:v::');
        if ($co == null) {
            $this->Menu();
            exit(1);
        } else {
            //    $this->__query($this->first, $this->c[0]);
        }

// (isset($this->options['h']) || isset($this->options['help']) ? $this->Help() : null);

// (isset($this->options['a']) || isset($this->options['auth']) ? $this->__getAuth($this->second) : null);

// (isset($this->options['i']) || isset($this->options['ip']) ? $this->__getIp($this->first, $this->second) : null);

// (isset($this->options['n']) || isset($this->options['asn']) ? $this->__getAsn($this->first,$this->second) : null);

// (isset($this->options['r']) || isset($this->options['ranges']) ?  $this->__getRanges($this->first,$this->second) : null);

// (isset($this->options['f']) || isset($this->options['firewall']) || isset($this->options['m']) || isset($this->options['format']) ? $this->__firewall($this->first, $this->second, $this->v[2]) : null);

// (isset($this->options['s']) || isset($this->options['status']) ? $this->__getStatus($this->second) : null);

// (isset($this->options['g']) || isset($this->options['config']) ? $this->__config($this->first) : null);

// (isset($this->options['u']) || isset($this->options['update']) ? $this->__update() : null);

        foreach ($co as $key => $value) {

            if ($key == 'h' || $key == 'help') {
                $this->Help();
            } elseif ($key == 'a' || $key == 'auth' && $key == 'o' || $key == 'output') {
                $this->__getAuth($this->second);
            }  elseif ($key == 'v' || $key == 'version') {
               print "IPFinder Command Line Interface ".$this->version." by ipfinder.io Teams \n\n";
            } elseif ($key == 'i' || $key == 'ip' && $key == 'o' || $key == 'output') {
                $this->__getIp($this->first, $this->second);
            } elseif ($key == 'n' || $key == 'asn' && $key == 'o' || $key == 'output') {
                $this->__getAsn($this->first, $this->second);
            } elseif ($key == 'r' || $key == 'ranges' && $key == 'o' || $key == 'output') {
                $this->__getRanges($this->first, $this->second);
            } elseif ($key == 'f' || $key == 'firewall' || $key == 'm' || $key == 'format' && $key == 'o' || $key == 'output') {
                $this->__firewall($this->first, $this->second, $this->v[2]);
            } elseif ($key == 's' || $key == 'status' && $key == 'o' || $key == 'output') {
                $this->__getStatus($this->second);
            } elseif ($key == 'g' || $key == 'config') {
                print $this->color->yellow.$this->lang['e_token'];
                $token = Shell::run();
                print $this->color->yellow.$this->lang['e_lang'];
                $lang = Shell::run();
                $this->__config(array('__token' =>$token,'__lang'=> $lang));
            } elseif ($key == 'u' || $key == 'update') {
                $this->__update();
            } elseif ($key == 'l' || $key == 'shell') {
                    start:print $this->color->white."\n<(ipfinder)$: ";
                    $sh = trim(fgets(STDIN, 1024));
                    switch ($sh) {
                        case 'help':
                        case 'h':
                            $this->Help();
                            goto start;
                            break;
                        case 'auth':
                        case 'a':
                            $this->__getAuth($this->second);
                            goto start; 
                            break;
                        case 'status':
                        case 's':
                            $this->__getStatus($this->second);
                            goto start; 
                            break;
                        case 'ip':
                        case 'i': 
                            print $this->color->yellow.$this->lang['e_ip'];
                            $ip = Shell::run();
                            print $this->color->yellow.$this->lang['e_output'];
                            $out = Shell::run();
                            $this->__getIp($ip, $out);
                            goto start; 
                            break;
                        case 'asn':
                        case 'n':    
                            print $this->color->yellow.$this->lang['e_asn'];
                            $asn = Shell::run();
                            print $this->color->yellow.$this->lang['e_output'];
                            $out = Shell::run();
                            $this->__getAsn($asn, $out);
                            goto start; 
                            break;
                        case 'ranges':
                        case 'r':    
                            print $this->color->yellow.$this->lang['e_org'];
                            $range = Shell::run();
                            print $this->color->yellow.$this->lang['e_output'];
                            $out = Shell::run();
                            $this->__getRanges($range, $out);
                            
                            goto start; 
                            break;
                        case 'firewall':
                        case 'f':    
                            print $this->color->yellow.$this->lang['e_firewall'];
                            $firewall = Shell::run();
                            print $this->color->yellow.$this->lang['e_format'];
                            $format = Shell::run();
                            print $this->color->yellow.$this->lang['e_output'];
                            $out = Shell::run();
                            $this->__firewall($firewall, $format, $out);
                            
                            goto start; 
                            break;
                        case 'exit':
                        case 'x':
                            exit();
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