<form method="POST" action="{{ route('config.stock') }}">
    @csrf

    <div class="card">
        <div class="card-body">
            <h5 class="mb-3">📦 Configuración de Stock</h5>

            <div class="mb-3">
                <label class="form-label">Stock mínimo global</label>
                <input
                    type="number"
                    name="stock_minimo"
                    class="form-control"
                    min="0"
                    value="{{ old('stock_minimo', $config->stock_minimo) }}"
                    required
                >
            </div>

            <div class="form-check mb-3">
                <input
                    class="form-check-input"
                    type="checkbox"
                    name="alerta_stock"
                    id="alerta_stock"
                    {{ old('alerta_stock', $config->alerta_stock) ? 'checked' : '' }}
                >
                <label class="form-check-label" for="alerta_stock">
                    Activar alertas de stock bajo
                </label>
            </div>

            <button type="submit" class="btn btn-primary">
                Guardar
            </button>
        </div>
    </div>
</form>
