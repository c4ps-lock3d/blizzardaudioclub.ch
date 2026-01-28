<?php

namespace Webkul\Store\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Webkul\Store\Http\Requests\ContactRequest;
use Webkul\Store\Mail\ContactUs;
use Webkul\Store\Http\Resources\ProductResource;
use Webkul\Theme\Repositories\ThemeCustomizationRepository;
use Illuminate\Http\Request;
use App\Models\Artiste;
use Webkul\Product\Models\Product;

class HomeController extends Controller
{
    /**
     * Using const variable for status
     */
    const STATUS = 1;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected ThemeCustomizationRepository $themeCustomizationRepository) {}

    /**
     * Loads the home page for the storefront.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        visitor()->visit();

        $customizations = $this->themeCustomizationRepository->orderBy('sort_order')->findWhere([
            'status'     => self::STATUS,
            'channel_id' => core()->getCurrentChannel()->id,
            'theme_code' => core()->getCurrentChannel()->theme,
        ]);

        return view('shop::home.index', compact('customizations'));
    }

    /**
     * Loads the home page for the storefront if something wrong.
     *
     * @return \Exception
     */
    public function notFound()
    {
        abort(404);
    }

    /**
     * Summary of contact.
     *
     * @return \Illuminate\View\View
     */
    public function contactUs()
    {
        return view('shop::home.contact-us');
    }

    public function locationSono()
    {
        return view('store::home.location-sono');
    }

    public function aPropos(Artiste $artiste)
    {
        $artiste = $artiste->newQuery();
        $artiste = $artiste->orderBy('name', 'asc');
        return view('store::home.a-propos', [
            'artistes' => $artiste->get(),
        ]);
    }

    public function artisteslist(Artiste $artiste)
    {
        return view('store::artistes.artistes-list');
    }

    public function artisteView(string $slug, Artiste $artistes, Product $products){
        // Inutile grâce au Model Binding --> $post = Post::findOrFail($post);
        if($artistes->slug != $slug){
            return to_route('store.artistes.artiste-view', ['slug' => $artistes->slug, 'id' => $artistes->id]);
        }
        
        // Récupérer les produits triés par date décroissante (plus récents en premier)
        $artisteProducts = $artistes->products()->orderBy('created_at', 'desc')->get();
        
        // Transformer les produits avec la ProductResource
        $productsData = ProductResource::collection($artisteProducts)->resolve();
        
        return view('store::artistes.artiste-view',[
            'artistes' => $artistes,
            'products' => $productsData,
            'count_products' => $artisteProducts->count(),
        ]);
    }

    /**
     * Summary of store.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendContactUsMail(ContactRequest $contactRequest)
    {
        try {
            Mail::queue(new ContactUs($contactRequest->only([
                'name',
                'email',
                'contact',
                'message',
            ])));

            session()->flash('success', trans('shop::app.home.thanks-for-contact'));
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());

            report($e);
        }

        return back();
    }
}
