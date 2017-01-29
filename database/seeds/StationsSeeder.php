<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

use App\Database\User;

class StationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      User::create([
        'name' => 'AIR TV',
        'compact_name' => "AIRTV",
        'username' => 'airtv',
        'email' => 'StationManager@AirTvonline.co.uk',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId1
      ]);

      User::create([
        'name' => 'BEDS TV',
        'compact_name' => "BEDSTV",
        'username' => 'bedstv',
        'email' => 'shayn.dickens@study.beds.ac.uk',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId1
      ]);

      User::create([
        'name' => 'CAMPUS TV',
        'compact_name' => "CAMPUSTV",
        'username' => 'campustv',
        'email' => 'CTV-manager@bath.ac.uk',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId1
      ]);

      User::create([
        'name' => 'CUTV',
        'compact_name' => "CUTV",
        'username' => 'cutv',
        'email' => 'stationmanager@cardiffunion.tv ',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId1
      ]);

      // 5
      User::create([
        'name' => 'CU-TV',
        'compact_name' => "CU-TV",
        'username' => 'cu-tv',
        'email' => 'president@cu-tv.uk',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId1
      ]);

      User::create([
        'name' => 'DEMON TV',
        'compact_name' => "DEMONTV",
        'username' => 'demontv',
        'email' => 'demontv@gmail.com',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId1
      ]);

      User::create([
        'name' => 'Dragon TV',
        'compact_name' => "DragonTV",
        'username' => 'dragontv',
        'email' => 'dragontvmanager@dragonmedia.uk',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId1
      ]);

      User::create([
        'name' => 'Tay Productions',
        'compact_name' => "TayProductions",
        'username' => 'tayproductions',
        'email' => 'tayproductions@dusamedia.com',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId1
      ]);

      User::create([
        'name' => 'EU TV',
        'compact_name' => "EUTV",
        'username' => 'eutv',
        'email' => 'eu.television@gmail.com',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId1
      ]);

      // 10
      User::create([
        'name' => 'FORGE TV',
        'compact_name' => "FORGETV",
        'username' => 'forgetv',
        'email' => 'tv.manager@forgetoday.com',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId1
      ]);

      User::create([
        'name' => 'FUSE TV',
        'compact_name' => "FUSETV",
        'username' => 'fusetv',
        'email' => 'fusetvmanchester@gmail.com',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId1
      ]);

      User::create([
        'name' => 'GCTV',
        'compact_name' => "GCTV",
        'username' => 'gctv',
        'email' => 'Src505@abdn.ac.uk',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId1
      ]);

      User::create([
        'name' => 'Guild TV',
        'compact_name' => "GuildTV",
        'username' => 'guildtv',
        'email' => 'stationmanager@guildtv.co.uk',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId1
      ]);

      User::create([
        'name' => 'GUST',
        'compact_name' => "GUST",
        'username' => 'gust',
        'email' => 'gust@gust.tv',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId1
      ]);

      // 15
      User::create([
        'name' => 'TORCH TV',
        'compact_name' => "TORCHTV",
        'username' => 'torchtv',
        'email' => 'tvhead@hullfire.com',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId1
      ]);

      User::create([
        'name' => 'ICTV',
        'compact_name' => "ICTV",
        'username' => 'ictv',
        'email' => 'ictv@imperial.ac.uk',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId2
      ]);

      User::create([
        'name' => 'KINGS TV',
        'compact_name' => "KINGSTV",
        'username' => 'kingstv',
        'email' => 'eleanor.kingstv@gmail.com',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId2
      ]);

      User::create([
        'name' => 'KTV',
        'compact_name' => "KTV",
        'username' => 'ktv',
        'email' => 'Committee@ktvlive.co.uk',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId2
      ]);

      User::create([
        'name' => 'LA1 TV',
        'compact_name' => "LA1TV",
        'username' => 'la1tv',
        'email' => 'b.kay@la1tv.co.uk',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId2
      ]);

      // 20
      User::create([
        'name' => 'LSTV',
        'compact_name' => "LSTV",
        'username' => 'lstv',
        'email' => 'stationmanager@lstv.co.uk',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId2
      ]);

      User::create([
        'name' => 'LSU TV',
        'compact_name' => "LSUTV",
        'username' => 'lsutv',
        'email' => 'lsutvmanager@lsu.co.uk',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId2
      ]);

      User::create([
        'name' => 'LUST',
        'compact_name' => "LUST",
        'username' => 'lust',
        'email' => 'lust@le.ac.uk',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId2
      ]);

      User::create([
        'name' => 'NSTV',
        'compact_name' => "NSTV",
        'username' => 'nstv',
        'email' => 'stationmanager@nstv.co.uk',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId2
      ]);

      User::create([
        'name' => 'NSU TV',
        'compact_name' => "NSUTV",
        'username' => 'nsutv',
        'email' => 'nsutvteam@gmail.com',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId2
      ]);

      // 25
      User::create([
        'name' => 'PULSE TV',
        'compact_name' => "PULSETV",
        'username' => 'pulsetv',
        'email' => 'ascott6@uclan.ac.uk',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId2
      ]);

      User::create([
        'name' => 'QMTV',
        'compact_name' => "QMTV",
        'username' => 'qmtv',
        'email' => 'qmtvstation@gmail.com',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId2
      ]);

      User::create([
        'name' => 'REBEL',
        'compact_name' => "REBEL",
        'username' => 'rebel',
        'email' => 'ekumm@essex.ac.uk',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId2
      ]);

      User::create([
        'name' => 'RGU TV',
        'compact_name' => "RGUTV",
        'username' => 'rgutv',
        'email' => 'rgutv@rguunion.co.uk',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId2
      ]);

      User::create([
        'name' => 'Rhubarb TV',
        'compact_name' => "RhubarbTV",
        'username' => 'rhubarbtv',
        'email' => 'manager@rhubarbtv.com',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId2
      ]);

      // 30
      User::create([
        'name' => 'RU:ON TV',
        'compact_name' => "RUONTV",
        'username' => 'ruontv',
        'email' => 'manager@ruon.tv',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId2
      ]);

      User::create([
        'name' => 'SCRATCH TV',
        'compact_name' => "SCRATCHTV",
        'username' => 'scratchtv',
        'email' => 'stationmanager@scratchtv.co.uk',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId2
      ]);

      User::create([
        'name' => 'SMOKE TV',
        'compact_name' => "SMOKETV",
        'username' => 'smoketv',
        'email' => 'manager.smoketv@su.westminster.ac.uk',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId2
      ]);

      User::create([
        'name' => 'Source TV',
        'compact_name' => "SourceTV",
        'username' => 'sourcetv',
        'email' => 'ac0405@coventry.ac.uk',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId3
      ]);

      User::create([
        'name' => 'Spa Life TV',
        'compact_name' => "SpaLifeTV",
        'username' => 'spalifetv',
        'email' => 'sean.jolly11@bathspa.ac.uk',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId3
      ]);

      // 35
      User::create([
        'name' => 'STAG TV',
        'compact_name' => "STAGTV",
        'username' => 'stagtv',
        'email' => 'operations@stagtv.co.uk',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId3
      ]);

      User::create([
        'name' => 'STRATH TV',
        'compact_name' => "STRATHTV",
        'username' => 'strathtv',
        'email' => 'filmandtvsoc@gmail.com',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId3
      ]);

      User::create([
        'name' => 'SU-TV',
        'compact_name' => "SUTV",
        'username' => 'sutv',
        'email' => 'sutv@swansea-societies.co.uk',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId3
      ]);

      User::create([
        'name' => 'SUSU TV',
        'compact_name' => "SUSUTV",
        'username' => 'susutv',
        'email' => 'stationmanager@susu.tv',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId3
      ]);

      User::create([
        'name' => 'TCTV',
        'compact_name' => "TCTV",
        'username' => 'tctv',
        'email' => 'tctv.stationmanager@newcastle.ac.uk',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId3
      ]);

      // 40
      User::create([
        'name' => 'TRENT TV',
        'compact_name' => "TRENTTV",
        'username' => 'trenttv',
        'email' => 'james.a.knuckey@live.co.uk',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId3
      ]);

      User::create([
        'name' => 'UB TV',
        'compact_name' => "UBTV",
        'username' => 'ubtv',
        'email' => 'contact.ubtv@gmail.com',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId3
      ]);

      User::create([
        'name' => 'UEA: TV',
        'compact_name' => "UEATV",
        'username' => 'ueatv',
        'email' => 'ueatv.stationmanager@uea.ac.uk',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId3
      ]);

      User::create([
        'name' => 'UNI: TV',
        'compact_name' => "UNITV",
        'username' => 'unitv',
        'email' => 'info@unitvlive.com',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId3
      ]);

      User::create([
        'name' => 'UPSU TV',
        'compact_name' => "UPSUTV",
        'username' => 'upsutv',
        'email' => 'team@upsu.tv',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId3
      ]);

      // 45
      User::create([
        'name' => 'UonTV',
        'compact_name' => "UonTV",
        'username' => 'uintv',
        'email' => 'lukesheehan@hotmail.co.uk',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId3
      ]);

      User::create([
        'name' => 'WTV',
        'compact_name' => "WTV",
        'username' => 'wtv',
        'email' => 'warwicktv@gmail.com',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId3
      ]);

      User::create([
        'name' => 'XTV',
        'compact_name' => "XTV",
        'username' => 'xtv',
        'email' => 'stationmanagers@xtvonline.co.uk',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId3
      ]);

      User::create([
        'name' => 'YSTV',
        'compact_name' => "YSTV",
        'username' => 'ystv',
        'email' => 'station.director@ystv.co.uk',
        'type' => 'station',
        'password' => "",
        // 'dropbox_account_id' => $dropboxId3
      ]);

    }
}
