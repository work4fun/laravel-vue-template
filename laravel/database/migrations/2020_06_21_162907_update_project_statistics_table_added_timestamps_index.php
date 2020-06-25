<?php

use App\ProjectStatistics;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProjectStatisticsTableAddedTimestampsIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(ProjectStatistics::TABLE_NAME, function (Blueprint $table) {
            $table->index(['created_at', 'updated_at'], 'timestamps');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
