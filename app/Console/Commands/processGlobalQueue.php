<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\GlobalQueue;
use Illuminate\Console\Command;

class processGlobalQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'globalq:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Global Queue items';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $maxActiveItems = 6;
        $maxItemAliveDays = 12;
        //expire active items after 3 hours if we have a bigger queue. Item will still rotate in until it hits maxItemAlive days
        $activeExpireHours = 1;

        $activeCount = GlobalQueue::where('active', 1)->count();
        $queueCount = GlobalQueue::all()->count();

        $this->info("$activeCount active items, $queueCount items on queue");

        //delete items older then set days
        $count = GlobalQueue::where('created_at', '<=', Carbon::now()->subDays($maxItemAliveDays)->toDateTimeString())->delete();
        $this->info("deleted $count items off queue");

        //make sure we have enough items
        if($activeCount < $maxActiveItems) {
            $itemsNeededCount = $maxActiveItems - $activeCount;

            $inactiveItems = GlobalQueue::where('active', 0)
                ->orderBy("created_at", "DESC")
                ->limit($itemsNeededCount)
                ->get();
            
            if($inactiveItems) {
                $this->info("activating " . count($inactiveItems) . " items");
                foreach($inactiveItems as $item) {
                    $item->active = 1;
                    $item->active_at = date('Y-m-d H:i:s');
                    $item->save();
                }
            }
        }else{
            //rotate items
            $expiredActiveItems = GlobalQueue::where('active_at', '<', Carbon::now()->subMinutes($activeExpireHours)->toDateTimeString())
                ->where('active', 1)
                ->get();

            if(count($expiredActiveItems) > 0) {
                $this->info("Expiring " . count($expiredActiveItems) . " active items");

                foreach($expiredActiveItems as $item) {
                    $item->active = 0;
                    $item->save();
                }

                $neededItemsCount = $maxActiveItems - GlobalQueue::where('active', 1)->count();

                $leastFreshItems = GlobalQueue::orderBy('active_at', "ASC")->limit($neededItemsCount)->get();
    
                $this->info(count($leastFreshItems) . " being added to queue for freshness");
                foreach($leastFreshItems as $item) {
                    $item->active = 1;
                    $item->active_at = date('Y-m-d H:i:s');
                    $item->save();
                }
            }
        }        

        $this->info("process done");
    }
}
