<html>
<head>
    <!-- Importing Materialize CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    
    <link rel="shortcut icon" href="ico/ico.png" id="favicon" />
    <title>Pingador - Utilitário ICMP</title>

    <style type="text/css">
        .divRecolhida{
            padding-right: 300px;
        }
    </style>
</head>
<body>

<div id="divPrincipal">
    <div class="navbar-fixed" id="fixadorNav">
        <nav>
            <div class="nav-wrapper blue">
                <a href="#" class="brand-logo center">Pingador</a>
                <ul id="nav-mobile" class="right">
                    <li><a href="#!" title="Configurações" onclick='btnConfigClick();' style="height: 100%;"><img src="ico/settings-white.png" width="30" style="margin-top: 18px;"></a></li>
                    <li><a href="#!" title="Baixar relatório" onclick="btnDownloadClick();" style="height: 100%;"><img src="ico/file-download-white.png" width="30" style="margin-top: 18px;"></a></li>
                </ul>
            </div>
        </nav>
    </div>

    <div class="container">
        <div class="row">
            <div class="col s12">
                <label for="textarea1">Insira os IPs ou domínios para os quais deseja pingar. Coloque um IP em cada linha.<br></label>
                <textarea id="txtIPs" rows="15"></textarea>
            </div>
            <div class="col s12">
                <a class="waves-effect waves-light btn blue" style="width: 100%; margin-top: 10px;" onclick="submitIPs();">PINGAR!</a>
            </div>
        </div>
    </div>

    <div class="divider"></div>

    <div id="divTabela">
        <table class="striped" id="tableHosts" style="width: 100%;">
            <thead>
            <tr>
                <th>Nº</th>
                <th>IP</th>
                <th>Status</th>
                <th>Tempo</th>
                <th>Tempo</th>
                <th><a href='#!' title='Atualizar tudo' onclick='refreshAll();'><img src='ico/refresh.svg' width='22'></a></th>
                <th><a href='#!' title='Limpar tudo' onclick='clearAll();'><img src='ico/clear.svg' width='22'></a></th>
            </tr>
            </thead>

            <tbody id="linhasIPs">

            </tbody>
        </table>
        <br><br>
    </div>
</div>

<ul id="slide-configs" class="sidenav sidenav-fixed" style="display: none;">
    <li><div class="user-view">
            <center><img src="ico/settings.svg" width="50"><br><b>CONFIGURAÇÕES</b></center>
        </div></li>
    <li><div class="divider"></div></li>
    <li class="container">
        <div class="input-field col s12 center-align">
            <small style="margin-bottom: -25px; padding: 0px; line-height: 5px;">PERMITIR LINHAS DUPLICADAS:</small>
            <div class="switch">
                <label>
                    NÃO
                    <input type="checkbox" id="checkPermiteDuplicadas" onchange="atualizaPermiteDuplicadas();">
                    <span class="lever"></span>
                    SIM
                </label>
            </div>
        </div>
    </li>
    <li><div class="divider"></div></li>
    <li class="container">
        <div class="input-field col s12 center-align">
            <small style="margin-bottom: -25px; padding: 0px; line-height: 15px;">NÚMERO MÁXIMO DE CONEXÕES SIMULTÂNEAS:</small>
            <input id="limitConex" type="number" min="1" max="100" value="30" onchange="atualizaLimitConn();" class="center-align">
        </div>
    </li>
    <li><div class="divider"></div></li>
    <li class="container">
        <div class="input-field col s12 center-align">
            <small style="margin-bottom: -25px; padding: 0px; line-height: 15px;">NOME DO ARQUIVO DE EXPORTAÇÃO:</small>
            <input id="nomeExport" type="text" value="testeIPs" onchange="atualizaFilename();" class="center-align">
        </div>
    </li>
    <li><div class="divider"></div></li>
    <li class="container">
        <div class="input-field col s12 center-align">
            <small style="margin-bottom: -25px; padding: 0px; line-height: 15px;">FORMATO DE EXPORTAÇÃO:</small>
            <div class="input-field">
                <select class="browser-default center" id="selectFiletype" name="selectFiletype" onchange="atualizaFiletype();">
                    <option id="optTXT" value=".txt" selected>.txt</option>
                    <option id="optXLSX" value=".xlsx">.xlsx</option>
                    <option id="optPDF" value=".pdf" disabled title="Ainda não implementado">.pdf</option>
                </select>
            </div>
        </div>
    </li>
    <li><div class="divider"></div></li>

</ul>

