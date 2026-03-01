<?php

namespace App\Http\Controllers;

use App\Models\Boleto;
use App\Models\OrdemCompra;
use Illuminate\Http\Request;

class BoletoController extends Controller
{
    public function index()
    {
        $boletos = Boleto::with('ordemCompra.fornecedor.pessoa')->get();
        $title = 'Boletos';
        $route = 'boleto.inserir';
        return view('boleto.index', compact('boletos', 'title', 'route'));
    }

    public function create()
    {
        return view('boleto.formulario', [
            'title' => 'Criar Boleto',
            'route' => 'boleto.inserir',
            'btn_label' => 'Criar',
            'defaultDates' => [
                'data_emissao' => now()->format('Y-m-d'),
                'data_vencimento' => now()->addDays(30)->format('Y-m-d'),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ordem_compra_id' => 'required|exists:ordem_compras,id',
            'valor' => 'required|numeric|min:0',
            'data_emissao' => 'required|date',
            'data_vencimento' => 'required|date',
            'observacoes' => 'nullable|string',
        ]);

        try {
            Boleto::create([
                'ordem_compra_id' => $request->ordem_compra_id,
                'valor' => $request->valor,
                'data_emissao' => $request->data_emissao,
                'data_vencimento' => $request->data_vencimento,
                'observacoes' => $request->observacoes,
                'status' => 'pendente',
            ]);

            return redirect()->route('boleto.index')->with('success', 'Boleto criado com sucesso.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['db_error' => 'Erro ao criar boleto: ' . $e->getMessage()]);
        }
    }

    public function edit(Boleto $boleto)
    {
        $boleto->load('ordemCompra.fornecedor.pessoa');

        return view('boleto.formulario', [
            'title' => 'Editar Boleto',
            'route' => ['boleto.editar', $boleto->id],
            'btn_label' => 'Salvar',
            'boleto' => $boleto,
        ]);
    }

    public function update(Request $request, Boleto $boleto)
    {
        $request->validate([
            'ordem_compra_id' => 'required|exists:ordem_compras,id',
            'valor' => 'required|numeric|min:0',
            'data_emissao' => 'required|date',
            'data_vencimento' => 'required|date',
            'observacoes' => 'nullable|string',
        ]);

        try {
            $boleto->update([
                'ordem_compra_id' => $request->ordem_compra_id,
                'valor' => $request->valor,
                'data_emissao' => $request->data_emissao,
                'data_vencimento' => $request->data_vencimento,
                'observacoes' => $request->observacoes,
            ]);

            return redirect()->route('boleto.index')->with('success', 'Boleto atualizado com sucesso.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['db_error' => 'Erro ao atualizar boleto: ' . $e->getMessage()]);
        }
    }

    public function destroy(Boleto $boleto)
    {
        try {
            $boleto->delete();
            return redirect()->route('boleto.index')->with('success', 'Boleto excluído com sucesso.');
        } catch (\Exception $e) {
            return redirect()->route('boleto.index')->withErrors(['db_error' => 'Erro ao excluir boleto: ' . $e->getMessage()]);
        }
    }

    public function marcarPago(Boleto $boleto)
    {
        try {
            $boleto->update(['status' => 'pago']);
            return redirect()->route('boleto.index')->with('success', 'Boleto marcado como pago.');
        } catch (\Exception $e) {
            return redirect()->route('boleto.index')->withErrors(['db_error' => 'Erro ao marcar boleto como pago: ' . $e->getMessage()]);
        }
    }

    public function getOrdensCompra()
    {
        $ordensCompra = OrdemCompra::with('fornecedor.pessoa')
            ->where('status', 'recebida')
            ->get()
            ->map(function ($oc) {
                $fornecedor = $oc->fornecedor->pessoa->nome_social ?? $oc->fornecedor->pessoa->nome_registro ?? '-';
                return [
                    'id' => $oc->id,
                    'fornecedor' => $fornecedor,
                    'valor_total' => $oc->valor_total,
                    'label' => "OC #{$oc->id} - {$fornecedor} - R$ " . number_format($oc->valor_total, 2, ',', '.'),
                ];
            });

        return response()->json($ordensCompra);
    }
}
