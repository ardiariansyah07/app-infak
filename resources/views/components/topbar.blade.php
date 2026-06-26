<div class="topbar">

    <button
        onclick="toggleSidebar()"
        class="btn-toggle">

        <i class="bi bi-list"></i>

    </button>

    <div class="dropdown">

        <button
            class="btn btn-light dropdown-toggle"
            data-bs-toggle="dropdown">

            {{ Auth::user()->name }}

        </button>

        <ul class="dropdown-menu dropdown-menu-end">

            <li>

                <form method="POST"
                      action="{{ route('logout') }}"
                      class="confirm-form"
                      data-confirm-title="Logout?"
                      data-confirm-text="Anda akan keluar dari aplikasi."
                      data-confirm-button="Ya, Logout">

                    @csrf

                    <button
                        type="submit"
                        class="dropdown-item">

                        Logout

                    </button>

                </form>

            </li>

        </ul>

    </div>

</div>
