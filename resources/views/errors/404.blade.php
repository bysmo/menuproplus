@extends('errors::aladin-layout')

@section('title', __('Page non trouvée'))
@section('code', '404')
@section('message', __('Oups ! Page introuvable'))
@section('description', __('La page que vous recherchez semble avoir disparu dans les méandres du cyberespace. Elle a peut-être été déplacée, supprimée ou n\'a jamais existé.'))

@section('help')
<div class="space-y-3">
    <div class="flex items-start gap-3">
        <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <h4 class="font-medium text-gray-900">{{ __('Vérifiez l\'URL') }}</h4>
            <p class="text-sm text-gray-600">{{ __('Assurez-vous que l\'adresse est correcte') }}</p>
        </div>
    </div>
    
    <div class="flex items-start gap-3">
        <div class="flex-shrink-0 w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        <div>
            <h4 class="font-medium text-gray-900">{{ __('Utilisez la recherche') }}</h4>
            <p class="text-sm text-gray-600">{{ __('Essayez de rechercher ce que vous cherchez depuis l\'accueil') }}</p>
        </div>
    </div>
    
    <div class="flex items-start gap-3">
        <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </div>
        <div>
            <h4 class="font-medium text-gray-900">{{ __('Contactez le support') }}</h4>
            <p class="text-sm text-gray-600">{{ __('Notre équipe est là pour vous aider') }}</p>
        </div>
    </div>
</div>
@endsection
