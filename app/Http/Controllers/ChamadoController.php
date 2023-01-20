<?php

namespace App\Http\Controllers;

use App\ChamadoTemp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;

class ChamadoController extends Controller
{
    public function formulario() {
        $dados = isset($_COOKIE['dados_sgti']) ? (array) json_decode($_COOKIE['dados_sgti']) : [];

        $view = ['dados' => $dados];

        return view('chamados.index', $view);
    }

    public function listar() {
        $chamados = ChamadoTemp::orderBy('datahora', 'desc')->get();

        $view = ['chamados' => $chamados];

        return view('chamados.listar', $view);
    }

    public function registrar(Request $request) {
        $form = $request->all();
        unset($form['_token']);

        if (isset($form['g-recaptcha-response'])) {
            $this->validarRecaptcha($form['g-recaptcha-response']);
        }

        $uploadedFile = isset($form['anexo']) ? $form['anexo'] : null;
        unset($form['anexo']);

        if (!$this->validarCampos($form)) {
            $request->session()->flash('warning', 'Preencha todos os campos e tente novamente e tente novamente.');

            return redirect()->back()->withInput($request->all());
        }

        $chaveAcesso = $this->getChaveAcesso();
        $form['chave_acesso'] = $chaveAcesso;

        $datahora = time();
        $form['datahora'] = $datahora;

        $ip = $_SERVER['REMOTE_ADDR'];
        $form['ip'] = $ip;

        try {
            DB::transaction(function() use ($form, $uploadedFile) {
                $chamado = ChamadoTemp::create($form);
                $id = $chamado->id;

                if ($uploadedFile != null) {
                    $form['anexo'] = $this->uploadAnexo($uploadedFile, $id);
                }

                ChamadoTemp::whereId($id)->update($form);
            });
        }
        catch(\Exception $e) {
            return redirect('chamados')->withInput($request->all());
        }

        $dados = json_encode([
            'nome' => $form['nome'],
            'empresa' => $form['empresa'],
            'email' => $form['email'],
            'telefone' => $form['telefone'],
        ]);

        $expireDate = time() + 31536000;

        setcookie('dados_sgti', $dados, $expireDate);

        return redirect('chamados/');
    }

    private function getChaveAcesso() {
        $chaveAcesso = sha1((string) time() . (string) rand(10000, 99999) . (string) rand(1000, 9999));

        while(!ChamadoTemp::where(['chave_acesso' => $chaveAcesso])->get()->isEmpty()) {
            sleep(1, 5);
            $chaveAcesso = sha1((string) time() . (string) rand(10000, 99999) . (string) rand(1000, 9999));
        }

        return $chaveAcesso;
    }

    private function validarRecaptcha($token){
        $post_data = http_build_query([
          'secret' => "SECRET",
          'response' => $token
        ]);
    
        $opts = ['http' => [
          'method'  => 'POST',
          'header'  => 'Content-type: application/x-www-form-urlencoded',
          'content' => $post_data
        ]];
    
        $context  = stream_context_create($opts);
        $response = file_get_contents(
          'https://www.google.com/recaptcha/api/siteverify',
          false,
          $context
        );
    
        $result = json_decode($response);
        if (!$result->success) {
          throw new \Exception('Recaptcha Invalido',1);
        }
      }

    private function validarCampos(array $campos) {
        $erros = [];

        foreach ($campos as $key=>$value) {
            if (empty($value)) {
                array_push($erros, $key);
            }
        }

        return $erros == [] ? true : false;
    }

    private function uploadAnexo($uploadedFile, $idChamado) {
        $path = '../public/anexos_chamados/chamado' . $idChamado;
        $fileName = 'anexo.' . $uploadedFile->getClientOriginalExtension();

        if (!is_dir($path)) {
            mkdir($path, 0777);
        }

        $uploadedFile->move($path, $fileName);

        $filePath = 'anexos_chamados/chamado' . $idChamado . '/' . $fileName;

        return $filePath;
    }
}
