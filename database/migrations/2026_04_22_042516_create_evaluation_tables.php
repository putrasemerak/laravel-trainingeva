<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('te_0001')) {
            Schema::create('te_0001', function (Blueprint $table) {
                $table->id('teid');
                $table->string('refnum')->nullable();
                $table->string('fullname')->nullable();
                $table->string('empno')->nullable();
                $table->string('div')->nullable();
                $table->string('dept')->nullable();
                $table->string('sec')->nullable();
                $table->string('subsec')->nullable();
                $table->string('unit')->nullable();
                $table->string('tcategory')->nullable();
                $table->string('topic')->nullable();
                $table->date('entryin')->nullable();
                $table->date('entryout')->nullable();
                $table->string('tduration')->nullable();
                $table->string('radiocom')->nullable(); // Methodology
                $table->string('tresult')->nullable();
                $table->string('range')->nullable();
                $table->string('range2')->nullable();
                $table->string('range3')->nullable();
                $table->string('range4')->nullable();
                $table->string('range5')->nullable();
                $table->string('range6')->nullable();
                $table->text('evaluator')->nullable();
                $table->text('remarkhr')->nullable();
                $table->decimal('totaleffective', 5, 2)->nullable();
                $table->string('status')->nullable();
                $table->date('dtissued')->nullable();
                $table->date('duedate')->nullable();
                $table->string('ename')->nullable();
                $table->string('eemp')->nullable();
                $table->string('eemail')->nullable();
                $table->date('dtevaluate')->nullable();
            });
        }

        if (!Schema::hasTable('te_audit_trail')) {
            Schema::create('te_audit_trail', function (Blueprint $table) {
                $table->id('ID');
                $table->string('USER_ID')->nullable();
                $table->string('USER_NAME')->nullable();
                $table->string('ACTION_TYPE')->nullable();
                $table->string('PAGE_NAME')->nullable();
                $table->text('DESCRIPTION')->nullable();
                $table->string('IP_ADDRESS')->nullable();
                $table->date('ADDDATE')->nullable();
                $table->time('ADDTIME')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('te_0001');
        Schema::dropIfExists('te_audit_trail');
    }
};
