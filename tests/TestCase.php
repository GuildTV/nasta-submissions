<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Database\User;

abstract class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    // use DatabaseTransactions;

    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    public $emailCount = 0;

    // user instances
    public $station = null;
    public $judge = null;
    public $admin = null;
    public $supportUser = null;

    public $faker = null;

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        putenv('DB_DEFAULT=test_mysql');

        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    public function setUp()
    {
        parent::setUp();

        $this->station = $this->loadStation();
        $this->assertNotNull($this->station, "Missing 'station' entry in database");

        $this->judge = $this->loadJudge();
        $this->assertNotNull($this->judge, "Missing 'judge' entry in database");

        $this->admin = $this->loadAdmin();
        $this->assertNotNull($this->admin, "Missing 'admin' entry in database");

        $this->supportUser = $this->loadSupportUser();
        $this->assertNotNull($this->supportUser, "Missing 'support' user entry in database");

        $this->faker = \Faker\Factory::create();

        Mail::getSwiftMailer()->registerPlugin(new TestMailListener($this));
    }

    public function loadStation()
    {
        return User::where('username', 'test_station')
            ->where('type', 'station')
            ->first();
    }
    public function loadJudge()
    {
        return User::where('username', 'test_judge')
            ->where('type', 'judge')
            ->first();
    }

    public function loadAdmin()
    {
        return User::where('username', 'test_admin')
            ->where('type', 'admin')
            ->first();
    }

    public function loadSupportUser()
    {
        return User::where('username', 'test_support')
            ->where('type', 'support')
            ->first();
    }

    public function postAjax($url, $data = [], $files = [], $headers = [])
    {
        $headers = array_merge($headers, [
            'HTTP_X-Requested-With'=> 'XMLHttpRequest'
        ]);

        $this->call('POST', $url, $data, [], $files, $headers);
        return $this;
    }

    public function putAjax($url, $data = [], $files = [], $headers = [])
    {
        $headers = array_merge($headers, [
            'HTTP_X-Requested-With'=> 'XMLHttpRequest'
        ]);

        $this->call('PUT', $url, $data, [], $files, $headers);
        return $this;
    }

    public function responseJson($assoc = false){
        $content = $this->response->getContent();
        $this->assertNotNull($content);

        $json = json_decode($content, $assoc);
        $this->assertNotNull($json);

        return $json;
    }

    public function assertEmailCount($count) {
        $this->assertEquals($count, $this->emailCount);
    }

    public function assertEmailSent(){
        $this->assertNotEquals(0, $this->emailCount);
    }
}

class TestMailListener implements Swift_Events_SendListener {
    public function __construct($parent){
        $this->parent = $parent;
    }

    public function beforeSendPerformed(Swift_Events_SendEvent $ev){
        $this->parent->emailCount++;
    }

    public function sendPerformed(Swift_Events_SendEvent $ev){
    }
}
