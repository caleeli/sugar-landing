<table>
  @foreach ($response as $res)
  <tr style="background-color: {{$res['verificacion'] ? 'lightgreen' : 'salmon'}}">
    <td colspan="2">{!! $res['verificacion'] ? 'ok' : $res['verificacionMsg'] !!}</td>
  </tr>
  <tr style="background-color: {{$res['verificacion'] ? 'lightgreen' : 'salmon'}}">
    <td><pre>{{json_encode($res['original'],JSON_PRETTY_PRINT)}}</pre></td>
    <td>id: <b>{{$res['id']}}</b><br/><pre>{{json_encode($res['datos'],JSON_PRETTY_PRINT)}}</pre></td>
  </tr>
  @endforeach
</table>
