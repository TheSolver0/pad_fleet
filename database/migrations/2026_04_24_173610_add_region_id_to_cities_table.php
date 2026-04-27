<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->foreignId('region_id')->nullable()->after('code')->constrained('regions')->nullOnDelete();
        });

        $regions = DB::table('regions')->pluck('id', 'name');
        $cities = DB::table('cities')->select(['id', 'region'])->get();
        foreach ($cities as $city) {
            if (! empty($city->region) && isset($regions[$city->region])) {
                DB::table('cities')->where('id', $city->id)->update(['region_id' => $regions[$city->region]]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropConstrainedForeignId('region_id');
        });
    }
};

