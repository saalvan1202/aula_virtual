<?php

namespace App\Http\Middleware;

use App\Services\InstitutoImagen;
use App\Services\Menu;
use App\Services\Variable;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'layouts/app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => function () {
                return [
                    'user' => auth()->user() ? [
                        'id' => auth()->user()->id,
                        'nombres' => ucwords(mb_strtolower(auth()->user()->persona->nombres)),
                        'perfil'=>ucwords(auth()->user()->perfil->descripcion),
                        'email'=>auth()->user()->email,
                        'nombre_completo'=>''
                    ] : null,
                ];
            },
            'menus'=>function(){
                return auth()->user() ? (new Menu())->all():[];
            },
            'imagenes'=>function(){
                return (new InstitutoImagen())->getArray();
            },
            'app_debug'=>config('app.debug'),
            'app_name'=>Variable::appName()
        ]);
    }
}
