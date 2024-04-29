const mix = require('laravel-mix');

mix.js('resources/js/slide-over.js', 'public/')
  .postCss('resources/css/slide-over.css', 'public/', [
    require('tailwindcss'),
  ]);
