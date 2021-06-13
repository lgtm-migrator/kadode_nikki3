
@extends("layouts.main")
@section("title","検索")

@section('header')
@parent
@endsection
@section('content')
<div class="board-main">
@empty($keyword)
<h1 class="text-center text-3xl my-4">検索ページ</h1>
<div style="min-height:100vh">
    <form class="move-label-wrapper flex"method="POST" action="/search">
        @csrf
        <p class="text-xl">キーワード検索</p>
        <div>
            <input  autocomplete="off"class="search-keyword" type="search" name="keyword" placeholder="キーワード(2~20字)">
        </div>
        <input type="submit" value="検索">
        
    </form>
</div>
@else
    
<h1 class="text-center text-3xl my-4">「{{$keyword}}」の検索結果 <span class="text-sm">[{{$counter}}件(最大200件)]</span><span class="text-xs">[クエリ時間:{{$queryTime}}ミリ秒]</span></h1>
<div class="flex w-full m-4 justify-center flex-wrap" >
    @empty($diaries)
        <h3 class="text-center text-3xl my-20">「{{$keyword}}」を含む日記はありません！</h3>
    @else
        @foreach($diaries as $diary )
            @component('components.diary.diaryFrame')
                @slot("uuid")
                {{$diary->uuid}}
                @endslot
                @slot("title")
                {{$diary->title}}
                @endslot
                @slot("content")
                {!! $diary->content !!}
                @endslot
                @slot("date")
                {{$diary->date}}
                @endslot
                @slot("feel")
                {{$diary->feel}}
                @endslot
            @endcomponent
        @endforeach
    @endempty
</div>

@endempty
</div>
      
@endsection
 