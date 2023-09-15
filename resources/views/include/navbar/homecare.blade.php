<li>
    <a href="javascript:;" class="has-arrow text-white">
        <div class="parent-icon text-white"><i class="bx bx-first-aid"></i>
        </div>
        <div class="menu-title">Homecare</div>
    </a>
    <ul>
        <li>
            <a href="{{ route('mainPermintaanHC')}}"><i class="bx bx-right-arrow-alt"></i>Permintaan Baru</a>
        </li>
        <li>
            <a href="{{ route('mainRiwayatHC')}}"><i class="bx bx-right-arrow-alt"></i>Riwayat Home Care</a>
        </li>
        <li>
            {{-- <a href="{{ route('indexLayananHC')}}"><i class="bx bx-right-arrow-alt"></i>Jenis Layanan</a> --}}
            <a href="{{ route('mainLayananHC')}}"><i class="bx bx-right-arrow-alt"></i>Jenis Layanan</a>
        </li>
        <li>
            <a href="{{ route('mainPaketHC')}}"><i class="bx bx-right-arrow-alt"></i>Paket Home Care</a>
        </li>
        <li>
            <a href="{{ route('mainTenagaMedis')}}"><i class="bx bx-right-arrow-alt"></i>Tenaga Medis</a>
        </li>
        <li>
            <a href="{{ route('formPengaturanHC')}}"><i class="bx bx-right-arrow-alt"></i>Pengaturan</a>
        </li>
        <li>
            <a href="{{ route('mainSyaratHC')}}"><i class="bx bx-right-arrow-alt"></i>Syarat & Aturan</a>
        </li>
    </ul>
</li>