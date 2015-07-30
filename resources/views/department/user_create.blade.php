<html>
<body>

<form method="post" action="{{route('postCreateUser')}}">

名称：<input type="text" name="name" value=""><br/>
账号：<input type="text" name="user_id" value=""><br/>
微信号：<input type="text" name="wx_id" value=""><br/>
性别：<select name="gender">
            <option value="1">男</option>
            <option value="2">女</option>
    </select>
    <br/>
    <br/>
父级：<select name="department_id">
        @foreach($departments as $d)
            <option value="{{$d->id}}">{{$d->name}}</option>
        @endforeach
    </select>
    <br/>
<input type="submit" value="确定">
</form>


</body>
</html>