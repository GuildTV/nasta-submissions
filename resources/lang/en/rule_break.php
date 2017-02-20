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
            'bit_rate' => 'Incorrect video bit rate',
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
        'wrapper' => 'Missing media wrapper',

        'video' => [
            'format' => 'Missing video format',
            'bit_rate_mode' => 'Missing video bit rate mode',
            'format_profile' => 'Missing video format profile',
            'pixel_aspect_ratio' => 'Missing video pixel aspect ratio',
            'frame_rate' => 'Missing video frame rate',
            'scan_type' => 'Missing video scan type',
            'standard' => 'Missing video standard',
            'width' => 'Missing video width',
            'height' => 'Missing video height',
            'bit_rate' => 'Missing video bit rate',
            'maximum_bit_rate' => 'Missing video maximum bit rate',
        ],

        'audio' => [
            'format' => 'Missing audio format',
            'channels' => 'Missing audio channel count',
            'sampling_rate' => 'Missing audio sample rate',
            'bit_rate' => 'Missing audio bit rate',
            'maximum_bit_rate' => 'Missing audio maximum bit rate',
        ],

        'not_submitted' => 'Entry has not been submitted',
        'file_result' => [
            'warning' => 'File #:id has a warning',
        ],
    ],


];
