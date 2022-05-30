<?php

namespace App\TwigExtensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MyCustomTwigExtensions extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new twigFilter('defaultImage',[$this,'defaultImage'])
        ];
    }
    public function defaultImage(string $path) :string {
        if (strlen(trim($path))== 0){
            return 'bureau.jpeg';
        }
        return $path ;
    }
}