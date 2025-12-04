<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Pedido;
use App\Models\PedidoPagamento;

class PaymentController extends Controller
{
    public function processarPedidos()
    {
        $token = env('ECOMPLETO_TOKEN');
        $baseUri = env('ECOMPLETO_BASE_URI');
        $endpoint = env('ECOMPLETO_ENDPOINT');

        $pedidos = Pedido::whereHas('pagamento', function ($q) {
            $q->where('id_formapagto', 3);
        })->where('id_situacao', 1)->get();

        $resultados = [];

        foreach ($pedidos as $pedido) {
            $pagamento = $pedido->pagamento;
            $cliente = $pedido->cliente;

            $exp = (string) $pagamento->vencimento;

            if (preg_match('/^\d{4}-\d{2}$/', $exp)) {
                $exp = substr($exp, 5, 2) . substr($exp, 2, 2); // '08' . '22' => '0822'
            }


            $body = [
                'external_order_id' => $pedido->id,
                'amount' => (float) $pedido->valor_total,
                'card_number' => preg_replace('/\D/', '', (string) $pagamento->num_cartao),
                'card_cvv' => (string) $pagamento->codigo_verificacao,
                'card_expiration_date' => $exp,
                'card_holder_name' => $pagamento->nome_portador,
                'customer' => [
                    'external_id' => $cliente->id,
                    'name' => $cliente->nome,
                    "type" => "individual",
                    'documents' =>
                    [
                        'type' => 'cpf',
                        'number' => $cliente->cpf_cnpj
                    ],
                    'birthday' => date('Y-m-d', strtotime(str_replace('/', '-', $cliente->data_nasc)))
                ]
            ];

            var_dump($body);



            $url = "{$baseUri}{$endpoint}?accessToken={$token}";
            $response = Http::post($url, $body);
            $retorno = $response->json();
            
            switch ($retorno['Transaction_code']) {
                case "00";
                    $pedido->id_situacao = 2; // Pago

                    break;
                case "01";
                    $pedido->id_situacao = 2; // Pagamento em análise
                    break;
                case "02";
                    $pedido->id_situacao = 3; // Pagamento extornado, pedido cancelado
                    break;
                case "03";
                    $pedido->id_situacao = 3; // Pagamento recusado, pedido cancelado
                    break;
                case "04";
                    $pedido->id_situacao = 3; // Sem crédito, pedido cancelado
            }
            $pedido->save();

            $pagamento->retorno_intermediador = json_encode($retorno);
            $pagamento->data_processamento = now();
            $pagamento->save();

            $resultados[] = [
                'pedido' => $pedido->id,
                'situacao' => $pedido->id_situacao,
                'retorno' => $retorno
            ];
        }

        return response()->json(['message' => 'Processamento concluido', 'resultados' => $resultados]);
    }
}
