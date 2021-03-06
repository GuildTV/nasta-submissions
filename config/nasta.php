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
      "bit_rate_tolerance" => 1.20,
      "maximum_bit_rate_tolerance" => 1.05,
    ],
    "video" => [
      "format" => [ "AVC", "H.264", "H264" ],
      "bit_rate_mode" => "VBR",
      "format_profile" => "High@L4.1",
      "pixel_aspect_ratio" => 1.0,
      "frame_rate" => 25,
      "scan_type" => "Progressive",
      "standard" => "PAL",
      "bit_rate_tolerance" => 1.20,
      "maximum_bit_rate_tolerance" => 1.05,
    ],
    "wrapper" => "video/mp4",
  ],

  'video_specs' => [ // this will misbehave if there is more than one of each resolution. also width & height are required in each of these
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

  'late_edit_period' => 60, // Minutes allowed to edit 
  'close_to_deadline_threshold' => 30, // Minutes classed as close to deadline

  // Folder in dropbox to move uploads once imported into the database
  'dropbox_imported_files_path' => "/Imported", // subfoldered by station name. no trailing slash

  'local_entries_path' => env('LOCAL_ENTRY_DIR', storage_path("app/entries") . "/"),

  'encode_profiles' => [
    'fix_audio' => 6,
    '1080p' => 7,
    '720p' => 8,
    'sd' => 9,
  ],

  'judge_support_email' => 'judges@nasta.tv',
];