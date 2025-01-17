<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNlpPackageUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nlp_package_users', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('所有ユーザー');
            $table->unsignedBigInteger('package_id')->comment('パッケージ');
            $table->timestamps();

            // 他テーブルとの関連付け
            $table->foreign('package_id')
                ->references('id')
                ->on('nlp_package_names')
                ->onDelete('cascade') // パッケージ消えたらこのテーブルも消す
;

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade') // ユーザー消えたらこのテーブルも消す
;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nlp_package_users');
    }
}
