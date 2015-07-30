<html>
<body>

<form method="post" action="{{route('postDepartmentCreate')}}">

名称：<input type="text" name="name" value="">
    <br/>
父级：<select name="parent">
        @foreach($departments as $d)
            <option value="{{$d->id}}">{{$d->name}}</option>
        @endforeach
    </select>
    <br/>
<input type="submit" value="确定">
</form>


</body>
</html>