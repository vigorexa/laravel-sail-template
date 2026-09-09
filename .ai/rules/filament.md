---
paths:
  - 'app/Support/Filament/**'
---

# Filament

## module.json filament.plugin via data_get
`$module->get('filament.plugin')` always returns null — nwidart's Json class does not resolve dot keys. Read the plugin class with `data_get($module->get('filament'), 'plugin')` (as in `ModuleFilamentPlugins::discover()`).
