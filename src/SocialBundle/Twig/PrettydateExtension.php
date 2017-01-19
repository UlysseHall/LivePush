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
            31536000 => 'an',
            2592000 => 'mois',
            604800 => 'semaine',
            86400 => 'jour',
            3600 => 'heure',
            60 => 'minute',
            1 => 'seconde'
        );

        foreach ($tokens as $unit => $text)
        {
            if ($time < $unit) continue;
            $numberOfUnits = floor($time / $unit);
            return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
        }
    }
    
    public function getName()
    {
        return 'prettydate_extension';
    }
}
