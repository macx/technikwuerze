<?php

/**
 * Renders an icon either inline (dev, read straight from the SVG source
 * file) or as a `<use>` reference into a built Vite spritemap (production).
 */
function tw_sprite_icon(string $sourcePath, string $spriteUrl, string $symbolId): string
{
  if (vite()->isDev()) {
    return (string) svg($sourcePath);
  }

  return '<svg><use xlink:href="' . esc($spriteUrl . '#' . $symbolId, 'attr') . '"></use></svg>';
}
