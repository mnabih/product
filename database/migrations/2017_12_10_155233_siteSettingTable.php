<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\SiteSetting;

class SiteSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sitesetting', function (Blueprint $table) {
            $table->increments('id');
            $table->string('site_name')->nullable();
            $table->string('site_logo')->nullable();
            $table->longText('site_description')->nullable();
            $table->longText('site_tagged')->nullable();
            $table->longText('site_copyrigth')->nullable();
            $table->timestamps();
        });

        $setting = new SiteSetting;
        $setting->site_name = 'aait';
        $setting->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sitesetting');
    }
}
