# Error pages and localisation

## Error pages

```bash
php artisan vendor:publish --tag=gentelella-errors
```

Writes `403`, `404`, `419`, `429`, `500` and `503` into `resources/views/errors`, on the template's
own `.error-page` markup.

**They have to be published; a package cannot register them.** Laravel's exception handler
*replaces* the `errors` view namespace at render time with the application's own view paths plus
its built-in fallback, so anything a package adds there is discarded. Publishing puts them where
the handler actually looks.

### What they show

A 4xx page shows the exception message when there is one, because `abort(403, 'Only owners may do
that')` is wording somebody chose deliberately.

**A 5xx page never does.** The message on a 500 is the raw exception — a stack of internals, often
including SQL and table names — and printing it is a production information leak. 419, 429 and 503
are the same: their messages are framework wording, not something to show a person. Those pages use
the fixed copy instead, and there is a test asserting a `SQLSTATE` message cannot reach the page.

## Localisation

Every string the package renders goes through `__()`, and the strings are registered as JSON
translations. To translate them, drop a file in your application's `lang` directory — no views to
publish:

```json
// lang/fr.json
{
    "Save": "Enregistrer",
    "Cancel": "Annuler",
    "Sign in": "Se connecter"
}
```

Anything you leave out falls through to the English key, so a partial translation is fine.

The full list of strings the package uses ships as `resources/lang/en.json`, which is a starting
point for a translator:

```bash
php artisan vendor:publish --tag=gentelella-lang
```

Only English ships. Rather than machine-translating strings nobody has checked, the catalogue is
provided for you to fill in.

### Auth wording is the package's own

The sign-in screen does **not** use Laravel's `auth.failed` or `auth.throttle` keys. Those live in
`lang/en/auth.php`, which Laravel no longer publishes by default — leaning on them renders the raw
key on a fresh app. The equivalent strings are in the package catalogue instead.
