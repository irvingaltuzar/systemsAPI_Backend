<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h3>Reporte - Nómina de PDF no generados</h3>
    <span style="font-size:10px;">Fecha: {{ $date_process_generated }}</span>
    <br>
    <br>
    <br>
    <table>
        <thead>
            <tr>
                <th style="font-size:12px;">#</th>
                <th style="font-size:12px;">No. de personal</th>
                <th style="font-size:12px;">Nombre</th>
                <th style="font-size:12px;">Nómina</th>
                <th style="font-size:12px;">Ubicación</th>
                <th style="font-size:12px;">Clave de Proceso</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $key => $row)
            <tr>
                <td style="font-size:10px;">{{ $key + 1 }}</td>
                <td style="font-size:10px;">{{ $row->personal_number }}</td>
                <td style="font-size:10px;">{{ $row->user_name }}</td>
                <td style="font-size:10px;">{{ $row->payroll }}</td>
                <td style="font-size:10px;">{{ $row->location }}</td>
                <td style="font-size:10px;">{{ $row->process_key }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>