<!--Formulário Oculto-->
<form id="formExport" name="formExport" method="post" action="export.php">
    <input type="hidden" id="txtLinhas" name="txtLinhas">
    <input type="hidden" id="filetype" name="filetype" value=".txt">
    <input type="hidden" id="filename" name="filename" value="testeIPs">
</form>

</body>

<!-- Importing jQuery -->
<script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Importing DataTables -->
<script type="text/javascript" src="//cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

<!-- Importing Materialize scripts -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>

<!-- Importing Pingador scripts -->
<script type="text/javascript" src="js/cookies.js"></script>
<script type="text/javascript" src="js/CIDR.js"></script>

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

    //Conexões sendo realizadas
    var conexoesSimultaneas = 0;

    //Instância da Tabela
    var tabelaHosts;
    
    $(document).ready(function() {
        $('.sidenav').sidenav({edge:'right'});
        $('select').formSelect();
        iniciaDataTables();
        insereIP("<?php print $_SERVER["REMOTE_ADDR"] == "::1" ? "192.168.90.11" : $_SERVER["REMOTE_ADDR"]; ?>");

        setInterval(function () {
            scanLines();
        }, 1000);
    });

    function iniciaDataTables() {
        tabelaHosts = $('#tableHosts').DataTable({
            "language":  {
                "sEmptyTable": "<center>Nenhum host listado</center>",
                "sInfo": "<small class='right'>mostrando de _START_ até _END_ de _TOTAL_ hosts</small>",
                "sInfoEmpty": "<small class='right'>mostrando 0 até 0 de 0 hosts</small>",
                "sInfoFiltered": "(Filtrados de _MAX_ hosts)",
                "sInfoPostFix": "",
                "sInfoThousands": ".",
                "sLengthMenu": "_MENU_ resultados por página",
                "sLoadingRecords": "Carregando...",
                "sProcessing": "Processando...",
                "sZeroRecords": "Nenhum host listado",
                "sSearch": "Pesquisar",
                "oPaginate": {
                    "sNext": "Próximo",
                    "sPrevious": "Anterior",
                    "sFirst": "Primeiro",
                    "sLast": "Último"
                },
                "oAria": {
                    "sSortAscending": ": Ordenar colunas de forma ascendente",
                    "sSortDescending": ": Ordenar colunas de forma descendente"
                }
            },
            "lengthMenu": [[-1], ["todos"]],
            'sDom': 'lrti',
            'ordering': true,
            "lengthChange": false,
            "order": [[ 0, 'asc' ]],
            "columnDefs": [
                { "targets": [ 4 ], "visible": false, },
                { "orderSequence": [ "desc", "asc" ], "targets": [ 0, 1, 2, 3, 4, 5 ] }
            ],
        });
    }
    
    function submitIPs() {
        var IPs = $("#txtIPs").val().split(/\r?\n/);
        var IPsInseridos = 0, IPsDuplicados = 0;

        jQuery.each(IPs, function (indice, elemento) {
            //Valida o IP e insere
            if(elemento != "" && elemento != " "){
                var testaCIDR = new CIDR(elemento);
                if(testaCIDR.isValid()){
                    ips = testaCIDR.getIPs();

                    ips.forEach(ip => {
                        insereIP(ip);
                        IPsInseridos++;
                    });
                }
                else if(insereIP(elemento)){
                    IPsInseridos++;
                }
                else{
                    IPsDuplicados++;
                }
            }
        });

        //Limpa a textarea
        $("#txtIPs").val("");

        //Emite Toasts
        if(IPsInseridos == 1){
            M.toast({html: 'Linha inserida!'});
        }
        else if(IPsInseridos > 1){
            M.toast({html: IPsInseridos+' linhas inseridas!'});
        }

        if(IPsDuplicados == 1){
            M.toast({html: 'Linha duplicada!'});
        }
        else if(IPsDuplicados > 1){
            M.toast({html: IPsDuplicados+' linhas duplicadas!'});
        }

    }
    
    function insereIP(IP) {
        //Verifica se o IP já existe
        var ipDuplicado = false;
        var linhas = $("#linhasIPs > tr");
        jQuery.each(linhas, function (indice, elemento) {
            var colunas = elemento.children;
            if($(colunas[1]).html() == IP){
                ipDuplicado = true;
            }
        });

        if(!ipDuplicado || getPermiteDuplicadas() == "true"){
            qntLinhas = tabelaHosts.rows()[0].length + 1;
            tabelaHosts.row.add( [
                qntLinhas,
                IP,
                'aguardando',
                preloader,
                "",
                '<a href=\'#!\' title=\'Pingar novamente\' onclick=\'refreshPing(this.parentNode.parentNode);\'><img src=\'ico/refresh.svg\' width=\'22\'></a>',
                '<a href=\'#!\' title=\'Excluir linha\' onclick=\'clearLine(this.parentNode.parentNode);\'><img src=\'ico/clear.svg\' width=\'22\'></a>'
            ]).draw(true);
            return true;
        }
        else {
            return false;
        }
    }
    
    function scanLines() {
        tabelaHosts.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
            if(tabelaHosts.cell(rowIdx, 2).data() == "aguardando" && conexoesSimultaneas < getLimiteConexoes()){
                executaPing(rowIdx);
            }
        });
    }
    
    function executaPing(rowIdx) {
        var ip = tabelaHosts.cell(rowIdx, 1).data();

        $.ajax({
            'url' : 'api/ping.php',
            type : 'get',
            crossDomain : true,
            data : {
                'ip': ip,
            },
            beforeSend : function(){
                conexoesSimultaneas++;
                tabelaHosts.cell(rowIdx, 2).data("conectando...")
                    .draw();
            }
        })
            .done(function(msg){
                if(msg.status == 'on'){
                    tabelaHosts.cell(rowIdx, 2).data("<span class='green-text'><b>CONECTADO</b></span>");
                    tabelaHosts.cell(rowIdx, 3).data(msg.tempo);
                    tabelaHosts.cell(rowIdx, 4).data(limpaTempo(msg.tempo))
                        .draw();
                }
                else if(msg.status == 'off'){
                    tabelaHosts.cell(rowIdx, 2).data("<span class='red-text'><b>NÃO CONECTADO</b></span>");
                    tabelaHosts.cell(rowIdx, 3).data("-");
                    tabelaHosts.cell(rowIdx, 4).data("-")
                        .draw();
                }
                else{
                    tabelaHosts.cell(rowIdx, 2).data("aguardando")
                        .draw();
                }
                conexoesSimultaneas--;
            })
            .fail(function(jqXHR, textStatus, msg){
                tabelaHosts.cell(rowIdx, 2).data("aguardando")
                    .draw();
                conexoesSimultaneas--;
            });
    }

    function refreshPing(elemento) {
        tabelaHosts.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
            if(elemento.children[0].innerHTML == tabelaHosts.cell(rowIdx, 0).data()){
                tabelaHosts.cell(rowIdx, 2).data("aguardando");
                tabelaHosts.cell(rowIdx, 3).data(preloader);
            }
        });
    }
    
    function refreshAll() {
        tabelaHosts.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
            tabelaHosts.cell(rowIdx, 2).data("aguardando");
            tabelaHosts.cell(rowIdx, 3).data(preloader);
        });
    }
    
    function clearAll() {
        tabelaHosts.rows().remove().draw();
    }
    
    function clearLine(elemento) {
        tabelaHosts.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
            if(elemento.children[1].innerHTML == tabelaHosts.cell(rowIdx, 1).data()){
                tabelaHosts.row(rowIdx).remove().draw();
            }
        });
        atualizaIndices();
    }
    
    function atualizaIndices() {
        tabelaHosts.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
            tabelaHosts.cell(rowIdx, 0).data(rowIdx+1);
        });
    }
    
    function btnConfigClick() {
        atualizaConfiguracoes();

        if($('#divPrincipal').hasClass("divRecolhida")){
            $('#slide-configs').hide();
            $('#divPrincipal').removeClass("divRecolhida");
            $('#fixadorNav').addClass("navbar-fixed");
        }
        else{
            $('#slide-configs').show();
            $('#divPrincipal').addClass("divRecolhida");
            $('#fixadorNav').removeClass("navbar-fixed");
        }
    }

    function atualizaConfiguracoes() {
        $("#limitConex").val(getLimiteConexoes());
        $("#nomeExport").val(getFilename());
        if(getPermiteDuplicadas() == "true") $("#checkPermiteDuplicadas").prop('checked', true);

        if(getFiletype() == ".txt"){
            $("#optTXT").prop('selected', true);
        }
        else if(getFiletype() == ".xlsx"){
            $("#optXLSX").prop('selected', true);
        }
        else if(getFiletype() == ".pdf"){
            $("#optPDF").prop('selected', true);
        }
    }

    function atualizaLimitConn() {
        var novoLimite = $("#limitConex").val();
        setCookie("limiteConexoes", novoLimite, 540320300);
    }

    function getLimiteConexoes() {
        if(getCookie("limiteConexoes") > 0){
            var limiteConexoes = getCookie("limiteConexoes");
        }
        else{
            var limiteConexoes = 30;
            setCookie("limiteConexoes", 30, 32000000);
        }
        return limiteConexoes;
    }

    function atualizaPermiteDuplicadas() {
        var novoValor = $("#checkPermiteDuplicadas").is(':checked');
        setCookie("permiteDuplicadas", novoValor, 540320300);

        if(novoValor == false && getQntLinhasDuplicadas() > 0){
            var confirmacao = confirm("Deseja apagar as linhas duplicadas?");

            if(confirmacao){
                var qntLinhasApagadas = apagaLinhasDuplicadas();

                if(qntLinhasApagadas == 1){
                    M.toast({html: 'Uma linha foi apagada.'});
                }
                else{
                    M.toast({html: qntLinhasApagadas+' linhas foram apagadas.'});
                }
            }
        }
    }

    function getPermiteDuplicadas() {
        if(getCookie("permiteDuplicadas") != undefined && getCookie("permiteDuplicadas") != ""){
            var permiteDuplicadas = getCookie("permiteDuplicadas");
        }
        else{
            var permiteDuplicadas = false;
            setCookie("permiteDuplicadas", false, 32000000);
        }
        return permiteDuplicadas;
    }

    function getQntLinhasDuplicadas() {
        var hosts = Array();
        var qntLinhasDuplicadas = 0;

        tabelaHosts.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
            if(hosts.includes(tabelaHosts.cell(rowIdx, 1).data())){
                qntLinhasDuplicadas++;
            }
            else{
                hosts.push(tabelaHosts.cell(rowIdx, 1).data());
            }
        });

        return qntLinhasDuplicadas;
    }

    function apagaLinhasDuplicadas() {
        var hosts = Array();
        var qntLinhasApagadas = 0;
        var ordemAnterior = tabelaHosts.order()[0];

        tabelaHosts.order([0, 'asc']).draw();
        tabelaHosts.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
            if(hosts.includes(tabelaHosts.cell(rowIdx - qntLinhasApagadas, 1).data())){
                tabelaHosts.row(rowIdx - qntLinhasApagadas).remove().draw();
                atualizaIndices();
                qntLinhasApagadas++;
            }
            else{
                hosts.push(tabelaHosts.cell(rowIdx - qntLinhasApagadas, 1).data());
            }
        });

        tabelaHosts.order(ordemAnterior).draw();

        return qntLinhasApagadas;
    }

    function atualizaFiletype() {
        var novoFiletype = $("#selectFiletype").val();
        setCookie("filetype", novoFiletype, 540320300);
    }

    function getFiletype() {
        if(getCookie("filetype") == ".txt" || getCookie("filetype") == ".xlsx"){
            var filetype = getCookie("filetype");
        }
        else{
            var filetype = ".txt";
            setCookie("filetype", ".txt", 32000000);
        }
        return filetype;
    }

    function atualizaFilename() {
        var novoFilename = $("#nomeExport").val();
        setCookie("filename", novoFilename, 540320300);
    }

    function getFilename() {
        if(getCookie("filename") != undefined && getCookie("filename") != ""){
            var filename = getCookie("filename");
        }
        else{
            var filename = "testeIPs";
            setCookie("filename", "testeIPs", 32000000);
        }
        return filename;
    }
    
    function btnDownloadClick() {
        //Verifica se há alguma linha pendente
        var linhaPendente = false;
        var linhas = $("#linhasIPs > tr");
        jQuery.each(linhas, function (indice, elemento) {
            var colunas = elemento.children;
            if($(colunas[2]).html() == "aguardando" || $(colunas[2]).html() == "conectando..."){
                linhaPendente = true;
            }
        });

        if(linhaPendente){
            M.toast({html: 'Aguarde todas as linhas serem testadas e clique novamente!'});
            return false;
        }
        else if($("#linhasIPs > tr").length == 1){
            M.toast({html: 'Não há nada para exportar!'});
            return false;
        }
        else{
            var linhas = $("#linhasIPs > tr");
            var strLinhas = "";

            jQuery.each(linhas, function (indice, elemento) {
                var colunas = elemento.children;

                strLinhas += $(colunas[0]).html()+","+$(colunas[1]).html()+","+$(colunas[2]).html()+";";
            });

            $("#txtLinhas").val(strLinhas);
            $("#filetype").val(getFiletype());
            $("#filename").val(getFilename());
            $("#formExport").submit();
        }
    }

    function limpaTempo(strOriginal) {
        var str;

        str = strOriginal.replace("=", "");
        str = str.replace(">", "");
        str = str.replace("<", "");
        str = str.replace("ms", "");

        return str;
    }
</script>

</html>