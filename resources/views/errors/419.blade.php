@extends('errors::aladin-layout')

@section('title', __('Page expirée'))
@section('code', '419')
@section('message', __('Session expirée'))
@section('description', __('Désolé, votre session a expiré en raison d\'une inactivité. Veuillez rafraîchir la page et réessayer.'))

@section('help')
<div class="space-y-3">
    <div class="flex items-start gap-3">
        <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
        </div>
        <div>
            <h4 class="font-medium text-gray-900">{{ __('Actualiser la page') }}</h4>
            <p class="text-sm text-gray-600">{{ __('Souvent, un simple rafraîchissement suffit.') }}</p>
        </div>
    </div>
    
    <div class="flex items-start gap-3">
        <div class="flex-shrink-0 w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
        </div>
        <div>
            <h4 class="font-medium text-gray-900">{{ __('Se reconnecter') }}</h4>
            <p class="text-sm text-gray-600">{{ __('Si le problème persiste, essayez de vous reconnecter.') }}</p>
        </div>
    </div>
</div>
@endsection
