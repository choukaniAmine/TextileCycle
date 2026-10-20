<?php

namespace App\Support;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class Qr
{
    /** Retourne un QR code en data-URI SVG, utilisable directement dans <img src="...">. */
    public static function svg(string $contenu, int $taille = 130): string
    {
        $renderer = new ImageRenderer(new RendererStyle($taille, 1), new SvgImageBackEnd());
        $svg = (new Writer($renderer))->writeString($contenu);

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}