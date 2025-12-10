@extends('errors::aladin-layout')

@section('title', __('Erreur serveur'))
@section('code', '500')
@section('message', __('Erreur interne du serveur'))
@section('description', __('Oups ! Quelque chose s\'est mal passé de notre côté. Nos équipes techniques ont été notifiées et travaillent à résoudre le problème.'))

@section('help')
<div class="space-y-3">
    <div class="flex items-start gap-3">
        <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
        </div>
        <div>
            <h4 class="font-medium text-gray-900">{{ __('Réessayez dans quelques instants') }}</h4>
            <p class="text-sm text-gray-600">{{ __('Le problème est généralement temporaire') }}</p>
        </div>
    </div>
    
    <div class="flex items-start gap-3">
        <div class="flex-shrink-0 w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <div>
            <h4 class="font-medium text-gray-900">{{ __('Vérifiez votre connexion') }}</h4>
            <p class="text-sm text-gray-600">{{ __('Assurez-vous d\'être connecté à Internet') }}</p>
        </div>
    </div>
    
    <div class="flex items-start gap-3">
        <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>
        <div>
            <h4 class="font-medium text-gray-900">{{ __('Contactez-nous si le problème persiste') }}</h4>
            <p class="text-sm text-gray-600">{{ __('support@aladintechnologies.com') }}</p>
        </div>
    </div>
</div>
@endsection
