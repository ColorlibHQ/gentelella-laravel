{{--
    The filter bar. Controls carry data-table-filter, which src/v4/tables.js
    collects and appends to every request — so a filter survives paging and
    sorting rather than being forgotten on the next draw.
--}}
@if ($panel->getFilters() !== [])
    <div class="card-body table-filters">
        <div class="form-row">
            @foreach ($panel->getFilters() as $filter)
                @php $view = 'gentelella::filters.'.$filter->type; @endphp
                @include(view()->exists($view) ? $view : 'gentelella::filters.text', ['filter' => $filter])
            @endforeach

            <div class="form-group form-actions">
                <button type="button" class="btn btn-outline btn-sm" data-table-filter-reset>
                    {{ __('Clear filters') }}
                </button>
            </div>
        </div>
    </div>
@endif
