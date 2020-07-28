<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->integer('admin_id')->unsigned()->primary()->comment('管理员ID');
            $table->string('site_name')->default('OLAINDEX')->comment('站点名称');
            $table->string('theme')->default('cosmo')->comment('站点主题');
            $table->string('hotlink_protection')->nullable()->comment('防盗链');
            $table->string('copyright')->nullable()->comment('自定义版权显示');
            $table->string('statistics')->nullable()->comment('统计代码');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
