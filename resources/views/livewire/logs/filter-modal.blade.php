<div wire:ignore.self class="modal fade" id="filterMobileModal" tabindex="-1" role="dialog" aria-labelledby="filterMobileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterMobileModalLabel">
                    Filter Log Aktivitas
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                @include('livewire.logs.filter-content')
            </div>
        </div>
    </div>
</div>
