<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('crp')->unique();
            $table->string('email');
            $table->string('phone_number');
            $table->string('physical_address');
            $table->string('postal_address');
            $table->date('registration_date')->nullable();
            $table->integer('postal_code_id')->unsigned()->index();
            $table->foreign('postal_code_id')->references('id')->on('postal_codes');
            $table->boolean('paid')->default(0);
            $table->string('company_unique_id')->unique();
            $table->string('kra_pin')->nullable();
            $table->string('account')->nullable();
            $table->integer('relationship_manager_id')->nullable();
            $table->string('customerid')->nullable();
            $table->unsignedInteger('group_id')->nullable();
            $table->foreign('group_id')->references('id')->on('groups');
            $table->string('approval_status')->default('pending');
            $table->boolean('kyc_status')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
        $table->dropForeign(['postal_code_id']);
        });
        Schema::dropIfExists('companies');
    }
}
