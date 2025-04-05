<?php

namespace Modules\Calls\Commands;

use App\Modules\Calls\Models\AsteriskCredentials;
use App\Modules\Calls\Services\AsteriskService;
use Illuminate\Console\Command;
use \PAMI\Client\Impl\ClientImpl as PamiClient;
use \PAMI\Message\Event\EventMessage;
use Throwable;

class AteriskListenerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asterisk:listener';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initiates a listener for asterisk service';

    protected $pamiClient;

    protected $AsteriskService;

    protected $asteriskCredentials;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->AsteriskService = new AsteriskService();
        $this->asteriskCredentials = new AsteriskCredentials();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info(now()." Preparing Connection!");

        try {
            $this->AsteriskService->endAllCalls();
            $this->handleAsteriskConnection();
        } catch (\Throwable $th) {
            $this->error(now(). ' Error on command: '. $th);
        }
    }

    /**
     * It opens a connection with asterisk, and then it processes the connection.
     */
    public function handleAsteriskConnection(){
        try{
            $firstConnection = $this->openAsteriskConnection();

            $this->info(now()." Conection open with success!");

            $firstConnection = $this->newAsteriskConnection($firstConnection);
            $this->processAsteriskConnection($firstConnection);
        }catch(Throwable $th){
            $this->error(now(). ' Error on command: '. $th);
            $this->info(now()." Retriyng conenction!");
            $this->handle();
        }
    }

    /**
     * This function is called when a new client connects to the server. It registers an event listener
     * that will be called when an event is received from the client
     * 
     * @param client The client object that is being registered.
     * 
     * @return The client object
     */
    public function newAsteriskConnection($client){
        try{
            $client->registerEventListener(function (EventMessage $event) {
                //Save the event to the database and process call
                $this->AsteriskService->handleAsteriskCall($event, $this);
            });

            return $client;
        }catch(Throwable $th){
            $this->error(now(). ' Error on command: '. $th);
            $this->info(now()." Retriyng conenction!");
            $this->handle();
        }
    }

    /**
     * It opens a connection to Asterisk, then it processes the connection for a certain amount of
     * time defined in the projects env file.
     * After that amount of time, it opens a new connection that overlaps the original connection.
     * After opening the new connection, it closes the existing connection.
     * After the amount of time defined passes, this cycle is repeated indefinitely.
     * 
     * @param client The client object that is being used to connect to Asterisk.
     */
    public function processAsteriskConnection($client){
        try{
            $activeConnection = 0;
            $dateStart = microtime(true);

            while ($activeConnection <= env('ASTERISK_REBOOT_TIME')) {
                $client->process($this);
                $activeConnection = (microtime(true) - $dateStart) * 1000;
            }

            $secondConnection = $this->openAsteriskConnection();
            $this->info(now()." New connection established!");
            $secondConnection = $this->newAsteriskConnection($secondConnection);

            $client->close();

            $this->info(now()." Original conection closed with success!");
            $this->processAsteriskConnection($secondConnection);
        }catch(Throwable $th){
            $this->error(now() . ' Error on command: ' . $th);
            $this->info(now()." Retriyng conenction!");
            $this->handle();
        }
    }

    public function openAsteriskConnection(){
        try{            
            $currentCredentials = $this->asteriskCredentials->first();

            $pamiClientOption = array(
                'host' => $currentCredentials->host,
                'scheme' =>  $currentCredentials->scheme,
                'port' => $currentCredentials->port,
                'username' => $currentCredentials->username,
                'secret' => $currentCredentials->secret,
                'connect_timeout' => (int)$currentCredentials->connect_timeout,
                'read_timeout' => (int)$currentCredentials->read_timeout,
            );

            $client = new PamiClient($pamiClientOption);
            $client->open();

            return $client;
        }catch(Throwable $th){
            $this->error(now(). ' Error on command: ' . $th);
            $this->info(now()." Retriyng conenction!");
            $this->handle();
        }
    }
}