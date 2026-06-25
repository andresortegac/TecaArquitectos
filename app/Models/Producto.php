<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Producto extends Model
{
    use HasFactory;

    public const IMAGE_DIR = 'uploads/productos';
    

    protected $fillable = [
        'nombre',
        'categorias',
        'cantidad',
        'costo',
        'ubicacion',
        'estado',
        'imagen',
        'tarifa_diaria',
    ];


    // 👇 Lo que ya tenías (intocable)
    public function arriendos()
    {
        return $this->hasMany(Arriendo::class);
    }

    // 🆕 NUEVO: relación con solicitudes
    public function solicitudes()
    {
        return $this->belongsToMany(
            Solicitud::class,
            'solicitud_productos'
        )->withPivot(
            'cantidad_solicitada',
            'cantidad_aprobada'
        )->withTimestamps();
    }
    public static function imageUrl(?string $imagen, ?string $nombre = null): string
    {
        if (!empty($imagen)) {
            $imagen = ltrim($imagen, '/');

            if (str_starts_with($imagen, 'http://') || str_starts_with($imagen, 'https://')) {
                return $imagen;
            }

            foreach (self::imagePathCandidates($imagen) as $path) {
                if (is_file(public_path($path))) {
                    return asset($path);
                }
            }
        }

        return asset(self::fallbackImagePath($nombre) ?? 'img/product-icon.svg');
    }

    public function getImagenUrlAttribute()
    {
        return self::imageUrl($this->imagen, $this->nombre);
    }

    private static function imagePathCandidates(string $imagen): array
    {
        $paths = [$imagen];

        if (!str_starts_with($imagen, 'storage/')) {
            $paths[] = 'storage/' . $imagen;
        }

        if (!str_starts_with($imagen, self::IMAGE_DIR . '/')) {
            $paths[] = self::IMAGE_DIR . '/' . basename($imagen);
        }

        if (!str_starts_with($imagen, 'img/img_producto/')) {
            $paths[] = 'img/img_producto/' . basename($imagen);
        }

        return array_values(array_unique($paths));
    }

    private static function fallbackImagePath(?string $nombre): ?string
    {
        if (empty($nombre)) {
            return null;
        }

        $name = Str::of($nombre)->ascii()->lower()->value();

        $rules = [
            'ruedas.jpeg' => ['rueda'],
            'andamio_verde.jpeg' => ['andamio', 'verde'],
            'andamio_gris.jpeg' => ['andamio'],
            'angulometalicos.jpeg' => ['angulo'],
            'apisonador.jpeg' => ['apisonador'],
            'chapetas.jpeg' => ['chapeta'],
            'sercha_roja.jpeg' => ['cercha', '2m'],
            'sercha_azul.jpeg' => ['cercha'],
            'puntales_verde.jpeg' => ['puntal', 'verde'],
            'puntales_rojo.jpeg' => ['puntal', 'rojo'],
            'puntales_gris.jpeg' => ['puntal'],
            'formaleta_20.jpeg' => ['formaleta', '0.20'],
            'formaleta_30.jpeg' => ['formaleta', '0.30'],
            'formaleta_40.jpeg' => ['formaleta'],
            'plataforma_copleta.jpeg' => ['plataforma'],
            'estensor.jpeg' => ['tensor'],
            'pluma.jpeg' => ['pluma'],
            'coluna_tipo_l.jpeg' => ['columna'],
            'escalera.jpeg' => ['escalera'],
            'tablon.jpeg' => ['tablon'],
            'tijera_corta.jpeg' => ['tijera', 'corta'],
            'tijera_larga.jpeg' => ['tijera'],
            'estencion_larga.jpeg' => ['extension', '50'],
            'estencion.jpeg' => ['extension'],
            'plancha_metaloca.jpeg' => ['plancha'],
        ];

        foreach ($rules as $file => $needles) {
            $matches = true;

            foreach ($needles as $needle) {
                if (!str_contains($name, $needle)) {
                    $matches = false;
                    break;
                }
            }

            $path = 'img/img_producto/' . $file;
            if ($matches && is_file(public_path($path))) {
                return $path;
            }
        }

        return null;
    }

}
 
