<div class="password-checklist" data-password-checklist="{{ $target ?? 'password' }}">
    <div class="password-check-item" data-rule="length">
        <i class="bi bi-circle"></i>
        Minimal 8 karakter
    </div>
    <div class="password-check-item" data-rule="upper">
        <i class="bi bi-circle"></i>
        Memiliki huruf kapital
    </div>
    <div class="password-check-item" data-rule="lower">
        <i class="bi bi-circle"></i>
        Memiliki huruf kecil
    </div>
    <div class="password-check-item" data-rule="symbol">
        <i class="bi bi-circle"></i>
        Memiliki karakter khusus
    </div>
</div>
