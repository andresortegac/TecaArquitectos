<form method="POST" action="{{ route('config.reportes') }}">
    @csrf

    <div class="card">
        <div class="card-body">
            <h5 class="mb-3">📊 Configuración de Reportes</h5>

            <div class="mb-3">
                <label class="form-label">Mes por defecto</label>
                <select
                    name="mes_defecto"
                    class="form-select"
                    required
                >
                    @foreach ($meses as $mes)
                        <option value="{{ $mes }}"
                            {{ old('mes_defecto', $config->mes_defecto) === $mes ? 'selected' : '' }}>
                            {{ $mes }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">
                Guardar
            </button>
        </div>
    </div>
</form>
