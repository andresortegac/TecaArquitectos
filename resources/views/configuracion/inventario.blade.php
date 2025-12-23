<form method="POST" action="{{ route('config.inventario') }}">
    @csrf

    <div class="card">
        <div class="card-body">
            <h5 class="mb-3">🏗️ Configuración de Inventario</h5>

            <div class="form-check mb-3">
                <input
                    class="form-check-input"
                    type="checkbox"
                    name="bloquear_sin_stock"
                    id="bloquear_sin_stock"
                    {{ old('bloquear_sin_stock', $config->bloquear_sin_stock) ? 'checked' : '' }}
                >
                <label class="form-check-label" for="bloquear_sin_stock">
                    Bloquear salidas sin stock
                </label>
            </div>

            <button type="submit" class="btn btn-primary">
                Guardar
            </button>
        </div>
    </div>
</form>
