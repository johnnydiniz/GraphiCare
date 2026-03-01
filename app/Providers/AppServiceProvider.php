<?php

namespace App\Providers;

use App\Models\Boleto;
use App\Models\Fatura;
use App\Models\MateriaPrima;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.nav', function ($view) {
            $notificacoes = collect();

            if (Auth::check()) {
                $hoje = Carbon::today();
                $limite = $hoje->copy()->addDays(7);

                $faturas = Fatura::where('status', 'pendente')
                    ->where('data_vencimento', '<=', $limite)
                    ->orderBy('data_vencimento')
                    ->get();

                foreach ($faturas as $fatura) {
                    $vencido = $fatura->data_vencimento->lt($hoje);
                    $dias = $vencido ? 0 : $hoje->diffInDays($fatura->data_vencimento);
                    $notificacoes->push([
                        'tipo' => 'Fatura',
                        'icone' => 'fa-file-invoice-dollar',
                        'referencia' => "Fatura #{$fatura->id}",
                        'valor' => $fatura->valor,
                        'data_vencimento' => $fatura->data_vencimento,
                        'vencido' => $vencido,
                        'dias' => $dias,
                        'rota' => 'fatura.index',
                    ]);
                }

                $boletos = Boleto::where('status', 'pendente')
                    ->where('data_vencimento', '<=', $limite)
                    ->orderBy('data_vencimento')
                    ->get();

                foreach ($boletos as $boleto) {
                    $vencido = $boleto->data_vencimento->lt($hoje);
                    $dias = $vencido ? 0 : $hoje->diffInDays($boleto->data_vencimento);
                    $notificacoes->push([
                        'tipo' => 'Boleto',
                        'icone' => 'fa-barcode',
                        'referencia' => "Boleto #{$boleto->id}",
                        'valor' => $boleto->valor,
                        'data_vencimento' => $boleto->data_vencimento,
                        'vencido' => $vencido,
                        'dias' => $dias,
                        'rota' => 'boleto.index',
                    ]);
                }

                $notificacoes = $notificacoes->sortBy('data_vencimento')->values();

                // Materiais com estoque crítico (apenas com aviso_estoque ativo)
                $materiaisCriticos = MateriaPrima::where('ativo', true)
                    ->where('aviso_estoque', true)
                    ->whereNotNull('estoque_minimo')
                    ->whereColumn('estoque_atual', '<', 'estoque_minimo')
                    ->get();

                foreach ($materiaisCriticos as $mat) {
                    $alertasEstoque[] = [
                        'referencia' => $mat->descricao,
                        'estoque_atual' => $mat->estoque_atual,
                        'estoque_minimo' => $mat->estoque_minimo,
                    ];
                }
            }

            $alertasEstoque = $alertasEstoque ?? [];
            $temVencido = $notificacoes->contains('vencido', true);
            $temAlerta = count($alertasEstoque) > 0;
            $totalCount = $notificacoes->count() + count($alertasEstoque);
            $view->with('notificacoes', $notificacoes);
            $view->with('alertasEstoque', $alertasEstoque);
            $view->with('notificacoesCount', $totalCount);
            $view->with('temVencido', $temVencido);
            $view->with('temAlerta', $temAlerta);
        });
    }
}
