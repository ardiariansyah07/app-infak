<script>

const sidebar =
    document.getElementById('sidebar');

const content =
    document.getElementById('content');

if(sidebar && content && localStorage.getItem('sidebar') === 'collapsed'){

    sidebar.classList.add('collapsed');

    content.classList.add('expanded');

}

function toggleSidebar(){
    if(!sidebar || !content){
        return;
    }

    sidebar.classList.toggle('collapsed');

    content.classList.toggle('expanded');

    if(sidebar.classList.contains('collapsed')){

        localStorage.setItem(
            'sidebar',
            'collapsed'
        );

    }else{

        localStorage.setItem(
            'sidebar',
            'expanded'
        );

    }
}

</script>

<script>

function toggleMasterData(event){

    event.preventDefault();

    let menu =
        document.getElementById('master-menu');

    let arrow =
        document.getElementById('master-arrow');

    if(!menu || !arrow){
        return;
    }

    if(getComputedStyle(menu).display === 'none'){

        menu.style.display = 'block';

        arrow.classList.remove(
            'bi-chevron-right'
        );

        arrow.classList.add(
            'bi-chevron-down'
        );

    }else{

        menu.style.display = 'none';

        arrow.classList.remove(
            'bi-chevron-down'
        );

        arrow.classList.add(
            'bi-chevron-right'
        );

    }
}

function toggleTransaksi(event){

    event.preventDefault();

    let menu =
        document.getElementById('transaksi-menu');

    let arrow =
        document.getElementById('transaksi-arrow');

    if(!menu || !arrow){
        return;
    }

    if(getComputedStyle(menu).display === 'none'){

        menu.style.display = 'block';

        arrow.classList.remove(
            'bi-chevron-right'
        );

        arrow.classList.add(
            'bi-chevron-down'
        );

    }else{

        menu.style.display = 'none';

        arrow.classList.remove(
            'bi-chevron-down'
        );

        arrow.classList.add(
            'bi-chevron-right'
        );

    }
}

</script>

<script>

document.querySelectorAll('[data-password-checklist]').forEach(checklist => {
    const input = document.getElementById(checklist.dataset.passwordChecklist);

    if(!input){
        return;
    }

    const rules = {
        length: value => value.length >= 8,
        upper: value => /[A-Z]/.test(value),
        lower: value => /[a-z]/.test(value),
        symbol: value => /[^A-Za-z0-9]/.test(value),
    };

    const updateChecklist = () => {
        Object.entries(rules).forEach(([rule, passes]) => {
            const item = checklist.querySelector(`[data-rule="${rule}"]`);
            const icon = item?.querySelector('i');

            if(!item || !icon){
                return;
            }

            item.classList.toggle('valid', passes(input.value));
            icon.className = passes(input.value) ? 'bi bi-check-circle-fill' : 'bi bi-circle';
        });
    };

    input.addEventListener('input', updateChecklist);
    updateChecklist();
});

</script>

<script>

document
.querySelectorAll('[data-select-filter]')
.forEach(input => {

    const select = document.getElementById(input.dataset.selectFilter);

    if(!select){
        return;
    }

    const options =
        Array.from(select.options).map(option => ({
            value: option.value,
            text: option.text,
        }));

    input.addEventListener('input', () => {

        const keyword =
            input.value.toLowerCase().trim();

        const selectedValue =
            select.value;

        select.innerHTML = '';

        options
            .filter(option => option.text.toLowerCase().includes(keyword))
            .forEach(option => {
                const element =
                    new Option(option.text, option.value);

                select.add(element);
            });

        if(Array.from(select.options).some(option => option.value === selectedValue)){
            select.value = selectedValue;
        }

    });

});

</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))

<script>

Swal.fire({

    toast:true,

    position:'top-end',

    icon:'success',

    title:'{{ session('success') }}',

    showConfirmButton:false,

    timer:3000

});

</script>

@endif

@if(session('error'))

<script>

Swal.fire({

    toast:true,

    position:'top-end',

    icon:'error',

    title:'{{ session('error') }}',

    showConfirmButton:false,

    timer:3000

});

</script>

@endif

<script>

document
.querySelectorAll('.delete-form')
.forEach(form => {

    form.addEventListener('submit', function(e){

        e.preventDefault();

        Swal.fire({

            title:'Hapus Data ?',

            text:'Data yang dihapus tidak bisa dikembalikan',

            icon:'warning',

            showCancelButton:true,

            confirmButtonText:'Ya, Hapus',

            cancelButtonText:'Batal'

        }).then((result)=>{

            if(result.isConfirmed){

                form.submit();

            }

        });

    });

});

</script>

<script>

document
.querySelectorAll('.confirm-form')
.forEach(form => {

    form.addEventListener('submit', function(e){

        e.preventDefault();

        Swal.fire({

            title: form.dataset.confirmTitle || 'Konfirmasi Aksi',

            text: form.dataset.confirmText || 'Pastikan data sudah benar sebelum diproses.',

            icon: form.dataset.confirmIcon || 'question',

            showCancelButton:true,

            confirmButtonText: form.dataset.confirmButton || 'Ya, Lanjutkan',

            cancelButtonText:'Batal'

        }).then((result)=>{

            if(result.isConfirmed){

                form.submit();

            }

        });

    });

});

document
.querySelectorAll('[data-logout-confirm]')
.forEach(link => {

    link.addEventListener('click', function(e){

        e.preventDefault();

        Swal.fire({

            title:'Logout?',

            text:'Anda akan keluar dari aplikasi.',

            icon:'question',

            showCancelButton:true,

            confirmButtonText:'Ya, Logout',

            cancelButtonText:'Batal'

        }).then((result)=>{

            if(result.isConfirmed){

                document.getElementById('logout-form')?.submit();

            }

        });

    });

});

