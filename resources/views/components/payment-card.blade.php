@php
    use App\Support\Periode;

    $items = collect($tagihan ?? []);
@endphp

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="fw-bold mb-3">{{ $title ?? 'Kartu Bayaran Infak' }}</h5>
        <div class="row g-3">
            @forelse($items as $item)
                @php
                    $statusClass = match ($item->status) {
                        'lunas' => 'success',
                        'sebagian' => 'warning text-dark',
                        default => 'secondary',
                    };
                @endphp
                <div class="col-md-4 col-lg-3">
                    <div class="border rounded-3 p-3 h-100 bg-light">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div class="fw-semibold">{{ Periode::label($item->periode) }}</div>
                            <span class="badge bg-{{ $statusClass }}">{{ ucfirst($item->status) }}</span>
                        </div>
                        <div class="small text-muted mt-2">Tagihan</div>
                        <div>Rp {{ number_format($item->nominal, 0, ',', '.') }}</div>
                        <div class="small text-muted mt-2">Terbayar</div>
                        <div>Rp {{ number_format($item->terbayar, 0, ',', '.') }}</div>
                        @if($item->sisa > 0)
                            <div class="small text-danger mt-2">Sisa Rp {{ number_format($item->sisa, 0, ',', '.') }}</div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted py-3">Belum ada tagihan infak.</div>
            @endforelse
        </div>
    </div>
</div>
