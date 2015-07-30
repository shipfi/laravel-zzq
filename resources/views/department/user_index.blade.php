<html>
<body>

<table border="1" width="100%">
    <tr>
        <td>userID</td>
        <td>name</td>

    </tr>
    @foreach($users as $user)
        <tr>
            <td>{{$user->userid}}</td>
            <td>{{$user->name}}</td>
        </tr>
    @endforeach
</table>

</body>
</html>