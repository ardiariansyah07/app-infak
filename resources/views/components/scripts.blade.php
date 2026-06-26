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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
