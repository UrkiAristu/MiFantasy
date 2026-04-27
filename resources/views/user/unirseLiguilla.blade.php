@extends('user.layouts.app')

@section('title', 'Unirse a Liguilla')

@section('content')
<div class="container my-5 d-flex flex-column align-items-center justify-content-center" style="min-height: 80vh;">
    <h1 class="mb-4">Unirse a una Liguilla</h1>

    <form action="{{ url('/user/liguillas/unirse') }}" method="POST" class="w-100" style="max-width: 500px;">
        @csrf
        <div class="mb-3 position-relative">
            <label for="codigo" class="form-label fs-4 fw-bold text-center w-100">Código de la Liguilla</label>
            <input
                type="text"
                name="codigo"
                id="codigo"
                value="{{ $codigo }}"
                class="form-control form-control-lg text-center fs-2"
                placeholder="ABC12345"
                maxlength="8"
                required
                autofocus
                style="height: 80px; letter-spacing: 6px; text-transform: uppercase; font-weight: bold;">
            <button type="button" id="btnPegar" class="btn btn-sm btn-outline-secondary position-absolute top-50 end-0 translate-middle-y mt-5 me-2 p-5" aria-label="Pegar contenido del portapapeles">
                <i class="bi bi-clipboard fs-1 p-0"></i>
            </button>
        </div>
        @if(session('success'))
        <div class="alert alert-success w-100 text-center">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger w-100 text-center">
            {{ session('error') }}
        </div>
        @endif

        @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
            {{ $error }}<br>
            @endforeach
        </div>
        @endif
        <button type="submit" class="btn btn-primary btn-lg w-100 mt-3">Unirme</button>
    </form>
</div>
@endsection
@push('scripts')
<script>
    const inputCodigo = document.getElementById('codigo');
    const btnPegar = document.getElementById('btnPegar');
    inputCodigo.addEventListener('input', function() {
        this.value = this.value
            .toUpperCase()
            .replace(/[^A-Z0-9]/g, '')
            .slice(0, 8);
    });
    btnPegar.addEventListener('click', async () => {
        if (navigator.clipboard) {
            let texto = await navigator.clipboard.readText();
            texto = texto.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 8);
            inputCodigo.value = texto;
            inputCodigo.focus();
            inputCodigo.setSelectionRange(texto.length, texto.length);
        } else {
            alert('Tu navegador no permite pegar automáticamente.');
        }
    });
</script>
@endpush