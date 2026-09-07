<?php

namespace App\Services\Documents;

use Illuminate\Support\HtmlString;
use Picqer\Barcode\Renderers\HtmlRenderer;
use Picqer\Barcode\Types\TypeCode128;

class Barcode
{
    /**
     * Code 128 drawn as positioned divs. dompdf renders those reliably, and the PNG
     * renderer needs GD or Imagick, neither of which is installed here.
     */
    public static function html(string $value, float $width = 300, float $height = 46): HtmlString
    {
        return new HtmlString(
            (new HtmlRenderer)->render((new TypeCode128)->getBarcode($value), $width, $height)
        );
    }
}
