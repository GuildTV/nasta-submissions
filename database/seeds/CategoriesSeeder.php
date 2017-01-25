<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

use App\Database\Category\Category;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $mondayClosing    = Carbon::create(2017, 2, 20, 17, 0, 0);
      $tuesdayClosing   = Carbon::create(2017, 2, 21, 17, 0, 0);
      $wednesdayClosing = Carbon::create(2017, 2, 22, 17, 0, 0);
      $thursdayClosing  = Carbon::create(2017, 2, 23, 17, 0, 0);
      $fridayClosing    = Carbon::create(2017, 2, 24, 17, 0, 0);

      Category::create([
        'id' => 'animation',
        'name' => 'Animation',
        'compact_name' => 'Animation',
        'description' => 'A single animation programme (or a shortened edit from an episode or series), or an original piece of animation of any type, which has been produced by the broadcaster.',
        'closing_at' => $mondayClosing
      ]);

      Category::create([
        'id' => 'best-broadcaster',
        'name' => 'Best Broadcaster',
        'compact_name' => 'BestBroadcaster',
        'description' => 'A showreel demonstrating the range, quality and skills of the stationand its programming, to be accompanied by a written report, with details of the operation of the station and contributions made which may not necessarily appear on screen.',
        'closing_at' => $mondayClosing
      ]);

      Category::create([
        'id' => 'cinematography',
        'name' => 'Cinematography',
        'compact_name' => 'Cinematography',
        'description' => 'The category for Best Cinematography is an opportunity for filmmakers to showcase their best work. The winning entry will display a knowledge of appropriate lighting, camera moves and other associated techniques of the craft, and how well these things are implemented in film or television.',
        'closing_at' => $mondayClosing
      ]);

      Category::create([
        'id' => 'comedy',
        'name' => 'Comedy',
        'compact_name' => 'Comedy',
        'description' => 'A single comedy programme/sketch (or a shortened edit from an episode or series).',
        'closing_at' => $mondayClosing
      ]);

      Category::create([
        'id' => 'documentary',
        'name' => 'Documentary',
        'compact_name' => 'Documentary',
        'description' => 'A self-contained programme (or a shortened edit from an episode or series) with the aim to inform viewers abouta specific subject.',
        'closing_at' => $tuesdayClosing
      ]);

      Category::create([
        'id' => 'drama',
        'name' => 'Drama',
        'compact_name' => 'Drama',
        'description' => 'An original, scripted dramatic production, which is either self-contained, an episode, or a shortened edit from an episode or series.',
        'closing_at' => $tuesdayClosing
      ]);

      Category::create([
        'id' => 'factual',
        'name' => 'Factual',
        'compact_name' => 'Factual',
        'description' => 'A single programme (or a shortened edit from an episode or series) featuring factual material, presented in any format.',
        'closing_at' => $tuesdayClosing
      ]);

      Category::create([
        'id' => 'freshers',
        'name' => 'Freshers\' Coverage',
        'compact_name' => 'Freshers',
        'description' => 'This category recognises the quality and diversity of a station’s covering of their campus’ Freshers’ week(s) activities.',
        'closing_at' => $tuesdayClosing
      ]);

      Category::create([
        'id' => 'ident',
        'name' => 'Ident',
        'compact_name' => 'Ident',
        'description' => 'A single, complete video representing or establishing your station’s identity. This entry must be a complete video not a cut down video or an edited highlights reel.',
        'closing_at' => $tuesdayClosing
      ]);

      Category::create([
        'id' => 'light-entertainment',
        'name' => 'Light Entertainment',
        'compact_name' => 'LightEntertainment',
        'description' => 'A single feature, programme (or a shortened edit from an episode or series) intended as light entertainment.',
        'closing_at' => $wednesdayClosing
      ]);

      Category::create([
        'id' => 'live',
        'name' => 'Live Broadcast',
        'compact_name' => 'Live',
        'description' => 'A programme (or a shortened edit from an episode or series) that has been broadcast live or shot as-live by your station. Entries for this category must not have had any audio or video processing applied after transmission.',
        'closing_at' => $wednesdayClosing
      ]);
      
      Category::create([
        'id' => 'mars-el-brogy',
        'name' => 'Mars el Brogy',
        'compact_name' => 'Mars',
        'description' => 'DETAILS TO COME',
        'closing_at' => $wednesdayClosing
      ]);
      
      Category::create([
        'id' => 'music',
        'name' => 'Music Programming',
        'compact_name' => 'Music',
        'description' => 'A feature, single programme (or a shortened edit from an episode or series) that’s central focus is live or recorded music, and/or airs views on music. Please note this is not a video piece set or edited to music.',
        'closing_at' => $wednesdayClosing
      ]);
      
      Category::create([
        'id' => 'news-current-affairs',
        'name' => 'News and Current Affairs',
        'compact_name' => 'NewsCurrentAffairs',
        'description' => 'A single programme (or a shortened edit from an episode or series) reporting or commenting on news or current affairs.',
        'closing_at' => $wednesdayClosing
      ]);
      
      Category::create([
        'id' => 'on-screen-female',
        'name' => 'On-Screen Female',
        'compact_name' => 'Female',
        'description' => 'A showreel to show the skills, styles and techniques of a particular female presenter or actor.',
        'closing_at' => $thursdayClosing
      ]);
      
      Category::create([
        'id' => 'on-screen-male',
        'name' => 'On-Screen Male',
        'compact_name' => 'Male',
        'description' => 'A showreel to show the skills, styles and techniques of a particular male presenter or actor.',
        'closing_at' => $thursdayClosing
      ]);
      
      Category::create([
        'id' => 'open',
        'name' => 'Open',
        'compact_name' => 'Open',
        'description' => 'A feature, single programme (or a shortened edit from an episode or series) of any type, which has been produced by your station.',
        'closing_at' => $thursdayClosing
      ]);
      
      Category::create([
        'id' => 'post-production',
        'name' => 'Post Production',
        'compact_name' => 'PostProduction',
        'description' => 'DETAILS TO COME',
        'closing_at' => $thursdayClosing
      ]);
      
      Category::create([
        'id' => 'sport',
        'name' => 'Sport',
        'compact_name' => 'Sport',
        'description' => 'Coverage of a sporting event or a single programme (or a shortened edit from an episode or series) which features live or recorded sport, and/or comments on sport or sports facilities.',
        'closing_at' => $thursdayClosing
      ]);
      
      Category::create([
        'id' => 'marketing',
        'name' => 'Station Marketing',
        'compact_name' => 'Marketing',
        'description' => 'A video submission demonstrating the achievements of your station’s marketing across your campus and online, incorporating special events, advertising and on- air branding. The submission must be accompanied by a written document detailing marketing strategies, tools, and techniques used by your station.',
        'closing_at' => $fridayClosing
      ]);
      
      Category::create([
        'id' => 'technical',
        'name' => 'Technical Achievement',
        'compact_name' => 'Technical',
        'description' => 'A report which gives an account of any technical achievement(s) and/or developed to support your station’s output.',
        'closing_at' => $fridayClosing
      ]);
      
      Category::create([
        'id' => 'tim-marshall',
        'name' => 'Tim Marshall award for Special Recognition',
        'compact_name' => 'TimMarshall',
        'description' => 'This category goes to a station worthy of special recognition for outstanding achievement, especially with respect to the station’s commitment to overcoming challenging circumstances and achievement through innovation in the past year.',
        'closing_at' => $fridayClosing
      ]);
      
      Category::create([
        'id' => 'title',
        'name' => 'Title Sequence',
        'compact_name' => 'Title',
        'description' => 'The introductory sequence to one of your station’s programmes. The very beginning of an exemplar programme may be included. This entry must be a complete video not a cut down video or an edited highlights reel.',
        'closing_at' => $fridayClosing
      ]);
      
      Category::create([
        'id' => 'writing',
        'name' => 'Writing',
        'compact_name' => 'Writing',
        'description' => 'A script in any genre or format, for any kind of show produced by the station. This can include, but is not limited to, fictional teleplays, factual links and features, documentary scripts, live scripts, news piece, etc..',
        'closing_at' => $fridayClosing
      ]);
      
    }
}
