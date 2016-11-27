<?php

return [
	'year' => 2017,

  // File stating the usage guide for within each folder
  'drive_rules_file' => "0B4xCe4AF2PEUc3RhcnRlcl9maWxlX2Rhc2hlclYw",

  'video_specs' => [ // Must be lowest to highest bitrate
    'N-SD' => [
      'w' => 1024,
      'h' => 576,
      'bitrate' => 5.2
    ],
    'N-HD' => [
      'w' => 1280,
      'h' => 720,
      'bitrate' => 10.2
    ],
    'N-FHD' => [
      'w' => 1920,
      'h' => 1080,
      'bitrate' => 15.2
    ],
  ],

  'video_bitrate_tolerance' => [
    'acceptable' => 1.01, // Auto accept 1%
    'needs_approval' => 1.10, // Need approval for up to 10%
  ],

];