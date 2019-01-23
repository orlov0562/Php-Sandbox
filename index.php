<!doctype html>
<html>
<head>
    <title>Sandbox</title>
    <style>
        *, *:before, *:after {
            -moz-box-sizing: border-box;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
        }

        html, body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            background-color:#31363b;
            color: #dbdcde;
            font-family:Consolas,"Liberation Mono",Menlo,Courier,monospace;
            font-size:12px;
            display:flex;
            flex-direction:column;

        }

        header {
            display:flex;
            background-color: black;
            padding:5px;
        }

        header > div {flex:1; display:flex; align-items:center;}
        header .about {justify-content:flex-end;}

        a {color:yellow; text-decoration:none;}
        a:hover {text-decoration:underline;}
        a.btn {
            background-color: #41464b;
            border: 1px solid #51565b;
            display:inline-block;
            min-width:100px;
            padding:5px 10px;
        }

        a.btn:hover {
            background-color: #51565b;
            border-bottom:1px solid white;
            text-decoration:none;
            cursor:pointer;
        }

       main {
            flex:1;
            display:flex;
        }

        #editor {
            flex:1;
        }

        iframe {
            flex:1;
            background-color: #41464b;
            border: 1px solid #51565b;
        }

        iframe body {color:white;}

    </style>

    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>
<body>

    <header>
        <div class="controls">
            <a class="btn" id="btn-run">Save & Run (Ctrl+S)</a>
            &nbsp;
            <a class="btn" id="btn-reset">Reset</a>
            &nbsp;
            <a class="btn" id="btn-orm">+Idiorm</a>
            <input type="checkbox" id="autosave" checked> Autosave
        </div>
        <div class="about">
            <a href="" target="_blank">PHP SandBox</a>, 2017
        </div>
    </header>

    <main>
        <div id="editor"><?=htmlspecialchars(file_get_contents('sandbox.php'))?></div>
        <iframe id="preview" src="show.php"></iframe>
    </main>

    <script src="bower_components/jquery/dist/jquery.min.js"></script>
    <script src="bower_components/ace-builds/src-min/ace.js" type="text/javascript" charset="utf-8"></script>

    <script>
        var editor = ace.edit("editor");
        editor.setTheme("ace/theme/monokai");
        editor.getSession().setMode("ace/mode/php");

        var updatesLock = false;
        var updateTimer = null;

        function defered_update_preview(){
            if (typeof updateTimer != null) {
                clearInterval(updateTimer);
                updateTimer = null;
            }
            updateTimer = setTimeout(update_preview, 1500);
        }

        function update_preview(){
            if(updatesLock) return;
            updatesLock = true;
            $.ajax({
                url: 'update.php',
                method:'POST',
                data:{
                    'source': editor.getSession().getValue()
                },
                dataType: 'json'
            }).fail(function(jqXHR, textStatus, errorThrown){
                // error
            }).done(function(data, textStatus, jqXHR) {
                if (data.hasOwnProperty('status') && (data.status == 'ok')) {
                    $('#preview').get(0).contentWindow.location.reload();
                }
            }).always(function(jqXHR, textStatus){
                updatesLock = false;
            });
        }

        function append_orm_snippet(){

            let snippet = "include 'idiorm.php';\n"
                +"ORM::configure('mysql:host=localhost;dbname=sandbox');\n"
                +"ORM::configure('username', 'mysql');\n"
                +"ORM::configure('password', 'sandbox');\n"
                +"ORM::configure('driver_options', [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8']);\n\n"
            ;
            editor.getSession().insert({
                row: 1,
                column: 0
            },snippet);
        }

        function reset_editor(){
            if (!confirm('Reset editor?')) return false;
            editor.getSession().setValue("<"+"?php\n");
        }


        $('#btn-run').click(update_preview);

        $('#btn-reset').click(reset_editor);

        $('#btn-orm').click(append_orm_snippet);

        editor.getSession().on("change", function(){
            if ($('#autosave').is(':checked')) {
                defered_update_preview();
            }
        });

        $(window).keypress(function(event) {
            if (!(event.which == 115 && event.ctrlKey) && !(event.which == 19)) return true;
            // Ctrl+S pressed
            update_preview();
            event.preventDefault();
            return false;
        });

    </script>

</body>
</html>
