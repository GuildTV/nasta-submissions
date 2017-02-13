<?php

return [

    'error' => [
        'resolution' => 'Invalid video resolution. No more checks were run',

        'video' => [
            'wrapper' => 'Incorrect media wrapper',
            'format' => 'Incorrect video format',
            'bit_rate_mode' => 'Incorrect video bit rate mode',
            'format_profile' => 'Incorrect video format profile',
            'pixel_aspect_ratio' => 'Incorrect video pixel aspect ratio',
            'frame_rate' => 'Incorrect video frame rate',
            'scan_type' => 'Incorrect video scan type',
            'standard' => 'Incorrect video srandard',
            'width' => 'Incorrect video width',
            'height' => 'Incorrect video height',
            'bit_rate' => 'Incorrect video bit ratea',
            'maximum_bit_rate' => 'Incorrect video maximum bit rate',
        ],

        'audio' => [
            'format' => 'Incorrect audio format',
            'channels' => 'Incorrect audio channel count',
            'sampling_rate' => 'Incorrect audio sample rate',
            'bit_rate' => 'Incorrect audio bit rate',
            'maximum_bit_rate' => 'Incorrect audio maximum bit rate',
        ],


        'file_count' => 'Wrong number of files were submitted',
        'matched_file_count' => 'Could not match files to constraints',
        'file_too_long' => 'File #:id is too long',

        'file_result' => [
            'missing' => 'File #:id is missing rule check results',
            'unknown' => 'File #:id has inconclusive rule check results',
            'break' => 'File #:id has broken the rules',
            'rejected' => 'File #:id has been rejected',
        ],
    ],

    'warning' => [
        'wrapper' => 'Unknown media wrapper',

        'video' => [
            'format' => 'Unknown video format',
            'bit_rate_mode' => 'Unknown video bit rate mode',
            'format_profile' => 'Unknown video format profile',
            'pixel_aspect_ratio' => 'Unknown video pixel aspect ratio',
            'frame_rate' => 'Unknown video frame rate',
            'scan_type' => 'Unknown video scan type',
            'standard' => 'Unknown video standard',
            'width' => 'Unknown video width',
            'height' => 'Unknown video height',
            'bit_rate' => 'Unknown video bit ratea',
            'maximum_bit_rate' => 'Unknown video maximum bit rate',
        ],

        'audio' => [
            'format' => 'Unknown audio format',
            'channels' => 'Unknown audio channel count',
            'sampling_rate' => 'Unknown audio sample rate',
            'bit_rate' => 'Unknown audio bit rate',
            'maximum_bit_rate' => 'Unknown audio maximum bit rate',
        ],

        'not_submitted' => 'Entry has not been submitted',
        'file_result' => [
            'warning' => 'File #:id has a warning',
        ],
    ],


];
