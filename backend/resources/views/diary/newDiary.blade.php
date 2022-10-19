@extends("layouts.main")
@section("title","ホーム")

@section('header')
@parent
@endsection
@section('content')
<div class="board-main">



    <div class="diary-main">
        <div>
            @component('components.diary.submitForm')
            @slot("db_method")
            {{route('CreateDiary')}}
            @slot("original_id")
            @endslot
            @endslot
            @slot("original_date")
            @endslot
            @slot("original_title")
            @endslot

            @slot("original_content")
            @endslot
            @endcomponent
        </div>
    </div>

</div>

@endsection
