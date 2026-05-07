@extends('layouts.apoteker')

@section('content')

<div class="page-section active" id="sec-alerts">
  <div class="page-header">
    <div>
      <div class="page-title">Alert Stok</div>
      <div class="page-sub">Obat yang perlu perhatian segera</div>
    </div>
  </div>

  <div class="grid22">
    <div>
      {{-- Sudah Expired --}}
      <div class="card" style="margin-bottom:14px">
        <div class="card-title" style="color:var(--red)">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#E63946">
            <path d="M440-440h80v-200h-80v200Zm40 120q17 0 28.5-11.5T520-360q0-17-11.5-28.5T480-400q-17 0-28.5 11.5T440-360q0 17 11.5 28.5T480-320ZM160-200v-80h80v-280q0-83 50-147.5T420-792v-28q0-25 17.5-42.5T480-880q25 0 42.5 17.5T540-820v28q80 20 130 84.5T720-560v280h80v80H160Zm320-300Zm0 420q-33 0-56.5-23.5T400-160h160q0 33-23.5 56.5T480-80ZM320-280h320v-280q0-66-47-113t-113-47q-66 0-113 47t-47 113v280Z"/>
          </svg>
          Sudah Expired
        </div>
        @forelse($sudahExpired as $item)
          <div style="padding:8px 0; border-bottom:1px solid #f0ece8; display:flex; justify-content:space-between;">
            <span>{{ $item->nama_obat }}</span>
            <span style="color:var(--red); font-size:0.85rem">
              Expired: {{ $item->tgl_expired->format('d/m/Y') }}
            </span>
          </div>
        @empty
          <p style="color:gray; font-size:0.9rem; padding:8px 0">Tidak ada obat yang sudah expired.</p>
        @endforelse
      </div>

      {{-- Stok Kritis --}}
      <div class="card">
        <div class="card-title" style="color:var(--amber)">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#F4A261">
            <path d="m40-120 440-760 440 760H40Zm138-80h604L480-720 178-200Zm330.5-51.5Q520-263 520-280t-11.5-28.5Q497-320 480-320t-28.5 11.5Q440-297 440-280t11.5 28.5Q463-240 480-240t28.5-11.5ZM440-360h80v-200h-80v200Zm40-100Z"/>
          </svg>
          Stok Kritis (&lt;10)
        </div>
        @forelse($stokKritis as $item)
          <div style="padding:8px 0; border-bottom:1px solid #f0ece8; display:flex; justify-content:space-between;">
            <span>{{ $item->nama_obat }}</span>
            <span style="color:var(--amber); font-size:0.85rem">
              Stok: {{ $item->jumlah }}
            </span>
          </div>
        @empty
          <p style="color:gray; font-size:0.9rem; padding:8px 0">Tidak ada stok kritis.</p>
        @endforelse
      </div>
    </div>

    {{-- Mendekati Expired --}}
    <div class="card">
      <div class="card-title" style="color:var(--orange)">
        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#E76F51">
          <path d="M339.5-108.5q-65.5-28.5-114-77t-77-114Q120-365 120-440t28.5-140.5q28.5-65.5 77-114t114-77Q405-800 480-800t140.5 28.5q65.5 28.5 114 77t77 114Q840-515 840-440t-28.5 140.5q-28.5 65.5-77 114t-114 77Q555-80 480-80t-140.5-28.5ZM480-440Zm112 168 56-56-128-128v-184h-80v216l152 152ZM224-866l56 56-170 170-56-56 170-170Zm512 0 170 170-56 56-170-170 56-56ZM480-160q117 0 198.5-81.5T760-440q0-117-81.5-198.5T480-720q-117 0-198.5 81.5T200-440q0 117 81.5 198.5T480-160Z"/>
        </svg>
        Mendekati Expired (&lt;90 hari)
      </div>
      @forelse($mendekatiExpired as $item)
        <div style="padding:8px 0; border-bottom:1px solid #f0ece8; display:flex; justify-content:space-between;">
          <span>{{ $item->nama_obat }}</span>
          <span style="color:var(--orange); font-size:0.85rem">
            Expired: {{ $item->tgl_expired->format('d/m/Y') }}
          </span>
        </div>
      @empty
        <p style="color:gray; font-size:0.9rem; padding:8px 0">Tidak ada obat mendekati expired.</p>
      @endforelse
    </div>
  </div>
</div>

@endsection