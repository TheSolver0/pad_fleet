{{-- Combobox "recherche rapide" réutilisable, propulsé par Alpine.js.
     Le parent doit fournir x-data="searchSelect({ options, selected })".
     $placeholder (optionnel) : texte affiché quand rien n'est saisi/sélectionné. --}}
<div class="position-relative">
    <input type="text"
        class="form-control"
        x-model="query"
        @focus="open = true"
        @click.outside="open = false"
        @keydown.escape="open = false"
        :placeholder="selectedLabel || @js($placeholder ?? 'Rechercher…')"
        autocomplete="off">
    <div class="list-group position-absolute w-100 shadow-sm"
        style="z-index:1060;max-height:220px;overflow-y:auto"
        x-show="open">
        <button type="button" class="list-group-item list-group-item-action text-muted" @click="choose('')">—</button>
        <template x-for="opt in filtered" :key="opt.value">
            <button type="button" class="list-group-item list-group-item-action"
                :class="{ 'active': String(opt.value) === String(selected) }"
                x-text="opt.label" @click="choose(opt.value)"></button>
        </template>
        <div class="list-group-item text-muted small" x-show="filtered.length === 0">Aucun résultat</div>
    </div>
</div>
