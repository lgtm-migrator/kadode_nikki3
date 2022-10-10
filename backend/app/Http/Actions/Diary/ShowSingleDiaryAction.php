<?php

declare(strict_types=1);

namespace App\Http\Actions\Diary;

use App\Http\Controllers\Controller;
use App\Models\Diary;
use App\UseCases\Diary\GetDiariesDateNextToDiaryById;
use App\UseCases\Diary\GetDiaryByDate;
use App\UseCases\Diary\GetDiaryByUuid;
use App\UseCases\Diary\ShapeContentWithNlp;
use App\UseCases\Diary\ShapeStatisticFromDiaries;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use App\Enums\DiaryStatisticStatus;

final class ShowSingleDiaryAction extends Controller
{
    public function __construct(
        private ShapeStatisticFromDiaries $shapeStatisticFromDiaries,
        private ShapeContentWithNlp $shapeContentWithNlp,
        private GetDiaryByUuid $getDiaryByUuid,
        private GetDiaryByDate $getDiaryByDate,
        private GetDiariesDateNextToDiaryById $getDiariesDateNextToDiaryById,
    ) {
    }

    public function __invoke($uuid): View|RedirectResponse
    {
        $diary = $this->getDiaryByUuid->invoke($uuid);
        if ($diary === []) {
            //日記無かったらリダイレクトさせる
            return redirect(route('ShowHome'));
        }
        $dateAndUuidBA = $this->getDiariesDateNextToDiaryById->invoke($diary['date']);


        /**
         * @todo 一時的に追加している日記加工処理
         */
        $resembleDiaries = "";
        $contentWithNlp = "";
        if ($diary['statisticStatus'] === DiaryStatisticStatus::existCorrectly) {

            /**
             * NLP付き表示を生成する(固有表現へのハイライトなど)
             * @todo 機能を改善して復活させる
             */
            // $contentWithNlp = $this->shapeContentWithNlp->invoke($diary);


            /**
             * modelでの型定義とwhere("hoge->fuga")でjsonの中身引っ張ってこれる
             * が、diariesのjsonが[{},{}]のようになっているのでvalueが直接取れない。よってrawで$[0]とかして取得
             * $[*]でも取れるが、今回はその日記で一番多く登場した人物とすることで関連度を向上させている
             */

            /**
             * 日記内で一番多く登場した人物がかぶる日記をランダムに3つ取得
             */
            // \Log::debug($diary->special_people[0]['name']);//一番の人の名前抽出
            //where('id', '<>',$diary->id)で自分自身を除く
            if (!empty($diary['special_people'])) {
                $resembleDiariesRaw = Diary::where('id', '<>', $diary['id'])->where(DB::raw('json_extract(`special_people`, "$[0].name")'), $diary['special_people'][0]['name'])->inRandomOrder()->limit(3)->get();
                $resembleDiaries = $this->shapeStatisticFromDiaries->invoke($resembleDiariesRaw);
            }
        }
        return view('diary/edit', ['diary' => $diary, 'contentWithNlp' => $contentWithNlp, 'dateAndUuidBA' => $dateAndUuidBA,  'resembleDiaries' => $resembleDiaries]);
    }
}