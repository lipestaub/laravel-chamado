<?php

namespace App\Http\Controllers;

use App\ChamadoTemp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
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

        foreach ($chamados as $chamado) {
            $chamado->datahora -= 10800;
        }

        $view = ['chamados' => $chamados];

        return view('chamados.listar', $view);
    }

    public function registrar(Request $request) {
        $form = $request->all();
        unset($form['_token']);

        if (isset($form['g-recaptcha-response'])) {
            $this->validarRecaptcha($form['g-recaptcha-response']);
            unset($form['g-recaptcha-response']);
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

        $json = json_encode([
            'api_user' => 'jstaub@sulvale.inf.br',
            'api_key' => 'ea6f04d342003d1db558eec8e145f5f3661d64e01164bc28a97991b',
            'to' => [[
                'email' => 'lipestaub@gmail.com',
                'name' => 'Felipe',
            ]],
            'from' => [
                'name' => 'Sulvale',
                'email' => 'jstaub@sulvale.inf.br',
                'reply_to' => 'jstaub@sulvale.inf.br',
            ],
            'subject' => 'Chamado aberto',
            'html' => '<span>chegou</span>',
            'text' => 'chegou',
            'addheaders' => [
                'x-priority' => '1',
            ]
        ]);

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL,"https://api.iagentesmtp.com.br/api/v3/send/");
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // curl_setopt_array($curl, [
        //     CURLOPT_URL => 'https://api.iagentesmtp.com.br/api/v3/send/',
        //     CURLOPT_POST => true,
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_POSTFIELDS => $json,
        // ]);

        $result = curl_exec($curl);

        dd($result);

        curl_close($curl);

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
          'secret' => env('SECRET'),
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
