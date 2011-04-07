#! /bin/bash
#
  convert -resize 200% -posterize 6 -charcoal 140 $img _stencilized-$img
