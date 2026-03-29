@extends('layouts.app')

@section('title', 'Perfil')

@section('content')
<div class="container-fluid py-5 px-4 px-md-5">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
            <h1 class="text-dark m-0 fs-2 fw-bold mb-4">Meu Perfil</h1>

            {{-- Dados pessoais --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Dados pessoais</h5>
                    <form method="POST" action="{{ route('perfil.atualizar') }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nome_registro" class="form-label small fw-medium">Nome completo</label>
                                <input type="text" class="form-control @error('nome_registro') is-invalid @enderror"
                                    id="nome_registro" name="nome_registro"
                                    value="{{ old('nome_registro', $pessoa->nome_registro) }}" required>
                                @error('nome_registro')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="nome_social" class="form-label small fw-medium">Nome social</label>
                                <input type="text" class="form-control @error('nome_social') is-invalid @enderror"
                                    id="nome_social" name="nome_social"
                                    value="{{ old('nome_social', $pessoa->nome_social) }}">
                                @error('nome_social')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="login" class="form-label small fw-medium">Login</label>
                                <input type="text" class="form-control @error('login') is-invalid @enderror"
                                    id="login" name="login"
                                    value="{{ old('login', $pessoa->login) }}" required>
                                @error('login')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-medium">CPF/CNPJ</label>
                                <input type="text" class="form-control" value="{{ $pessoa->cpf_cnpj }}" disabled>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-medium text-white">
                                Salvar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Alterar senha --}}
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Alterar senha</h5>
                    <form method="POST" action="{{ route('perfil.senha') }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="senha_atual" class="form-label small fw-medium">Senha atual</label>
                                <input type="password" class="form-control @error('senha_atual') is-invalid @enderror"
                                    id="senha_atual" name="senha_atual" required>
                                @error('senha_atual')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="senha" class="form-label small fw-medium">Nova senha</label>
                                <input type="password" class="form-control @error('senha') is-invalid @enderror"
                                    id="senha" name="senha" required>
                                @error('senha')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="senha_confirmation" class="form-label small fw-medium">Confirmar nova senha</label>
                                <input type="password" class="form-control"
                                    id="senha_confirmation" name="senha_confirmation" required>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-medium text-white">
                                Alterar senha
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
