<?session_start(); error_reporting(0);?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>AJAX Загрузка файла</title>
<link rel="stylesheet" type="text/css" href="style.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>
<script type="text/javascript" src="ajaxupload.3.5.js"></script>
<script type="text/javascript">
    $(function(){
        var btnUpload=$('#upl_button');
        var status=$('#upl_status');
        new AjaxUpload(btnUpload, {
            action: 'uploader.php',
            name: 'upl_file',
            data: {sid : '<?=session_id()?>'},
            onSubmit: function(file, ext){
                status.html('');
                if (! (ext && /^(jpg|jpeg|png|bmp|gif|ico)$/.test(ext))){
                    status.html('Допустимые форматы:'+"<br />"+'jpg / jpeg / bmp / png / gif / ico');
                    return false;
                }
                $('#file').fadeOut(0);
                $('#loading').attr('src', 'loading.gif').fadeIn(0);
            },
            onComplete: function(file, response){
                status.html('');
                $('#file').html('');
                var arr_resp = response.split("#%#");
                if(arr_resp[0]==="true"){
                    $('#loading').fadeOut(0);
                    $('#file').attr('src', 'files/' + arr_resp[1]).fadeIn(0);
                }else{
                    status.html(response);
                }
            }
        });
    });
</script>
</head>
<body>
    <div id="add_file">
        <div id="upl_button">загрузить файл</div><hr />
        <img id="loading" src="loading.gif" height="28" style="display: none;" />
        <img id="file" src="" height="150" style="display: none;" />
        <div id="upl_status"></div>
    </div>
</body>
</html>