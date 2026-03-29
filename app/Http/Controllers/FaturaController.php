<?php

namespace App\Http\Controllers;

use App\Models\Fatura;
use App\Models\OrdemServico;
use Illuminate\Http\Request;

/**
 * Gerencia operações CRUD e ações relacionadas a faturas.
 *
 * @package App\Http\Controllers
 */
class FaturaController extends Controller
{
    /**
     * Exibe a listagem de faturas.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $faturas = Fatura::with('ordemServico.cliente.pessoa')->get();
        $title = 'Faturas';
        $route = 'fatura.salvar';
        return view('fatura.index', compact('faturas', 'title', 'route'));
    }

    /**
     * Exibe o formulário para criação de uma nova fatura.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('fatura.formulario', [
            'title' => 'Criar Fatura',
            'route' => 'fatura.salvar',
            'btn_label' => 'Criar',
            'defaultDates' => [
                'data_emissao' => now()->format('Y-m-d'),
                'data_vencimento' => now()->addDays(30)->format('Y-m-d'),
            ],
        ]);
    }

    /**
     * Armazena uma nova fatura no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'ordem_servico_id' => 'required|exists:ordem_servicos,id',
            'valor' => 'required|numeric|min:0',
            'data_emissao' => 'required|date',
            'data_vencimento' => 'required|date',
            'observacoes' => 'nullable|string',
        ]);

        try {
            Fatura::create([
                'ordem_servico_id' => $request->ordem_servico_id,
                'valor' => $request->valor,
                'data_emissao' => $request->data_emissao,
                'data_vencimento' => $request->data_vencimento,
                'observacoes' => $request->observacoes,
                'status' => 'pendente',
            ]);

            return redirect()->route('fatura.index')->with('success', 'Fatura criada com sucesso.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['db_error' => 'Erro ao criar fatura: ' . $e->getMessage()]);
        }
    }

    /**
     * Exibe o formulário de edição de uma fatura existente.
     *
     * @param  \App\Models\Fatura  $fatura
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Fatura $fatura)
    {
        $fatura->load('ordemServico.cliente.pessoa');

        return view('fatura.formulario', [
            'title' => 'Editar Fatura',
            'route' => ['fatura.atualizar', $fatura->id],
            'btn_label' => 'Salvar',
            'fatura' => $fatura,
        ]);
    }

    /**
     * Atualiza uma fatura existente no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Fatura  $fatura
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Fatura $fatura)
    {
        $request->validate([
            'ordem_servico_id' => 'required|exists:ordem_servicos,id',
            'valor' => 'required|numeric|min:0',
            'data_emissao' => 'required|date',
            'data_vencimento' => 'required|date',
            'observacoes' => 'nullable|string',
        ]);

        try {
            $fatura->update([
                'ordem_servico_id' => $request->ordem_servico_id,
                'valor' => $request->valor,
                'data_emissao' => $request->data_emissao,
                'data_vencimento' => $request->data_vencimento,
                'observacoes' => $request->observacoes,
            ]);

            return redirect()->route('fatura.index')->with('success', 'Fatura atualizada com sucesso.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['db_error' => 'Erro ao atualizar fatura: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove uma fatura do banco de dados.
     *
     * @param  \App\Models\Fatura  $fatura
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Fatura $fatura)
    {
        try {
            $fatura->delete();
            return redirect()->route('fatura.index')->with('success', 'Fatura excluída com sucesso.');
        } catch (\Exception $e) {
            return redirect()->route('fatura.index')->withErrors(['db_error' => 'Erro ao excluir fatura: ' . $e->getMessage()]);
        }
    }

    /**
     * Marca uma fatura como paga.
     *
     * @param  \App\Models\Fatura  $fatura
     * @return \Illuminate\Http\RedirectResponse
     */
    public function marcarPaga(Fatura $fatura)
    {
        try {
            $fatura->update(['status' => 'paga']);
            return redirect()->route('fatura.index')->with('success', 'Fatura marcada como paga.');
        } catch (\Exception $e) {
            return redirect()->route('fatura.index')->withErrors(['db_error' => 'Erro ao marcar fatura como paga: ' . $e->getMessage()]);
        }
    }

    /**
     * Retorna as ordens de serviço finalizadas em formato JSON para seleção via AJAX.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrdensServico()
    {
        $ordensServico = OrdemServico::with('cliente.pessoa')
            ->where('status', 'finalizada')
            ->get()
            ->map(function ($os) {
                $cliente = $os->cliente->pessoa->nome_social ?? $os->cliente->pessoa->nome_registro ?? '-';
                return [
                    'id' => $os->id,
                    'cliente' => $cliente,
                    'valor_final' => $os->valor_final,
                    'label' => "OS #{$os->id} - {$cliente} - R$ " . number_format($os->valor_final, 2, ',', '.'),
                ];
            });

        return response()->json($ordensServico);
    }
}
