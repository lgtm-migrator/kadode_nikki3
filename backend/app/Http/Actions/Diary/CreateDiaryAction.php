<?php

declare(strict_types=1);

namespace App\Http\Actions\Diary;

use App\Http\Controllers\Controller;
use App\Http\Requests\Diary\CreateDiaryRequest;
use App\Models\Diary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

final class CreateDiaryAction extends Controller
{
    public function __invoke(CreateDiaryRequest $request): Redirector|RedirectResponse
    {
        $request->date ??= Carbon::today()->format('y-m-d');

        $form = [
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
            'date' => $request->date,
            'uuid' => Str::uuid(),
        ];

        Diary::create($form);
        // ここで統計用テーブル作成するのもありだが、後方互換を保つためにはできないたいめ、生成時になかったら作る方式を採用している
        return redirect(route('ShowHome'));
    }
}
