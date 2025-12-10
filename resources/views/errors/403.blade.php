@extends('errors::aladin-layout')

@section('title', __('Accès refusé'))
@section('code', '403')
@section('message', __('Accès refusé'))
@section('description', __('Vous n\'avez pas les permissions nécessaires pour accéder à cette page. Si vous pensez qu\'il s\'agit d\'une erreur, contactez votre administrateur.'))

@section('help')
<div class="space-y-3">
    <div class="flex items-start gap-3">
        <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </div>
        <div>
            <h4 class="font-medium text-gray-900">{{ __('Connectez-vous avec un compte autorisé') }}</h4>
            <p class="text-sm text-gray-600">{{ __('Assurez-vous d\'utiliser le bon compte') }}</p>
        </div>
    </div>
    
    <div class="flex items-start gap-3">
        <div class="flex-shrink-0 w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        <div>
            <h4 class="font-medium text-gray-900">{{ __('Demandez l\'accès') }}</h4>
            <p class="text-sm text-gray-600">{{ __('Contactez votre administrateur pour obtenir les permissions') }}</p>
        </div>
    </div>
    
    <div class="flex items-start gap-3">
        <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
        </div>
        <div>
            <h4 class="font-medium text-gray-900">{{ __('Retournez à l\'accueil') }}</h4>
            <p class="text-sm text-gray-600">{{ __('Explorez les sections accessibles') }}</p>
        </div>
    </div>
</div>
@endsection
