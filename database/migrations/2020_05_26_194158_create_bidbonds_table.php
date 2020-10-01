<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBidbondsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bidbonds', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tender_no')->index();
            $table->text('purpose');
            $table->string('addressee')->nullable();
            $table->date('effective_date');
            $table->date('expiry_date');
            $table->dateTime('expired_at')->nullable();
            $table->date('deal_date')->nullable();
            $table->integer('amount');
            $table->string('currency')->default('KES');
            $table->string('period');
            $table->string('company_id')->nullable();
            $table->unsignedInteger('counter_party_id');
            $table->string('reference')->nullable();
            $table->decimal('charge', 10, 2);
            $table->string('template_secret');
            $table->string('secret')->unique();
            $table->boolean('paid')->default(false);
            $table->string('agent_id')->nullable();
            $table->string('created_by');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('template_secret')->references('secret')->on('bid_bond_templates');
            $table->foreign('counter_party_id')->references('id')->on('counter_parties');
<<<<<<< HEAD
         // $table->foreign('company_id')->references('company_unique_id')->on('companies');
=======
            $table->foreign('company_id')->references('id')->on('bidbond_companies');
>>>>>>> 9b8ff0a3567816648c7981d69ae37a46521c0a2d
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bidbonds');
    }
}
