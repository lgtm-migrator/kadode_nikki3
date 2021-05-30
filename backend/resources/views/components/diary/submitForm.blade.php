<div>
<p id="diaryDate" class="text-xl text-center">
    本日の日付
</p>

<form class="flex justify-center flex-wrap flex-col "method="POST" action="/diary/edit">
    @csrf
    <input style="height:32px" class="mx-auto w-2/3" type="text" name="title" >
    <div class="flex justify-center items-center">
        <p class="text-xl mr-4">きもち:</p>
    <select style="width:8em;"class="text-sm my-2" name="feel"size="1" >
        <option value="1">1(悪い)</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5" selected>5(ふつう)</option>
        <option value="6">6</option>
        <option value="7">7</option>
        <option value="8">8</option>
        <option value="9">9</option>
        <option value="10">10(良い)</option>
    </select>
    </div>
    <textarea class="p-4 w-full diary-content-edit" type="text" name="content" ></textarea>
    
    <input type="submit" value="日記を書き込む">
</form>
@error('title')
<div class="text-red-700">{{ $message }}</div>
@enderror
</div>