<?php

return [
	'year' => 2017,

  // File stating the usage guide for within each folder
  'assets' => [
    'lineup' => [ // links generated with https://sites.google.com/site/gdocs2direct/
      'example' => "https://drive.google.com/uc?export=download&id=0B0ZjlKJimJWvTnJtYXdZOHhVS0E",
      'bg' => "https://drive.google.com/uc?export=download&id=0B0ZjlKJimJWvc1JsUkFQZUg2LVE",
      'overlay' => "https://drive.google.com/uc?export=download&id=0B0ZjlKJimJWvTzZQY2IzVmlvQkE",
    ],
    'guide' => "https://drive.google.com/uc?export=download&id=0B0ZjlKJimJWvaGFwWWtyZXhvWUk", // submissions guide
  ],

  'video_specs_common' => [
    "audio" => [
      "format" => [ "AAC" ],
      "bit_rate" => 192,
      "maximum_bit_rate" => 320,
      "channels" => 2,
      "sampling_rate" => [ 48000, 44100 ],
    ],
    "video" => [
      "format" => [ "AVC", "H.264", "H264" ],
      "bit_rate_mode" => "VBR",
      "format_profile" => "High@L4.1",
      "pixel_aspect_ratio" => 1.0,
      "frame_rate" => 25,
      "scan_type" => "Progressive",
      "standard" => "PAL",
    ]
  ],

  'video_specs' => [
    'N-SD' => [
      "video" => [
        "bit_rate" => 3000,
        "maximum_bit_rate" => 5000,
        "width" => 1024,
        "height" => 576,
      ]
    ],
    'N-HD' => [
      "video" => [
        "bit_rate" => 5000,
        "maximum_bit_rate" => 10000,
        "width" => 1280,
        "height" => 720,
      ]
    ],
    'N-FHD' => [
      "video" => [
        "bit_rate" => 10000,
        "maximum_bit_rate" => 15000,
        "width" => 1920,
        "height" => 1080,
      ]
    ],
  ],

  'video_bitrate_tolerance' => [
    'bit_rate' => [
      'acceptable' => 1.05, // Auto accept 5%
      'needs_approval' => 1.40, // Need approval for up to 40%
    ],
    'maximum_bit_rate' => [
      'acceptable' => 1.01, // Auto accept 1%
      'needs_approval' => 1.10, // Need approval for up to 10%
    ],
  ],

  'late_edit_period' => 60, // Minutes allowed to edit 
  'close_to_deadline_threshold' => 30, // Minutes classed as close to deadline

  // Folder in dropbox to move uploads once imported into the database
  'dropbox_imported_files_path' => "/Imported", // subfoldered by station name. no trailing slash

  'local_entries_path' => env('LOCAL_ENTRY_DIR', storage_path("app/entries") . "/"),
];