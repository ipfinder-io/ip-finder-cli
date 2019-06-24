<?php namespace App;

/**
 *  Copyright 2019 Mohamed Benrebia <mohamed@ipfinder.io>
 */

/**
 * Class Color os
 */
class Color
{
    /*
    *
    * do not instantiate it
    *
    */
   public $colors = array(
        'LIGHT_RED'      => "\033[1;31m",
        'LIGHT_GREEN'     => "\033[1;32m",
        'YELLOW'     => "\033[1;33m",
        'LIGHT_BLUE'     => "\033[1;34m",
        'MAGENTA'     => "\033[1;35m",
        'LIGHT_CYAN'     => "\033[1;36m",
        'WHITE'     => "\033[1;37m",
        'NORMAL'     => "\033[0m",
        'BLACK'     => "\033[0;30m",
        'RED'         => "\033[0;31m",
        'GREEN'     => "\033[0;32m",
        'BROWN'     => "\033[0;33m",
        'BLUE'         => "\033[0;34m",
        'CYAN'         => "\033[0;36m",
        'BOLD'         => "\033[1m",
        'UNDERSCORE'     => "\033[4m",
        'REVERSE'     => "\033[7m",

);
    public function __construct()
    {

         //   system("clear");
            $this->white        = "\033[1;37m"; // WHITE
            $this->yellow       = "\033[1;33m"; // YELLOW
            $this->red          = "\033[1;31m"; // RED LIGHT
            $this->green        = "\033[32m"; // GREEN
            $this->blue         = "\033[0;34m"; // BLUE
            $this->light_grey   = "\033[0;37m"; // LIGHT GREY
            $this->brown        = "\033[0;33m"; // BROWN
            $this->light_purple = "\033[1;35m"; // LIGHT PURPLE
            $this->red          = "\033[0;31m"; // RED
            $this->light_cyan   = "\033[1;36m"; // LIGHT CYAN
            $this->light_blue   = "\033[1;34m"; // LIGHT BLUE
            $this->dark_red     = "\033[02;31m"; // DARK RED
            $this->green_l      = "\033[1;32m"; // GREEN LIGHT
            $this->purple       = "\033[0;35m"; // PURPLE
            $this->dark_gey     = "\033[1;30m"; // DARK GREY
            $this->end          = "\033[0m"; // END OF COLOR
    }
}
