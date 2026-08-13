<div class="sa-empty">
    <div class="sa-empty-icon">
        {{ $icon ?? '📭' }}
    </div>

    <h3 class="sa-empty-title">
        {{ $title }}
    </h3>

    <p class="sa-empty-desc">
        {{ $description }}
    </p>

    @isset($action)
        <div class="sa-empty-action">
            {{ $action }}
        </div>
    @endisset
</div>
