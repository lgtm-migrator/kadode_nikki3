<?php

declare(strict_types=1);

namespace App\Http\Actions;

use App\Http\Controllers\Controller;
use App\Models\Diary;
use App\Models\Statistic;
use App\Models\StatisticPerMonth;
use App\Models\User;
use App\Models\UserReadNotification;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

final class ShowAdminHomeAction extends Controller
{
    /**
     * @todo ここは完全に壊れているが、うすゆきしか使用しない上、200で動くので、リプレースで消えるまで放置
     */
    public function __invoke(): View|Factory
    {
        $users = User::all(['id', 'created_at', 'updated_at', 'email_verified_at', 'user_rank_id', 'user_role_id', 'appearance_id'])->toArray();

        /**
         * @todo こことんでもない量のSQLが走ってしまうので要調整
         * @todo ただし、管理者ページは一般ユーザーアクセスしないので放置でもよいかも
         * foreachの中で&$userして参照渡ししないと代入されない←これまでそんなこと無かったのになんで？？？
         */
        foreach ($users as &$user) {
            $statistic = ''; // 怖いので初期化
            // 日記数統計から取ってきていないのは統計データーがそもそも無いが、日記はある可能性があるため
            $user['diary_count'] = Diary::withoutGlobalScopes()->where('user_id', $user['id'])->count() ?? 0;
            $user['latest_diary'] = Diary::withoutGlobalScopes()->where('user_id', $user['id'])->orderBy('date', 'desc')->first(['date'])->date ?? 'なし';
            $user['oldest_diary'] = Diary::withoutGlobalScopes()->where('user_id', $user['id'])->orderBy('date', 'asc')->first(['date'])->date ?? 'なし';
            $user['statistics_per_month_count'] = StatisticPerMonth::withoutGlobalScopes()->where('user_id', $user['id'])->count() ?? 0;
            $user['diary_average'] = '未測定'; // 無い可能性もあるので0に
            $statistic = Statistic::withoutGlobalScopes()->where('user_id', $user['id'])->first(['statistic_progress', 'total_words', 'total_diaries']);
            if (!empty($statistic)) {
                // 統計あっても生成中の可能性があるので
                if (100 === $statistic->statistic_progress) {
                    $user['diary_average'] = round($statistic->total_words / $statistic->total_diaries);
                } else {
                    $user['diary_average'] = '生成中'; // 無い可能性もあるので0に
                }
            }

            /** @todo withで取るべきだが、後にこのメソッドごと廃止予定なので一旦これで放置 */
            $userReadNotification = UserReadNotification::withoutGlobalScopes()->where('user_id', $user['id'])->first();
            if (null === $userReadNotification) {
                $user['is_showed_update_user_rank'] = '☐';
                $user['is_showed_update_system_info'] = '☐';
                $user['is_showed_service_info'] = '☐';
            } else {
                $user['is_showed_update_user_rank'] = $userReadNotification->is_showed_update_user_rank ? '☑' : '☐';
                $user['is_showed_update_system_info'] = $userReadNotification->is_showed_update_system_info ? '☑' : '☐';
                $user['is_showed_service_info'] = $userReadNotification->is_showed_service_info ? '☑' : '☐';
            }
            $user = $user;
        }
        // 参照渡ししちゃったのでちゃんと消しとく
        unset($user);

        return view('admin/homeAdmin', ['users' => $users]);
    }
}
