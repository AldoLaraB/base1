<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestione Avatar') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if (session('status') === 'avatar-aggiornato')
                        <div class="mb-4 text-sm font-medium text-green-600">
                            Avatar aggiornato con successo!
                        </div>
                    @endif

                    @if (session('status') === 'avatar-rimosso')
                        <div class="mb-4 text-sm font-medium text-green-600">
                            Avatar rimosso con successo!
                        </div>
                    @endif

                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Avatar attuale</h3>
                        
                        <div class="flex items-center space-x-6">
                            @if($avatar)
                                <img src="{{ $avatar->url }}" alt="Avatar" 
                                     class="w-24 h-24 rounded-full object-cover border-4 border-gray-200">
                                <form method="POST" action="{{ route('profile.avatar.destroy') }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                            onclick="return confirm('Sei sicuro di voler rimuovere l\'avatar?')">
                                        Rimuovi avatar
                                    </button>
                                </form>
                            @else
                                <div class="flex items-center space-x-6">
                                    <div class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-3xl border-4 border-gray-200">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <p class="text-sm text-gray-600">Nessun avatar caricato</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Carica nuovo avatar</h3>
                        
                        <form method="POST" action="{{ route('profile.avatar.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-4">
                                <x-input-label for="avatar" value="Scegli un'immagine" />
                                <input type="file" name="avatar" id="avatar" accept="image/jpeg,image/png,image/gif,image/webp"
                                       class="mt-1 block w-full text-sm text-gray-500
                                              file:mr-4 file:py-2 file:px-4
                                              file:rounded-md file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-blue-50 file:text-blue-700
                                              hover:file:bg-blue-100">
                                <x-input-error :messages="$errors->get('avatar')" class="mt-2" />
                                <p class="text-sm text-gray-500 mt-2">Formati accettati: JPG, PNG, GIF, WEBP</p>
                            </div>

                            <div class="flex items-center gap-4">
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Carica avatar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>