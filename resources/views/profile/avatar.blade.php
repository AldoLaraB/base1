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
                        <div class="mb-4 text-green-600">Avatar aggiornato con successo!</div>
                    @endif

                    @if (session('status') === 'avatar-rimosso')
                        <div class="mb-4 text-green-600">Avatar rimosso con successo!</div>
                    @endif

                    <div class="flex flex-col items-center mb-6">
                        <h3 class="text-lg font-medium mb-4">Avatar attuale</h3>
                        
                        @if($avatar)
                            <img src="{{ $avatar->url }}" alt="Avatar" 
                                 class="w-32 h-32 rounded-full object-cover border-4 border-gray-200 mb-4">
                            <form method="POST" action="{{ route('profile.avatar.destroy') }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                                        onclick="return confirm('Sei sicuro di voler rimuovere l\'avatar?')">
                                    Rimuovi avatar
                                </button>
                            </form>
                        @else
                            <div class="w-32 h-32 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 text-4xl mb-4">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <p class="text-gray-500">Nessun avatar caricato</p>
                        @endif
                    </div>

                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium mb-4">Carica nuovo avatar</h3>
                        
                        <form method="POST" action="{{ route('profile.avatar.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-4">
                                
                                <input type="file" name="avatar" id="avatar" accept="image/jpeg,image/png,image/gif,image/webp"
                                       class="mt-1 block w-full text-sm text-gray-500
                                              file:mr-4 file:py-2 file:px-4
                                              file:rounded-md file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-blue-50 file:text-blue-700
                                              hover:file:bg-blue-100">
                                @error('avatar')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Carica avatar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>