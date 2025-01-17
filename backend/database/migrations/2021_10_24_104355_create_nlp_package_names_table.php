<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNlpPackageNamesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nlp_package_names', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('genre_id')->comment('パッケージのジャンルID');
            $table->unsignedBigInteger('user_id')->comment('作成ユーザーID');
            $table->string('name')->comment('パッケージ名');
            $table->string('is_publish')->comment('公開設定');
            $table->string('description')->nullable()->comment('説明');
            $table->timestamps();

            // 他テーブルとの関連付け
            $table->foreign('genre_id')
                ->references('id')
                ->on('nlp_package_genres')
                ->onDelete('cascade') // ジャンル消えたらパッケージも消す
;
            // 他テーブルとの関連付け
            $table->foreign('user_id')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nlp_package_names');
    }
}
