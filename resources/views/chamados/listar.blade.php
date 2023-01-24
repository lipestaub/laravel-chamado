<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Chamados</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    
    {!!Html::style("bootstrap/css/bootstrap.min.css")!!}
    {!!Html::script("bootstrap/js/bootstrap.bundle.min.js")!!}
</head>
<body>
    <div class="container"> 
        @foreach ($chamados as $chamado)
            <div class="border border-dark m-3 p-1">
                <div class="text-center m-2">
                    <h4>Chamado {{ $chamado->id }}</h4>
                </div>
                <div class="row text-center m-3">
                    <div class="col-sm-2">
                        <b>Nome</b>
                        <br>
                        {{ $chamado->nome }}
                    </div>
                    <div class="col-sm-2">
                        <b>Empresa</b>
                        <br>
                        {{ $chamado->empresa }}
                    </div>
                    <div class="col-sm-5">
                        <b>C&oacute;digo</b>
                        <br>
                        {{ $chamado->chave_acesso }}
                    </div>
                    <div class="col-sm-3">
                        <b>Hor&aacute;rio</b>
                        <br>
                        {{ date('d/m/Y H:i:s', $chamado->datahora) }}
                    </div>
                </div>
                <div class="mt-4">
                    <div class="row m-3">
                        <div class="col-sm-12">
                            <b>Solicita&ccedil;&atilde;o: </b>{{ $chamado->titulo }}
                        </div>
                    </div>
                    <div class="row col-sm-12 m-3">
                        <div class="col-sm-12">
                            <b>Descri&ccedil;&atilde;o: </b>{{ $chamado->mensagem }}
                        </div>
                    </div>
                    @if ($chamado->anexo)
                    <div class="row col-sm-12 m-3">
                        <div class="col-sm-12">
                            <b>Anexo: </b><a href="{{ asset($chamado->anexo) }}">Abrir</a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</body>
</html>