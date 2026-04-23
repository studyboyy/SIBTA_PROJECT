<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan TA Kaprodi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #0f172a;
            margin: 24px;
        }

        h1,
        h2 {
            margin: 0 0 8px;
        }

        p {
            margin: 0 0 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 20px;
            font-size: 12px;
        }

        th,
        td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            text-align: left;
        }

        th {
            background: #f8fafc;
        }

        .muted {
            color: #475569;
            font-size: 12px;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="no-print" style="margin-bottom: 16px;">
        <button onclick="window.print()">Cetak / Simpan sebagai PDF</button>
    </div>

    <h1>Laporan Komprehensif TA - Kaprodi</h1>
    <p class="muted">Tanggal cetak: {{ now()->translatedFormat('d F Y H:i') }}</p>

    <h2>Ringkasan Statistik</h2>
    <table>
        <tbody>
            <tr>
                <td>Total Mahasiswa TA</td>
                <td>{{ $statistik['total_mahasiswa_ta'] }}</td>
            </tr>
            <tr>
                <td>Siap Sidang</td>
                <td>{{ $statistik['siap_sidang'] }}</td>
            </tr>
            <tr>
                <td>Selesai Sidang</td>
                <td>{{ $statistik['selesai_sidang'] }}</td>
            </tr>
            <tr>
                <td>Masih Proses</td>
                <td>{{ $statistik['masih_proses'] }}</td>
            </tr>
            <tr>
                <td>Rata-rata Progres</td>
                <td>{{ $statistik['rata_rata_progres'] }}%</td>
            </tr>
        </tbody>
    </table>

    <h2>Beban Bimbingan Dosen</h2>
    <table>
        <thead>
            <tr>
                <th>Dosen</th>
                <th>NIDN</th>
                <th>Mahasiswa</th>
                <th>Sesi</th>
                <th>Kuota</th>
                <th>Utilisasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($bebanDosen as $row)
                <tr>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['nidn'] ?: '-' }}</td>
                    <td>{{ $row['total_mahasiswa'] }}</td>
                    <td>{{ $row['total_sesi'] }}</td>
                    <td>{{ $row['kuota'] }}</td>
                    <td>{{ $row['utilisasi'] }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Belum ada data dosen.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Detail Mahasiswa TA</h2>
    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>NIM</th>
                <th>Prodi</th>
                <th>Fase</th>
                <th>Progres</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($detailRows as $row)
                <tr>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['nim'] }}</td>
                    <td>{{ $row['prodi'] }}</td>
                    <td>{{ $row['phase'] }}</td>
                    <td>{{ $row['progress'] }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Belum ada data mahasiswa TA.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
