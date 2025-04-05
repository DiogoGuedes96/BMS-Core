<?php

namespace App\Modules\Calls\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ASMCallsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (env('BMS_CLIENT') == 'ASM') {
            $this->asmCallsModules();
        }
    }

    public function asmCallsModules()
    {
        $calls = [
            [
                "caller_phone" => "911911922",
                "linkedid" => "1690310576.87134",
                "status" => "connected",
                "client_name" => "",
                "hangup_status" => "19",
                "created_at" => "2023-07-25 18:42:53",
                "updated_at" => "2023-07-25 18:44:04",
            ],
            [
                "caller_phone" => "969786604",
                "linkedid" => "1690308816.87131",
                "status" => "connected",
                "client_name" => "",
                "hangup_status" => "16",
                "created_at" => "2023-07-25 18:13:33",
                "updated_at" => "2023-07-27 14:53:34",
            ],
            [
                "caller_phone" => "910066327",
                "linkedid" => "1690308354.87126",
                "status" => "connected",
                "client_name" => "",
                "hangup_status" => "16",
                "created_at" => "2023-07-25 18:05:51",
                "updated_at" => "2023-07-27 15:00:40",
            ],
            [
                "caller_phone" => "964367459",
                "linkedid" => "1690308339.87125",
                "status" => "connected",
                "client_name" => "",
                "hangup_status" => "16",
                "created_at" => "2023-07-25 18:05:36",
                "updated_at" => "2023-07-27 15:16:54",
            ],
            [
                "caller_phone" => "964367459",
                "linkedid" => "1690308307.87123",
                "status" => "hangup",
                "client_name" => "",
                "hangup_status" => "16",
                "created_at" => "2023-07-25 18:05:04",
                "updated_at" => "2023-07-27 15:35:30",
            ],
            [
                "caller_phone" => "944955966",
                "linkedid" => "1690308157.87120",
                "status" => "connected",
                "client_name" => "",
                "hangup_status" => "16",
                "created_at" => "2023-07-25 18:02:34",
                "updated_at" => "2023-07-25 18:03:31",
            ],
            [
                "caller_phone" => "915186196",
                "linkedid" => "1690308111.87116",
                "status" => "hangup",
                "client_name" => "",
                "hangup_status" => "16",
                "created_at" => "2023-07-25 18:01:48",
                "updated_at" => "2023-07-27 15:35:01",
            ],
            [
                "caller_phone" => "919521984",
                "linkedid" => "1690308068.87114",
                "status" => "hangup",
                "client_name" => "",
                "hangup_status" => "16",
                "created_at" => "2023-07-25 18:01:05",
                "updated_at" => "2023-07-25 18:01:57",
            ],
            [
                "caller_phone" => "912047029",
                "linkedid" => "1690308016.87111",
                "status" => "hangup",
                "client_name" => "",
                "hangup_status" => "16",
                "created_at" => "2023-07-25 18:00:13",
                "updated_at" => "2023-07-25 18:00:56",
            ],
            [
                "caller_phone" => "988999900",
                "linkedid" => "1690307903.87108",
                "status" => "hangup",
                "client_name" => "",
                "hangup_status" => "16",
                "created_at" => "2023-07-25 17:58:20",
                "updated_at" => "2023-07-25 17:59:46",
            ],
            [
                "caller_phone" => "916774779",
                "linkedid" => "1690307603.87096",
                "status" => "hangup",
                "client_name" => "",
                "hangup_status" => "16",
                "created_at" => "2023-07-25 17:53:20",
                "updated_at" => "2023-07-25 17:56:25",
            ],
            [
                "caller_phone" => "932124290",
                "linkedid" => "1690307570.87093",
                "status" => "hangup",
                "client_name" => "",
                "hangup_status" => "16",
                "created_at" => "2023-07-25 17:52:47",
                "updated_at" => "2023-07-25 17:56:28",
            ],
            [
                "caller_phone" => "977966955",
                "linkedid" => "1690307529.87091",
                "status" => "hangup",
                "client_name" => "",
                "hangup_status" => "19",
                "created_at" => "2023-07-25 17:52:06",
                "updated_at" => "2023-07-25 17:53:17",
            ],
            [
                "caller_phone" => "916774779",
                "linkedid" => "1690307476.87088",
                "status" => "hangup",
                "client_name" => "",
                "hangup_status" => "127",
                "created_at" => "2023-07-25 17:51:14",
                "updated_at" => "2023-07-25 17:51:20",
            ],
            [
                "caller_phone" => "944933922",
                "linkedid" => "1690307376.87084",
                "status" => "hangup",
                "client_name" => "",
                "hangup_status" => "16",
                "created_at" => "2023-07-25 17:49:33",
                "updated_at" => "2023-07-25 17:51:00",
            ],
            [
                "caller_phone" => "911887511",
                "linkedid" => "1690307324.87081",
                "status" => "hangup",
                "client_name" => "",
                "hangup_status" => "16",
                "created_at" => "2023-07-25 17:48:41",
                "updated_at" => "2023-07-25 17:49:23",
            ],
            [
                "caller_phone" => "296302110",
                "linkedid" => "1690307268.87079",
                "status" => "hangup",
                "client_name" => "",
                "hangup_status" => "16",
                "created_at" => "2023-07-25 17:47:46",
                "updated_at" => "2023-07-25 17:50:22",
            ],
            [
                "caller_phone" => "919294000",
                "linkedid" => "1690307143.87058",
                "status" => "hangup",
                "client_name" => "",
                "hangup_status" => "16",
                "created_at" => "2023-07-25 17:45:40",
                "updated_at" => "2023-07-25 17:45:56",
            ],
            [
                "caller_phone" => "296302110",
                "linkedid" => "1690307109.87055",
                "status" => "hangup",
                "client_name" => "",
                "hangup_status" => "127",
                "created_at" => "2023-07-25 17:45:07",
                "updated_at" => "2023-07-25 17:46:39",
            ],
            [
                "caller_phone" => "913944795",
                "linkedid" => "1690307105.87054",
                "status" => "hangup",
                "client_name" => "",
                "hangup_status" => "16",
                "created_at" => "2023-07-25 17:45:02",
                "updated_at" => "2023-07-25 17:46:00",
            ],
            [
                "caller_phone" => "919294000",
                "linkedid" => "1690307077.87041",
                "status" => "hangup",
                "client_name" => "",
                "hangup_status" => "16",
                "created_at" => "2023-07-25 17:44:34",
                "updated_at" => "2023-07-25 17:44:52",
            ],
            [
                "caller_phone" => "296480000",
                "linkedid" => "1690307061.87036",
                "status" => "hangup",
                "client_name" => "",
                "hangup_status" => "16",
                "created_at" => "2023-07-25 17:44:18",
                "updated_at" => "2023-07-25 17:47:35",
            ],
            [
                "caller_phone" => "910214584",
                "linkedid" => "1690306925.87020",
                "status" => "hangup",
                "client_name" => "",
                "hangup_status" => "16",
                "created_at" => "2023-07-25 17:42:02",
                "updated_at" => "2023-07-25 17:43:40",
            ],
            [
                "caller_phone" => "296302110",
                "linkedid" => "1690306825.87017",
                "status" => "hangup",
                "client_name" => "",
                "hangup_status" => "16",
                "created_at" => "2023-07-25 17:40:22",
                "updated_at" => "2023-07-25 17:41:03",
            ],
            [
                "caller_phone" => "296302110",
                "linkedid" => "1690306761.87014",
                "status" => "hangup",
                "client_name" => "",
                "hangup_status" => "16",
                "created_at" => "2023-07-25 17:39:18",
                "updated_at" => "2023-07-25 17:40:08",
            ],
            [
                "caller_phone" => "911911922",
                "linkedid" => "1690310576.87134",
                "status" => "hungup",
                "client_name" => "",
                "hangup_status" => "127",
                "created_at" => "2023-07-25 18:42:53",
                "updated_at" => "2023-07-25 18:44:04",
            ],
            [
                "caller_phone" => "911911922",
                "linkedid" => "1690310576.87134",
                "status" => "hungup",
                "client_name" => "",
                "hangup_status" => "127",
                "created_at" => "2023-07-25 18:42:53",
                "updated_at" => "2023-07-25 18:44:04",
            ],
            [
                "caller_phone" => "911911922",
                "linkedid" => "1690310576.87134",
                "status" => "hungup",
                "client_name" => "",
                "hangup_status" => "127",
                "created_at" => "2023-07-25 18:42:53",
                "updated_at" => "2023-07-25 18:44:04",
            ],
            [
                "caller_phone" => "911911922",
                "linkedid" => "1690310576.87134",
                "status" => "hungup",
                "client_name" => "",
                "hangup_status" => "127",
                "created_at" => "2023-07-25 18:42:53",
                "updated_at" => "2023-07-25 18:44:04",
            ],
        ];

        foreach ($calls as $call){
            if (!DB::table('asterisk_calls')->where('linkedid', $call['linkedid'])->exists()) {
                DB::table('asterisk_calls')->insert($call);
            }
        }
    }
}
