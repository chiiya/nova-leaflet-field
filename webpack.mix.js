let mix = require('laravel-mix');

mix
  .setPublicPath('dist')
  .js('resources/js/field.js', 'js')
  .vue()
  .postCss('resources/css/field.css', 'css')
  .options({ processCssUrls: false });
