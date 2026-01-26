<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Make description and short_description optional
        DB::table('attributes')
            ->whereIn('code', ['description', 'short_description'])
            ->update(['is_required' => 0]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert to required (optional, you can comment this out if you don't want to revert)
        // DB::table('attributes')
        //     ->whereIn('code', ['description', 'short_description'])
        //     ->update(['is_required' => 1]);
    }
};
