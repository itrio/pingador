<html>
<head>
<link rel="stylesheet" href="vendor/materialize/css/materialize.min.css">
</head>
<body>

<nav>
    <div class="nav-wrapper blue">
        <a href="#" class="brand-logo center">Pingador</a>
    </div>
</nav>

<div class="container">
    <div class="row">
        <form class="col s12">
            <div class="row">
                <div class="col s12">
                    <label for="textarea1">Insira os IPs que deseja pingar. Coloque um IP em cada linha.<br></label>
                    <textarea id="txtIPs" rows="15"></textarea>
                </div>
                <div class="col s12">
                    <a class="waves-effect waves-light btn blue" style="width: 100%; margin-top: 10px;" onclick="submitIPs();">PINGAR!</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="divider"></div>

<div class="" id="divTabela">
    <table class="striped">
        <thead>
        <tr>
            <th>Nº</th>
            <th>IP</th>
            <th>Status</th>
            <th>Tempo</th>
            <th><a href='#!' title='Atualizar tudo' onclick='refreshAll();'><img src='ico/refresh.svg' width='22'></a></th>
            <th><a href='#!' title='Limpar tudo' onclick='clearAll();'><img src='ico/clear.svg' width='22'></a></th>
        </tr>
        </thead>

        <tbody id="linhasIPs">

        </tbody>
    </table>
</div>

</body>

<script type="text/javascript" src="vendor/jquery/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="vendor/materialize/js/materialize.min.js"></script>

<script type="text/javascript">
    var preloader = "<div class=\"preloader-wrapper small active\" style='max-width: 15px; max-height: 15px;'>\n" +
        "    <div class=\"spinner-layer spinner-green-only\">\n" +
        "      <div class=\"circle-clipper left\">\n" +
        "        <div class=\"circle\"></div>\n" +
        "      </div><div class=\"gap-patch\">\n" +
        "        <div class=\"circle\"></div>\n" +
        "      </div><div class=\"circle-clipper right\">\n" +
        "        <div class=\"circle\"></div>\n" +
        "      </div>\n" +
        "    </div>\n" +
        "  </div>";
    
    $(document).ready(function() {
        insereIP("<?php print $_SERVER["REMOTE_ADDR"] == "::1" ? "192.168.90.11" : $_SERVER["REMOTE_ADDR"]; ?>");

        setInterval(function () {
            scanLines();
        }, 1000);
    });
    
    function submitIPs() {
        var IPs = $("#txtIPs").val().split(/\r?\n/);

        jQuery.each(IPs, function (indice, elemento) {
            //Valida o IP e insere
            if(elemento != "" && elemento != " "){
                insereIP(elemento);
            }
        });

        //Limpa a textarea
        $("#txtIPs").val("");
    }
    
    function insereIP(IP) {
        qntLinhas = $("#linhasIPs > tr").length + 1;
        var linha = "<tr>\n" +
            "            <td>"+qntLinhas+"</td>\n" +
            "            <td>"+IP+"</td>\n" +
            "            <td>aguardando</td>\n" +
            "            <td>"+preloader+"</td>\n" +
            "            <td><a href='#!' title='Pingar novamente' onclick='refreshPing(this.parentNode.parentNode);'><img src='ico/refresh.svg' width='22'></a></td>\n" +
            "            <td><a href='#!' title='Excluir linha' onclick='clearLine(this.parentNode.parentNode);'><img src='ico/clear.svg' width='22'></a></td>\n" +
            "        </tr>";
        $("#linhasIPs").append(linha);
    }
    
    function scanLines() {
        var linhas = $("#linhasIPs > tr");

        jQuery.each(linhas, function (indice, elemento) {
            var colunas = elemento.children;

            if($(colunas[2]).html() == "aguardando"){
                executaPing(colunas);
            }
        });
    }
    
    function executaPing(colunas) {
        var ip = $(colunas[1]).html();

        $.ajax({
            'url' : 'api/ping.php',
            type : 'get',
            crossDomain : true,
            data : {
                'ip' : ip,
            },
            beforeSend : function(){
                $(colunas[2]).html("conectando...");
            }
        })
            .done(function(msg){
                if(msg.status == 'on'){
                    $(colunas[2]).html("<span class='green-text'><b>CONECTADO</b></span>");
                    $(colunas[3]).html(""+msg.tempo+"");
                }
                else if(msg.status == 'off'){
                    $(colunas[2]).html("<span class='red-text'><b>NÃO CONECTADO</b></span>");
                    $(colunas[3]).html("-");
                }
                else{
                    $(colunas[2]).html("aguardando");
                }
            })
            .fail(function(jqXHR, textStatus, msg){
                $(colunas[2]).html("aguardando");
            });
    }

    function refreshPing(elemento) {
        executaPing(elemento.children);
    }
    
    function refreshAll() {
        var linhas = $("#linhasIPs > tr");

        jQuery.each(linhas, function (indice, elemento) {
            var colunas = elemento.children;

            $(colunas[2]).html("aguardando");
            $(colunas[3]).html(preloader);

        });
    }
    
    function clearAll() {
        $("#linhasIPs").html("");
    }
    
    function clearLine(elemento) {
        
    }
</script>

</html>