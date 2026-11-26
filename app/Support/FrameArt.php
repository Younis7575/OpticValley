<?php

namespace App\Support;

/**
 * Generates clean studio-style SVG renders of eyewear frames. Used for the
 * starter catalogue so the store looks finished before real product
 * photography is uploaded from the admin panel.
 */
class FrameArt
{
    public const COLORS = [
        'black' => ['#16181c', '#2b2f36'],
        'tortoise' => ['#4a2810', '#a5662c'],
        'gold' => ['#a8823a', '#e3c47c'],
        'silver' => ['#8d949c', '#d9dde2'],
        'navy' => ['#16233d', '#33486e'],
        'crystal' => ['#c6ccd3', '#eef1f4'],
        'burgundy' => ['#4e1220', '#86303f'],
        'green' => ['#1f3a2c', '#46705a'],
        'rose gold' => ['#a96d5b', '#e6b3a0'],
        'ivory' => ['#d9d2c5', '#f4efe6'],
        'gunmetal' => ['#3b4048', '#6e7680'],
        'blue' => ['#1d4e89', '#4f86c6'],
    ];

    public const TINTS = [
        'dark' => ['#141a22', '#3c4a5a'],
        'brown' => ['#3a2414', '#8a5a34'],
        'green' => ['#17261d', '#3f5c49'],
        'blue' => ['#0f2a4a', '#3d78b3'],
        'rose' => ['#6b3540', '#d99aa4'],
    ];

    public static function svg(string $shape, string $color = 'black', ?string $tint = null, bool $metal = false, int $view = 0, string $bg = '#f3efe9'): string
    {
        [$c1, $c2] = self::COLORS[$color] ?? self::COLORS['black'];
        $uid = substr(md5($shape.$color.$tint.$view.($metal ? 'm' : 'a')), 0, 6);
        $sw = $metal ? 6 : 13;
        $lensPath = self::lens($shape);
        $isSun = $tint !== null;
        [$t1, $t2] = $isSun ? (self::TINTS[$tint] ?? self::TINTS['dark']) : ['#ffffff', '#e9eef3'];
        $lensOpacity = $isSun ? '0.93' : '0.38';
        $crystal = $color === 'crystal' ? ' opacity="0.75"' : '';

        $bridgeExtra = $shape === 'aviator' ? '<path d="M 362 246 L 438 246" stroke-width="5" stroke-linecap="round"/>' : '';
        $pads = $metal ? '<g fill="#e7e2da" stroke="#b9b2a7" stroke-width="2"><ellipse cx="378" cy="330" rx="7" ry="11"/><ellipse cx="422" cy="330" rx="7" ry="11"/></g>' : '';
        $glint = '<path d="M -40 -40 Q -10 -50 20 -42" stroke="#fff" stroke-width="7" stroke-linecap="round" fill="none" opacity="0.35"/>';
        $frameGroup = '<g>'
            .'<g transform="translate(290 300)">'.$lensPath['fill'].'</g>'
            .'<g transform="translate(510 300) scale(-1 1)">'.$lensPath['fill'].'</g>'
            .'<g transform="translate(290 300)">'.$glint.'</g>'
            .'<g transform="translate(510 300)">'.$glint.'</g>'
            .'<g fill="none" stroke="url(#fr'.$uid.')" stroke-width="'.$sw.'" stroke-linejoin="round"'.$crystal.'>'
            .'<g transform="translate(290 300)">'.$lensPath['stroke'].'</g>'
            .'<g transform="translate(510 300) scale(-1 1)">'.$lensPath['stroke'].'</g>'
            .'<path d="M 366 282 Q 400 258 434 282" stroke-linecap="round"/>'
            .$bridgeExtra
            .'<path d="M 208 278 L 150 268" stroke-linecap="round"/>'
            .'<path d="M 592 278 L 650 268" stroke-linecap="round"/>'
            .'</g>'.$pads.'</g>';

        $lensFill = str_replace('__FILL__', "url(#ln{$uid})", $frameGroup);
        $lensFill = str_replace('__OP__', $lensOpacity, $lensFill);

        $transform = match ($view) {
            1 => 'translate(400 300) scale(1.12 1.18) rotate(-7) skewX(-6) translate(-400 -300)',
            2 => 'translate(-470 -330) scale(2.1)',
            default => 'translate(400 290) scale(1.3) translate(-400 -300)',
        };

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600" width="800" height="600">
  <defs>
    <radialGradient id="bg{$uid}" cx="50%" cy="42%" r="75%">
      <stop offset="0" stop-color="#ffffff"/>
      <stop offset="1" stop-color="{$bg}"/>
    </radialGradient>
    <linearGradient id="fr{$uid}" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="{$c2}"/>
      <stop offset="0.55" stop-color="{$c1}"/>
      <stop offset="1" stop-color="{$c2}"/>
    </linearGradient>
    <linearGradient id="ln{$uid}" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0" stop-color="{$t1}"/>
      <stop offset="1" stop-color="{$t2}"/>
    </linearGradient>
    <filter id="sh{$uid}" x="-20%" y="-50%" width="140%" height="200%"><feGaussianBlur stdDeviation="14"/></filter>
  </defs>
  <rect width="800" height="600" fill="url(#bg{$uid})"/>
  <ellipse cx="400" cy="455" rx="300" ry="18" fill="#000" opacity="0.10" filter="url(#sh{$uid})"/>
  <g transform="{$transform}">{$lensFill}</g>
</svg>
SVG;
    }

    private static function lens(string $shape): array
    {
        $d = match ($shape) {
            'round' => '<circle r="64"/>',
            'oval' => '<ellipse rx="78" ry="54"/>',
            'rectangle' => '<rect x="-82" y="-48" width="164" height="96" rx="20"/>',
            'square' => '<rect x="-70" y="-62" width="140" height="124" rx="24"/>',
            'aviator' => '<path d="M -80 -45 Q -78 -62 -40 -62 L 55 -62 Q 82 -62 80 -35 Q 76 20 40 55 Q 15 75 -15 70 Q -60 62 -76 15 Q -82 -15 -80 -45 Z"/>',
            'cat-eye' => '<path d="M -95 -55 Q -40 -50 30 -50 Q 75 -48 78 -15 Q 80 30 45 52 Q 0 66 -45 50 Q -78 35 -84 -10 Q -88 -35 -95 -55 Z"/>',
            'wayfarer' => '<path d="M -85 -50 L 80 -50 Q 88 -50 86 -40 L 72 30 Q 66 55 40 55 L -45 55 Q -72 55 -78 30 L -90 -38 Q -92 -50 -85 -50 Z"/>',
            'geometric' => '<path d="M -40 -60 L 40 -60 L 80 0 L 40 60 L -40 60 L -80 0 Z"/>',
            default => '<circle r="64"/>',
        };
        $fill = preg_replace('/\/>$/', ' fill="__FILL__" opacity="__OP__"/>', $d);

        return ['fill' => $fill, 'stroke' => $d];
    }
}
