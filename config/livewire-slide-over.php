<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default Slide Over Position
    |--------------------------------------------------------------------------
    | Configure which way the slide-over will open
    |
    | Available slide overs position
    | Position::Right, Position::Left, Position::Bottom
    |
    */

    'default_position' => \Laravelcm\LivewireSlideOvers\Position::Right,

    /*
    |--------------------------------------------------------------------------
    | Slide Over Component Defaults
    |--------------------------------------------------------------------------
    |
    | Configure the default properties for a slide-over component.
    |
    | Supported slide_over_max_width
    | 'sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', '6xl', '7xl'
    */

    'component_defaults' => [
        'slide_over_max_width' => 'xl',
        'close_slide_over_on_click_away' => true,
        'close_slide_over_on_escape' => true,
        'close_slide_over_on_escape_is_forceful' => true,
        'dispatch_close_event' => false,
        'destroy_on_close' => false,
    ],

];
