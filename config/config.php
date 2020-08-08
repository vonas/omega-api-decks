<?php

use Game\Repository\NameMatchOptions;
use Image\ImageType;
use Render\CellOverlap;
use Render\Rectangle;
use Render\Spacing;
use Render\TableLayout;
use Render\Vector;


# TODO: disable all HTML warnings and errors


#####
// Config::put_env('DATA_DIR', '/Users/jonas/Apache/data');
// Config::put_env('DATABASE_URL', 'file:///Users/jonas/Apache/deck-list/database.db');
// Config::put_env('CARD_IMAGE_URL', 'file:///Volumes/Extension/Omega/pics_small');
// Config::put_env('CARD_IMAGE_URL_EXT', 'jpg');
// Config::put_env('WEBHOOK_UPDATE_TOKEN', '7925587626b24ee84bf5a86c2634cfa982297e21f9ebfed8efa0f11b621794d3');
#####


Config::require_env('DATA_DIR');


Config::set_all([

  'formats' => [
    YdkeFormatStrategy::class,
    YdkFormatStrategy::class,
    OmegaFormatStrategy::class,
    NameFormatDecodeStrategy::class
  ],

  'images' => [
    // the path to the background image for a deck list.
    // the dimensions of the tables which are defined later
    // in this file depend on the characteristics of this image.
    'background' => STATIC_DIR . '/background-simple.jpg',

    // the placeholder image for a card that is not found in the database.
    'placeholder' => STATIC_DIR . '/unknown.jpg',

    // if true, resized images are being resampled.
    'resample_resized' => true
  ],

  'output' => [
    // the image type as which a generated deck list image is encoded.
    'image_type' => ImageType::JPEG,
    'content_disposition' => [
      // the filename specified in the Content-Disposition header.
      // this is the name of the file one would download from their browser.
      'filename' => 'deck-list'
    ]
  ],

  'cache' => [
    // the directory in which cached images are stored
    'directory' => Config::get_env('DATA_DIR') . '/cache',

    // images are stored in subfolders which are named by the first N
    // characters of their filename. this clustering enables faster glob
    // pattern matching and possibly also shorter file lookup times.
    'subfolder_length' => 2,

    // if true, card images are stored with their original dimensions.
    // doing this results in resizing each card image on every (!) request,
    // which will most certainly add up to a couple hundred milliseconds
    // of additional execution time. enabling this is not recommended.
    'cache_original' => false
  ],

  'repository' => [
    // the name of the generated database file.
    'path' => Config::get_env('DATA_DIR') . '/card.db',
    'options' => new NameMatchOptions(
      2 / 5, // maximum difference in length between input and a name (per letter)
      1 / 5  // maximum amount of allowed errors per letter
    ),
    // the name of the lock file that is used for keeping updates atomic.
    'update_lock_file' => Config::get_env('DATA_DIR') . '/update.lock~'
  ],

]);


$table_x = 25; // common x root component of every table
$table_width = 902; // same width for each table
$cell_spacing = new Spacing(10, 7); // spacing between cells


// the dimensions of a table's cell
Config::set('cell', new Rectangle(81, 118));

Config::set('tables', [

  'main' => [
    'root' => new Vector($table_x, 60),
    'spacing' => $cell_spacing,
    'width' => $table_width,
    'height' => 526,
    'layout' => [
      'primary' => TableLayout::LEFT_TO_RIGHT,
      'secondary' => TableLayout::TOP_TO_BOTTOM
    ],
    'overlap' => CellOverlap::VERTICAL
  ],

  'extra' => [
    'root' => new Vector($table_x, 645),
    'spacing' => $cell_spacing,
    'width' => $table_width,
    'height' => Config::get('cell')->height(),
    'layout' => [
      'primary' => TableLayout::LEFT_TO_RIGHT,
      'secondary' => TableLayout::TOP_TO_BOTTOM
    ],
    'overlap' => CellOverlap::HORIZONTAL
  ],

  'side' => [
    'root' => new Vector($table_x, 818),
    'spacing' => $cell_spacing,
    'width' => $table_width,
    'height' => Config::get('cell')->height(),
    'layout' => [
      'primary' => TableLayout::LEFT_TO_RIGHT,
      'secondary' => TableLayout::TOP_TO_BOTTOM
    ],
    'overlap' => CellOverlap::HORIZONTAL
  ]

]);


if (file_exists(__DIR__ . '/config.dev.php'))
  require(__DIR__ . '/config.dev.php');