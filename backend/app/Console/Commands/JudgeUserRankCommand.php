<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserReadNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class JudgeUserRankCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:judgeUserRank';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ユーザーランクの更新審査を行うコマンド';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * ユーザーランクを上げるメソッド.
     */
    public function updateUserRank(int $user_id, int $currentUserRank): void
    {
        // ユーザーランクアップ対象の場合の処理
        // ユーザー通知のフラグをオン、idを上げる、日付更新
        // @todo 2回クエリ走っているのでjoinで一緒にupdateできたらもっと早くなりそう
        User::where('id', $user_id)->update(['user_rank_id' => $currentUserRank + 1, 'user_rank_updated_at' => Carbon::now()]);
        /*
         * 新規のユーザーはテーブルが存在しないのでここで作成する(1日に1回実行されるので自然とテーブル作れるのでここだけupdateOrCreateをしている)
         * @todo user作ったときに一緒にテーブル作れると一番良いのだが、ライブラリの中触らないといけない雰囲気なので断念
         */
        UserReadNotification::updateOrCreate(['user_id' => $user_id], ['is_showed_update_user_rank' => 0]);
        echo 'id:'.$user_id.'/ランクアップ'.$currentUserRank.'→'.($currentUserRank + 1)."\n";
    }

    /**
     * $user_idの$tableの数をカウントする.
     *
     * @todo これ汎用的なのでここに置く必要ある？　と思ったが$tableそのまま出しててSQLインジェクション怖いの
     */
    public function countTables(int $user_id, string $table): int
    {
        /**
         * なぜかuser以外のeloquent取れないため、rawで取得
         * $tableにはユーザー入力値**絶対**入らないのでSQLインジェクションは起きない.
         */
        $counter = DB::select(DB::raw('select count(*) as counter from '.$table.' where user_id='.$user_id));

        return $counter[0]->counter;
    }

    public function handle(): int
    {
        $users = User::get();

        // User以外の情報がeloquentで引っ張れないので注意！！

        foreach ($users as $user) {
            /** @var int */
            $rank_id = $user->user_rank_id;

            /** @var int */
            $user_id = $user->id;

            switch ($rank_id) {
                case 1:
                    if ($this->countTables($user_id, 'diaries') >= 1) {
                        $this->updateUserRank($user_id, $rank_id);
                    }

                    break;

                case 2:
                    if ($this->countTables($user_id, 'diaries') >= 15) {
                        $this->updateUserRank($user_id, $rank_id);
                    }

                    break;

                case 3:
                    if ($user->created_at < Carbon::now()->subDays(30)) {
                        $this->updateUserRank($user_id, $rank_id);
                    }

                    break;

                case 4:
                    if ($this->countTables($user_id, 'statistics') >= 1) {
                        $this->updateUserRank($user_id, $rank_id);
                    }

                    break;

                case 5:
                    if ($this->countTables($user_id, 'diaries') >= 30) {
                        $this->updateUserRank($user_id, $rank_id);
                    }

                    break;

                case 6:
                    // 日記テーブル内に文字数用のカラムあるが、あれは統計処理走らせた後じゃないと存在しないため、ここでは別で計測してる
                    $diaries = DB::select(DB::raw('select content from diaries where user_id='.$user_id));
                    $counter = 0;
                    foreach ($diaries as $diary) {
                        $counter += mb_strlen($diary->content);
                    }
                    if ($counter >= 15000) {
                        $this->updateUserRank($user_id, $rank_id);
                    }

                    break;
            }
        }

        return Command::SUCCESS;
    }
}
