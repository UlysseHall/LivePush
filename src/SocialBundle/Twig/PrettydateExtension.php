<?php

namespace SocialBundle\Twig;

class PrettydateExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('prettydate', array($this, 'prettydateFilter')),
        );
    }
    
    public function prettydateFilter($date)
    {
        $time = strtotime($date);
        $time = time() - $time;
        $time = ($time<1)? 1 : $time;
        $tokens = array (
            86400 => 'j',
            3600 => 'h',
            60 => 'm',
            1 => 's'
        );

        foreach ($tokens as $unit => $text)
        {
            if ($time < $unit) continue;
            $numberOfUnits = floor($time / $unit);
            return $numberOfUnits . $text;
        }
    }
    
    public function getName()
    {
        return 'prettydate_extension';
    }
}
