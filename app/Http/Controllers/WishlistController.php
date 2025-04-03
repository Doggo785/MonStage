<?php
namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Offre;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = Wishlist::where('ID_User', auth()->id())
            ->with('offre')
            ->paginate(6);
        return view('dashboard.wishlist.index', compact('wishlists'));
    }

    public function add(Request $request)
    {
        $request->validate(['offre_id' => 'required|exists:Offre,ID_Offre']);

        Wishlist::firstOrCreate([
            'ID_User' => auth()->id(),
            'ID_Offre' => $request->offre_id,
            'Date_ajout' => now(),
        ]);

        return back()->with('success', 'Offre ajoutée à la wishlist.');
    }

    public function remove($id)
    {
        Wishlist::where('ID_User', auth()->id())->where('ID_Offre', $id)->delete();

        return back()->with('success', 'Offre retirée de la wishlist.');
    }
}