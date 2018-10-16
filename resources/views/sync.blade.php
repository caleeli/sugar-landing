<script src="/js/jquery-3.1.1.min.js"></script>
<p>Fallados: {{$fallados}}</p>
<p>Válidados: {{$validados}}</p>
<p><button disabled id="boton" type="button" onclick="process();">PROCESAR</button></p>
<p><a href="{{$sqlUrl}}" >DESCARGAR SQL</a></p>
<textarea id="log" style="width: 100%" rows="12"></textarea>
<div id="log2" style="width: 100%" rows="12"></div>
<script>
    var gral = {!!json_encode($response)!!};
    var i = 0, l = gral.length;
    $('#log2').val('');
    function process()
    {
        $('#boton').prop('disabled', true);
        if (gral[i].verificacion) {
            $.ajax({
                method: 'get',
                url: 'process',
                data: {
                    d: JSON.stringify(gral[i])
                },
                dataType: 'json',
                success: function (res) {
                    $('#log').val($('#log').val() + '\u2713');
                    i++;
                    if (i<l) {
                        setTimeout(process, 0);
                    }
                },
                error: function (res) {
                    if (!$('#log2').html()) {
                        $('#log2').html(res.responseText);
                    }
                    $('#log').val($('#log').val() + '\u2715');
                    i++;
                    if (i<l) {
                        setTimeout(process, 0);
                    }
                }
            });
        } else {
            $('#log').val($('#log').val() + '~');
            i++;
            if (i<l) {
                setTimeout(process, 0);
            }
        }
    }
</script>