<div wire:ignore.self class="modal fade" id="logDetailModal" tabindex="-1" role="dialog" aria-labelledby="logDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logDetailModalLabel">
                    Detail Log Aktivitas @if($selectedLog) #{{ $selectedLog->id }} @endif
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body pb-4">
                @if($selectedLog)
                    <!-- Flutter / Mobile-Style Detail Header Banner -->
                    <div class="log-detail-banner">
                        <div class="log-detail-badge-row">
                            <div>
                                <span class="badge badge-light border mr-1">
                                    {{ strtoupper($selectedLog->module) }}
                                </span>
                                <span class="badge badge-secondary">
                                    {{ $selectedLog->action }}
                                </span>
                            </div>
                            <div>
                                <span class="{{ $selectedLog->level_badge_class }}">
                                    {{ strtoupper($selectedLog->level) }}
                                </span>
                            </div>
                        </div>

                        <div class="log-detail-message">
                            {{ $selectedLog->message }}
                        </div>

                        <div class="log-detail-time">
                            <i class="far fa-clock mr-1.5"></i>
                            <span>{{ $selectedLog->created_at->translatedFormat('d F Y, H:i:s') }}</span>
                            <span class="text-muted ml-2">({{ $selectedLog->created_at->diffForHumans() }})</span>
                        </div>
                    </div>

                    <!-- Mobile ListTile Style Metadata Group -->
                    <div class="log-list-tile-group">
                        @if($selectedLog->request_id)
                            <!-- Request ID ListTile -->
                            <div class="log-list-tile">
                                <div class="log-list-tile-icon">
                                    <i class="fas fa-fingerprint"></i>
                                </div>
                                <div class="log-list-tile-content">
                                    <span class="log-list-tile-label">Request ID (Trace ID)</span>
                                    <span class="log-list-tile-value font-mono text-[11.5px] text-primary">
                                        {{ $selectedLog->request_id }}
                                    </span>
                                </div>
                            </div>
                        @endif

                        <!-- User ListTile -->
                        <div class="log-list-tile">
                            <div class="log-list-tile-icon">
                                <i class="far fa-user"></i>
                            </div>
                            <div class="log-list-tile-content">
                                <span class="log-list-tile-label">Pengguna (User)</span>
                                <span class="log-list-tile-value">
                                    @if($selectedLog->user)
                                        <strong>{{ $selectedLog->user->name }}</strong>
                                        <span class="text-muted font-normal">({{ $selectedLog->user->email }})</span>
                                    @else
                                        <span class="text-muted font-italic">Sistem / Tamu (Guest)</span>
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- IP Address ListTile -->
                        <div class="log-list-tile">
                            <div class="log-list-tile-icon">
                                <i class="fas fa-network-wired"></i>
                            </div>
                            <div class="log-list-tile-content">
                                <span class="log-list-tile-label">Alamat IP</span>
                                <span class="log-list-tile-value font-mono">
                                    {{ $selectedLog->ip_address ?? '-' }}
                                </span>
                            </div>
                        </div>

                        <!-- User Agent ListTile -->
                        <div class="log-list-tile">
                            <div class="log-list-tile-icon">
                                <i class="fas fa-desktop"></i>
                            </div>
                            <div class="log-list-tile-content">
                                <span class="log-list-tile-label">Perangkat & Browser</span>
                                <span class="log-list-tile-value text-[12px] text-muted leading-relaxed">
                                    {{ $selectedLog->user_agent ?? '-' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Context Payload (JSON Data) -->
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="font-weight-bold text-dark text-[13px]">
                                <i class="fas fa-code text-primary mr-1"></i> Context Payload (JSON)
                            </span>
                            @if(!empty($selectedLog->context))
                                <span class="badge badge-secondary">
                                    {{ count($selectedLog->context) }} Keys
                                </span>
                            @endif
                        </div>

                        @if(!empty($selectedLog->context))
                            <pre class="log-json-code"><code>{{ json_encode($selectedLog->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</code></pre>
                        @else
                            <div class="alert alert-light border text-muted text-center py-3 mb-0">
                                <i class="fas fa-info-circle mr-1"></i> Tidak ada context payload tambahan pada log ini.
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                        <p class="mb-0">Memuat rincian data...</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
