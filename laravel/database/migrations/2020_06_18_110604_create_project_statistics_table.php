<?php

use App\ProjectStatistics;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(ProjectStatistics::TABLE_NAME, function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId("project_id")->index("project_id_index");
            $table->string("field", "32")->index("field_index");
            $table->string("value", "50")->index("value_index");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_statistics');
    }
}
