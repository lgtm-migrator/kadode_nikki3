<?php

declare(strict_types=1);

namespace App\Http\Actions;

use App\Http\Controllers\Controller;
use App\Models\NERLabel;
use App\Models\NlpPackageGenre;
use App\Models\NlpPackageName;
use App\Models\PackageNER;
use App\UseCases\NERLabel\GetAllNERLabelInOptionTabFormat;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

final class ShowAdminIndividualPackageAction extends Controller
{
    public function __construct(
        private GetAllNERLabelInOptionTabFormat $getAllNERLabelInOptionTabFormat
    ) {
    }

    public function __invoke(int $packageId): View|Factory
    {
        // パッケージ表示(最近更新のあったものから取り出す)
        $packageObj = NlpPackageName::withoutGlobalScopes()->where('id', $packageId)->first();
        // パッケージジャンル表示
        $NlpPackageGenre = NlpPackageGenre::get();
        // 固有表現ルールの中身取得

        if (1 === $packageObj->genre_id) {
            // 固有表現パッケージだったら
            $packageObj->packageNER = PackageNER::where('package_id', $packageObj->id)->orderBy('updated_at', 'desc')->get();
        }

        // 固有表現ラベル取得
        $NERLabelsInOptionTabFormat = $this->getAllNERLabelInOptionTabFormat->invoke(NERLabel::all()->toArray());

        return view('admin/packages/individual', ['packageObj' => $packageObj, 'NlpPackageGenre' => $NlpPackageGenre, 'NERLabelsInOptionTabFormat' => $NERLabelsInOptionTabFormat]);
    }
}
