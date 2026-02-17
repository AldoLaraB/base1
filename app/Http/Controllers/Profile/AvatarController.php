<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvatarController extends Controller
{
    public function edit()
    {
        return view('profile.avatar', [
            'user' => Auth::user(),
            'avatar' => Auth::user()->primaryMedia('avatar')
        ]);
    }

    public function update(Request $request)
{
    $request->validate([
        'avatar' => 'required|image|mimes:jpeg,png,gif,webp'
    ]);

    try {
        $user = Auth::user();
        
        // Rimuovi avatar vecchio se esiste
        $oldAvatar = $user->primaryMedia('avatar');
        if ($oldAvatar) {
            $user->deleteMedia($oldAvatar);
        }
        
        // Salva nuovo avatar
        $user->addMedia($request->file('avatar'), 'avatar', true);

        return back()->with('status', 'avatar-aggiornato');
        
    } catch (\InvalidArgumentException $e) {
        return back()->withErrors(['avatar' => $e->getMessage()]);
    } catch (\Exception $e) {
        return back()->withErrors(['avatar' => 'Errore durante il caricamento.']);
    }
}

    public function destroy()
    {
        $user = Auth::user();
        $avatar = $user->primaryMedia('avatar');
        
        if ($avatar) {
            $user->deleteMedia($avatar);
        }

        return back()->with('status', 'avatar-rimosso');
    }
}