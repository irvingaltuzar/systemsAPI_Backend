<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmirhPersonalRequisition extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmirh_personal_requisition', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('type',100);
            $table->string('department',100);
            $table->string('level_position',100);
            $table->datetime('date');
            $table->datetime('date_validation_rh')->nullable();
            $table->datetime('date_received_rh')->nullable();
            $table->datetime('date_estimate_coverage')->nullable();
            $table->string('vacancy',100)->nullable();
            $table->string('personal_substitution',100)->nullable();
            $table->string('type_vacancy',100);
            $table->tinyInteger('num_vacancy');
            $table->string('time_travel',50)->nullable();
            $table->tinyInteger('days_travel')->nullable();
            $table->string('reason_replacement',100)->nullable();
            $table->string('temp_reason',100)->nullable();
            $table->tinyInteger('days_temp_reason')->nullable();
            $table->float('salary');
            $table->string('estimate',50)->nullable();
            $table->string('user',100);
            $table->unsignedBigInteger('email_domain_id')->nullable();;
            $table->foreign('email_domain_id')->references('id')->on('dmi_control_email_domain')->onDelete('cascade');
            $table->text('resources')->nullable();
            $table->string('software_aditional',200)->nullable();
            $table->string('personal_location',100)->nullable();
            $table->string('file',250)->nullable();
            $table->string('document',250)->nullable();
            $table->string('company_name',250)->nullable();
            $table->Integer('branch_code')->nullable();
            $table->string('status',100)->nullable();
            $table->Integer('status_recruitment_id')->nullable();;
            $table->foreign('status_recruitment_id')->references('id')->on('dmi_cat_status_recruitment')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dmirh_personal_requisition');
    }
}
