<div class="d-flex gap-2 flex-wrap justify-content-end">
    <a href="{{ route('admin.import.template', $master) }}" class="btn btn-outline-success">
        <i class="bi bi-file-earmark-spreadsheet"></i>
        Template XLSX
    </a>
    <form action="{{ route('admin.import.store', $master) }}" method="POST" enctype="multipart/form-data" class="d-flex gap-2">
        @csrf
        <input type="file" name="file" class="form-control" accept=".xlsx" required>
        <button class="btn btn-success">
            <i class="bi bi-upload"></i>
            Import
        </button>
    </form>
</div>