</script>

<script>

document.addEventListener('DOMContentLoaded', () => {

    const pageSize = 10;

    document
        .querySelectorAll('table')
        .forEach((table, tableIndex) => {

            if(table.dataset.enhancedTable === 'false' || table.dataset.tableEnhanced === 'true'){
                return;
            }

            const theadRow =
                table.querySelector('thead tr');

            const tbody =
                table.querySelector('tbody');

            if(!theadRow || !tbody){
                return;
            }

            table.dataset.tableEnhanced = 'true';

            const firstHeaderText =
                theadRow
                    .querySelector('th')
                    ?.textContent
                    .trim()
                    .toLowerCase();

            const hasNumberColumn =
                firstHeaderText === 'no' || firstHeaderText === 'nomor';

            if(hasNumberColumn){
                theadRow
                    .querySelector('th')
                    ?.classList
                    .add('table-number-column');
            }else{

                const numberHeader =
                    document.createElement('th');

                numberHeader.textContent = 'No';
                numberHeader.classList.add('table-number-column');
                theadRow.prepend(numberHeader);

            }

            const originalRows =
                Array
                    .from(tbody.querySelectorAll(':scope > tr'))
                    .map(row => {

                        const isEmptyRow =
                            row.children.length === 1
                            && row.children[0].hasAttribute('colspan');

                        if(isEmptyRow){
                            row.children[0].colSpan =
                                theadRow.children.length;

                            return {
                                row,
                                empty:true,
                                searchText:'',
                            };
                        }

                        const numberCell =
                            hasNumberColumn
                                ? row.querySelector('td')
                                : document.createElement('td');

                        numberCell.classList.add(
                            'table-number-column',
                            'text-muted',
                            'fw-semibold'
                        );

                        if(! hasNumberColumn){
                            row.prepend(numberCell);
                        }

                        return {
                            row,
                            empty:false,
                            numberCell,
                            searchText:row.textContent.toLowerCase(),
                        };
                    });

            const dataRows =
                originalRows.filter(item => ! item.empty);

            if(dataRows.length === 0){
                return;
            }

            const wrapper =
                table.closest('.table-responsive') || table.parentElement;

            const controls =
                document.createElement('div');

            controls.className =
                'table-enhancer d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3';

            const searchGroup =
                document.createElement('div');

            searchGroup.className =
                'table-search input-group';

            searchGroup.innerHTML = `
                <span class="input-group-text bg-white">
                    <i class="bi bi-search"></i>
                </span>
                <input
                    type="search"
                    class="form-control"
                    placeholder="Cari data tabel..."
                    aria-label="Cari data tabel">
            `;

            const info =
                document.createElement('div');

            info.className =
                'table-info text-muted small';

            const pagination =
                document.createElement('div');

            pagination.className =
                'table-pagination d-flex align-items-center gap-2';

            controls.append(searchGroup, info);
            wrapper.before(controls);
            wrapper.after(pagination);

            const searchInput =
                searchGroup.querySelector('input');

            const emptySearchRow =
                document.createElement('tr');

            emptySearchRow.innerHTML =
                `<td colspan="${theadRow.children.length}" class="text-center text-muted py-4">Data tidak ditemukan.</td>`;

            emptySearchRow.hidden = true;
            tbody.append(emptySearchRow);

            let currentPage = 1;
            let filteredRows = [...dataRows];

            const render = () => {

                const keyword =
                    searchInput.value.toLowerCase().trim();

                filteredRows =
                    dataRows.filter(item => item.searchText.includes(keyword));

                const totalPages =
                    Math.max(1, Math.ceil(filteredRows.length / pageSize));

                if(currentPage > totalPages){
                    currentPage = totalPages;
                }

                const start =
                    (currentPage - 1) * pageSize;

                const end =
                    start + pageSize;

                originalRows.forEach(item => {
                    item.row.hidden = true;
                });

                filteredRows
                    .slice(start, end)
                    .forEach((item, index) => {
                        item.row.hidden = false;
                        item.numberCell.textContent = start + index + 1;
                    });

                emptySearchRow.hidden =
                    filteredRows.length > 0;

                const firstShown =
                    filteredRows.length === 0 ? 0 : start + 1;

                const lastShown =
                    Math.min(end, filteredRows.length);

                info.textContent =
                    `Menampilkan ${firstShown}-${lastShown} dari ${filteredRows.length} data`;

                pagination.innerHTML = '';

                if(filteredRows.length <= pageSize){
                    return;
                }

                const previousButton =
                    document.createElement('button');

                previousButton.type = 'button';
                previousButton.className = 'btn btn-light border btn-sm';
                previousButton.innerHTML = '<i class="bi bi-chevron-left"></i>';
                previousButton.disabled = currentPage === 1;
                previousButton.addEventListener('click', () => {
                    currentPage--;
                    render();
                });

                const nextButton =
                    document.createElement('button');

                nextButton.type = 'button';
                nextButton.className = 'btn btn-light border btn-sm';
                nextButton.innerHTML = '<i class="bi bi-chevron-right"></i>';
                nextButton.disabled = currentPage === totalPages;
                nextButton.addEventListener('click', () => {
                    currentPage++;
                    render();
                });

                const pageLabel =
                    document.createElement('span');

                pageLabel.className = 'small text-muted';
                pageLabel.textContent = `Halaman ${currentPage} dari ${totalPages}`;

                pagination.append(previousButton, pageLabel, nextButton);
            };

            searchInput.addEventListener('input', () => {
                currentPage = 1;
                render();
            });

            render();
        });
});

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
