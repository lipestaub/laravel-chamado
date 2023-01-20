<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Chamado</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    
    {!!Html::style("bootstrap/css/bootstrap.min.css")!!}
    {!!Html::script("bootstrap/js/bootstrap.bundle.min.js")!!}
</head>
<body>
    @if (Session::has('warning'))
        <div class="card text-bg-primary mb-3">
            <div class="card-header">Aviso</div>
            <div class="card-body">
                <span class="card-text">{{ Session::get('warning') }}</span>
            </div>
        </div>
        <br>
    @endif
    <div class="container">
        {{ Form::open(['url' => 'chamados/registrar', 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
        <div>
            <div>
                <div>
                    <b>{{ Form::label('nome', 'Nome') }}</b>
                    {{ Form::text('nome', null, ['class' => 'form-control', 'required' => ''])}}
                </div>
                <div>
                    <b>{{ Form::label('empresa', 'Empresa') }}</b>
                    <div>
                        {{ Form::text('empresa', null, ['class' => 'form-control', 'required' => ''])}}
                    </div>
                </div>
                <div>
                    <b>{{ Form::label('email', 'E-mail') }}</b>
                    <div>
                        {{ Form::text('email', null, ['class' => 'form-control', 'required' => ''])}}
                    </div>
                </div>
                <div>
                    <b>{{ Form::label('telefone', 'Telefone') }}</b>
                    <div>
                        {{ Form::text('telefone', null, ['class' => 'form-control', 'required' => ''])}}
                    </div>
                </div>
            </div>
            <br>
            <div>
                <b>{{ Form::label('titulo', 'Solicitação') }}</b>
                <div>
                    {{ Form::text('titulo', null, ['class' => 'form-control', 'required' => ''])}}
                </div>
                <br>
                <b>{{ Form::label('mensagem', 'Descrição') }}</b>
                <div>
                    {{ Form::text('mensagem', null, ['class' => 'form-control'])}}
                </div>
            </div>
            <div>
                <b>{{ Form::label('anexo', 'Anexo') }}</b>
                <div>
                    {{ Form::file('anexo') }}
                </div>
            </div>
            <div>
                <!-- recaptcha -->
            </div>
            <div>
                <button type="submit" class="btn btn-primary">Enviar</button>
            </div>
    {{ Form::close() }}
</body>
</html>