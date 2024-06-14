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
            $table->id();
            $table->string('app_name')->default('PrimeBomber');
            $table->longText('apis')->nullable();
            $table->string('ga_id')->nullable();
            $table->bigInteger('max_bombing')->default(5000);
            $table->integer("max_load")->default(5);
            $table->longText('notice');
            $table->string('max_load_text')->default("Server is busy now..");
            $table->string('forbidden_text')->default("Contact Pme");
            $table->string('footer')->default("MADE BY WITH LOVE BY IGN0R3DH4X0R");
            $table->longText('protect_notice');
            $table->longText('share_text')->nullable();
            $table->string('admin_contact')->default('https://t.me/ignoredhaxor');
            $table->string('youtube_link')->default('https://t.me/ignoredhaxor');
            $table->string('facebook_link')->default('https://t.me/ignoredhaxor');
            $table->boolean('did_run_before');
            $table->boolean('public_bombing_page')->default(true);
            $table->string('use_multi_threads')->default(true);
            $table->timestamps();
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
