<?php
namespace App\Shell;
/**
 * Class Shell
 */
class Shell
{
    /**
     * Helper method for ask and get input
     *
     *@return string input
     */
    public static function run()
    {
        $handle = fopen("php://stdin", "r");
        do {
            $line = trim(fgets($handle));
        } while ($line == null);
        fclose($handle);
        return $line;
        
    }
}
