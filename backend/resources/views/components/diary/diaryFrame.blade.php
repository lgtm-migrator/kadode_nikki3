
<div class="diary_dashboard m-2">
    
    <section>
        <p class=" text-center text-xl">{{$date}}</p>
        <p class=" text-center text-2xl">{{$title}}</p>
        <div class=" text-xl flex items-center justify-center" style="height: 54px"><p>きもち:{{$feel}}</p></div>
    </section>
    <article class="p-2 text-sm ">
        <p class="overflow-y-hidden">{{$content}}</p>
    </article>
<a href="{{url('/edit')}}/{{$uuid}}"><span class="material-icons-outlined">edit</span>編集</a>

</div>