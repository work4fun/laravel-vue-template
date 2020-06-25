<?php

use App\ProjectStatistics;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProjectStatisticsTableAddSequence extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(ProjectStatistics::TABLE_NAME, function (Blueprint $table) {
            $table->string('sequence', 32)->default("");
            $table->dropIndex('value_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(ProjectStatistics::TABLE_NAME, function (Blueprint $table) {
            $table->removeColumn('sequence');
            $table->index(["value"], 'value_index');
        });
    }
}
