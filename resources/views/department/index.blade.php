<html>
<body>

<table border="1" width="100%">
    <tr>
        <td>#</td>
        <td>排序</td>
        <td>名称</td>
        <td>父级</td>
        <td>操作</td>


    </tr>
    @foreach($departments as $d)
        <tr>
            <td>{{$d->id}}</td>
            <td>{{$d->order}}</td>
            <td>{{$d->name}}</td>
            <td>{{$d->parentid}}</td>
            <td><a href="{{route('departmentlist',[$d->id])}}">成员列表</a></td>
        </tr>
    @endforeach
</table>

</body>
</html>