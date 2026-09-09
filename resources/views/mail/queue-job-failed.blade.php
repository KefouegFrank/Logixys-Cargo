<x-mail::message>
# Un traitement en arrière-plan a échoué

Un travail en file d'attente n'a pas pu aboutir après plusieurs tentatives. Les e-mails
concernés n'ont pas été envoyés.

**Travail :** {{ $jobName }}

<x-mail::panel>
{{ $reason }}
</x-mail::panel>

Consultez la table `failed_jobs` et les journaux applicatifs. Une fois la cause corrigée,
`php artisan queue:retry all` relance les envois en attente.

Les alertes suivantes sont regroupées : ce message ne repart qu'après une période de calme.
</x-mail::message>
